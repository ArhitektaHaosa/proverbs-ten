<?php

use App\Http\Controllers\ContributionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

// Public GET routes: no session (register without StartSession).

Route::get('/', fn () => view('home'))->name('home');
Route::get('/proverb/{slug}', fn () => abort(404))->name('proverb.show');
Route::get('/language/{code}', fn () => abort(404))->name('language.show');
Route::get('/country/{slug}', fn () => abort(404))->name('country.show');
Route::get('/theme/{slug}', fn () => abort(404))->name('theme.show');
Route::get('/idea/{slug}', fn () => abort(404))->name('idea.show');
Route::get('/day/{date}', function (string $date) {
    if ($date === 'today') {
        return redirect()->route('day.show', ['date' => now()->toDateString()]);
    }
    abort(404);
})->name('day.show');
Route::get('/search', fn () => view('search'))->name('search');
Route::get('/contribute', fn () => view('contribute'))->name('contribute');
Route::get('/quiz', fn () => view('quiz'))->name('quiz');
Route::get('/quiz/daily', fn () => view('quiz.daily'))->name('quiz.daily');
Route::get('/leaderboard', fn () => view('leaderboard'))->name('leaderboard');

Route::get('/about', [PageController::class, 'show'])->defaults('page', 'about')->name('about');
Route::get('/privacy', [PageController::class, 'show'])->defaults('page', 'privacy')->name('privacy');
Route::get('/cookies', [PageController::class, 'show'])->defaults('page', 'cookies')->name('cookies');
Route::get('/terms', [PageController::class, 'show'])->defaults('page', 'terms')->name('terms');
Route::get('/licensing', [PageController::class, 'show'])->defaults('page', 'licensing')->name('licensing');
Route::get('/content-policy', [PageController::class, 'show'])->defaults('page', 'content-policy')->name('content-policy');
Route::get('/contact', [PageController::class, 'show'])->defaults('page', 'contact')->name('contact');

Route::get('/token', fn () => response()->json(['csrf' => csrf_token()]))->name('token');

// Forms (uncached)
Route::post('/contribute', [ContributionController::class, 'store'])->name('contribute.store');
Route::post('/quiz/start', [QuizController::class, 'start'])->name('quiz.start');
Route::post('/quiz/submit', [QuizController::class, 'submit'])->name('quiz.submit');
Route::post('/report', fn () => back())->name('report.store');
