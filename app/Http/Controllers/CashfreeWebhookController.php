<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class CashfreeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        $signature = $request->header('x-webhook-signature');
        $secret = config('services.cashfree.webhook_secret');

        if (! $this->verifySignature($payload, $signature, $secret)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transaction = Transaction::where('gateway_order_id', $payload['data']['order']['order_id'])->first();

        if (! $transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($payload['event_time'] && $payload['type'] === 'PAYMENT_SUCCESS_WEBHOOK') {
            $transaction->update([
                'status' => 'completed',
                'gateway_payment_id' => $payload['data']['payment']['cf_payment_id'],
                'meta' => $payload,
            ]);

            // Create a purchase record
            $transaction->document->purchases()->create([
                'user_id' => $transaction->user_id,
                'transaction_id' => $transaction->id,
            ]);
        } else {
            $transaction->update(['status' => 'failed', 'meta' => $payload]);
        }

        return response()->json(['message' => 'Webhook handled successfully']);
    }

    private function verifySignature(array $payload, $signature, $secret)
    {
        // Implementation of Cashfree's signature verification logic
        // This is a simplified example. Refer to Cashfree's documentation for the exact implementation.
        $data = $payload['data']['order']['order_id'] . $payload['data']['order']['order_amount'];
        $hash = hash_hmac('sha256', $data, $secret);

        return hash_equals($hash, $signature);
    }
}