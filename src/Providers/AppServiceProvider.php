<?php
namespace Debugsolver\Bappe\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Debugsolver\Bappe\Middlewares\CheckWeb;


class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../views', 'indoc');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    public function register()
    {
    	$router = $this->app->make(Router::class);
        $router->middlewareGroup('web-check', array(
                StartSession::class,
                ShareErrorsFromSession::class
            )
        );
        $router->pushMiddlewareToGroup('web-check', CheckWeb::class);
    }
}
