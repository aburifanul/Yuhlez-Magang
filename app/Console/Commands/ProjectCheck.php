<?php

namespace App\Console\Commands;

use App\Models\Certificate;
use App\Models\Company;
use App\Models\Intern;
use App\Models\InternshipParticipant;
use App\Models\InternshipPosition;
use App\Models\InternshipProgram;
use App\Models\InternshipProgramBanner;
use App\Models\InternshipRegistration;
use App\Models\User;
use App\Models\Work;
use App\Models\WorkMember;
use App\Models\WorkPhoto;
use Closure;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

class ProjectCheck extends Command
{
    protected $signature = 'project:check';

    protected $description =
        'Check Model, Migration, Table, Relationship, Controller, Route, Method dan View';

    protected int $ok = 0;
    protected int $warning = 0;
    protected int $error = 0;

    protected array $migrationCache = [];

    /*
    |--------------------------------------------------------------------------
    | MODEL → CONTROLLER → ROUTE
    |--------------------------------------------------------------------------
    */

    protected array $modules = [

        User::class => [
            'controller' => \App\Http\Controllers\Root\UserController::class,
            'route_prefix' => 'users',
            'route_name' => 'root.users',
        ],

        Company::class => [
            'controller' => \App\Http\Controllers\Root\CompanyController::class,
            'route_prefix' => 'companies',
            'route_name' => 'root.companies',
        ],

        Intern::class => [
            'controller' => \App\Http\Controllers\Root\InternController::class,
            'route_prefix' => 'interns',
            'route_name' => 'root.interns',
        ],

        InternshipProgram::class => [
            'controller' => \App\Http\Controllers\Root\InternshipProgramController::class,
            'route_prefix' => 'internship-programs',
            'route_name' => 'root.internship-programs',
        ],

        InternshipProgramBanner::class => [
            'controller' => \App\Http\Controllers\Root\InternshipProgramBannerController::class,
            'route_prefix' => 'internship-program-banners',
            'route_name' => 'root.internship-program-banners',
        ],

        InternshipPosition::class => [
            'controller' => \App\Http\Controllers\Root\InternshipPositionController::class,
            'route_prefix' => 'internship-positions',
            'route_name' => 'root.internship-positions',
        ],

        InternshipRegistration::class => [
            'controller' => \App\Http\Controllers\Root\InternshipRegistrationController::class,
            'route_prefix' => 'internship-registrations',
            'route_name' => 'root.internship-registrations',
        ],

        InternshipParticipant::class => [
            'controller' => \App\Http\Controllers\Root\InternshipParticipantController::class,
            'route_prefix' => 'internship-participants',
            'route_name' => 'root.internship-participants',
        ],

        Certificate::class => [
            'controller' => \App\Http\Controllers\Root\CertificateController::class,
            'route_prefix' => 'certificates',
            'route_name' => 'root.certificates',
        ],

        Work::class => [
            'controller' => \App\Http\Controllers\Root\WorkController::class,
            'route_prefix' => 'works',
            'route_name' => 'root.works',
        ],

        WorkPhoto::class => [
            'controller' => \App\Http\Controllers\Root\WorkPhotoController::class,
            'route_prefix' => 'work-photos',
            'route_name' => 'root.work-photos',
        ],

        WorkMember::class => [
            'controller' => \App\Http\Controllers\Root\WorkMemberController::class,
            'route_prefix' => 'work-members',
            'route_name' => 'root.work-members',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | HANDLE
    |--------------------------------------------------------------------------
    */

    public function handle(): int
    {
        $this->ok = 0;
        $this->warning = 0;
        $this->error = 0;

        $this->loadMigrations();

        $this->header();

        foreach ($this->modules as $modelClass => $config) {
            $this->checkModule(
                $modelClass,
                $config
            );
        }

        $this->summary();

        return $this->error > 0
            ? self::FAILURE
            : self::SUCCESS;
    }

    /*
    |--------------------------------------------------------------------------
    | HEADER
    |--------------------------------------------------------------------------
    */

    protected function header(): void
    {
        $this->newLine();

        $this->info(
            '============================================================'
        );

        $this->info(
            'YUHLEZ MAGANG — PROJECT CHECK'
        );

        $this->info(
            'MODEL → MIGRATION → TABLE → RELATIONSHIP → CONTROLLER → ROUTE → VIEW'
        );

        $this->info(
            '============================================================'
        );

        $this->newLine();
    }

    /*
    |--------------------------------------------------------------------------
    | MIGRATION CACHE
    |--------------------------------------------------------------------------
    */

    protected function loadMigrations(): void
    {
        $migrationPath = database_path('migrations');

        if (!File::isDirectory($migrationPath)) {
            return;
        }

        foreach (File::allFiles($migrationPath) as $file) {
            $this->migrationCache[
                $file->getFilename()
            ] = File::get(
                $file->getPathname()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MODULE
    |--------------------------------------------------------------------------
    */

    protected function checkModule(
        string $modelClass,
        array $config
    ): void {

        $modelName =
            class_basename($modelClass);

        $this->line(
            '============================================================'
        );

        $this->info(
            strtoupper($modelName)
        );

        $this->line(
            '------------------------------------------------------------'
        );

        /*
        |--------------------------------------------------------------------------
        | MODEL
        |--------------------------------------------------------------------------
        */

        if (!class_exists($modelClass)) {

            $this->error++;

            $this->line(
                'Model           : ❌ ' . $modelName
            );

            $this->line(
                'STATUS          : ❌ MODEL TIDAK DITEMUKAN'
            );

            $this->newLine();

            return;
        }

        $this->ok++;

        /** @var Model $model */
        $model = new $modelClass();

        $table = $model->getTable();

        $this->line(
            "Model           : ✅ {$modelName}"
        );

        /*
        |--------------------------------------------------------------------------
        | MIGRATION
        |--------------------------------------------------------------------------
        */

        if (
            $this->migrationExistsForTable($table)
        ) {

            $this->ok++;

            $this->line(
                "Migration       : ✅ {$table}"
            );

        } else {

            $this->warning++;

            $this->line(
                "Migration       : ⚠️ {$table} tidak ditemukan"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        if (
            Schema::hasTable($table)
        ) {

            $this->ok++;

            $this->line(
                "Table           : ✅ {$table}"
            );

        } else {

            $this->error++;

            $this->line(
                "Table           : ❌ {$table}"
            );

            $this->line(
                "STATUS          : ❌ TABLE TIDAK DITEMUKAN"
            );

            $this->newLine();

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | RELATIONSHIPS
        |--------------------------------------------------------------------------
        */

        $this->line('');
        $this->line('Relationships');

        $relationshipCount =
            $this->checkRelationships($model);

        if ($relationshipCount === 0) {

            $this->line(
                '  └─ Tidak ada relationship'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CONTROLLER
        |--------------------------------------------------------------------------
        */

        $this->line('');
        $this->line('Controller');

        $controller =
            $config['controller'];

        if (
            class_exists($controller)
        ) {

            $this->ok++;

            $this->line(
                '  └─ ' .
                class_basename($controller) .
                ' : ✅'
            );

            $this->checkControllerMethods(
                $controller,
                $config
            );

        } else {

            $this->error++;

            $this->line(
                '  └─ ' .
                class_basename($controller) .
                ' : ❌ TIDAK DITEMUKAN'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ROUTES
        |--------------------------------------------------------------------------
        */

        $this->line('');
        $this->line('Routes');

        $routes =
            $this->getModuleRoutes(
                $config['route_name']
            );

        if ($routes->isEmpty()) {

            $this->error++;

            $this->line(
                '  └─ Route : ❌ TIDAK DITEMUKAN'
            );

        } else {

            foreach ($routes as $route) {

                $this->ok++;

                $methods =
                    implode(
                        '|',
                        array_diff(
                            $route->methods(),
                            ['HEAD']
                        )
                    );

                $this->line(
                    "  ├─ {$methods} /{$route->uri()} : ✅"
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VIEWS
        |--------------------------------------------------------------------------
        */

        $this->line('');
        $this->line('Views');

        $this->checkViews(
            $config['route_name']
        );

        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        $status =
            $this->moduleStatus(
                $modelClass,
                $config
            );

        $this->line('');
        $this->line(
            "STATUS          : {$status}"
        );

        $this->newLine();
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    protected function checkRelationships(
        Model $model
    ): int {

        $count = 0;

        try {

            $reflection =
                new ReflectionClass($model);

            foreach (
                $reflection->getMethods(
                    ReflectionMethod::IS_PUBLIC
                ) as $method
            ) {

                if (
                    $method->class !== get_class($model) ||
                    $method->getNumberOfParameters() > 0
                ) {
                    continue;
                }

                try {

                    $relation =
                        $method->invoke($model);

                    if (
                        !$relation instanceof BelongsTo &&
                        !$relation instanceof HasMany &&
                        !$relation instanceof HasOne &&
                        !$relation instanceof MorphMany &&
                        !$relation instanceof BelongsToMany
                    ) {
                        continue;
                    }

                    $count++;

                    $name =
                        $method->getName();

                    $related =
                        $relation->getRelated();

                    $relatedClass =
                        get_class($related);

                    $relatedTable =
                        $related->getTable();

                    $type =
                        class_basename(
                            get_class($relation)
                        );

                    /*
                    |------------------------------------------------------------------
                    | TARGET TABLE
                    |------------------------------------------------------------------
                    */

                    if (
                        Schema::hasTable(
                            $relatedTable
                        )
                    ) {

                        $tableStatus = '✅';

                    } else {

                        $tableStatus = '❌';

                        $this->error++;
                    }

                    /*
                    |------------------------------------------------------------------
                    | FK
                    |------------------------------------------------------------------
                    */

                    $fkStatus = true;

                    if (
                        $relation instanceof BelongsTo
                    ) {

                        $foreignKey =
                            $relation->getForeignKeyName();

                        $fkStatus =
                            Schema::hasColumn(
                                $model->getTable(),
                                $foreignKey
                            );

                    } elseif (
                        $relation instanceof HasMany ||
                        $relation instanceof HasOne
                    ) {

                        $foreignKey =
                            $relation->getForeignKeyName();

                        $fkStatus =
                            Schema::hasColumn(
                                $relatedTable,
                                $foreignKey
                            );

                    } elseif (
                        $relation instanceof MorphMany
                    ) {

                        $idColumn =
                            $relation->getForeignKeyName();

                        $typeColumn =
                            $relation->getMorphType();

                        $fkStatus =
                            Schema::hasColumn(
                                $relatedTable,
                                $idColumn
                            ) &&
                            Schema::hasColumn(
                                $relatedTable,
                                $typeColumn
                            );
                    }

                    if (!$fkStatus) {
                        $this->error++;
                    }

                    /*
                    |------------------------------------------------------------------
                    | OUTPUT
                    |------------------------------------------------------------------
                    */

                    $status =
                        $fkStatus &&
                        $tableStatus === '✅'
                            ? '✅'
                            : '❌';

                    if ($status === '✅') {
                        $this->ok++;
                    }

                    $this->line(
                        "  ├─ {$name}() → " .
                        class_basename($relatedClass) .
                        " [{$type}] : {$status}"
                    );

                    if (
                        $status === '❌'
                    ) {

                        $this->line(
                            "  │    Table Target : {$tableStatus} {$relatedTable}"
                        );
                    }

                } catch (Throwable $e) {

                    continue;
                }
            }

        } catch (Throwable $e) {

            $this->warning++;

            $this->line(
                '  └─ Relationship : ⚠️ gagal diperiksa'
            );
        }

        return $count;
    }

    /*
    |--------------------------------------------------------------------------
    | CONTROLLER METHODS
    |--------------------------------------------------------------------------
    */

    protected function checkControllerMethods(
        string $controller,
        array $config
    ): void {

        try {

            $reflection =
                new ReflectionClass($controller);

            $methods = [
                'index',
                'create',
                'store',
                'show',
                'edit',
                'update',
                'destroy',
            ];

            foreach ($methods as $methodName) {

                if (
                    !$reflection->hasMethod(
                        $methodName
                    )
                ) {

                    $this->error++;

                    $this->line(
                        "  └─ {$methodName}() : ❌"
                    );

                    continue;
                }

                $method =
                    $reflection->getMethod(
                        $methodName
                    );

                if (
                    !$method->isPublic()
                ) {

                    $this->error++;

                    $this->line(
                        "  └─ {$methodName}() : ❌ bukan public"
                    );

                    continue;
                }

                $this->ok++;

                $this->line(
                    "  └─ {$methodName}() : ✅"
                );
            }

        } catch (Throwable $e) {

            $this->error++;

            $this->line(
                '  └─ Methods : ❌ gagal diperiksa'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ROUTES
    |--------------------------------------------------------------------------
    */

    protected function getModuleRoutes(
        string $routeNamePrefix
    ) {

        return collect(
            Route::getRoutes()
        )->filter(
            function ($route) use (
                $routeNamePrefix
            ) {

                $name =
                    $route->getName();

                return $name &&
                    str_starts_with(
                        $name,
                        $routeNamePrefix . '.'
                    );
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | VIEWS
    |--------------------------------------------------------------------------
    */

    protected function checkViews(
        string $routeNamePrefix
    ): void {

        $routes =
            $this->getModuleRoutes(
                $routeNamePrefix
            );

        $checked = [];

        foreach ($routes as $route) {

            $name =
                $route->getName();

            if (!$name) {
                continue;
            }

            $action =
                $route->getAction('uses');

            if (
                !is_string($action)
            ) {
                continue;
            }

            if (
                !str_contains(
                    $action,
                    '@'
                )
            ) {
                continue;
            }

            [
                $controller,
                $methodName
            ] = explode(
                '@',
                $action,
                2
            );

            if (
                !class_exists($controller)
            ) {
                continue;
            }

            try {

                $reflection =
                    new ReflectionClass(
                        $controller
                    );

                if (
                    !$reflection->hasMethod(
                        $methodName
                    )
                ) {
                    continue;
                }

                $method =
                    $reflection->getMethod(
                        $methodName
                    );

                $file =
                    $method->getFileName();

                if (
                    !$file ||
                    !File::exists($file)
                ) {
                    continue;
                }

                $source =
                    implode(
                        '',
                        array_slice(
                            file($file),
                            $method->getStartLine() - 1,
                            $method->getEndLine()
                                - $method->getStartLine()
                                + 1
                        )
                    );

                preg_match_all(
                    "/return\s+view\(\s*['\"]([^'\"]+)['\"]/",
                    $source,
                    $matches
                );

                foreach (
                    $matches[1] ?? []
                    as $view
                ) {

                    if (
                        isset(
                            $checked[$view]
                        )
                    ) {
                        continue;
                    }

                    $checked[$view] = true;

                    $viewPath =
                        resource_path(
                            'views/' .
                            str_replace(
                                '.',
                                '/',
                                $view
                            ) .
                            '.blade.php'
                        );

                    if (
                        File::exists(
                            $viewPath
                        )
                    ) {

                        $this->ok++;

                        $this->line(
                            "  ├─ {$view} : ✅"
                        );

                    } else {

                        $this->warning++;

                        $this->line(
                            "  ├─ {$view} : ⚠️ belum dibuat"
                        );
                    }
                }

            } catch (Throwable $e) {

                continue;
            }
        }

        if (
            empty($checked)
        ) {

            $this->line(
                '  └─ Tidak ada View yang terdeteksi'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MODULE STATUS
    |--------------------------------------------------------------------------
    */

    protected function moduleStatus(
        string $modelClass,
        array $config
    ): string {

        $model = new $modelClass();

        $table =
            $model->getTable();

        /*
        |------------------------------------------------------------------
        | MODEL
        |------------------------------------------------------------------
        */

        if (
            !class_exists($modelClass)
        ) {
            return '❌ MODEL';
        }

        /*
        |------------------------------------------------------------------
        | TABLE
        |------------------------------------------------------------------
        */

        if (
            !Schema::hasTable($table)
        ) {
            return '❌ TABLE';
        }

        /*
        |------------------------------------------------------------------
        | CONTROLLER
        |------------------------------------------------------------------
        */

        if (
            !class_exists(
                $config['controller']
            )
        ) {
            return '❌ CONTROLLER';
        }

        /*
        |------------------------------------------------------------------
        | ROUTE
        |------------------------------------------------------------------
        */

        if (
            $this->getModuleRoutes(
                $config['route_name']
            )->isEmpty()
        ) {
            return '❌ ROUTE';
        }

        /*
        |------------------------------------------------------------------
        | VIEW
        |------------------------------------------------------------------
        */

        $hasMissingView = false;

        foreach (
            $this->getModuleRoutes(
                $config['route_name']
            ) as $route
        ) {

            $action =
                $route->getAction('uses');

            if (
                !is_string($action) ||
                !str_contains(
                    $action,
                    '@'
                )
            ) {
                continue;
            }

            [
                $controller,
                $methodName
            ] = explode(
                '@',
                $action,
                2
            );

            if (
                !class_exists($controller)
            ) {
                continue;
            }

            try {

                $reflection =
                    new ReflectionClass(
                        $controller
                    );

                if (
                    !$reflection->hasMethod(
                        $methodName
                    )
                ) {
                    continue;
                }

                $method =
                    $reflection->getMethod(
                        $methodName
                    );

                $file =
                    $method->getFileName();

                if (
                    !$file ||
                    !File::exists($file)
                ) {
                    continue;
                }

                $source =
                    implode(
                        '',
                        array_slice(
                            file($file),
                            $method->getStartLine() - 1,
                            $method->getEndLine()
                                - $method->getStartLine()
                                + 1
                        )
                    );

                preg_match_all(
                    "/return\s+view\(\s*['\"]([^'\"]+)['\"]/",
                    $source,
                    $matches
                );

                foreach (
                    $matches[1] ?? []
                    as $view
                ) {

                    $viewPath =
                        resource_path(
                            'views/' .
                            str_replace(
                                '.',
                                '/',
                                $view
                            ) .
                            '.blade.php'
                        );

                    if (
                        !File::exists(
                            $viewPath
                        )
                    ) {

                        $hasMissingView = true;
                    }
                }

            } catch (Throwable $e) {
                continue;
            }
        }

        if ($hasMissingView) {
            return '⚠️ VIEW BELUM LENGKAP';
        }

        return '✅ LENGKAP';
    }

    /*
    |--------------------------------------------------------------------------
    | MIGRATION TABLE
    |--------------------------------------------------------------------------
    */

    protected function migrationExistsForTable(
        string $table
    ): bool {

        foreach (
            $this->migrationCache
            as $content
        ) {

            if (
                preg_match(
                    '/Schema::create\([\'"]' .
                    preg_quote(
                        $table,
                        '/'
                    ) .
                    '[\'"]/',
                    $content
                )
            ) {
                return true;
            }
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    protected function summary(): void
    {
        $this->line(
            '============================================================'
        );

        $this->info(
            'PROJECT CHECK SUMMARY'
        );

        $this->line(
            '============================================================'
        );

        $this->line(
            'Modules Checked : ' .
            count($this->modules)
        );

        $this->line(
            '✅ OK           : ' .
            $this->ok
        );

        $this->line(
            '⚠️ Warning      : ' .
            $this->warning
        );

        $this->line(
            '❌ Error        : ' .
            $this->error
        );

        $this->newLine();

        if (
            $this->error === 0 &&
            $this->warning === 0
        ) {

            $this->info(
                '🎉 PROJECT STRUCTURE PERFECTLY LINKED!'
            );

        } elseif (
            $this->error === 0
        ) {

            $this->warn(
                '⚠️ BACKEND TERHUBUNG. MASIH ADA WARNING.'
            );

        } else {

            $this->error(
                '❌ PROJECT BELUM LENGKAP.'
            );
        }

        $this->newLine();
    }
}