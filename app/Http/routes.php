<?php



Route::get('/', function()
{
return "etst";
});



$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {



	// Set our namespace for the underlying routes
	$api->group(['namespace' => 'Api\Controllers', 'middleware' => '\Barryvdh\Cors\HandleCors::class'], function ($api) {

	$api->get('/', function(){
		return "123";
	});
		

	});

	});

*/