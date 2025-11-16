<div>
    <header class="document-header bg-white px-4 py-4 shadow-sm">
        <div class="container mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <!-- Back Link -->
            <a href="{{ url('/') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Search
            </a>

            <!-- Right: Status & Purchase -->
            <div class="flex flex-col items-end gap-2">
                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @if(config('services.cashfree.key') === 'YOUR_TEST_APP_ID_HERE' || !config('services.cashfree.key'))
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-2 rounded text-sm">
                        <strong>Test Mode:</strong> Cashfree not configured.
                    </div>
                @endif

                @livewire('purchase-button', ['document' => $document])
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $document->title }}</h1>

        <!-- Document Preview -->
        <div class="preview-container relative rounded-lg mb-8 overflow-hidden">
            @if($previewDataUri)
                @php $isPdf = Str::contains($previewDataUri, 'application/pdf'); @endphp

                @if($isPdf)

                    <div class="preview-container relative rounded-lg mb-8 overflow-hidden border border-gray-200 bg-white">
    <!-- Mobile warning -->
    <div id="mobileWarning" class="md:hidden bg-blue-50 border-b border-blue-200 p-3 text-sm text-blue-800 text-center hidden">
        <div class="flex items-center justify-center space-x-2">
            <span>📱</span>
            <span>Viewing PDF on mobile. Pinch to zoom.</span>
        </div>
    </div>
    
    <!-- PDF Container with responsive height -->
    <div class="relative w-full h-[70vh] md:h-[160vh] lg:h-[140vh] overflow-auto md:overflow-hidden">
        <iframe id="securePDF" 
                src="{{ $previewDataUri }}#toolbar=0&navpanes=0&scrollbar=1&view=FitV"
                class="w-full h-full min-h-[500px] md:min-h-[800px] border-none"
                allow="clipboard-read; clipboard-write"
                loading="eager"
                style="transform: scale(1); transform-origin: 0 0;">
        </iframe>

        <!-- Security Overlay -->
        <div id="securityOverlay" class="absolute inset-0 z-10" style="pointer-events: auto;"></div>
    </div>
    
    <!-- Mobile help text -->
    <div class="md:hidden bg-gray-50 border-t border-gray-200 p-2 text-xs text-gray-600 text-center">
        Use two fingers to scroll horizontally in the PDF
    </div>
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
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <p class="text-lg">Preview unavailable. File missing or cannot be rendered.</p>
                    </div>
                </div>
            @endif
        </div>
    </main>

    <footer class="mt-12 py-6 border-t border-gray-200 text-center text-gray-500 text-sm">
        <p>Document preview will expire after 10 minutes of viewing.</p>
    </footer>
</div>

