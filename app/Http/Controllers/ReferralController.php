<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\Course;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index()
    {
        $referrals = Referral::where('referrer_id', auth()->id())->latest()->paginate(20);
        $totalEarnings = $referrals->sum('commission_amount');
        return view('referrals.index', compact('referrals', 'totalEarnings'));
    }

    public function generateLink(Request $request, Course $course)
    {
        $referral = Referral::firstOrCreate(
            ['referrer_id' => auth()->id(), 'course_id' => $course->id, 'status' => 'pending'],
            ['referral_code' => Referral::generateCode()]
        );
        $link = route('marketplace.show', $course) . '?ref=' . $referral->referral_code;
        return response()->json(['link' => $link, 'code' => $referral->referral_code]);
    }
}
