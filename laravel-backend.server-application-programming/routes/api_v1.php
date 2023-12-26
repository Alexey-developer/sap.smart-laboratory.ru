<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\MainController;
use App\Http\Controllers\Api\v1\NeuralNetworkController;
use App\Http\Controllers\Api\v1\EntityController;
use App\Http\Controllers\Api\v1\NNModelController;
use App\Http\Controllers\Api\v1\PassportAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [PassportAuthController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/search-pictures', [MainController::class, 'search_pictures']);
    Route::post('/get-user-neural-networks', [NeuralNetworkController::class, 'get_user_neural_networks']);
    Route::post('/store-neural-network', [NeuralNetworkController::class, 'store_neural_network']);
    Route::post('/delete-neural-network', [NeuralNetworkController::class, 'delete_neural_network']);
    Route::post('/get-user-neural-network-by-id', [NeuralNetworkController::class, 'get_user_neural_network_by_id']);

    Route::post('/store-neural-network-entity', [EntityController::class, 'store_neural_network_entity']);
    Route::post('/store-neural-network-model', [NNModelController::class, 'store_neural_network_model']);
    Route::post('/predict', [NeuralNetworkController::class, 'predict']);
    Route::post('/delete-neural-network-entity', [EntityController::class, 'delete_neural_network_entity']);
    Route::post('/get-entity-by-id', [EntityController::class, 'get_entity_by_id']);

    Route::post('/add-images2entity', [EntityController::class, 'add_images2entity']);

    Route::post('/download-entity-images', [EntityController::class, 'download_entity_images']);
});
