<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentPreviewController extends Controller
{
    /**
     * Stream an inline PDF preview for the given document.
     * This endpoint requires a valid signed URL but does not enforce auth,
     * matching the existing preview behavior while avoiding data: URIs.
     */
    public function inline(Request $request, Document $document)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        if (! Storage::disk('private')->exists($document->file_path)) {
            abort(404);
        }

        $bytes = Storage::disk('private')->get($document->file_path);

        return response($bytes, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'.addslashes($document->title ?: 'document').'.pdf"')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }
}