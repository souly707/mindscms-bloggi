<?php

use Illuminate\Support\Facades\Route;

/*
--------------------------------------------------------------------------
  Web Routes
--------------------------------------------------------------------------
*/

Route::get('/', ['as' => 'frontend.index', 'uses' => 'Frontend\IndexController@Index']);

/*
--------------------------------------------------------------------------
  Authentication FrontEnd Routes
--------------------------------------------------------------------------
*/

Route::get('/login',                    ['as' => 'frontend.show_login_form',         'uses' => 'Frontend\Auth\LoginController@showLoginForm']);
Route::post('login',                    ['as' => 'frontend.login',                   'uses' => 'Frontend\Auth\LoginController@login']);
Route::post('logout',                   ['as' => 'frontend.logout',                  'uses' => 'Frontend\Auth\LoginController@logout']);
Route::get('register',                  ['as' => 'frontend.show_register_form',      'uses' => 'Frontend\Auth\RegisterController@showRegistrationForm']);
Route::post('register',                 ['as' => 'frontend.register',                'uses' => 'Frontend\Auth\RegisterController@register']);
Route::get('password/reset',            ['as' => 'password.request',                 'uses' => 'Frontend\Auth\ForgotPasswordController@showLinkRequestForm']);
Route::post('password/email',           ['as' => 'password.email',                   'uses' => 'Frontend\Auth\ForgotPasswordController@sendResetLinkEmail']);
Route::get('password/reset/{token}',    ['as' => 'password.reset',                   'uses' => 'Frontend\Auth\ResetPasswordController@showResetForm']);
Route::post('password/reset',           ['as' => 'password.update',                  'uses' => 'Frontend\Auth\ResetPasswordController@reset']);
Route::get('email/verify',              ['as' => 'verification.notice',              'uses' => 'Frontend\Auth\VerificationController@show']);
Route::get('email/verify/{id}/{hash}',  ['as' => 'verification.verify',              'uses' => 'Frontend\Auth\VerificationController@verify']);
Route::post('email/resend',             ['as' => 'verification.resend',              'uses' => 'Frontend\Auth\VerificationController@resend']);


