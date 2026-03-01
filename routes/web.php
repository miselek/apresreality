<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractTemplateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PriceAnalysisController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Contacts
Route::resource('contacts', ContactController::class);
Route::post('contacts/{contact}/activity', [ContactController::class, 'addActivity'])->name('contacts.activity.store');
Route::post('contacts/{contact}/assign-process', [ContactController::class, 'assignProcess'])->name('contacts.assign-process');
Route::post('contacts/{contact}/advance-step', [ContactController::class, 'advanceStep'])->name('contacts.advance-step');

// Processes
Route::resource('processes', ProcessController::class)->only(['index', 'show', 'edit', 'update']);

// Tasks
Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::post('tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

// Notifications
Route::resource('notifications', NotificationController::class);
Route::post('notifications/preview', [NotificationController::class, 'preview'])->name('notifications.preview');
Route::post('notifications/send', [NotificationController::class, 'send'])->name('notifications.send');

// Price Analyses
Route::resource('price-analyses', PriceAnalysisController::class);
Route::post('price-analyses/{price_analysis}/report', [PriceAnalysisController::class, 'generateReport'])->name('price-analyses.report');
Route::get('price-analyses/{price_analysis}/download', [PriceAnalysisController::class, 'downloadReport'])->name('price-analyses.download');

// Contracts
Route::resource('contracts', ContractController::class)->names('contracts');
Route::post('contracts/{contract}/validate-ai', [ContractController::class, 'validateContract'])->name('contracts.validate');
Route::post('contracts/{contract}/verify-client', [ContractController::class, 'verifyClient'])->name('contracts.verify-client');
Route::get('contracts/{contract}/download', [ContractController::class, 'download'])->name('contracts.download');

// Contract Templates
Route::get('contract-templates', [ContractTemplateController::class, 'index'])->name('contract-templates.index');
Route::post('contract-templates', [ContractTemplateController::class, 'store'])->name('contract-templates.store');
Route::delete('contract-templates/{contractTemplate}', [ContractTemplateController::class, 'destroy'])->name('contract-templates.destroy');

// Nemovitosti
Route::resource('nemovitosti', PropertyController::class)->parameters(['nemovitosti' => 'nemovitost']);
Route::post('nemovitosti/{nemovitost}/advance', [PropertyController::class, 'advanceStatus'])->name('nemovitosti.advance');
Route::post('nemovitosti/{nemovitost}/photos', [PropertyController::class, 'uploadPhotos'])->name('nemovitosti.photos.store');
Route::delete('nemovitosti/{nemovitost}/photos/{photo}', [PropertyController::class, 'deletePhoto'])->name('nemovitosti.photos.destroy');
Route::post('nemovitosti/{nemovitost}/photos/reorder', [PropertyController::class, 'reorderPhotos'])->name('nemovitosti.photos.reorder');
Route::post('nemovitosti/{nemovitost}/photos/{photo}/primary', [PropertyController::class, 'setPrimaryPhoto'])->name('nemovitosti.photos.primary');
Route::post('nemovitosti/{nemovitost}/interests', [PropertyController::class, 'addInterest'])->name('nemovitosti.interests.store');
Route::delete('nemovitosti/{nemovitost}/interests/{interest}', [PropertyController::class, 'removeInterest'])->name('nemovitosti.interests.destroy');
Route::post('nemovitosti/{nemovitost}/events', [PropertyController::class, 'addEvent'])->name('nemovitosti.events.store');
Route::put('nemovitosti/{nemovitost}/events/{event}', [PropertyController::class, 'updateEvent'])->name('nemovitosti.events.update');
Route::delete('nemovitosti/{nemovitost}/events/{event}', [PropertyController::class, 'deleteEvent'])->name('nemovitosti.events.destroy');
Route::post('nemovitosti/{nemovitost}/events/{event}/complete', [PropertyController::class, 'completeEvent'])->name('nemovitosti.events.complete');
Route::get('nemovitosti/{nemovitost}/landing', [PropertyController::class, 'landing'])->name('nemovitosti.landing');
