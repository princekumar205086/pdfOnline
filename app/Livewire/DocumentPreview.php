<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use App\Models\Transaction;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DocumentPreview extends Component
{
    public Document $document;

    public function mount(Document $document)
    {
        $this->document = $document;

        // If returning from Cashfree, verify payment
        $paymentId = request()->query('payment_id');
        $orderId = request()->query('order_id');
        if ((
            $paymentId || $orderId
        ) && Auth::check()) {
            $cashfreeUrl = config('services.cashfree.url');
            $apiKey = config('services.cashfree.key');
            $apiSecret = config('services.cashfree.secret');

            if ($cashfreeUrl && $apiKey && $apiSecret) {
                try {
                    // Prefer verifying via Orders endpoint
                    $headers = [
                        'x-client-id' => $apiKey,
                        'x-client-secret' => $apiSecret,
                        'x-api-version' => '2022-01-01',
                    ];

                    $verified = false;
                    $data = null;

                    if ($orderId) {
                        $orderResp = Http::withHeaders($headers)
                            ->timeout(30)
                            ->get(rtrim($cashfreeUrl, '/') . '/orders/' . $orderId);

                        if ($orderResp->successful()) {
                            $data = $orderResp->json();
                            // Try multiple shapes: direct, nested
                            $status = $data['order_status']
                                ?? ($data['order']['order_status'] ?? null);

                            if (in_array($status, ['PAID', 'COMPLETED', 'SUCCESS'])) {
                                $verified = true;
                            } elseif ($status) {
                                // Explicit failure
                                $verified = false;
                            }
                        } else {
                            // Fall through to payment endpoint
                        }
                    }

                    if (!$verified && $paymentId) {
                        $payResp = Http::withHeaders($headers)
                            ->timeout(30)
                            ->get(rtrim($cashfreeUrl, '/') . '/payments/' . $paymentId);

                        if ($payResp->successful()) {
                            $data = $payResp->json();
                            $status = $data['payment_status'] ?? $data['status'] ?? null;
                            $orderId = $orderId
                                ?? ($data['order_id'] ?? ($data['order']['order_id'] ?? null));
                            if (in_array($status, ['SUCCESS', 'COMPLETED'])) {
                                $verified = true;
                            }
                        }
                    }

                    if ($verified && $orderId) {
                        $transaction = Transaction::where('gateway_order_id', $orderId)
                            ->where('document_id', $this->document->id)
                            ->where('user_id', Auth::id())
                            ->first();

                        if ($transaction) {
                            $transaction->update([
                                'status' => 'completed',
                                'gateway_payment_id' => $paymentId,
                                'meta' => $data,
                            ]);

                            Purchase::firstOrCreate([
                                'user_id' => $transaction->user_id,
                                'document_id' => $transaction->document_id,
                            ], [
                                'transaction_id' => $transaction->id,
                            ]);
                        }
                    } else {
                        if ($orderId) {
                            Transaction::where('gateway_order_id', $orderId)
                                ->where('document_id', $this->document->id)
                                ->where('user_id', Auth::id())
                                ->update([
                                    'status' => 'failed',
                                    'meta' => $data,
                                ]);
                        }
                        session()->flash('error', 'Payment verification failed: endpoint or status not valid.');
                    }
                } catch (\Exception $e) {
                    session()->flash('error', 'Payment verification error: ' . $e->getMessage());
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.document-preview');
    }
}