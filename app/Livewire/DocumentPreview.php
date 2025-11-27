<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use App\Models\Transaction;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DocumentPreview extends Component
{
    public Document $document;
    public ?string $previewDataUri = null;

    public function mount(Document $document)
    {
        // Load full document with files
        $this->document = Document::with('files')->findOrFail($document->id);

        // Read query values
        $type = request('type');
        $fileId = request('file_id');

        // If additional price selected
        if ($type === 'additional' && $fileId) {

            $file = $this->document->files->where('id', $fileId)->first();

            if ($file) {
                // Override the price with additional file price
                $this->document->price = $file->price;  // ⭐ THE IMPORTANT FIX
                $this->document->file_path = $file->file_path; // ⭐ Also load correct file for preview
            }
        }

        // Prepare PDF preview
        try {
            if (Storage::disk('private')->exists($this->document->file_path)) {
                $bytes = Storage::disk('private')->get($this->document->file_path);
                $this->previewDataUri = 'data:application/pdf;base64,' . base64_encode($bytes);
            }
        } catch (\Throwable $e) {
            $this->previewDataUri = null;
        }
    }

    public function render()
    {
        return view('livewire.document-preview');
    }
}