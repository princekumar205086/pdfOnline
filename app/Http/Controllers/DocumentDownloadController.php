<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentDownloadController extends Controller
{
    public function download(Request $request, Document $document)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        $purchase = Purchase::where('user_id', Auth::id())
            ->where('document_id', $document->id)
            ->first();

        if (! $purchase) {
            abort(403);
        }

        // Record the download time
        $purchase->update(['downloaded_at' => now()]);

        return Storage::disk('private')->download($document->file_path);
    }
}