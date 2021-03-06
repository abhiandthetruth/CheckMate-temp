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

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
//Home Route
Route::get('/', function () {
    return view('welcome');
});


// Text Recog routes.
Route::get('/annotate', 'AnnotationController@displayForm');
Route::post('/annotate', 'AnnotationController@annotateImage');

//Authentication Routes.
Auth::routes();


//----After login routes.----

//Home
Route::get('/home', 'HomeController@index')->name('home');

//Paper routes


//view paper
Route::get('/papers/', 'PapersController@index');

//Add a paper
Route::get('/papers/create', 'PapersController@create');
Route::post('/papers/create', 'PapersController@add');

//Add a question
Route::get('/papers/{code}', 'PapersController@addQuestions');
Route::post('/papers/{code}', 'PapersController@submitQuestions');

//Check paper
Route::post('/papers/{id}/check', 'CheckController@checkmate');

Route::get('/papers/{id}/result', 'PapersController@viewresult');

// View Result
Route::get('/result/{id}', 'PapersController@showresult');
