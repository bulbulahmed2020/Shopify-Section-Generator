<?php

use App\Http\Controllers\SectionGeneratorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SectionGeneratorController::class, 'index'])->name('generator.index');
Route::post('/generate', [SectionGeneratorController::class, 'generate'])->name('generator.generate');
