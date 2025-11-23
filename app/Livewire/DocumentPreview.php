<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class DocumentPreview extends Component
{
    public Document $document;
    public ?string $previewDataUri = null;
    public bool $showDetailsModal = false;
    public bool $showPageSelector = false;
    public ?string $selectedPages = null;
    public int $totalPages = 0;
    public array $pageArray = [];
    public float $pricePerPage = 15;
    public float $calculatedPrice = 0;

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->generateSecurePreviewUrl();
        $this->getTotalPages();
    }

    public function generateSecurePreviewUrl()
    {
        try {
            if (Storage::disk('private')->exists($this->document->file_path)) {
                $bytes = Storage::disk('private')->get($this->document->file_path);
                $this->previewDataUri = 'data:application/pdf;base64,' . base64_encode($bytes);
            }
        } catch (\Throwable $e) {
            $this->previewDataUri = null;
        }
    }

    public function getTotalPages()
    {
        try {
            if (Storage::disk('private')->exists($this->document->file_path)) {
                $bytes = Storage::disk('private')->get($this->document->file_path);
                $pdf = new \Smalot\PdfParser\Parser();
                $document = $pdf->parseContent($bytes);
                $this->totalPages = count($document->getPages());
            }
        } catch (\Throwable $e) {
            $this->totalPages = 0;
        }
    }

    public function openDetailsModal()
    {
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
    }

    public function openPageSelector()
    {
        $this->showPageSelector = true;
    }

    public function closePageSelector()
    {
        $this->showPageSelector = false;
        $this->selectedPages = null;
        $this->pageArray = [];
        $this->calculatedPrice = 0;
    }

    public function parsePages()
    {
        $this->validate([
            'selectedPages' => 'required|string',
        ], [
            'selectedPages.required' => 'Please enter page numbers or ranges.',
        ]);

        $this->pageArray = [];
        $parts = explode(',', str_replace(' ', '', $this->selectedPages));

        foreach ($parts as $part) {
            if (strpos($part, '-') !== false) {
                // Handle range like "5-7"
                [$start, $end] = explode('-', $part);
                $start = (int) trim($start);
                $end = (int) trim($end);

                if ($start < 1 || $end > $this->totalPages || $start > $end) {
                    $this->addError('selectedPages', "Invalid page range: {$part}. Pages must be between 1 and {$this->totalPages}.");
                    return;
                }

                for ($i = $start; $i <= $end; $i++) {
                    $this->pageArray[] = $i;
                }
            } else {
                // Handle single page like "4"
                $page = (int) trim($part);

                if ($page < 1 || $page > $this->totalPages) {
                    $this->addError('selectedPages', "Invalid page number: {$page}. Pages must be between 1 and {$this->totalPages}.");
                    return;
                }

                $this->pageArray[] = $page;
            }
        }

        // Remove duplicates and sort
        $this->pageArray = array_unique($this->pageArray);
        sort($this->pageArray);

        // Calculate price
        $this->calculatedPrice = count($this->pageArray) * $this->pricePerPage;
    }

    public function getPdfViewerUrl()
    {
        if (!$this->previewDataUri) {
            return null;
        }

        $signed = URL::temporarySignedRoute('document.stream', now()->addMinutes(10), ['document' => $this->document->id]);
        $viewerUrl = asset('pdfjs-5.4.394-dist/web/viewer.html');
        return "{$viewerUrl}?file=" . urlencode($signed);
    }

    public function render()
    {
        return view('livewire.document-preview');
    }
}