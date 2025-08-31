<?php

use App\Http\Controllers\{AnalyticsController,
    AuthController,
    EducationController,
    EventController,
    EventMediaController,
    ExperienceController,
    MessageController,
    PortfolioItemController,
    ReelController,
    SkillController,
    SocialMediaController,
    SubjectController,
    SubjectMediaController,
    UserMetaController};
use Illuminate\Support\Facades\Route;


Route::prefix('/api')->group(function () {

    // Auth
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // Public endpoints
    Route::post('/contact', [MessageController::class, 'store']);
    Route::get('/portfolio_items', [PortfolioItemController::class, 'index']);
    Route::get('/portfolio_items/{portfolioItem}', [PortfolioItemController::class, 'show']);
    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::get('/subject-media', [SubjectMediaController::class, 'index']);
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);

    // Protected endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('portfolio_items', PortfolioItemController::class)->except(['index', 'show']);
        Route::apiResource('subjects', SubjectController::class)->except(['index']);
        Route::apiResource('subject-media', SubjectMediaController::class)->except(['index']);
        Route::apiResource('events', EventController::class)->except(['index', 'show']);
        Route::apiResource('event-media', EventMediaController::class);
        Route::apiResource('messages', MessageController::class)->only(['index', 'show', 'destroy']);
        Route::get('/analytics/summary', [AnalyticsController::class, 'summary']);
    });

    // UserMeta + nested
    Route::apiResource('user-metas', UserMetaController::class);

    Route::prefix('user-metas/{userMeta}')->group(function () {
        Route::apiResource('education', EducationController::class);
        Route::apiResource('experience', ExperienceController::class);
        Route::apiResource('skills', SkillController::class);
        Route::apiResource('social-media', SocialMediaController::class);
        Route::apiResource('reels', ReelController::class);
    });
});

