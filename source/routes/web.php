<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Frontend\ArticleDetailController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\BlogMenuController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\CommentsController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\DocumentController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PageStaticController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\ProductDetailController;
use App\Http\Controllers\Frontend\ShoppingCartController;
use App\Http\Controllers\Frontend\TrackOrderController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'account'], function(){
    Route::get('register',[RegisterController::class, 'getFormRegister'])->name('get.register');
    Route::post('register',[RegisterController::class, 'postRegister']);

    Route::get('login','LoginController@getFormLogin')->name('get.login');
    Route::post('login','LoginController@postLogin');

    Route::get('logout',[LoginController::class, 'getLogout'])->name('get.logout');
    Route::get('reset-password',[ResetPasswordController::class, 'getEmailReset'])->name('get.email_reset_password');
    Route::post('reset-password',[ResetPasswordController::class, 'checkEmailResetPassword']);

    Route::get('new-password',[ResetPasswordController::class, 'newPassword'])->name('get.new_password');
    Route::post('new-password',[ResetPasswordController::class, 'savePassword']);

    Route::get('/{social}/redirect',  [SocialAuthController::class, 'redirect'])->name('get.login.social');
    Route::get('/{social}/callback',  [SocialAuthController::class, 'callback'])->name('get.login.social_callback');
});

// Login admin
Route::group(['prefix' => 'admin-auth'], function() {
    Route::get('login', [AdminLoginController::class, 'getLoginAdmin'])->name('get.login.admin');
    Route::post('login', [AdminLoginController::class, 'postLoginAdmin']);
    Route::get('logout', [AdminLoginController::class, 'getLogoutAdmin'])->name('get.logout.admin');
});


Route::group(['namespace' => 'Frontend'], function() {
    Route::get('',[HomeController::class, 'index'])->name('get.home');
    Route::get('ajax-load-product-recently',[HomeController::class, 'getLoadProductRecently'])->name('ajax_get.product_recently');
    Route::get('ajax-load-slide',[HomeController::class, 'loadSlideHome'])->name('ajax_get.slide');
    Route::get('san-pham', [ProductController::class, 'index'])->name('get.product.list');
    Route::get('danh-muc/{slug}', [CategoryController::class, 'index'])->name('get.category.list');
    Route::get('san-pham/{slug}', [ProductDetailController::class, 'getProductDetail'])->name('get.product.detail');
    Route::get('san-pham/{slug}/danh-gia', [ProductDetailController::class, 'getListRatingProduct'])->name('get.product.rating_list');

    Route::get('bai-viet', [BlogController::class, 'index'])->name('get.blog.home');
    Route::get('menu/{slug}', [BlogMenuController::class, 'getArticleByMenu'])->name('get.article.by_menu');
    Route::get('bai-viet/{slug}', [ArticleDetailController::class, 'index'])->name('get.blog.detail');
    // GIỏ hàng
    Route::get('don-hang', [ShoppingCartController::class, 'index'])->name('get.shopping.list');
    Route::prefix('shopping')->group(function () {
        Route::get('add/{id}', [ShoppingCartController::class, 'add'])->name('get.shopping.add');
        Route::get('delete/{id}', [ShoppingCartController::class, 'delete'])->name('get.shopping.delete');
        Route::get('update/{id}', [ShoppingCartController::class, 'update'])->name('ajax_get.shopping.update');
        Route::get('theo-doi-don-hang', [TrackOrderController::class, 'index'])->name('get.track \Cart::count() }}');
        Route::post('pay', [ShoppingCartController::class, 'postPay'])->name('post.shopping.pay');
    });

    //Comment
    Route::group(['prefix' => 'comment', 'middleware' => 'check_user_login'], function(){
        Route::post('ajax-comment', [CommentsController::class, 'store'])->name('ajax_post.comment');
    });

    Route::get('lien-he', [ContactController::class, 'index'])->name('get.contact');
    Route::get('convert-word-to-pdf',[ContactController::class, 'convertWordToPdf'])->name('convert.word.to.pdf');
    Route::post('lien-he', [ContactController::class, 'store']);
    Route::get('san-pham-ban-da-xem', [PageStaticController::class, 'getProductView'])->name('get.static.product_view');
    Route::get('ajax/san-pham-ban-da-xem', [PageStaticController::class, 'getListProductView'])->name('ajax_get.product_view');
    Route::get('huong-dan-mua-hang', [PageStaticController::class, 'getShoppingGuide'])->name('get.static.shopping_guide');
    Route::get('chinh-sach-doi-tra', [PageStaticController::class, 'getReturnPolicy'])->name('get.static.return_policy');
    Route::get('cham-soc-khach-hang', [PageStaticController::class, 'getCustomerCare'])->name('get.static.customer_care');



    Route::get('ajax/load-document', [PageStaticController::class, 'getDocumentAjax'])->name('get_ajax.static.document');
    Route::get('demo/view-file', [PageStaticController::class, 'getDemoViewFile']);

    Route::group(['prefix' => 'document'], function(){
        Route::get('/index', [DocumentController::class, 'index'])->name('get.document.index');
        Route::get('/list', [DocumentController::class, 'list'])->name('get.document.list');
        Route::get('/detail', [DocumentController::class, 'detail'])->name('get.document.detail');
    });
});

include 'route_api.php';
