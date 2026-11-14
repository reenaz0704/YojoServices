<?php
/** @var App\Core\Router $router */

// JSON API used by the public frontend (Fetch API). All writes require CSRF.
$router->get('/api/csrf', 'Api\FormController@token');
$router->post('/api/enquiry', 'Api\FormController@enquiry', ['csrf']);
$router->post('/api/assessment', 'Api\AssessmentController@store', ['csrf']);
$router->get('/api/faqs', 'Api\ContentController@faqs');
$router->get('/api/services', 'Api\ContentController@services');
$router->get('/api/search', 'Api\ContentController@search');
