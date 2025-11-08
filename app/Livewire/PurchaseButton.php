<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

class PurchaseButton extends Component
{
    public Document $document;
    public $purchase;

    public function mount()
    {
        if (Auth::check()) {
            $this->purchase = Purchase::where('user_id', Auth::id())
                ->where('document_id', $this->document->id)
                ->first();
        }
    }

    public function render()
    {
        return view('livewire.purchase-button');
    }

    public function createPayment()
    {
        $this->validate(['document.price' => 'required|numeric|min:1']);

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Create a transaction record
        $transaction = $this->document->transactions()->create([
            'gateway' => 'cashfree',
            'gateway_order_id' => 'order_' . uniqid(), // Replace with a more robust order ID
            'user_id' => Auth::id(),
            'amount' => $this->document->price,
            'status' => 'pending',
        ]);

        // Call Cashfree API to create a payment link
        $cashfreeUrl = config('services.cashfree.url');
        $apiKey = config('services.cashfree.key');
        $apiSecret = config('services.cashfree.secret');

        // Validate configuration
        if (!$cashfreeUrl || !$apiKey || !$apiSecret) {
            session()->flash('error', 'Payment gateway is not properly configured. Please contact support.');
            $transaction->update(['status' => 'failed']);
            return;
        }

        try {
            $headers = [
                'x-client-id' => $apiKey,
                'x-client-secret' => $apiSecret,
                'x-api-version' => '2022-01-01',
                'Content-Type' => 'application/json',
            ];

            $payload = [
                'order_id' => $transaction->gateway_order_id,
                'order_amount' => $transaction->amount,
                'order_currency' => 'INR',
                'customer_details' => [
                    'customer_id' => (string) Auth::id(),
                    'customer_email' => Auth::user()->email,
                    'customer_phone' => '9999999999', // Replace with actual phone number if available
                ],
                'order_meta' => [
                    'return_url' => route('document.preview', $this->document->id) . '?order_id={order_id}&payment_id={payment_id}',
                ],
            ];

            // Include webhook notify_url only if configured
            if (config('services.cashfree.webhook_secret')) {
                $payload['order_meta']['notify_url'] = route('cashfree.webhook');
            }

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post(rtrim($cashfreeUrl, '/') . '/orders', $payload);

            if ($response->successful()) {
                $paymentLink = $response->json('payment_link');
                if ($paymentLink) {
                    return redirect($paymentLink);
                } else {
                    session()->flash('error', 'Payment link not received from gateway.');
                    $transaction->update(['status' => 'failed']);
                }
            } else {
                $errorMessage = $response->json('message') ?? 'Payment gateway error';
                session()->flash('error', 'Payment gateway error: ' . $errorMessage);
                $transaction->update(['status' => 'failed']);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Connection error: ' . $e->getMessage());
            $transaction->update(['status' => 'failed']);
        }

        // The payment processing logic is now handled in the try-catch block above
    }

    public function getDownloadUrlProperty()
    {
        if (!$this->purchase) {
            return null;
        }

        return URL::temporarySignedRoute(
            'document.download',
            now()->addMinutes(10),
            ['document' => $this->document]
        );
    }
}