<?php
use Laravel\Lumen\Routing\Router;
/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/



$router->get('/', function () use ($router) {

    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {

    $router->post('articles', ['uses' => 'ArticleController@create']);

    $router->get('articles', ['uses' => 'ArticleController@allArticles']);

    $router->get('articles/{id?}', ['uses' => 'ArticleController@singleArticle']);

    $router->post('articles/{id}/comment', ['uses' => 'ArticleController@comment']);

    $router->post('articles/{id}/like', ['uses' => 'ArticleController@likeArticle']);

    $router->post('articles/{id}/view', ['uses' => 'ArticleController@viewArticle']);



    // $router->post('articles', ['uses' => 'ArticleController@create']);

    // $router->delete('articles/{id}', ['uses' => 'ArticleController@delete']);

    // $router->put('articles/{id}', ['uses' => 'ArticleController@update']);
});
