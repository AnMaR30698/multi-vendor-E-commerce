<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/send-verification-code', [VerificationController::class, 'sendVerificationCode']);
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount'])->middleware('auth:api');
});

Route::prefix('user')->middleware('auth:api')->group(function () {
    Route::post('/customer/edit', [UserController::class, 'editCustomer']);
    Route::post('/vendor/edit', [UserController::class, 'editVendor']);
    Route::post('/delivery-boy/edit', [UserController::class, 'editDeliveryBoy']);
    Route::post('/admin/edit', [UserController::class, 'editAdmin']);    Route::delete('/delete', [UserController::class, 'delete']);
});

Route::get('allProducts',[ProductsController::class,'allProducts']);

Route::prefix('products/{store_id}')->middleware('auth:api')->group(function (){
    Route::get('/',[ProductsController::class,'index']);
    Route::middleware('role:admin|vendor')->group(function (){
        Route::post('/',[ProductsController::class,'store']);
        Route::put('update/{product_id}',[ProductsController::class,'update']);
        Route::delete('delete/{product_id}',[ProductsController::class,'delete']); 
   });                      
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::middleware(['auth:api','role:admin'])->group(function (){
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    }); 
});

Route::prefix('subcategories')->group(function () {
    Route::get('/', [SubcategoryController::class, 'index']);
    Route::get('/{id}', [SubcategoryController::class, 'show']);
    Route::middleware(['auth:api','role:admin'])->group(function (){
        Route::post('/', [SubcategoryController::class, 'store']);
        Route::put('/{id}', [SubcategoryController::class, 'update']);
        Route::delete('/{id}', [SubcategoryController::class, 'destroy']);
    });
});

Route::prefix('cart')->middleware('auth:api')->group( function () {
    Route::post('/items', [CartController::class, 'addItem'])->name('api.cart.add');
    Route::get('/items', [CartController::class, 'getCartItems'])->name('api.cart.items');
    Route::put('/items/{cartItem}', [CartController::class, 'updateCartItem'])->name('api.cart.update');
    Route::delete('/items/{cartItem}', [CartController::class, 'deleteCartItem'])->name('api.cart.delete');
});