/*
--------------------------------------------------------------------------
  Users  Routes And Must Be Verivied
--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'verified'], function () {

  Route::get('/dashboard',                          ['as' => 'frontend.dashboard',              'uses' => 'Frontend\UsersController@index']);

  Route::get('user/notifications/get',             'Frontend\Notifications@getNotifications');
  Route::any('user/notifications/read',            'Frontend\Notifications@markAsRead');
  Route::any('user/notifications/read/{id}',       'Frontend\Notifications@markAsReadAndRedirect');


  Route::get('/edit-info',                          ['as' => 'user.edit_info',                  'uses' => 'Frontend\UsersController@edit_info']);
  Route::post('/edit-info',                         ['as' => 'user.update_info',                'uses' => 'Frontend\UsersController@update_info']);
  Route::post('/edit-password',                     ['as' => 'user.update_password',            'uses' => 'Frontend\UsersController@update_password']);

  Route::get('/create-post',                        ['as' => 'user.post.create',                'uses' => 'Frontend\UsersController@create_post']);
  Route::post('/create-post',                       ['as' => 'user.post.store',                 'uses' => 'Frontend\UsersController@store_post']);

  Route::get('/edit-post/{post_id}',                ['as' => 'user.post.edit',                  'uses' => 'Frontend\UsersController@edit_post']);
  Route::put('/edit-post/{post_id}',                ['as' => 'user.post.update',                'uses' => 'Frontend\UsersController@update_post']);
  Route::post('/delete-post/media/{media_id}',      ['as' => 'user.post.media.destroy',         'uses' => 'Frontend\UsersController@destroy_post_media']);
  Route::delete('/delete-post/{post_id}',           ['as' => 'user.post.destroy',               'uses' => 'Frontend\UsersController@destroy_post']);

  Route::get('/comments',                           ['as' => 'user.comments',                   'uses' => 'Frontend\UsersController@show_comments']);
  Route::get('/edit-comment/{comment_id}',          ['as' => 'user.comment.edit',               'uses' => 'Frontend\UsersController@edit_comment']);
  Route::put('/edit-comment/{comment_id}',          ['as' => 'user.comment.update',             'uses' => 'Frontend\UsersController@update_comment']);
  Route::delete('/delete-comment/{comment}',        ['as' => 'user.comment.destroy',            'uses' => 'Frontend\UsersController@destroy_comment']);
});


/*
--------------------------------------------------------------------------
  Authentication BackEnd Routes
--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'admin'], function () {

  Route::get('/login',                    ['as' => 'admin.show_login_form',         'uses' => 'Backend\Auth\LoginController@showLoginForm']);
  Route::post('login',                    ['as' => 'admin.login',                   'uses' => 'Backend\Auth\LoginController@login']);
  Route::post('logout',                   ['as' => 'admin.logout',                  'uses' => 'Backend\Auth\LoginController@logout']);
  Route::get('password/reset',            ['as' => 'password.request',              'uses' => 'Backend\Auth\ForgotPasswordController@showLinkRequestForm']);
  Route::post('password/email',           ['as' => 'password.email',                'uses' => 'Backend\Auth\ForgotPasswordController@sendResetLinkEmail']);
  Route::get('password/reset/{token}',    ['as' => 'password.reset',                'uses' => 'Backend\Auth\ResetPasswordController@showResetForm']);
  Route::post('password/reset',           ['as' => 'password.update',               'uses' => 'Backend\Auth\ResetPasswordController@reset']);


  Route::group(['middleware' => ['roles', 'role:admin|editor']], function () {

    Route::any('/notifications/get',        'Backend\NotificationsController@getNotifications');
    Route::any('/notifications/read',       'Backend\NotificationsController@markAsRead');
    Route::any('/notifications/read/{id}',  'Backend\NotificationsController@markAsReadAndRedirect');

    Route::get('/',                             ['as' => 'admin.index_route',            'uses' => 'Backend\AdminController@index']);
    Route::get('/index',                        ['as' => 'admin.index',                  'uses' => 'Backend\AdminController@index']);

    // Delete Users Images Routes
    Route::get('/posts/delete/image/{id}',        ['as' => 'admin.posts.media.destroy',           'uses' => 'Backend\PostController@destroy_image']);
    Route::get('/pages/delete/image/{id}',        ['as' => 'admin.pages.media.destroy',           'uses' => 'Backend\PagesController@destroy_image']);
    Route::get('/users/delete/image/{id}',        ['as' => 'admin.users.media.destroy',           'uses' => 'Backend\UsersController@destroy_image']);
    Route::get('/supervisors/delete/image/{id}',  ['as' => 'admin.supervisors.media.destroy',     'uses' => 'Backend\SupervisorController@destroy_image']);

    // resource Routes
    Route::resource('posts',              'Backend\PostController',                 ['as' => 'admin']);
    Route::resource('pages',              'Backend\PagesController',                ['as' => 'admin']);
    Route::resource('post_comments',      'Backend\PostCommentsController',         ['as' => 'admin']);
    Route::resource('post_categories',    'Backend\PostCategoriesController',       ['as' => 'admin']);
    Route::resource('users',              'Backend\UsersController',                ['as' => 'admin']);
    Route::resource('contact_us',         'Backend\ContactUsController',            ['as' => 'admin']);
    Route::resource('supervisors',        'Backend\SupervisorController',           ['as' => 'admin']);
    Route::resource('settings',           'Backend\SettingsController',             ['as' => 'admin']);
  });
});


// Contact Us
Route::get('/contact-us',                 ['as' => 'frontend.contact',        'uses' => 'Frontend\IndexController@contact']);
Route::post('/contact-us',                ['as' => 'frontend.do_contact',     'uses' => 'Frontend\IndexController@do_contact']);

// Category Slug Route
Route::get('/category/{category_slug}',   ['as' => 'frontend.category.posts',  'uses' => 'Frontend\IndexController@category']);
// Archive Route
Route::get('/archive/{date}',             ['as' => 'frontend.archive.posts',   'uses' => 'Frontend\IndexController@archive']);
// Author Route
Route::get('/author/{username}',          ['as' => 'frontend.author.posts',    'uses' => 'Frontend\IndexController@author']);

// Search Route
Route::get('/search',                     ['as' => 'frontend.search',         'uses' => 'Frontend\IndexController@search']);

// Show Post By Slug
Route::get('/{post}',                     ['as' => 'posts.show',              'uses' => 'Frontend\IndexController@post_show']);
// Add Comment To Post
Route::post('/{post}',                    ['as' => 'posts.add.comment',       'uses' => 'Frontend\IndexController@store_comment']);