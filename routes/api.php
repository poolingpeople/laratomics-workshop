<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| GET /info
|--------------------------------------------------------------------------
| Get generic information about the main Laravel app, that should be used or
| displayed in the workshop
*/
Route::get('info', 'ApplicationController@info');

/*
|--------------------------------------------------------------------------
| POST /pattern
|--------------------------------------------------------------------------
| Store a newly created Pattern.
*/
Route::post('pattern', 'PatternController@store');

/*
|--------------------------------------------------------------------------
| PUT /pattern/{pattern}
|--------------------------------------------------------------------------
| Update an existing Pattern.
*/
Route::put('pattern/{pattern}', 'PatternController@update');

/*
|--------------------------------------------------------------------------
| PUT /pattern/status/{pattern}
|--------------------------------------------------------------------------
| Update the status of the Pattern.
*/
Route::put('pattern/status/{pattern}', 'PatternController@status');

/*
|--------------------------------------------------------------------------
| GET /pattern/exists/{pattern}
|--------------------------------------------------------------------------
|
*/
Route::get('pattern/exists/{pattern}', 'PatternController@exists');

/*
|--------------------------------------------------------------------------
| GET /navi
|--------------------------------------------------------------------------
| Get the navigation.
*/
Route::get('navi', 'NavigationController@get');

/*
|--------------------------------------------------------------------------
| GET /{pattern}
|--------------------------------------------------------------------------
| Get all all information for the preview screen.
| Except the actual rendered preview, that will be loaded by an iframe.
*/
Route::get('{pattern}', 'PatternController@preview');

/*
|--------------------------------------------------------------------------
| DELETE /{pattern}
|--------------------------------------------------------------------------
| Route to remove the given Pattern.
*/
Route::delete('{pattern}', 'PatternController@remove');


