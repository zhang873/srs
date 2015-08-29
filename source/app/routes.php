<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/** ------------------------------------------
 *  Route model binding
 *  ------------------------------------------
 */
Route::model('user', 'User');
Route::model('comment', 'Comment');
Route::model('post', 'Post');
Route::model('role', 'Role');

/** ------------------------------------------
 *  Route constraint patterns
 *  ------------------------------------------
 */
Route::pattern('comment', '[0-9]+');
Route::pattern('post', '[0-9]+');
Route::pattern('user', '[0-9]+');
Route::pattern('role', '[0-9]+');
Route::pattern('token', '[0-9a-z]+');


/** ------------------------------------------
 *  Admin Routes
 *  ------------------------------------------
 */
Route::group(array('prefix' => 'admin', 'before' => 'auth'), function()
{

	# User Management
	Route::get('users/{user}/show', 'AdminUsersController@getShow');
	Route::get('users/{user}/edit', 'AdminUsersController@getEdit');
	Route::post('users/{user}/edit', 'AdminUsersController@postEdit');
	Route::get('users/{user}/delete', 'AdminUsersController@getDelete');
	Route::post('users/{user}/delete', 'AdminUsersController@postDelete');
	Route::controller('users', 'AdminUsersController');

	# User Role Management
	Route::get('roles/{role}/show', 'AdminRolesController@getShow');
	Route::get('roles/{role}/edit', 'AdminRolesController@getEdit');
	Route::post('roles/{role}/edit', 'AdminRolesController@postEdit');
	Route::get('roles/{role}/delete', 'AdminRolesController@getDelete');
	Route::post('roles/{role}/delete', 'AdminRolesController@postDelete');
	Route::controller('roles', 'AdminRolesController');

    # Admin Education Department
    Route::get('edu_department', 'AdminEduDepartmentController@getIndex');
    Route::get('edu_department/data_add', 'AdminEduDepartmentController@getCreate');
    Route::post('edu_department/data_add', 'AdminEduDepartmentController@postCreate');
    Route::get('edu_department/data', 'AdminEduDepartmentController@getDataForAdd');
    Route::get('edu_department/{id}/edit', 'AdminEduDepartmentController@getEdit');
    Route::post('edu_department/{id}/edit', 'AdminEduDepartmentController@postEdit');
	
    # Admin mianxiumiaokao
    Route::get('exemption/index', 'AdminExemptionController@getIndex');
    Route::get('exemption/data', 'AdminExemptionController@getDataForAdd');
    Route::get('exemption/selectiondata', 'AdminExemptionController@getDataForSelection');
    Route::get('exemption/input_student', 'AdminExemptionController@getInputStudent');
    Route::post('exemption/query', 'AdminExemptionController@postQuery');
    Route::post('exemption/input_require', 'AdminExemptionController@postRequire');
    Route::post('exemption/student_selection', 'AdminExemptionController@postSelection');
    Route::post('exemption/insert_exemption', 'AdminExemptionController@postInsertExemption');
    Route::post('exemption', 'AdminExemptionController@postIntoExemption');
    Route::controller('exemption','AdminExemptionController');
	
	# Admin Dashboard
	Route::controller('/', 'AdminDashboardController');


});

/** ------------------------------------------
 *  Frontend Routes
 *  ------------------------------------------
 */

//:: User Account Routes ::
Route::post('user/login', 'UserController@postLogin');

# User RESTful Routes (Login, Logout, Register, etc)
Route::post('user/{user}/edit', 'UserController@postEdit');
Route::get('forgot','UserController@getForgot');
Route::controller('user', 'UserController');

# Index Page - Last route, no matches
Route::get('/', array('before' => 'detectLang','uses' => 'UserController@getLogin'));
