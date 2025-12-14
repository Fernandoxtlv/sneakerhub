<?php

use Illuminate\Support\Facades\Route;

// =========================================
// PUBLIC ROUTES (Client)
// =========================================

Route::get('/', [App\Http\Controllers\Client\CatalogController::class, 'index'])->name('home');
Route::get('/catalog', [App\Http\Controllers\Client\CatalogController::class, 'index'])->name('catalog');
Route::get('/category/{category:slug}', [App\Http\Controllers\Client\CatalogController::class, 'category'])->name('category.show');
Route::get('/brand/{brand:slug}', [App\Http\Controllers\Client\CatalogController::class, 'brand'])->name('brand.show');
Route::get('/product/{product:slug}', [App\Http\Controllers\Client\CatalogController::class, 'show'])->name('product.show');
Route::get('/search', [App\Http\Controllers\Client\CatalogController::class, 'search'])->name('search');

// Cart routes (work for guests and authenticated users)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [App\Http\Controllers\Client\CartController::class, 'index'])->name('index');
    Route::post('/add', [App\Http\Controllers\Client\CartController::class, 'add'])->name('add');
    Route::patch('/update/{item}', [App\Http\Controllers\Client\CartController::class, 'update'])->name('update');
    Route::delete('/remove/{item}', [App\Http\Controllers\Client\CartController::class, 'remove'])->name('remove');
    Route::post('/apply-coupon', [App\Http\Controllers\Client\CartController::class, 'applyCoupon'])->name('apply-coupon');
    Route::delete('/remove-coupon', [App\Http\Controllers\Client\CartController::class, 'removeCoupon'])->name('remove-coupon');
    Route::get('/count', [App\Http\Controllers\Client\CartController::class, 'count'])->name('count');
});

// =========================================
// AUTHENTICATION ROUTES
// =========================================

Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});

// =========================================
// CLIENT AUTHENTICATED ROUTES
// =========================================

Route::middleware(['auth'])->group(function () {
    // Checkout
    Route::get('/checkout', [App\Http\Controllers\Client\CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [App\Http\Controllers\Client\CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{order}', [App\Http\Controllers\Client\CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/yape/{order}', [App\Http\Controllers\Client\CheckoutController::class, 'yapePayment'])->name('checkout.yape');

    // Order History
    Route::get('/orders', [App\Http\Controllers\Client\OrderHistoryController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\Client\OrderHistoryController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/download-boleta', [App\Http\Controllers\Client\OrderHistoryController::class, 'downloadBoleta'])->name('orders.download-boleta');

    // Profile
    Route::get('/profile', [App\Http\Controllers\Client\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Client\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\Client\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =========================================
// ADMIN ROUTES (Owner, Admin, Worker)
// =========================================

Route::middleware(['auth', 'role:owner|admin|worker'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard.index');

    // Products (CRUD)
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    Route::post('/products/{product}/images', [App\Http\Controllers\Admin\ProductController::class, 'uploadImages'])->name('products.upload-images');
    Route::delete('/products/{product}/images/{image}', [App\Http\Controllers\Admin\ProductController::class, 'deleteImage'])->name('products.delete-image');
    Route::post('/products/{product}/images/{image}/set-main', [App\Http\Controllers\Admin\ProductController::class, 'setMainImage'])->name('products.set-main-image');
    Route::patch('/products/{product}/toggle-active', [App\Http\Controllers\Admin\ProductController::class, 'toggleActive'])->name('products.toggle-active');
    Route::patch('/products/{product}/update-stock', [App\Http\Controllers\Admin\ProductController::class, 'updateStock'])->name('products.update-stock');

    // Categories
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);

    // Brands
    Route::resource('brands', App\Http\Controllers\Admin\BrandController::class);

    // Orders
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('/orders/{order}/payment-status', [App\Http\Controllers\Admin\OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
    Route::get('/orders/{order}/download-invoice', [App\Http\Controllers\Admin\OrderController::class, 'downloadInvoice'])->name('orders.download-invoice');
    Route::post('/orders/{order}/generate-invoice', [App\Http\Controllers\Admin\OrderController::class, 'generateInvoice'])->name('orders.generate-invoice');

    // Payments
    Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/confirm', [App\Http\Controllers\Admin\PaymentController::class, 'confirm'])->name('payments.confirm');

    // Stock Movements
    Route::get('/stock-movements', [App\Http\Controllers\Admin\StockMovementController::class, 'index'])->name('stock-movements.index');
    Route::post('/stock-movements', [App\Http\Controllers\Admin\StockMovementController::class, 'store'])->name('stock-movements.store');
});

// =========================================
// ADMIN ROUTES (Owner and Admin only)
// =========================================

Route::middleware(['auth', 'role:owner|admin'])->prefix('admin')->name('admin.')->group(function () {

    // Users Management
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);

    // Reports
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales', [App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/products', [App\Http\Controllers\Admin\ReportController::class, 'products'])->name('reports.products');
    Route::get('/reports/export/csv', [App\Http\Controllers\Admin\ReportController::class, 'exportCsv'])->name('reports.export-csv');
    Route::get('/reports/export/pdf', [App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('reports.export-pdf');

    // Coupons
    Route::resource('coupons', App\Http\Controllers\Admin\CouponController::class);
});

// =========================================
// OWNER ONLY ROUTES
// =========================================

Route::middleware(['auth', 'role:owner'])->prefix('admin')->name('admin.')->group(function () {

    // Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
});
