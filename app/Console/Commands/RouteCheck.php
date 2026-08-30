<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

class RouteCheck extends Command
{
    protected $signature = 'route:check';

    protected $description = 'Mengecek koneksi Route Root → Controller → Method → Model → View';

    protected int $ok = 0;
    protected int $warning = 0;
    protected int $error = 0;

    public function handle()
    {
        $this->newLine();
        $this->info('ROOT ROUTE → CONTROLLER → METHOD → MODEL → VIEW CHECK');
        $this->newLine();

        $routes = collect(Route::getRoutes())
            ->filter(function ($route) {
                return str_starts_with(
                    ltrim($route->uri(), '/'),
                    'root'
                );
            });

        if ($routes->isEmpty()) {
            $this->warn('Tidak ditemukan route dengan prefix root.');
            return self::SUCCESS;
        }

        $totalRoutes = 0;

        foreach ($routes as $route) {

            $totalRoutes++;

            $methods = implode('|', $route->methods());
            $uri = '/' . ltrim($route->uri(), '/');

            $action = $route->getActionName();

            /*
            |--------------------------------------------------------------------------
            | ROUTE CHECK
            |--------------------------------------------------------------------------
            */

            $routeStatus = '✅';
            $this->ok++;

            $this->line("{$routeStatus} {$methods} {$uri}");

            /*
            |--------------------------------------------------------------------------
            | CLOSURE CHECK
            |--------------------------------------------------------------------------
            */

            if ($route->getAction('uses') instanceof \Closure) {

                $this->line('   Controller: ⚠️ Closure');
                $this->warning++;

                $this->newLine();

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | CONTROLLER + METHOD
            |--------------------------------------------------------------------------
            */

            $uses = $route->getAction('uses');

            if (!is_string($uses) || !str_contains($uses, '@')) {

                $this->line("   Controller: ⚠️ {$action}");
                $this->warning++;

                $this->newLine();

                continue;
            }

            [$controllerClass, $method] = explode('@', $uses, 2);

            /*
            |--------------------------------------------------------------------------
            | CONTROLLER EXISTS
            |--------------------------------------------------------------------------
            */

            if (class_exists($controllerClass)) {

                $this->line(
                    "   Controller: ✅ {$controllerClass}"
                );

                $this->ok++;

            } else {

                $this->line(
                    "   Controller: ❌ {$controllerClass} tidak ditemukan"
                );

                $this->error++;

                $this->newLine();

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | METHOD EXISTS
            |--------------------------------------------------------------------------
            */

            try {

                $reflection = new ReflectionClass($controllerClass);

                if ($reflection->hasMethod($method)) {

                    $reflectionMethod = $reflection->getMethod($method);

                    if ($reflectionMethod->isPublic()) {

                        $this->line(
                            "   Method:    ✅ {$method}()"
                        );

                        $this->ok++;

                    } else {

                        $this->line(
                            "   Method:    ❌ {$method}() bukan public"
                        );

                        $this->error++;

                    }

                } else {

                    $this->line(
                        "   Method:    ❌ {$method}() tidak ditemukan"
                    );

                    $this->error++;

                    $this->newLine();

                    continue;
                }

            } catch (Throwable $e) {

                $this->line(
                    "   Method:    ❌ Error membaca {$method}()"
                );

                $this->line(
                    "              {$e->getMessage()}"
                );

                $this->error++;

                $this->newLine();

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | CONTROLLER SOURCE CHECK
            |--------------------------------------------------------------------------
            */

            try {

                $reflection = new ReflectionClass($controllerClass);

                $fileName = $reflection->getFileName();

                if ($fileName && File::exists($fileName)) {

                    $source = File::get($fileName);

                    /*
                    |--------------------------------------------------------------------------
                    | MODEL DETECTION
                    |--------------------------------------------------------------------------
                    */

                    preg_match_all(
                        '/use\s+(App\\\\Models\\\\[A-Za-z0-9_]+);/',
                        $source,
                        $modelImports
                    );

                    $models = array_unique($modelImports[1] ?? []);

                    if (!empty($models)) {

                        foreach ($models as $model) {

                            $modelName = class_basename($model);

                            if (class_exists($model)) {

                                $this->line(
                                    "   Model:      ✅ {$modelName}"
                                );

                                $this->ok++;

                            } else {

                                $this->line(
                                    "   Model:      ❌ {$modelName} tidak ditemukan"
                                );

                                $this->error++;
                            }
                        }

                    } else {

                        $this->line(
                            "   Model:      ⚠️ Tidak terdeteksi"
                        );

                        $this->warning++;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | VIEW DETECTION
                    |--------------------------------------------------------------------------
                    */

                    preg_match_all(
                        "/view\(\s*['\"]([^'\"]+)['\"]/",
                        $source,
                        $views
                    );

                    $detectedViews = array_unique($views[1] ?? []);

                    if (!empty($detectedViews)) {

                        foreach ($detectedViews as $view) {

                            $viewPath = resource_path(
                                'views/' .
                                str_replace('.', '/', $view) .
                                '.blade.php'
                            );

                            if (File::exists($viewPath)) {

                                $this->line(
                                    "   View:       ✅ {$view}"
                                );

                                $this->ok++;

                            } else {

                                $this->line(
                                    "   View:       ❌ {$view} tidak ditemukan"
                                );

                                $this->error++;
                            }
                        }

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | REDIRECT CHECK
                        |--------------------------------------------------------------------------
                        */

                        if (
                            str_contains($source, 'redirect(') ||
                            str_contains($source, 'to_route(')
                        ) {

                            $this->line(
                                "   View:       ⚠️ Redirect / tidak menggunakan view langsung"
                            );

                            $this->warning++;

                        } else {

                            $this->line(
                                "   View:       ⚠️ Tidak terdeteksi"
                            );

                            $this->warning++;
                        }
                    }
                }

            } catch (Throwable $e) {

                $this->line(
                    "   Inspection: ⚠️ Tidak dapat membaca source controller"
                );

                $this->warning++;
            }

            $this->newLine();
        }

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $this->info('========================================');
        $this->info('ROOT ROUTE CHECK SUMMARY');
        $this->info('========================================');

        $this->line("Total Root Route : {$totalRoutes}");
        $this->line("✅ OK             : {$this->ok}");
        $this->line("⚠️ Warning        : {$this->warning}");
        $this->line("❌ Error          : {$this->error}");

        $this->newLine();

        if ($this->error === 0) {

            $this->info(
                '🎉 Tidak ditemukan error pada Route → Controller → Method.'
            );

            if ($this->warning > 0) {

                $this->warn(
                    '⚠️ Ada warning yang perlu diperiksa secara manual.'
                );
            }

        } else {

            $this->error(
                '❌ Masih ada Route / Controller / Method yang bermasalah.'
            );
        }

        return $this->error > 0
            ? self::FAILURE
            : self::SUCCESS;
    }
}