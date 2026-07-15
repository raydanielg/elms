<?php

namespace App\Http\Controllers;

use App\Models\PaymentGateway;
use App\Models\PaymentWebhookLog;
use App\Services\Payment\PaymentManager;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    public function index()
    {
        $this->authorize('superAdminOnly');
        $gateways = PaymentGateway::orderBy('priority')->get();
        $manager = app(PaymentManager::class);
        $availableDrivers = $manager->availableDrivers();
        return view('payment-gateways.index', compact('gateways', 'availableDrivers'));
    }

    public function store(Request $request)
    {
        $this->authorize('superAdminOnly');
        $validated = $request->validate([
            'driver' => 'required|string|unique:payment_gateways,driver',
            'label' => 'required|string',
            'category' => 'required|string',
            'priority' => 'integer|min:0',
            'credentials' => 'nullable|array',
            'supported_currencies' => 'nullable|array',
            'supported_countries' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        PaymentGateway::create($validated);
        return response()->json(['message' => 'Payment gateway added successfully']);
    }

    public function update(Request $request, PaymentGateway $gateway)
    {
        $this->authorize('superAdminOnly');
        $validated = $request->validate([
            'label' => 'string',
            'priority' => 'integer|min:0',
            'credentials' => 'nullable|array',
            'supported_currencies' => 'nullable|array',
            'supported_countries' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        $gateway->update($validated);
        return response()->json(['message' => 'Payment gateway updated']);
    }

    public function destroy(PaymentGateway $gateway)
    {
        $this->authorize('superAdminOnly');
        $gateway->delete();
        return response()->json(['message' => 'Payment gateway removed']);
    }

    public function webhookLogs()
    {
        $this->authorize('superAdminOnly');
        $logs = PaymentWebhookLog::latest()->paginate(50);
        return view('payment-gateways.logs', compact('logs'));
    }

    public function handleWebhook(Request $request, string $gateway)
    {
        $payload = $request->all();

        PaymentWebhookLog::create([
            'gateway' => $gateway,
            'event_id' => $payload['event_id'] ?? null,
            'transaction_reference' => $payload['data']['id'] ?? ($payload['CheckoutRequestID'] ?? null),
            'payload' => $payload,
            'status' => 'received',
        ]);

        $manager = app(PaymentManager::class);
        try {
            $driver = $manager->driver($gateway);
            $result = $driver->handleWebhook($payload);

            if (($result['status'] ?? '') === 'paid') {
                $reference = $result['reference'] ?? null;
                if ($reference) {
                    $transaction = \App\Models\Transaction::where('gateway_reference', $reference)->first();
                    if ($transaction && $transaction->status !== 'completed') {
                        $transaction->update(['status' => 'completed']);
                    }
                }
            }

            return response()->json(['status' => 'processed']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
