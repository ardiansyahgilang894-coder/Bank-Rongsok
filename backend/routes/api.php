<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ActivityController;
use App\Http\Controllers\API\FundController;
use App\Http\Controllers\API\ScrapSaleController;
use App\Http\Controllers\API\DonationDistributionController;
use App\Http\Controllers\API\DashboardController;

// Auth routes (public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::get('/dashboard/revenue-years', [DashboardController::class, 'revenueYears']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard
    Route::get('/dashboard/analytics', [DashboardController::class, 'getAnalytics']);
    Route::get('/dashboard/monthly-revenue', [DashboardController::class, 'getMonthlyRevenue']);
    Route::get('/dashboard/recent-activities', [DashboardController::class, 'getRecentActivities']);
    Route::get('/dashboard/recent-distributions', [DashboardController::class, 'getRecentDistributions']);
    

    // Activities (Galeri Kegiatan)
    Route::apiResource('activities', ActivityController::class);
    Route::post('activities/{activity}/upload-image', [ActivityController::class, 'uploadImage']);
    Route::delete('activities/{activity}/images/{image}', [ActivityController::class, 'deleteImage']);

    // Funds (Transparansi Dana)
    Route::apiResource('funds', FundController::class);
    Route::get('funds/stats/overview', [FundController::class, 'getStats']);

    // Scrap Sales (Laporan Penjualan Rongsok)
    Route::apiResource('scrap-sales', ScrapSaleController::class);
    Route::get('scrap-sales/report/overview', [ScrapSaleController::class, 'getReport']);

    // Donation Distributions (Dokumentasi Penyaluran Bantuan)
    Route::apiResource('donations', DonationDistributionController::class);
    Route::post('donations/{donationDistribution}/items', [DonationDistributionController::class, 'addItem']);
});
