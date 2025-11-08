<div>
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">Search Land Records</h1>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
            <select wire:model.live="document_type" class="border p-2 rounded">
                <option value="">Select Document Type</option>
                @foreach($this->documentTypes as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
            
            <select wire:model.live="district" class="border p-2 rounded">
                <option value="">Select District</option>
                @foreach($this->districts as $district)
                    <option value="{{ $district }}">{{ $district }}</option>
                @endforeach
            </select>
            
            <select wire:model.live="anchal" class="border p-2 rounded" @if(!$this->districts->count()) disabled @endif>
                <option value="">Select Anchal Office</option>
                @foreach($this->anchals as $anchal)
                    <option value="{{ $anchal }}">{{ $anchal }}</option>
                @endforeach
            </select>
            
            <select wire:model.live="mauza" class="border p-2 rounded" @if(!$this->mauzas->count()) disabled @endif>
                <option value="">Select Mauza</option>
                @foreach($this->mauzas as $mauza)
                    <option value="{{ $mauza }}">{{ $mauza }}</option>
                @endforeach
            </select>
            
            <select wire:model.live="thana_no" class="border p-2 rounded" @if(!$this->thanas->count()) disabled @endif>
                <option value="">Select Thana No.</option>
                @foreach($this->thanas as $thana)
                    <option value="{{ $thana }}">{{ $thana }}</option>
                @endforeach
            </select>
        </div>

        <table class="w-full border-collapse border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Document Title</th>
                    <th class="border p-2">District</th>
                    <th class="border p-2">Anchal</th>
                    <th class="border p-2">Mauza</th>
                    <th class="border p-2">Price</th>
                    <th class="border p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($documents as $document)
                    <tr>
                        <td class="border p-2">{{ $document->title }}</td>
                        <td class="border p-2">{{ $document->district }}</td>
                        <td class="border p-2">{{ $document->anchal }}</td>
                        <td class="border p-2">{{ $document->mauza }}</td>
                        <td class="border p-2">₹{{ $document->price }}</td>
                        <td class="border p-2">
                            <a href="{{ url('/document/' . $document->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded">Preview</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center p-4">No documents found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $documents->links() }}
        </div>
    </div>
</div>