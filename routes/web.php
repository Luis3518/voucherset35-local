<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/alumnos');
Route::resource('alumnos', StudentController::class)
    ->parameters(['alumnos' => 'student'])
    ->names('students')
    ->except('show');

Route::get('/comidas', [MealController::class, 'index'])->name('meals.index');
Route::patch('/comidas', [MealController::class, 'update'])->name('meals.update');

Route::post('/calendario/feriados', [CalendarController::class, 'importHolidays'])->name('calendar.import-holidays');
Route::resource('calendario', CalendarController::class)
    ->parameters(['calendario' => 'exclusion'])
    ->names('calendar')
    ->except('show');

Route::get('/vouchers', [VoucherController::class, 'index'])->name('vouchers.index');
Route::post('/vouchers', [VoucherController::class, 'generate'])->name('vouchers.generate');
