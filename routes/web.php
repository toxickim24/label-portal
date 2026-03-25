<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactNoteController;
use App\Http\Controllers\ContactTagController;
use App\Http\Controllers\ImportController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'approved'])->name('dashboard');

Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Contact Management
    Route::resource('contacts', ContactController::class);
    Route::get('contacts/pipeline/view', [ContactController::class, 'pipeline'])->name('contacts.pipeline');
    Route::post('contacts/{contact}/assign', [ContactController::class, 'assign'])->name('contacts.assign');
    Route::post('contacts/{contact}/status', [ContactController::class, 'updateStatus'])->name('contacts.status');
    Route::post('contacts/{id}/restore', [ContactController::class, 'restore'])->name('contacts.restore');
    Route::post('contacts/bulk/delete', [ContactController::class, 'bulkDelete'])->name('contacts.bulk-delete');
    Route::post('contacts/bulk/assign', [ContactController::class, 'bulkAssign'])->name('contacts.bulk-assign');
    Route::post('contacts/bulk/tag', [ContactController::class, 'bulkTag'])->name('contacts.bulk-tag');

    // Contact Notes
    Route::post('contacts/{contact}/notes', [ContactNoteController::class, 'store'])->name('contact-notes.store');
    Route::put('contact-notes/{note}', [ContactNoteController::class, 'update'])->name('contact-notes.update');
    Route::post('contact-notes/{note}/toggle-pin', [ContactNoteController::class, 'togglePin'])->name('contact-notes.toggle-pin');
    Route::delete('contact-notes/{note}', [ContactNoteController::class, 'destroy'])->name('contact-notes.destroy');

    // Contact Tags
    Route::get('contact-tags', [ContactTagController::class, 'index'])->name('contact-tags.index');
    Route::post('contact-tags', [ContactTagController::class, 'store'])->name('contact-tags.store');
    Route::put('contact-tags/{tag}', [ContactTagController::class, 'update'])->name('contact-tags.update');
    Route::delete('contact-tags/{tag}', [ContactTagController::class, 'destroy'])->name('contact-tags.destroy');
    Route::get('contact-tags/search', [ContactTagController::class, 'search'])->name('contact-tags.search');
    Route::get('contact-tags/popular', [ContactTagController::class, 'popular'])->name('contact-tags.popular');

    // CSV Import Management
    Route::get('imports', [ImportController::class, 'index'])->name('imports.index');
    Route::get('imports/create', [ImportController::class, 'create'])->name('imports.create');
    Route::post('imports', [ImportController::class, 'store'])->name('imports.store');
    Route::get('imports/template', [ImportController::class, 'downloadTemplate'])->name('imports.template');
    Route::get('imports/{import}', [ImportController::class, 'show'])->name('imports.show');
    Route::get('imports/{import}/map', [ImportController::class, 'map'])->name('imports.map');
    Route::post('imports/{import}/process', [ImportController::class, 'process'])->name('imports.process');
    Route::post('imports/{import}/cancel', [ImportController::class, 'cancel'])->name('imports.cancel');
    Route::get('imports/{import}/failed-rows', [ImportController::class, 'downloadFailedRows'])->name('imports.failed-rows');
    Route::delete('imports/{import}', [ImportController::class, 'destroy'])->name('imports.destroy');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'approved', 'role:admin'])->group(function () {
    // User Management
    Route::resource('users', UserController::class);
    Route::post('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::post('users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
    Route::post('users/{user}/unsuspend', [UserController::class, 'unsuspend'])->name('users.unsuspend');
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');

    // Settings Management
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/branding', [SettingsController::class, 'updateBranding'])->name('settings.branding');
    Route::post('settings/reset-logos', [SettingsController::class, 'resetLogos'])->name('settings.reset-logos');
});

require __DIR__.'/auth.php';
