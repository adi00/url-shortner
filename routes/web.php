<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\RedirectShortUrlController;




Route::get('/s/{code}', RedirectShortUrlController::class)->name('short-urls.redirect');
Route::get('/', function () {
  return redirect()->route('dashboard');
});


Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class,'index'])->name('login');
    Route::post('/login', [AuthController::class,'login'])->name('post.login');
   
     Route::get('/invitations/{token}/accept', [InvitationController::class, 'showAccept'])
        ->name('invitations.accept');
     Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept']);
});

Route::middleware('auth')->group(function (){
   Route::post('/logout', [AuthController::class,'logout'])->name('post.logout');
   Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');

   Route::get('/short-urls', [ShortUrlController::class, 'index'])->name('short-urls.index');
   Route::get('/short-urls/create', [ShortUrlController::class, 'create'])->name('short-urls.create');
   Route::get('/short-urls/export', [ShortUrlController::class, 'export'])->name('short-urls.export');
   Route::post('/short-urls', [ShortUrlController::class, 'store'])->name('short-urls.store');
   Route::delete('/short-urls/{shortUrl}', [ShortUrlController::class, 'destroy'])->name('short-urls.destroy');

   Route::get('/invitations/create',[InvitationController::class,'create'])->name('invitations.create');
   Route::post('/invitations/create',[InvitationController::class,'store'])->name('invitations.store');

});