<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Public GET routes: no session, no cookies (see AGENTS.md section 12).
// Register this group without StartSession / ShareErrorsFromSession middleware.

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/proverb/{slug}', function (string $slug) {
    // resolve via Proverb::public() scope later
    abort(404);
})->name('proverb.show');

Route::get('/language/{code}', function (string $code) {
    abort(404);
})->name('language.show');

Route::get('/country/{slug}', function (string $slug) {
    abort(404);
})->name('country.show');

Route::get('/theme/{slug}', function (string $slug) {
    abort(404);
})->name('theme.show');

Route::get('/idea/{slug}', function (string $slug) {
    abort(404);
})->name('idea.show');

Route::get('/day/{date}', function (string $date) {
    if ($date === 'today') {
        return redirect()->route('day.show', ['date' => now()->toDateString()]);
    }
    abort(404);
})->name('day.show');

Route::get('/search', function () {
    return view('search');
})->name('search');

Route::get('/contribute', function () {
    return view('contribute');
})->name('contribute');

Route::get('/quiz', function () {
    return view('quiz');
})->name('quiz');

Route::get('/quiz/daily', function () {
    return view('quiz.daily');
})->name('quiz.daily');

Route::get('/leaderboard', function () {
    return view('leaderboard');
})->name('leaderboard');

// Static / legal pages (markdown with placeholders)
Route::get('/about', [PageController::class, 'show'])->defaults('page', 'about')->name('about');
Route::get('/privacy', [PageController::class, 'show'])->defaults('page', 'privacy')->name('privacy');
Route::get('/cookies', [PageController::class, 'show'])->defaults('page', 'cookies')->name('cookies');
Route::get('/terms', [PageController::class, 'show'])->defaults('page', 'terms')->name('terms');
Route::get('/licensing', [PageController::class, 'show'])->defaults('page', 'licensing')->name('licensing');
Route::get('/content-policy', [PageController::class, 'show'])->defaults('page', 'content-policy')->name('content-policy');
Route::get('/contact', [PageController::class, 'show'])->defaults('page', 'contact')->name('contact');

// Uncached token endpoint for forms (CSRF + player token)
Route::get('/token', function () {
    return response()->json([
        'csrf' => csrf_token(),
        // player token issued later by PlayerService
    ]);
})->name('token');
