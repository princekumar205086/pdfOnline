<div class="min-h-screen bg-gray-50">
    <header class="w-full bg-white border-b">
        <div class="max-w-7xl mx-auto px-6 h-14 flex items-center justify-end gap-4">
            @if (Route::has('login'))
                @auth
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-1.5 border rounded text-sm text-gray-700 hover:bg-gray-50">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-1.5 text-sm text-gray-700">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-4 py-1.5 border rounded text-sm text-gray-700 hover:bg-gray-50">Register</a>
                    @endif
                @endauth
            @endif
        </div>
    </header>
    <img src="{{ asset('banner.png') }}" alt="Banner" class="w-full h-auto object-cover">

    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="flex flex-wrap gap-3 mb-6">
            <button class="bg-gray-200 rounded px-4 py-2 text-base font-medium">Track your document status</button>
            <button class="bg-gray-200 rounded px-4 py-2 text-base font-medium">My Documents</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
            <div>
                <select wire:model.live="document_type" class="border p-2 rounded w-full">
                    <option value="">Select Document Type</option>
                    @foreach($this->documentTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
                @error('document_type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <select wire:model.live="district" class="border p-2 rounded w-full">
                    <option value="">Select District</option>
                    @foreach($this->districts as $district)
                        <option value="{{ $district }}">{{ $district }}</option>
                    @endforeach
                </select>
                @error('district')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <select wire:model.live="anchal" class="border p-2 rounded w-full" @if(!$this->districts->count()) disabled @endif>
                    <option value="">Select Anchal Office</option>
                    @foreach($this->anchals as $anchal)
                        <option value="{{ $anchal }}">{{ $anchal }}</option>
                    @endforeach
                </select>
                @error('anchal')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <select wire:model.live="mauza" class="border p-2 rounded w-full" @if(!$this->mauzas->count()) disabled @endif>
                    <option value="">Select Mauza</option>
                    @foreach($this->mauzas as $mauza)
                        <option value="{{ $mauza }}">{{ $mauza }}</option>
                    @endforeach
                </select>
                @error('mauza')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <select wire:model.live="thana_no" class="border p-2 rounded w-full" @if(!$this->thanas->count()) disabled @endif>
                    <option value="">Select Thana No.</option>
                    @foreach($this->thanas as $thana)
                        <option value="{{ $thana }}">{{ $thana }}</option>
                    @endforeach
                </select>
                @error('thana_no')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex flex-wrap gap-3 mb-6">
            <button wire:click="search" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Search</button>
            <button wire:click="resetSelection" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">Reset</button>
        </div>

        @if(!$result)
            <p class="text-gray-600 text-center py-8">Please select the filter to see the right details.</p>
        @elseif($documents->isEmpty())
            <p class="text-red-500 text-center py-8">No documents found for the given filters.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 bg-white rounded-md overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-3 text-left">Document Title</th>
                            <th class="border p-3 text-left">District</th>
                            <th class="border p-3 text-left">Anchal</th>
                            <th class="border p-3 text-left">Mauza</th>
                            <th class="border p-3 text-left">File Title</th>
                            <th class="border p-3 text-left">File Name</th>
                            <th class="border p-3 text-left">Price</th>
                            <th class="border p-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $document)
                            <tr class="hover:bg-gray-50">
                                <td class="border p-3">{{ $document->title }}</td>
                                <td class="border p-3">{{ $document->district }}</td>
                                <td class="border p-3">{{ $document->anchal }}</td>
                                <td class="border p-3">{{ $document->mauza }}</td>
                                <td class="border p-3">-</td>
                                <td class="border p-3">{{ $document->file_path ? basename($document->file_path) : '-' }}</td>
                                <td class="border p-3">₹{{ $document->price }}</td>
                                <td class="border p-3 text-center">
                                     <a href="{{ url('/document/' . $document->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded">Preview</a>
                                </td>
                            </tr>
                            @foreach($document->files as $file)
                                <tr class="hover:bg-gray-50">
                                    <td class="border p-3">{{ $document->title }}</td>
                                    <td class="border p-3">{{ $document->district }}</td>
                                    <td class="border p-3">{{ $document->anchal }}</td>
                                    <td class="border p-3">{{ $document->mauza }}</td>
                                    <td class="border p-3">{{ $file->title ?? '-' }}</td>
                                    <td class="border p-3">{{ basename($file->file_path) }}</td>
                                    <td class="border p-3">₹{{ $file->price ?? $document->price }}</td>
                                    <td class="border p-3 text-center">
                                         <a href="{{ url('/document/' . $document->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded">Preview</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</div>
