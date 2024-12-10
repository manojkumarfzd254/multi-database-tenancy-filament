<?php

use App\Filament\Pages\CompanyRegistration;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', CompanyRegistration::class)->name('company.registration');
