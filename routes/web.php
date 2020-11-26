<?php

use Illuminate\Support\Facades\Route;

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

// Auth Routes
\Illuminate\Support\Facades\Auth::routes(['verify' => true, 'register' => false]);

// Authenticated users route group
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'verified', 'online'], 'namespace' => 'App\Http\Controllers\Admin'], function(){
    Route::get('/', 'HomeController@index')->name('admin.home');

    // Only developer routes
    Route::group(['prefix' => 'dev', 'middleware' => 'dev', 'namespace' => 'Dev'], function(){

        // Dev/Permissions
        Route::prefix('permissions')->group(function (){
            Route::get('/', 'PermissionController@index')->name('permissions.index');

            // Only for ajax operations
            Route::middleware('only.ajax')->group(function (){
                Route::get('ajax', 'PermissionController@ajaxList')->name('permission.ajax');
                Route::get('checkboxes', 'PermissionController@checkboxesView')->name('permission.checkboxes');

                Route::post('/', 'PermissionController@create')->name('permissions.create');
                Route::get('{id}', 'PermissionController@find')->name('permissions.find');
                Route::put('{id}', 'PermissionController@update')->name('permissions.update');
                Route::delete('{id}', 'PermissionController@delete')->name('permissions.delete');
            });
        });
    });

    // Files
    Route::prefix('files')->group(function (){
        Route::post('/', 'FileController@upload')->name('files.upload');
        Route::get('{id}', 'FileController@download')->name('files.download');

        // Only for ajax operations
        Route::middleware('only.ajax')->group(function (){
            Route::delete('{id}', 'FileController@delete')->name('files.delete');
        });
    });

    // User profile page
    Route::prefix('profile')->group(function (){
        Route::get('/', 'ProfileController@index')->name('profile.index');
        Route::get('reset-password', 'ProfileController@requestPassword')->name('profile.reset_password');

        // Only for ajax operations
        Route::middleware('only.ajax')->group(function (){
            Route::put('/', 'ProfileController@update')->name('profile.update');
        });
    });

    // Settings page
    Route::prefix('settings')->group(function (){
        Route::get('/', 'SettingController@index')->name('settings.index');

        // Only for ajax operations
        Route::middleware('only.ajax')->group(function (){
            Route::put('/', 'SettingController@update')->name('settings.update');
        });
    });

    // Users page
    Route::prefix('users')->group(function (){
        Route::get('/', 'UserController@index')->name('users.index');

        // Only for ajax operations
        Route::middleware('only.ajax')->group(function (){
            Route::post('/', 'UserController@create')->name('users.create');
            Route::get('{id}', 'UserController@find')->name('users.find');
            Route::put('{id}', 'UserController@update')->name('users.update');
            Route::delete('{id}', 'UserController@delete')->name('users.delete');
            Route::get('restore/{id}', 'UserController@restore')->name('users.restore');
        });
    });

    // Menus page
    Route::group(['prefix' => 'menus', 'namespace' => 'Menu'], function(){
        Route::get('/', 'GroupController@index')->name('menus.index');

        // Only for ajax operations
        Route::middleware('only.ajax')->group(function (){
            Route::post('/', 'GroupController@create')->name('menus.create');
            Route::get('{id}', 'GroupController@find')->name('menus.find');
            Route::put('{id}', 'GroupController@update')->name('menus.update');
            Route::delete('{id}', 'GroupController@delete')->name('menus.delete');
            Route::get('restore/{id}', 'GroupController@restore')->name('menus.restore');
        });

        Route::prefix('{groupId}/items')->group(function (){
            Route::get('/', 'ItemController@index')->name('menu_items.index');

            // Only for ajax operations
            Route::middleware('only.ajax')->group(function (){
                Route::put('save-sequence', 'ItemController@saveSequence')->name('menu_items.save_sequence');
                Route::get('ajax', 'ItemController@ajaxList')->name('menu_items.ajax');

                Route::post('/', 'ItemController@create')->name('menu_items.create');
                Route::get('{id}', 'ItemController@find')->name('menu_items.find');
                Route::put('{id}', 'ItemController@update')->name('menu_items.update');
                Route::delete('{id}', 'ItemController@delete')->name('menu_items.delete');
                Route::get('restore/{id}', 'ItemController@restore')->name('menu_items.restore');
            });
        });
    });

});
