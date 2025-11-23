<div>
    <header class="document-header bg-black p-2 flex items-center justify-between shadow-sm">
        <a href="{{ url('/') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Search
        </a>

        <!-- Right: Status & Details Button -->
        <div class="flex flex-col items-end gap-2">
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded text-sm">
                    {{ session('error') }}
                </div>
            @endif
            <div class="flex gap-2">
                <button wire:click="openDetailsModal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-medium">
                    View Details
                </button>
                <button wire:click="openPageSelector" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm font-medium">
                    Download Pages
                </button>
            </div>
        </div>
    </header>

    <div class="preview-container overflow-hidden">
        @if($previewDataUri)
            @php $isPdf = Str::contains($previewDataUri, 'application/pdf'); @endphp
            @if($isPdf)
                <div class="relative w-full h-[92vh]">
                    <iframe src="{{ $this->getPdfViewerUrl() }}" class="w-full h-full border-0" id="pdfViewer" wire:ignore
                        loading="eager"></iframe>
                </div>
            @else
                <div class="flex justify-center items-center w-full h-[120vh] bg-white">
                    <img src="{{ $previewDataUri }}" alt="Document Preview"
                        class="max-w-full max-h-full object-contain select-none" draggable="false" />
                </div>
            @endif
        @else
            <div class="flex items-center justify-center w-full h-64 bg-gray-800 text-white">
                <div class="text-center">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <p class="text-lg">Preview unavailable. File missing or cannot be rendered.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Document Details Modal -->
    @if($showDetailsModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="closeDetailsModal">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-6 border-b">
                    <h2 class="text-2xl font-bold text-gray-900">Document Details</h2>
                    <button wire:click="closeDetailsModal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Document Type</label>
                            <p class="text-gray-900 mt-1">{{ $document->document_type ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">District</label>
                            <p class="text-gray-900 mt-1">{{ $document->district ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Anchal</label>
                            <p class="text-gray-900 mt-1">{{ $document->anchal ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mauza</label>
                            <p class="text-gray-900 mt-1">{{ $document->mauza ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Thana No</label>
                            <p class="text-gray-900 mt-1">{{ $document->thana_no ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <p class="text-gray-900 mt-1">{{ $document->title ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if($document->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <p class="text-gray-900 mt-1">{{ $document->description }}</p>
                        </div>
                    @endif

                    <div class="flex gap-3 mt-6 pt-4 border-t">
                        <button wire:click="closeDetailsModal" class="flex-1 bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 font-medium">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Page Selector Modal -->
    @if($showPageSelector)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click.self="closePageSelector">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
                <div class="flex items-center justify-between p-6 border-b">
                    <h2 class="text-2xl font-bold text-gray-900">Select Pages to Download</h2>
                    <button wire:click="closePageSelector" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Pages: <span class="text-blue-600 font-bold">{{ $totalPages }}</span></label>
                        <p class="text-xs text-gray-600 mb-3">Price: ₹{{ $pricePerPage }} per page</p>
                    </div>

                    <div>
                        <label for="pages" class="block text-sm font-medium text-gray-700 mb-2">Enter Page Numbers</label>
                        <input 
                            type="text" 
                            wire:model.defer="selectedPages" 
                            id="pages"
                            placeholder="e.g., 1,3,5 or 4,5-7,10" 
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <p class="text-xs text-gray-600 mt-2">Examples: Enter single pages separated by commas (1,3,5) or use ranges (5-10)</p>
                        
                        @error('selectedPages')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    @if(!empty($pageArray))
                        <div class="bg-gray-50 p-4 rounded border border-gray-200">
                            <p class="text-sm font-medium text-gray-700 mb-2">Selected Pages ({{ count($pageArray) }} pages):</p>
                            <p class="text-sm text-gray-900 mb-3">{{ implode(', ', $pageArray) }}</p>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded p-3">
                                <p class="text-lg font-bold text-blue-900">
                                    Total Cost: ₹{{ number_format($calculatedPrice, 2) }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-3 mt-6 pt-4 border-t">
                        <button wire:click="parsePages" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-medium">
                            Calculate Price
                        </button>
                        <button wire:click="closePageSelector" class="flex-1 bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 font-medium">
                            Cancel
                        </button>
                    </div>

                    @if(!empty($pageArray) && $calculatedPrice > 0)
                        <div class="pt-4 border-t">
                            <livewire:page-purchase-button :document="$document" :pages="$pageArray" :price="$calculatedPrice" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('keydown', function (e) {
            if (e.key === 'PrintScreen') {
                e.preventDefault();
                showSecurityWarning('Screenshot blocked for security');
                return false;
            }

            const forbiddenCombos = [
                e.ctrlKey && ['s', 'p'].includes(e.key.toLowerCase()),
                e.metaKey && ['s', 'p'].includes(e.key.toLowerCase()),
            ];

            if (forbiddenCombos.some(Boolean)) {
                e.preventDefault();
                showSecurityWarning('Action blocked for security');
                return false;
            }
        });

        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
            showSecurityWarning('Right-click disabled for security');
            return false;
        });

        function showSecurityWarning(message) {
            const existing = document.querySelector('.security-warning');
            if (existing) existing.remove();

            const warning = document.createElement('div');
            warning.className = 'security-warning fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-600 text-white px-4 py-2 rounded-lg shadow-lg z-50 text-sm';
            warning.textContent = message;
            document.body.appendChild(warning);

            setTimeout(() => warning.remove(), 3000);
        }
    });
</script>