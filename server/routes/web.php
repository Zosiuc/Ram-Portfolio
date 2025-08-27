<?php

use App\Models\SubjectMedia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController,
    EducationController,
    EventMediaController,
    ExperienceController,
    PortfolioItemController,
    EventController,
    MessageController,
    AnalyticsController,
    ReelController,
    SkillController,
    SocialMediaController,
    SubjectController,
    SubjectMediaController,
    UserMetaController};

Route::post('/login',[AuthController::class,'login']);
Route::get('/me',[AuthController::class,'me'])->middleware('auth:sanctum');
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');

// public endpoints
Route::post('/contact',[MessageController::class,'store']);
Route::get('/portfolio',[PortfolioItemController::class,'index']);
Route::get('/subjects',[SubjectController::class,'index']);
Route::get('/subject_media',[SubjectMedia::class,'index']);
Route::get('/portfolio/{portfolioItem}',[PortfolioItemController::class,'show']);
Route::get('/events',[EventController::class,'index']);
Route::get('/events/{event}',[EventController::class,'show']);

// protected
Route::middleware('auth:sanctum')->group(function(){
    Route::apiResource('portfolio_items', PortfolioItemController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('subject_media', SubjectMediaController::class);
    Route::apiResource('portfolio', PortfolioItemController::class)->except(['index','show']);
    Route::apiResource('events', EventController::class)->except(['index','show']);
    Route::apiResource('event_media', EventMediaController::class);
    Route::apiResource('messages', MessageController::class);
    Route::get('/messages',[MessageController::class,'index']);
    Route::get('/analytics/summary',[AnalyticsController::class,'summary']);
});

Route::apiResource('user-metas', UserMetaController::class);

Route::prefix('user-metas/{userMeta}')->group(function () {
    Route::apiResource('education', EducationController::class);
    Route::apiResource('experience', ExperienceController::class);
    Route::apiResource('skills', SkillController::class);
    Route::apiResource('social-media', SocialMediaController::class);
    Route::apiResource('reels', ReelController::class);
});
