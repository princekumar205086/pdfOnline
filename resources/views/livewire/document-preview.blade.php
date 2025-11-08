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
                <div class="border relative h-96" oncontextmenu="return false;">
                    @if($previewDataUri)
                        <iframe
                            src="{{ $previewDataUri }}#toolbar=0&navpanes=0&scrollbar=0"
                            class="w-full h-full"
                            sandbox="allow-scripts allow-same-origin"
                            style="border:0;"
                        ></iframe>
                        <!-- Transparent overlay to discourage interactions like clicking toolbar -->
                        <div class="absolute inset-0" style="pointer-events: none;"></div>
                    @else
                        <div class="flex items-center justify-center w-full h-full">
                            <p class="text-center text-gray-500">Preview unavailable. The file may be missing or cannot be rendered.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>