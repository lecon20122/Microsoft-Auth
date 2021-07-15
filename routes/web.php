<?php

use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmailMailable;
use Carbon\Carbon;
use App\Http\Controllers\TestController;

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

Route::get('/', 'HomeController@welcome');

Route::get('/callback', 'AuthController@MicrosoftCallback');
Route::get('/signout', 'AuthController@signout');
Route::get('/signin', 'AuthController@signin');

Route::get('json' , [TestController::class , 'ChunkJsonFile']);

Route::post('upload' , [TestController::class,'upload']);
Route::get('store' , [TestController::class,'store']);

