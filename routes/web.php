<?php

Route::redirect('/home', '/admin');
Auth::routes(['register' => false]);

Route::get('/', 'HomeController@index')->name('home');
Route::get('search', 'HomeController@search')->name('search');
Route::resource('jobs', 'JobController')->only(['index', 'show']);
Route::get('category/{category}', 'CategoryController@show')->name('categories.show');
Route::get('location/{location}', 'LocationController@show')->name('locations.show');
Route::get('admin/categories/getsubcategories', 'Admin\CategoriesController@getCategoriesByParent');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Categories
    Route::delete('categories/destroy', 'CategoriesController@massDestroy')->name('categories.massDestroy');
    Route::resource('categories', 'CategoriesController');

    // Locations
    Route::delete('locations/destroy', 'LocationsController@massDestroy')->name('locations.massDestroy');
    Route::resource('locations', 'LocationsController');

    // Companies
    Route::delete('companies/destroy', 'CompaniesController@massDestroy')->name('companies.massDestroy');
    Route::post('companies/media', 'CompaniesController@storeMedia')->name('companies.storeMedia');
    Route::resource('companies', 'CompaniesController');

    // Jobs
    Route::delete('jobs/destroy', 'JobsController@massDestroy')->name('jobs.massDestroy');
    Route::resource('jobs', 'JobsController');
});


// Route::get('{lang}/{any?}/{category}/{post_id}', function ($lang, $any = null, $category, $postId) {
//     // var_dump($any);
//     // exit;
//     // echo 'Language: ';
//     // echo $lang . '<br>';
//     // echo 'Address: ';
//     // echo str_replace('/', ' ', $any) . '<br>';
//     // echo 'Category: ';
//     // echo $category . '<br>';
//     // echo 'Post ID: ';
//     // echo $postId . '<br>';



//     // // $any is optional
//     // if($any) {
//     //     $paramsArray = explode('/', $any);
//     //     // Use $paramsArray array for other parameters
//     // }

// })->where('any', '(.*)')->name('home');


Route::get('{lang}/{any?}/{category}/{post_id}', 'HomeController@index')->where('any', '(.*)')->name('home');
Route::post('areas/save', 'AreaController@store');
Route::resource('blog', 'BlogController');
Route::post('blog/like', 'BlogController@like');
Route::post('blog/dislike', 'BlogController@dislike');
Route::post('blog/leave-comment', 'BlogController@leaveComment');
Route::post('blog/upload-post-image', 'BlogController@uploadPostImage');