<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

class PagePurchaseButton extends Component
{
    public Document $document;
    public array $pages = [];
    public float $price = 0;
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
        return view('livewire.page-purchase-button');
    }

    public function createPayment()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'price' => 'required|numeric|min:15',
            'pages' => 'required|array|min:1',
        ]);

        // Create a transaction record with selected pages
        $transaction = $this->document->transactions()->create([
            'gateway' => 'cashfree',
            'gateway_order_id' => 'order_' . uniqid(),
            'user_id' => Auth::id(),
            'amount' => $this->price,
            'selected_pages' => json_encode($this->pages),
            'status' => 'pending',
        ]);

        // Call Cashfree API
        $cashfreeUrl = config('services.cashfree.url');
        $apiKey = config('services.cashfree.key');
        $apiSecret = config('services.cashfree.secret');

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
                    'customer_phone' => '9999999999',
                ],
                'order_meta' => [
                    'return_url' => route('document.preview', $this->document->id) . '?order_id={order_id}',
                ],
            ];

            if (config('services.cashfree.webhook_secret')) {
                $payload['order_meta']['notify_url'] = route('cashfree.webhook');
            }

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->withoutVerifying() // Add this to bypass SSL verification for sandbox
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
    }
}
