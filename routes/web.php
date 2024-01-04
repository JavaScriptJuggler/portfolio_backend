<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\BlogController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\ServiceController;

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

/* $router->get('/', function () use ($router) {
    return $router->app->version();
}); */

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');

    /* getting frontend data */
    $router->get('/getHomeData', 'FrontendController@getHomePageData');
    $router->post('/getSocialLinks', 'ProfileController@getSocialLinks');
    $router->get('/getAboutData', 'FrontendController@getAboutData');
    $router->get('/getblogs', 'FrontendController@getblogs');
    $router->get('/getSingleBlogs', 'FrontendController@getSingleBlogs');
    $router->get('/getPortfolios', 'FrontendController@getPortfolios');
    $router->get('/getSinglePortfolio', 'FrontendController@getSinglePortfolio');
    $router->post('/sendmessage', 'FrontendController@sendmessage');

    $router->group(['middleware' => 'auth'], function () use ($router) {
        /* hero sectiopn cms */
        $router->post('/getHero', 'HeroController@index');
        $router->post('/storeHero', 'HeroController@storeHero');

        /* service section cms */
        $router->post('/storeServiceSectionCms', 'ServiceController@storeService');
        $router->post('/getServiceSectionCms', 'ServiceController@fetchServices');
        $router->post('/deleteServiceItems', 'ServiceController@deleteServiceItems');

        /* cta section */
        $router->post('/saveCta', 'CtaController@saveCta');
        $router->post('/getCta', 'CtaController@getCta');

        /* about section */
        $router->post('/saveAboutCms', 'AboutController@saveAboutCms');
        $router->post('/getAboutCms', 'AboutController@getAboutCms');

        /* skill section */
        $router->post('/saveSkills', 'AboutController@saveSkills');
        $router->post('/getSkills', 'AboutController@getSkills');
        $router->post('/deleteSkills', 'AboutController@deleteSkills');

        /* blog section */
        $router->post('/getBlog', 'BlogController@getBlog');
        $router->post('/searchBlog', 'BlogController@searchBlog');
        $router->post('/saveBlog', 'BlogController@saveBlog');
        $router->post('/deleteBlog', 'BlogController@deleteBlog');

        /* portfolio section */
        $router->post('/savePortfolio', 'PortfolioController@savePortfolio');
        $router->post('/getPortfolio', 'PortfolioController@getPortfolio');
        $router->post('/getPortfolioCategory', 'PortfolioController@getPortfolioCategory');
        $router->post('/searchPortfolio', 'PortfolioController@searchPortfolio');
        $router->post('/deletePortfolio', 'PortfolioController@deletePortfolio');

        /* profile section */
        $router->post('/saveSocialLinks', 'ProfileController@saveSocialLinks');
        $router->post('/changePassword', 'ProfileController@changePassword');
    });
});
