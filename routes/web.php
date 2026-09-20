<?php

use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\LlmsTxtController;
use App\Http\Controllers\PlanTripController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TripPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/llms.txt', LlmsTxtController::class)->name('llms-txt');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/start-planning', [PlanTripController::class, 'create'])->name('plan-trip.create');
Route::post('/start-planning', [PlanTripController::class, 'store'])->name('plan-trip.store');

Route::get('/make-enquiry', [EnquiryController::class, 'create'])->name('enquiry.create');
Route::post('/make-enquiry', [EnquiryController::class, 'store'])->name('enquiry.store');

Route::get('/trips/{trip}/print', TripPdfController::class)->name('trip.pdf');

Route::get('/{path?}', FrontendController::class)
    ->where('path', '.*')
    ->name('frontend.show');
