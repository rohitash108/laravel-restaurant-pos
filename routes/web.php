<?php

use App\Http\Controllers\AddonsController;
use App\Http\Controllers\Admin\RestaurantsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponsController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\KanbanViewController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\OrderByQRController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PrintJobController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\RestaurantTableController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ——— Public: Auth ———
Route::get('login', [CustomAuthController::class, 'login'])->name('login');
Route::post('login', [CustomAuthController::class, 'authenticate'])->name('login.submit')->middleware('throttle:5,1');
Route::post('logout', [CustomAuthController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.submit');
Route::get('/forgot-password', function () { return view('forgot-password'); })->name('forgot-password');
Route::post('/forgot-password', [CustomAuthController::class, 'sendResetLink'])->name('forgot-password.submit')->middleware('throttle:3,1');
Route::get('/reset-password', function () { return view('reset-password'); })->name('reset-password');
Route::post('/reset-password', [CustomAuthController::class, 'resetPassword'])->name('reset-password.submit');
Route::get('/otp', function () { return view('otp'); })->name('otp');
Route::get('/email-verification', function () { return view('email-verification'); })->name('email-verification');

// ——— Public: Order by QR (no login) ———
Route::get('/order/{restaurant:slug}/{table}/qr', [OrderByQRController::class, 'qrImage'])
    ->where('table', '[a-zA-Z0-9\-]+')
    ->name('order.by-qr.qr-image');
Route::get('/order/{restaurant:slug}/{table}', [OrderByQRController::class, 'show'])
    ->where('table', '[a-zA-Z0-9\-]+')
    ->name('order.by-qr');
Route::post('/order/place', [OrderByQRController::class, 'placeOrder'])->name('order.by-qr.place');
Route::get('/order/{restaurant:slug}/{table}/success/{order}', [OrderByQRController::class, 'success'])
    ->name('order.by-qr.success');
Route::get('/order/{restaurant:slug}/{table}/order-status', [OrderByQRController::class, 'orderStatus'])
    ->name('order.by-qr.order-status');

// ——— Authenticated app (restaurant staff only; super admin is redirected to admin/restaurants) ———
Route::middleware(['auth', 'restaurant', 'redirect_super_admin_to_admin', 'subscription'])->group(function () {
    Route::get('/', function () {
        if (request()->filled('switch_restaurant')) {
            $id = (int) request('switch_restaurant');
            if (auth()->user()->restaurant_id === $id) {
                session(['current_restaurant_id' => $id]);
            }
        }
        return redirect()->route('dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/addons', [AddonsController::class, 'index'])->name('addons');
    Route::post('/addons', [AddonsController::class, 'store'])->name('addons.store');
    Route::put('/addons/{addon}', [AddonsController::class, 'update'])->name('addons.update');
    Route::delete('/addons/{addon}', [AddonsController::class, 'destroy'])->name('addons.destroy');
    Route::get('/audit-report', [ReportController::class, 'audit'])->name('audit-report');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::get('/coupons', [CouponsController::class, 'index'])->name('coupons');
    Route::post('/coupons', [CouponsController::class, 'store'])->name('coupons.store');
    Route::put('/coupons/{coupon}', [CouponsController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{coupon}', [CouponsController::class, 'destroy'])->name('coupons.destroy');
    Route::get('/customer-report', [ReportController::class, 'customer'])->name('customer-report');
    Route::get('/customer', [CustomerController::class, 'index'])->name('customer');
    Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
    Route::put('/customer/{customer}', [CustomerController::class, 'update'])->name('customer.update');
    Route::post('/customer/{customer}/receive-payment', [CustomerController::class, 'receivePayment'])->name('customer.receive-payment');
    Route::delete('/customer/{customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');
    Route::get('/earning-report', [ReportController::class, 'earning'])->name('earning-report');
    Route::get('/invoices', [InvoicesController::class, 'index'])->name('invoices');
    Route::get('/invoice-details/{order}', [InvoicesController::class, 'show'])->name('invoice-details');
    Route::get('/receipt-print/{order}', [InvoicesController::class, 'receipt'])->name('receipt-print');
    Route::get('/kot-print/{order}', [InvoicesController::class, 'kot'])->name('kot-print');
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/ingredients', [InventoryController::class, 'ingredientsIndex'])->name('ingredients.index');
        Route::get('/ingredients/create', [InventoryController::class, 'ingredientsCreate'])->name('ingredients.create');
        Route::post('/ingredients', [InventoryController::class, 'ingredientsStore'])->name('ingredients.store');
        Route::get('/ingredients/{ingredient}/edit', [InventoryController::class, 'ingredientsEdit'])->name('ingredients.edit');
        Route::put('/ingredients/{ingredient}', [InventoryController::class, 'ingredientsUpdate'])->name('ingredients.update');
        Route::get('/stock-in', [InventoryController::class, 'stockInForm'])->name('stock-in');
        Route::post('/stock-in', [InventoryController::class, 'stockInStore'])->name('stock-in.store');
        Route::get('/waste', [InventoryController::class, 'wasteForm'])->name('waste');
        Route::post('/waste', [InventoryController::class, 'wasteStore'])->name('waste.store');
        Route::get('/items/{item}/recipe', [InventoryController::class, 'itemRecipe'])->name('item.recipe');
        Route::put('/items/{item}/recipe', [InventoryController::class, 'itemRecipeUpdate'])->name('item.recipe.update');
    });

    Route::get('/items', [ItemController::class, 'index'])->name('items');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    Route::patch('/items/{item}/hide', [ItemController::class, 'hide'])->name('items.hide');
    Route::get('/kanban-view', [KanbanViewController::class, 'index'])->name('kanban-view');
    Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen');
    Route::get('/order-report', [ReportController::class, 'order'])->name('order-report');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('/orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
    Route::get('/pos', [PosController::class, 'index'])->name('pos');
    Route::post('/pos/cart-receipt-print', [PosController::class, 'cartReceiptPrint'])->name('pos.cart-receipt-print');
    Route::get('/pos/order/{order}/edit', [PosController::class, 'editOrder'])->name('orders.edit');
    Route::get('/payments', [PaymentsController::class, 'index'])->name('payments');
    Route::get('/reservations', [ReservationsController::class, 'index'])->name('reservations');
    Route::post('/reservations', [ReservationsController::class, 'store'])->name('reservations.store');
    Route::put('/reservations/{reservation}', [ReservationsController::class, 'update'])->name('reservations.update');
    Route::delete('/reservations/{reservation}', [ReservationsController::class, 'destroy'])->name('reservations.destroy');

    // Tables (restaurant-scoped)
    Route::get('/table', [RestaurantTableController::class, 'index'])->name('table');
    Route::post('/table', [RestaurantTableController::class, 'store'])->name('table.store');
    Route::put('/table/{table}', [RestaurantTableController::class, 'update'])->name('table.update');
    Route::delete('/table/{table}', [RestaurantTableController::class, 'destroy'])->name('table.destroy');
    Route::get('/table/{table}/qr-print', [RestaurantTableController::class, 'printCard'])->name('table.qr-print');

    // ——— Settings Pages (now dynamic) ———
    Route::get('/store-settings', [SettingsController::class, 'storeSettings'])->name('store-settings');
    Route::post('/store-settings', [SettingsController::class, 'storeSettingsUpdate'])->name('store-settings.update');

    Route::get('/payment-settings', [SettingsController::class, 'paymentSettings'])->name('payment-settings');
    Route::post('/payment-settings', [SettingsController::class, 'paymentSettingsUpdate'])->name('payment-settings.update');

    Route::get('/delivery-settings', [SettingsController::class, 'deliverySettings'])->name('delivery-settings');
    Route::post('/delivery-settings', [SettingsController::class, 'deliverySettingsUpdate'])->name('delivery-settings.update');

    Route::get('/print-settings', [SettingsController::class, 'printSettings'])->name('print-settings');
    Route::post('/print-settings', [SettingsController::class, 'printSettingsUpdate'])->name('print-settings.update');

    Route::get('/notifications-settings', [SettingsController::class, 'notificationsSettings'])->name('notifications-settings');
    Route::post('/notifications-settings', [SettingsController::class, 'notificationsSettingsUpdate'])->name('notifications-settings.update');

    Route::get('/integrations-settings', [SettingsController::class, 'integrationsSettings'])->name('integrations-settings');
    Route::post('/integrations-settings', [SettingsController::class, 'integrationsSettingsUpdate'])->name('integrations-settings.update');

    // ——— Tax Settings (CRUD) ———
    Route::get('/tax-settings', [TaxController::class, 'index'])->name('tax-settings');
    Route::post('/tax-settings', [TaxController::class, 'store'])->name('tax-settings.store');
    Route::put('/tax-settings/{tax}', [TaxController::class, 'update'])->name('tax-settings.update');
    Route::delete('/tax-settings/{tax}', [TaxController::class, 'destroy'])->name('tax-settings.destroy');

    // ——— Users Management (CRUD) ———
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // ——— Role & Permission ———
    Route::get('/role-permission', [RolePermissionController::class, 'index'])->name('role-permission');
    Route::post('/role-permission', [RolePermissionController::class, 'store'])->name('role-permission.store');
    Route::put('/role-permission/{role}', [RolePermissionController::class, 'update'])->name('role-permission.update');
    Route::delete('/role-permission/{role}', [RolePermissionController::class, 'destroy'])->name('role-permission.destroy');

    Route::get('/sales-report', [ReportController::class, 'sales'])->name('sales-report');

    // ——— Print Jobs (tablet polls for pending jobs) ———
    Route::get('/print-jobs/next', [PrintJobController::class, 'next'])->name('print-jobs.next');
    Route::post('/print-jobs/enqueue', [PrintJobController::class, 'enqueue'])->name('print-jobs.enqueue');
    Route::post('/print-jobs/{job}/printed', [PrintJobController::class, 'markPrinted'])->name('print-jobs.printed');
    Route::post('/print-jobs/{job}/failed', [PrintJobController::class, 'markFailed'])->name('print-jobs.failed');
});

// ——— Super Admin: Dashboard, Restaurants, Profile ———
Route::middleware(['auth', 'super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('restaurants', RestaurantsController::class)->except(['show']);
    Route::get('restaurants/{restaurant}', [RestaurantsController::class, 'show'])->name('restaurants.show');

    // ——— Owner only: Subscription Plans & Subscriptions ———
    Route::middleware('owner_admin')->group(function () {
        Route::get('subscription-plans', [SubscriptionController::class, 'plans'])->name('subscription-plans');
        Route::post('subscription-plans', [SubscriptionController::class, 'storePlan'])->name('subscription-plans.store');
        Route::put('subscription-plans/{plan}', [SubscriptionController::class, 'updatePlan'])->name('subscription-plans.update');
        Route::delete('subscription-plans/{plan}', [SubscriptionController::class, 'destroyPlan'])->name('subscription-plans.destroy');

        Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
        Route::post('subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
        Route::get('subscriptions/{subscription}/balance-history', [SubscriptionController::class, 'balanceHistory'])->name('subscriptions.balance-history');
        Route::post('subscriptions/{subscription}/debit-balance', [SubscriptionController::class, 'debitBalance'])->name('subscriptions.debit-balance');
        Route::post('subscriptions/{subscription}/record-payment', [SubscriptionController::class, 'recordPayment'])->name('subscriptions.record-payment');
        Route::delete('subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
    });
});

