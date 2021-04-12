<?php

namespace Tonysm\TurboLaravel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Tonysm\TurboLaravel\Broadcasters\Broadcaster;
use Tonysm\TurboLaravel\Broadcasters\LaravelBroadcaster;
use Tonysm\TurboLaravel\Commands\TurboInstallCommand;
use Tonysm\TurboLaravel\Facades\Turbo as TurboFacade;
use Tonysm\TurboLaravel\Http\TurboResponseFactory;

class TurboServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/turbo-laravel.php' => config_path('turbo-laravel.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../resources/views' => base_path('resources/views/vendor/turbo-laravel'),
            ], 'views');

            $this->commands([
                TurboInstallCommand::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'turbo-laravel');

        $this->bindBladeMacros();
        $this->bindRequestAndResponseMacros();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/turbo-laravel.php', 'turbo-laravel');

        $this->app->singleton(Turbo::class);
        $this->app->bind(Broadcaster::class, LaravelBroadcaster::class);
    }

    private function bindBladeMacros(): void
    {
        Blade::if('turbonative', function () {
            return TurboFacade::isTurboNativeVisit();
        });

        Blade::directive('domid', function ($expression) {
            return "<?php echo e(\\Tonysm\\TurboLaravel\\dom_id($expression)); ?>";
        });

        Blade::directive('domclass', function ($expression) {
            return "<?php echo e(\\Tonysm\\TurboLaravel\\dom_class($expression)); ?>";
        });

        Blade::directive('channel', function ($expression) {
            return "<?php echo e(\\Tonysm\\TurboLaravel\\turbo_channel($expression)); ?>";
        });
    }

    private function bindRequestAndResponseMacros(): void
    {
        Response::macro('turboStream', function (Model $model, string $action = null) {
            return resolve(TurboStreamResponseMacro::class)->handle($model, $action);
        });

        Response::macro('turboStreamView', function ($view, array $data = []) {
            if (! $view instanceof View) {
                $view = view($view, $data);
            }

            return TurboResponseFactory::makeStream($view->render());
        });

        Request::macro('wantsTurboStream', function () {
            return Str::contains($this->header('Accept'), Turbo::TURBO_STREAM_FORMAT);
        });
    }
}
