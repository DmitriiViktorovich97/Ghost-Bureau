<?php

use App\Http\Controllers\GhostController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/ghosts', [GhostController::class, 'index'])->name('ghosts.index');
Route::get('/ghosts/create', [GhostController::class, 'create'])->name('ghosts.create');
Route::post('/ghosts', [GhostController::class, 'store'])->name('ghosts.store');
Route::post('/ghosts/auto-all', [GhostController::class, 'autoMatchAll'])->name('ghosts.auto-all');
Route::get('/ghosts/{ghost}', [GhostController::class, 'show'])->name('ghosts.show');
Route::post('/ghosts/{ghost}/auto', [GhostController::class, 'autoMatch'])->name('ghosts.auto');
Route::post('/ghosts/{ghost}/assign', [GhostController::class, 'assign'])->name('ghosts.assign');
Route::post('/ghosts/{ghost}/unassign', [GhostController::class, 'unassign'])->name('ghosts.unassign');
Route::delete('/ghosts/{ghost}', [GhostController::class, 'destroy'])->name('ghosts.destroy');

Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create');
Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');

Route::get('/report', [ReportController::class, 'index'])->name('report.index');

Route::view('/ai-worklog', 'ai-worklog')->name('ai-worklog');

Route::view('/test-scenarios', 'test-scenarios')->name('test-scenarios');

Route::get('/', fn() => redirect()->route('ghosts.index'));