<!-- for security -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const pdfContainer = document.querySelector('.preview-container');
        if (!pdfContainer) return;

        const ACCESS_DURATION = 300000; // 10 minutes (in ms)

        window.addEventListener('keydown', (e) => {
            const forbiddenCombos = [
                e.ctrlKey && ['s', 'p', 'u', 'c'].includes(e.key.toLowerCase()),
                e.metaKey && ['s', 'p', 'u', 'c'].includes(e.key.toLowerCase()),
                e.key === 'PrintScreen',
                (e.key === 'S' && e.shiftKey && e.metaKey), // macOS screenshot
                (e.shiftKey && e.key === 'S' && e.metaKey),
                (e.shiftKey && e.key === 'S' && e.ctrlKey),
                (e.shiftKey && e.key === 'S' && e.altKey),
                (e.shiftKey && e.key === 'S' && e.metaKey),
                (e.shiftKey && e.key === 'S' && e.ctrlKey), // Windows + Shift + S
            ];

            if (forbiddenCombos.some(Boolean)) {
                e.preventDefault();
                showWarning('⚠️ Screenshot / Copy / Save blocked for security reasons');
            }
        });

        //  Detect DevTools open
        let devToolsDetected = false;
        const detectDevTools = setInterval(() => {
            const threshold = 160;
            if (window.outerWidth - window.innerWidth > threshold || window.outerHeight - window.innerHeight > threshold) {
                if (!devToolsDetected) {
                    devToolsDetected = true;
                    showWarning('⚠️ Developer tools detected! Action blocked.');
                }
            } else devToolsDetected = false;
        }, 1000);

        // Auto-hide PDF after duration
        setTimeout(() => {
            pdfContainer.innerHTML = `
            <div class="flex flex-col items-center justify-center w-full h-64 bg-gray-800 text-white">
                <svg class="w-12 h-12 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-lg">PDF access expired. Please reopen to view again.</p>
            </div>
        `;
            clearInterval(detectDevTools);
        }, ACCESS_DURATION);

        function showWarning(msg) {
            const existing = document.querySelector('.security-warning');
            if (existing) existing.remove();

            const div = document.createElement('div');
            div.className = 'security-warning fixed top-4 left-1/2 -translate-x-1/2 bg-red-600 text-white px-5 py-3 rounded-lg shadow-lg z-50 text-sm';
            div.textContent = msg;
            document.body.appendChild(div);
            setTimeout(() => div.remove(), 3000);
        }

        document.addEventListener('contextmenu', (e) => e.preventDefault());
    });


     document.addEventListener('DOMContentLoaded', () => {
            const previewContainer = document.querySelector('.preview-container');
            const securityOverlay = document.getElementById('securityOverlay');
            const ACCESS_DURATION = 600000; // 10 minutes (in ms)
            
            if (!previewContainer) return;

            // Enhanced security event handlers
            const blockEvent = (e) => {
                // Block right-click
                if (e.type === 'contextmenu') {
                    e.preventDefault();
                    showWarning('⚠️ Right-click disabled for security');
                    return false;
                }
                
                // Block keyboard shortcuts
                if (e.type === 'keydown') {
                    const forbiddenCombos = [
                        e.ctrlKey && ['s', 'p', 'u', 'c', 'a'].includes(e.key.toLowerCase()),
                        e.metaKey && ['s', 'p', 'u', 'c', 'a'].includes(e.key.toLowerCase()),
                        e.key === 'PrintScreen',
                        (e.key === 'S' && e.shiftKey && e.metaKey), // macOS screenshot
                        (e.shiftKey && e.key === 'S' && e.ctrlKey), // Windows + Shift + S
                        e.key === 'F12', // DevTools
                        (e.ctrlKey && e.shiftKey && ['i', 'c', 'j'].includes(e.key.toLowerCase())), // DevTools shortcuts
                    ];

                    if (forbiddenCombos.some(Boolean)) {
                        e.preventDefault();
                        e.stopPropagation();
                        showWarning('⚠️ Action blocked for security');
                        return false;
                    }
                }
                
                // Block drag operations
                if (e.type === 'dragstart') {
                    e.preventDefault();
                    return false;
                }
            };

            // Apply event listeners to the security overlay
            ['contextmenu', 'keydown', 'dragstart'].forEach(event => {
                securityOverlay.addEventListener(event, blockEvent);
            });

            // Also apply to document for global key blocking
            document.addEventListener('keydown', blockEvent);
            
            // Disable text selection in the entire preview area
            previewContainer.addEventListener('selectstart', (e) => {
                e.preventDefault();
                return false;
            });

            // Detect DevTools open
            let devToolsDetected = false;
            const detectDevTools = setInterval(() => {
                const threshold = 160;
                const widthThreshold = window.outerWidth - window.innerWidth > threshold;
                const heightThreshold = window.outerHeight - window.innerHeight > threshold;
                
                if (widthThreshold || heightThreshold) {
                    if (!devToolsDetected) {
                        devToolsDetected = true;
                        showWarning('⚠️ Developer tools detected! Actions blocked.');
                        // Optionally blur content when DevTools detected
                        document.querySelector('.blur-content').style.filter = 'blur(5px)';
                    }
                } else {
                    if (devToolsDetected) {
                        devToolsDetected = false;
                        document.querySelector('.blur-content').style.filter = 'blur(3px)';
                    }
                }
            }, 500);

            // Auto-hide content after duration
            const timeoutId = setTimeout(() => {
                previewContainer.innerHTML = `
                    <div class="flex flex-col items-center justify-center w-full h-64 bg-gray-800 text-white">
                        <svg class="w-12 h-12 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-lg">Document access expired. Please reopen to view again.</p>
                    </div>
                `;
                clearInterval(detectDevTools);
                clearTimeout(timeoutId);
            }, ACCESS_DURATION);

            // Show security warnings
            function showWarning(msg) {
                const existing = document.querySelector('.security-warning');
                if (existing) existing.remove();

                const div = document.createElement('div');
                div.className = 'security-warning fixed top-4 left-1/2 -translate-x-1/2 bg-red-600 text-white px-5 py-3 rounded-lg shadow-lg z-50 text-sm';
                div.textContent = msg;
                document.body.appendChild(div);
                setTimeout(() => div.remove(), 3000);
            }

            // Additional protection: periodically check for iframe manipulation
            setInterval(() => {
                const iframe = document.getElementById('securePDF');
                if (iframe) {
                    // Ensure iframe security attributes remain intact
                    iframe.style.pointerEvents = 'none';
                    
                    // Check if iframe has been modified or replaced
                    if (!iframe.classList.contains('no-pointer-events')) {
                        iframe.classList.add('no-pointer-events');
                    }
                }
            }, 1000);
        });

        // Add this to your security script
document.addEventListener('DOMContentLoaded', () => {
    // Check if mobile device
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (isMobile) {
        console.log('Mobile device detected - applying mobile optimizations');
        
        // Adjust iframe height for mobile
        const iframe = document.getElementById('securePDF');
        if (iframe) {
            iframe.style.height = '70vh';
            iframe.style.minHeight = '400px';
        }
        
        // Show mobile-specific message
        const mobileWarning = document.getElementById('mobileWarning');
        if (mobileWarning) {
            mobileWarning.style.display = 'block';
        }
    }
});
</script>