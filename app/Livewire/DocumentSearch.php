<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Document;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class DocumentSearch extends Component
{
    use WithPagination;

    public $document_type = '';
    public $district = '';
    public $anchal = '';
    public $mauza = '';
    public $thana_no = '';
    public $result = false;
    public $showModal = false;

    protected $rules = [
        'document_type' => 'required',
        'district' => 'required',
        'anchal' => 'required',
        'mauza' => 'required',
        'thana_no' => 'required'
    ];

    public function search()
    {
        $this->validate();
        $this->resetPage();
        $this->result = true;
    }

   public function resetSelection()
{
    $this->reset(['document_type', 'district', 'anchal', 'mauza', 'thana_no']);
    $this->result = false;
    $this->resetPage();
}

    public function getDocumentTypesProperty(): Collection
    {
        return Document::where('is_active', true)
            ->distinct()
            ->orderBy('document_type')
            ->pluck('document_type');
    }

    public function getDistrictsProperty(): Collection
    {
        return Document::where('is_active', true)
            ->distinct()
            ->orderBy('district')
            ->pluck('district');
    }

    public function getAnchalsProperty(): Collection
    {
        return Document::where('is_active', true)
            ->when($this->district, function ($query) {
                $query->where('district', $this->district);
            })
            ->distinct()
            ->orderBy('anchal')
            ->pluck('anchal');
    }

    public function getMauzasProperty(): Collection
    {
        return Document::where('is_active', true)
            ->when($this->district, function ($query) {
                $query->where('district', $this->district);
            })
            ->when($this->anchal, function ($query) {
                $query->where('anchal', $this->anchal);
            })
            ->distinct()
            ->orderBy('mauza')
            ->pluck('mauza');
    }

    public function getThanasProperty(): Collection
    {
        return Document::where('is_active', true)
            ->when($this->district, function ($query) {
                $query->where('district', $this->district);
            })
            ->when($this->anchal, function ($query) {
                $query->where('anchal', $this->anchal);
            })
            ->when($this->mauza, function ($query) {
                $query->where('mauza', $this->mauza);
            })
            ->distinct()
            ->orderBy('thana_no')
            ->pluck('thana_no');
    }

    public function render(): View
    {
        $documents = collect();
        if ($this->result) {
            $query = Document::query()->where('is_active', true)->with('files');

            if ($this->document_type) {
                $query->where('document_type', 'like', '%' . $this->document_type . '%');
            }

            if ($this->district) {
                $query->where('district', 'like', '%' . $this->district . '%');
            }

            if ($this->anchal) {
                $query->where('anchal', 'like', '%' . $this->anchal . '%');
            }

            if ($this->mauza) {
                $query->where('mauza', 'like', '%' . $this->mauza . '%');
            }

            if ($this->thana_no) {
                $query->where('thana_no', 'like', '%' . $this->thana_no . '%');
            }

            $documents = $query->paginate(10);
        }

        return view('livewire.document-search', [
            'documents' => $documents,
        ]);
    }
}