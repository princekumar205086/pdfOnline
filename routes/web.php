<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentDownloadController;
use App\Http\Controllers\CashfreeWebhookController;
use App\Livewire\DocumentSearch;
use App\Livewire\DocumentPreview;
use Illuminate\Support\Facades\Route;

// Home page - Document Search
Route::get('/', DocumentSearch::class);

// Document routes
Route::get('/document/{document}', DocumentPreview::class)->name('document.preview');
Route::get('/download/{document}', [DocumentDownloadController::class, 'download'])->middleware('signed')->name('document.download');

// Webhook for payment notifications
Route::post('/webhooks/cashfree', [CashfreeWebhookController::class, 'handle'])->name('cashfree.webhook');

// Dashboard route from Breeze
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes from Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
