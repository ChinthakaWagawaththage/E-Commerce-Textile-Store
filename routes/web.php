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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('admin')->namespace('Admin')->group(function(){
    //Admin Routes
    Route::match(['get','post'],'/','AdminController@login');

    Route::group(['middleware'=>['admin']],function(){
        Route::get('dashboard','AdminController@dashboard');
        Route::get('settings','AdminController@settings');
        Route::get('logout','AdminController@logout');
        Route::post('check-current-pwd','AdminController@checkCurrentPassword');
        Route::post('update-current-pwd','AdminController@updateCurrentPassword');
        Route::match(['get','post'],'update-admin-details','AdminController@updateAdminDetails');

        //sections
        Route::get('sections','SectionController@sections');
        Route::post('update-section-status','SectionController@updateSectionStatus');

        //categories
        Route::get('categories','CategoryController@categories');
        Route::post('update-category-status','CategoryController@updateCategoryStatus');
        Route::match(['get','post'],'add-edit-category/{id?}','CategoryController@addEditCategory');
        Route::post('append-categories-level','CategoryController@appendCategoryLevel');
        Route::get('delete-category-image/{id}','CategoryController@deleteCategoryImage');
        Route::get('delete-category/{id}','CategoryController@deleteCategory');

        //products
        Route::get('products','ProductsController@products');
        Route::post('update-product-status','ProductsController@updateProductStatus');
        Route::get('delete-product/{id}','ProductsController@deleteProduct');
        Route::match(['get','post'],'add-edit-product/{id?}','ProductsController@addEditProduct');
        Route::get('delete-product-image/{id}','ProductsController@deleteProductImage');
        Route::get('delete-product-video/{id}','ProductsController@deleteProductVideo');

        //Attributes
        Route::match(['get','post'],'add-attributes/{id}','ProductsController@addAttributes');
    });

});
