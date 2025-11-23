<div>
    @if (session()->has('error'))
        <div class="bg-red-500 text-white p-4 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <button wire:click="createPayment" class="w-full bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 font-bold text-lg">
        Pay ₹{{ number_format($price, 2) }} to Download Selected Pages
    </button>
    <p class="text-sm text-gray-500 mt-2">Download link expires in 10 minutes after payment.</p>
</div>
