<?php
/** @var App\Core\Router $router */

// ---- Public website ----
$router->get('/', 'HomeController@index');
$router->get('/about', 'PageController@about');
$router->get('/faq', 'PageController@faq');
$router->get('/legal/{slug}', 'PageController@legal');

$router->get('/services', 'ServiceController@index');
$router->get('/services/{category}', 'ServiceController@category');
$router->get('/services/{category}/{slug}', 'ServiceController@show');

$router->get('/industries', 'IndustryController@index');
$router->get('/industries/{slug}', 'IndustryController@show');

$router->get('/case-studies', 'CaseStudyController@index');
$router->get('/case-studies/{slug}', 'CaseStudyController@show');

$router->get('/insights', 'ResourceController@index');
$router->get('/insights/{slug}', 'ResourceController@show');
// The former Resources section was removed; old links are permanently redirected.
$router->get('/resources', 'ResourceController@legacyIndex');
$router->get('/resources/{slug}', 'ResourceController@legacyShow');
$router->get('/who-we-are', 'PageController@whoWeAre');
$router->get('/frameworks/{slug}', 'PageController@framework');

$router->get('/contact', 'ContactController@contact');
$router->get('/book-consultation', 'ContactController@consultation');
$router->get('/request-proposal', 'ContactController@proposal');
$router->get('/security-assessment', 'ContactController@assessment');

$router->get('/media/{file}', 'MediaController@show');
$router->get('/sitemap.xml', 'PageController@sitemap');

// ---- Admin dashboard ----
$router->get('/admin/login', 'Admin\AuthController@loginForm');
$router->post('/admin/login', 'Admin\AuthController@login', ['csrf']);
$router->post('/admin/logout', 'Admin\AuthController@logout', ['csrf']);
$router->get('/admin', 'Admin\DashboardController@index', ['auth']);
$router->get('/admin/profile', 'Admin\AuthController@profile', ['auth']);
$router->post('/admin/profile', 'Admin\AuthController@updateProfile', ['auth', 'csrf']);

$router->get('/admin/enquiries', 'Admin\EnquiryController@index', ['auth']);
$router->get('/admin/enquiries/export', 'Admin\EnquiryController@export', ['auth', 'role:admin']);
$router->get('/admin/enquiries/{id}', 'Admin\EnquiryController@show', ['auth']);
$router->post('/admin/enquiries/{id}', 'Admin\EnquiryController@update', ['auth', 'role:editor', 'csrf']);
$router->post('/admin/enquiries/{id}/delete', 'Admin\EnquiryController@destroy', ['auth', 'role:admin', 'csrf']);

$router->get('/admin/assessments', 'Admin\AssessmentController@index', ['auth']);
$router->get('/admin/assessments/{id}', 'Admin\AssessmentController@show', ['auth']);
$router->post('/admin/assessments/{id}', 'Admin\AssessmentController@update', ['auth', 'role:editor', 'csrf']);

$router->get('/admin/settings', 'Admin\SettingsController@index', ['auth', 'role:admin']);
$router->post('/admin/settings', 'Admin\SettingsController@save', ['auth', 'role:admin', 'csrf']);
$router->post('/admin/manage/{resource}/{id}/move', 'Admin\CrudController@move', ['auth', 'role:editor', 'csrf']);
$router->get('/admin/activity', 'Admin\DashboardController@activity', ['auth', 'role:admin']);
$router->post('/admin/upload', 'Admin\UploadController@store', ['auth', 'role:editor', 'csrf']);

// Generic content CRUD (services, posts, industries, faqs, testimonials, users …)
$router->get('/admin/manage/{resource}', 'Admin\CrudController@index', ['auth']);
$router->get('/admin/manage/{resource}/create', 'Admin\CrudController@create', ['auth', 'role:editor']);
$router->post('/admin/manage/{resource}/create', 'Admin\CrudController@store', ['auth', 'role:editor', 'csrf']);
$router->get('/admin/manage/{resource}/{id}/edit', 'Admin\CrudController@edit', ['auth', 'role:editor']);
$router->post('/admin/manage/{resource}/{id}/edit', 'Admin\CrudController@update', ['auth', 'role:editor', 'csrf']);
$router->post('/admin/manage/{resource}/{id}/delete', 'Admin\CrudController@destroy', ['auth', 'role:editor', 'csrf']);
$router->post('/admin/manage/{resource}/{id}/toggle', 'Admin\CrudController@toggle', ['auth', 'role:editor', 'csrf']);
