<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MirrorPageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', StorefrontController::class)->name('storefront.home');
Route::get('/thue-vietmap-live-pro', StorefrontController::class);
Route::get('/thue-vietmap-live-pro.html', StorefrontController::class);

Route::get('/thanh-toan', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/thanh-toan', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/thanh-toan/{order}', [CheckoutController::class, 'show'])->name('checkout.show');

Route::get('/order-detail', [OrderController::class, 'search'])->name('orders.search');
Route::get('/don-hang/{order:order_number}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/order-history-ip', [OrderController::class, 'history'])->name('orders.history');
Route::get('/order-history-ip.html', [OrderController::class, 'history']);

Route::get('/blog', [MirrorPageController::class, 'page'])->defaults('page', 'blog');
Route::get('/blog.html', [MirrorPageController::class, 'page'])->defaults('page', 'blog.html');
Route::get('/blog/{slug}', [MirrorPageController::class, 'blog'])->where('slug', '[A-Za-z0-9\-]+');
Route::get('/blog/{slug}.html', [MirrorPageController::class, 'blog'])->where('slug', '[A-Za-z0-9\-]+');

Route::get('/ma-giam-gia', [MirrorPageController::class, 'page'])->defaults('page', 'ma-giam-gia');
Route::get('/ma-giam-gia.html', [MirrorPageController::class, 'page'])->defaults('page', 'ma-giam-gia.html');
Route::get('/terms', [MirrorPageController::class, 'page'])->defaults('page', 'terms');
Route::get('/terms.html', [MirrorPageController::class, 'page'])->defaults('page', 'terms.html');
Route::get('/lien-he', [MirrorPageController::class, 'page'])->defaults('page', 'lien-he');
Route::get('/lien-he.html', [MirrorPageController::class, 'page'])->defaults('page', 'lien-he.html');
Route::get('/gioi-thieu', [MirrorPageController::class, 'page'])->defaults('page', 'gioi-thieu');
Route::get('/gioi-thieu.html', [MirrorPageController::class, 'page'])->defaults('page', 'gioi-thieu.html');
