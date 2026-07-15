<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\InstructorLevel;
use App\Models\RevenueShare;
use App\Models\Course;
use App\Models\User;
use App\Models\Refund;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionEngine
{
    public function processSale(Course $course, User $student, float $grossAmount, array $options = []): Transaction
    {
        $instructor = $course->owner;
        $level = InstructorLevel::forInstructor($instructor);
        $commissionRate = $level?->commission_rate ?? 25;

        // Plan-based discount on commission (e.g., Pro Creator plan gets -5%)
        if (isset($options['plan_commission_discount'])) {
            $commissionRate = max(0, $commissionRate - $options['plan_commission_discount']);
        }

        // First-sale bonus: 0% commission for first N sales
        $salesCount = Transaction::where('instructor_id', $instructor->id)
            ->where('type', 'course_purchase')
            ->where('status', 'completed')
            ->count();
        $firstSaleBonusLimit = config('elms.first_sale_bonus_count', 0);
        if ($firstSaleBonusLimit > 0 && $salesCount < $firstSaleBonusLimit) {
            $commissionRate = 0;
        }

        $commissionAmount = round($grossAmount * ($commissionRate / 100), 2);
        $gatewayFee = $options['gateway_fee'] ?? 0;
        $taxAmount = $options['tax_amount'] ?? 0;
        $affiliateCommission = $options['affiliate_commission'] ?? 0;

        $netAmount = $grossAmount - $commissionAmount - $gatewayFee - $taxAmount - $affiliateCommission;

        // Institution revenue share (if applicable)
        $institutionShare = 0;
        $teacherNet = $netAmount;
        if ($course->tenant_id && $instructor->role === 'teacher') {
            $revenueShare = RevenueShare::forTeacher($instructor->id, $course->tenant_id);
            if ($revenueShare) {
                $institutionShare = round($netAmount * ($revenueShare->institution_percentage / 100), 2);
                $teacherNet = $netAmount - $institutionShare;
            }
        }

        return DB::transaction(function () use ($course, $student, $instructor, $grossAmount, $commissionRate, $commissionAmount, $gatewayFee, $taxAmount, $affiliateCommission, $netAmount, $institutionShare, $teacherNet, $level, $options) {
            $transaction = Transaction::create([
                'tenant_id' => $course->tenant_id,
                'user_id' => $student->id,
                'course_id' => $course->id,
                'instructor_id' => $instructor->id,
                'type' => 'course_purchase',
                'amount' => $grossAmount,
                'currency' => $options['currency'] ?? 'USD',
                'status' => 'completed',
                'payment_method' => $options['payment_method'] ?? null,
                'transaction_reference' => $options['reference'] ?? null,
                'gross_amount' => $grossAmount,
                'commission_amount' => $commissionAmount,
                'commission_rate_applied' => $commissionRate,
                'gateway_fee' => $gatewayFee,
                'tax_amount' => $taxAmount,
                'net_amount' => $teacherNet,
                'metadata' => array_merge($options['metadata'] ?? [], [
                    'institution_share' => $institutionShare,
                    'affiliate_commission' => $affiliateCommission,
                ]),
                'coupon_id' => $options['coupon_id'] ?? null,
                'referral_id' => $options['referral_id'] ?? null,
                'instructor_level_at_sale' => $level?->name ?? 'Starter',
            ]);

            // Credit instructor wallet (pending during refund window)
            $wallet = Wallet::getOrCreateForUser($instructor->id);
            $wallet->credit($teacherNet, pending: true);

            // Credit institution wallet if revenue share applies
            if ($institutionShare > 0 && $course->tenant_id) {
                $admin = User::where('tenant_id', $course->tenant_id)
                    ->where('role', 'admin')
                    ->first();
                if ($admin) {
                    $instWallet = Wallet::getOrCreateForUser($admin->id);
                    $instWallet->credit($institutionShare, pending: true);
                }
            }

            // Notify instructor
            app(NotificationService::class)->notify(
                $instructor->id,
                'success',
                'You made a sale!',
                "Your course \"{$course->title}\" was purchased by a new student. Net earnings: {$teacherNet} {$transaction->currency}"
            );

            return $transaction;
        });
    }

    public function processRefund(Transaction $transaction, string $reason, int $processedBy = null): Refund
    {
        return DB::transaction(function () use ($transaction, $reason, $processedBy) {
            $refund = Refund::create([
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'amount' => $transaction->gross_amount,
                'reason' => $reason,
                'status' => 'completed',
                'processed_by' => $processedBy,
                'processed_at' => now(),
            ]);

            $transaction->update(['status' => 'refunded']);

            // Reverse wallet credit
            $wallet = Wallet::where('user_id', $transaction->instructor_id)->first();
            if ($wallet) {
                $netAmount = (float)$transaction->net_amount;
                if ($wallet->pending_balance >= $netAmount) {
                    $wallet->decrement('pending_balance', $netAmount);
                } else {
                    $wallet->decrement('balance', $netAmount);
                    $wallet->decrement('total_earned', $netAmount);
                }
            }

            return $refund;
        });
    }

    public function releasePendingFunds(int $daysThreshold = 7): int
    {
        $cutoff = now()->subDays($daysThreshold);
        $transactions = Transaction::where('type', 'course_purchase')
            ->where('status', 'completed')
            ->where('created_at', '<=', $cutoff)
            ->whereNotNull('instructor_id')
            ->whereDoesntHave('refund')
            ->get();

        $released = 0;
        foreach ($transactions as $tx) {
            $wallet = Wallet::where('user_id', $tx->instructor_id)->first();
            if ($wallet && $wallet->pending_balance >= (float)$tx->net_amount) {
                $wallet->movePendingToAvailable((float)$tx->net_amount);
                $released++;
            }
        }

        return $released;
    }
}
