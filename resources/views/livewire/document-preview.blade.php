<div>
    <div class="container mx-auto py-8">
        <a href="{{ url('/') }}" class="text-blue-500">&larr; Back to Search</a>

        <h1 class="text-2xl font-bold my-4">{{ $document->title }}</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h2 class="text-xl font-bold mb-2">Document Details</h2>
                <p><strong>District:</strong> {{ $document->district }}</p>
                <p><strong>Anchal:</strong> {{ $document->anchal }}</p>
                <p><strong>Mauza:</strong> {{ $document->mauza }}</p>
                <p><strong>Thana No:</strong> {{ $document->thana_no }}</p>
                <p><strong>Price:</strong> ₹{{ $document->price }}</p>

                <div class="mt-4">
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(config('services.cashfree.key') === 'YOUR_TEST_APP_ID_HERE' || !config('services.cashfree.key'))
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                            <strong>Test Mode:</strong> Cashfree payment gateway is not configured. 
                            <br>To enable payments:
                            <br>1. Sign up at <a href="https://sandbox.cashfree.com" target="_blank" class="underline">Cashfree Sandbox</a>
                            <br>2. Add your App ID and Secret Key to the .env file
                        </div>
                    @endif
                    
                    @livewire('purchase-button', ['document' => $document])
                </div>
            </div>
            <div>
                <h2 class="text-xl font-bold mb-2">PDF Preview</h2>
                <div class="border p-4 h-96">
                    {{-- This is a placeholder for the PDF preview. 
                         A more advanced implementation might use a library like PDF.js 
                         or an iframe to display a watermarked version. --}}
                    <p class="text-center text-gray-500">PDF preview is not available in this demo.</p>
                </div>
            </div>
        </div>
    </div>
</div>