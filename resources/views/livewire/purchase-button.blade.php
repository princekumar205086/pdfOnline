<div>
    @if (session()->has('error'))
        <div class="bg-red-500 text-white p-4 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if ($purchase)
        <a href="{{ $this->downloadUrl }}" class="bg-green-500 text-white px-4 py-2 rounded">
            Download Now
        </a>
        <p class="text-sm text-gray-500 mt-2">Link expires in 10 minutes.</p>
    @else
        <button wire:click="createPayment" class="bg-blue-500 text-white px-4 py-2 rounded">
            Pay ₹{{ $document->price }} to Download
        </button>
    @endif
</div>