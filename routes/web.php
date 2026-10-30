<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// Google OAuth Web Routes
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Manual Book PDF Direct View & Download Routes
Route::get('/docs/manual-book.pdf', function () {
    $path = public_path('docs/manual-book.pdf');
    if (!file_exists($path)) {
        $path = base_path('docs/MANUAL BOOK ABDIMAS DESA HANJELI - WEBSITE SMART DRYER.pdf');
    }
    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="MANUAL-BOOK-WEBSITE-SMART-DRYER-HANJELI.pdf"',
    ]);
});

Route::get('/manual-book/download', function () {
    $path = public_path('docs/manual-book.pdf');
    if (!file_exists($path)) {
        $path = base_path('docs/MANUAL BOOK ABDIMAS DESA HANJELI - WEBSITE SMART DRYER.pdf');
    }
    return response()->download($path, 'MANUAL-BOOK-WEBSITE-SMART-DRYER-HANJELI.pdf');
});

// Single Page Application Fallback
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|auth/google|docs|manual-book).*$');
