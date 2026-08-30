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
        'Check Migration, Model, Relationship, FK, Route, Controller, Method, Model, View dan Redirect';

    protected int $ok = 0;
    protected int $warning = 0;
    protected int $error = 0;

    protected array $migrationCache = [];

    protected array $models = [
        Certificate::class,
        Company::class,
        Intern::class,
        InternshipParticipant::class,
        InternshipPosition::class,
        InternshipProgram::class,
        InternshipProgramBanner::class,
        InternshipRegistration::class,
        User::class,
        Work::class,
        WorkMember::class,
        WorkPhoto::class,
    ];

    public function handle(): int
    {
        $this->ok = 0;
        $this->warning = 0;
        $this->error = 0;

        $this->loadMigrations();

        $this->header();

        $this->checkModels();

        $this->checkRoutes();

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
            'MIGRATION → MODEL → RELATIONSHIP → FK → ROUTE → CONTROLLER → VIEW'
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
    | MODEL CHECK
    |--------------------------------------------------------------------------
    */

    protected function checkModels(): void
    {
        $this->info(
            '[1] MODEL → TABLE → RELATIONSHIP → FK → MIGRATION'
        );

        $this->line(
            '------------------------------------------------------------'
        );

        foreach ($this->models as $modelClass) {
            $this->checkModel($modelClass);
        }

        $this->newLine();
    }

    protected function checkModel(string $modelClass): void
    {
        try {

            if (!class_exists($modelClass)) {

                $this->error++;

                $this->line(
                    '❌ ' .
                    class_basename($modelClass) .
                    ' — Model tidak ditemukan'
                );

                return;
            }

            /** @var Model $model */
            $model = new $modelClass();

            $modelName = class_basename($modelClass);

            $table = $model->getTable();

            /*
            |--------------------------------------------------------------------------
            | MODEL
            |--------------------------------------------------------------------------
            */

            $this->ok++;

            /*
            |--------------------------------------------------------------------------
            | TABLE
            |--------------------------------------------------------------------------
            */

            if (!Schema::hasTable($table)) {

                $this->error++;

                $this->line(
                    "❌ {$modelName} → {$table}"
                );

                $this->line(
                    "   Table: ❌ tidak ditemukan"
                );

                return;
            }

            $this->ok++;

            /*
            |--------------------------------------------------------------------------
            | MIGRATION
            |--------------------------------------------------------------------------
            */

            $migrationFound =
                $this->migrationExistsForTable($table);

            if ($migrationFound) {

                $this->ok++;

            } else {

                $this->warning++;
            }

            $this->line(
                "✅ {$modelName} → {$table}"
            );

            $this->line(
                '   Migration: ' .
                (
                    $migrationFound
                    ? '✅'
                    : '⚠️ File tidak ditemukan'
                )
            );

            /*
            |--------------------------------------------------------------------------
            | RELATIONSHIPS
            |--------------------------------------------------------------------------
            */

            $this->checkRelationships($model);

        } catch (Throwable $e) {

            $this->error++;

            $this->line(
                "❌ {$modelClass}"
            );

            $this->line(
                "   Error: {$e->getMessage()}"
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    protected function checkRelationships(Model $model): void
    {
        $reflection =
            new ReflectionClass($model);

        foreach (
            $reflection->getMethods(
                ReflectionMethod::IS_PUBLIC
            )
            as $method
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

                $relationName =
                    $method->getName();

                $relatedModel =
                    $relation->getRelated();

                $relatedClass =
                    get_class($relatedModel);

                $relatedTable =
                    $relatedModel->getTable();

                /*
                |--------------------------------------------------------------------------
                | RELATED MODEL
                |--------------------------------------------------------------------------
                */

                if (!class_exists($relatedClass)) {

                    $this->error++;

                    $this->line(
                        "   ❌ {$relationName}() → " .
                        class_basename($relatedClass) .
                        " (Model tidak ditemukan)"
                    );

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | RELATED TABLE
                |--------------------------------------------------------------------------
                */

                if (
                    Schema::hasTable($relatedTable)
                ) {

                    $this->ok++;

                    $tableStatus = '✅';

                } else {

                    $this->error++;

                    $tableStatus = '❌';
                }

                $type =
                    class_basename(
                        get_class($relation)
                    );

                $this->line(
                    "   {$tableStatus} {$relationName}() → " .
                    class_basename($relatedClass) .
                    " [{$type}]"
                );

                $this->line(
                    "      Table Target: {$tableStatus} {$relatedTable}"
                );

                /*
                |--------------------------------------------------------------------------
                | BELONGS TO
                |--------------------------------------------------------------------------
                */

                if ($relation instanceof BelongsTo) {

                    $this->checkForeignKey(
                        $model->getTable(),
                        $relation->getForeignKeyName()
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | HAS MANY / HAS ONE
                |--------------------------------------------------------------------------
                */

                elseif (
                    $relation instanceof HasMany ||
                    $relation instanceof HasOne
                ) {

                    $this->checkForeignKey(
                        $relatedTable,
                        $relation->getForeignKeyName()
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | BELONGS TO MANY
                |--------------------------------------------------------------------------
                */

                elseif (
                    $relation instanceof BelongsToMany
                ) {

                    $pivotTable =
                        $relation->getTable();

                    if (
                        Schema::hasTable($pivotTable)
                    ) {

                        $this->ok++;

                        $this->line(
                            "      Pivot Table: ✅ {$pivotTable}"
                        );

                    } else {

                        $this->error++;

                        $this->line(
                            "      Pivot Table: ❌ {$pivotTable}"
                        );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | MORPH MANY
                |--------------------------------------------------------------------------
                */

                elseif (
                    $relation instanceof MorphMany
                ) {

                    $this->checkMorphKeys(
                        $relatedTable,
                        $relation->getForeignKeyName(),
                        $relation->getMorphType()
                    );
                }

            } catch (Throwable $e) {

                continue;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FOREIGN KEY
    |--------------------------------------------------------------------------
    */

    protected function checkForeignKey(
        string $table,
        string $foreignKey
    ): void {

        if (
            Schema::hasColumn(
                $table,
                $foreignKey
            )
        ) {

            $this->ok++;

            $this->line(
                "      FK Column: ✅ {$foreignKey}"
            );

        } else {

            $this->error++;

            $this->line(
                "      FK Column: ❌ {$foreignKey}"
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | MIGRATION FK
        |--------------------------------------------------------------------------
        */

        if (
            $this->migrationContainsColumn(
                $table,
                $foreignKey
            )
        ) {

            $this->ok++;

            $this->line(
                "      Migration FK: ✅ {$foreignKey}"
            );

        } else {

            $this->warning++;

            $this->line(
                "      Migration FK: ⚠️ {$foreignKey} tidak terdeteksi"
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MORPH
    |--------------------------------------------------------------------------
    */

    protected function checkMorphKeys(
        string $table,
        string $idColumn,
        string $typeColumn
    ): void {

        $idExists =
            Schema::hasColumn(
                $table,
                $idColumn
            );

        $typeExists =
            Schema::hasColumn(
                $table,
                $typeColumn
            );

        if (
            $idExists &&
            $typeExists
        ) {

            $this->ok += 2;

            $this->line(
                "      Morph FK: ✅ {$idColumn}"
            );

            $this->line(
                "      Morph Type: ✅ {$typeColumn}"
            );

        } else {

            $this->error++;

            $this->line(
                "      Morph Columns: ❌"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MIGRATION MORPH
        |--------------------------------------------------------------------------
        */

        $morphName =
            str_replace(
                '_id',
                '',
                $idColumn
            );

        $migrationFound = false;

        foreach (
            $this->migrationCache
            as $content
        ) {

            if (
                !preg_match(
                    '/Schema::create\([\'"]' .
                    preg_quote($table, '/') .
                    '[\'"]/',
                    $content
                )
            ) {
                continue;
            }

            if (
                preg_match(
                    '/->(?:morphs|nullableMorphs)\([\'"]' .
                    preg_quote($morphName, '/') .
                    '[\'"]\)/',
                    $content
                )
            ) {

                $migrationFound = true;

                break;
            }
        }

        if ($migrationFound) {

            $this->ok++;

            $this->line(
                "      Migration Morph: ✅ {$morphName}"
            );

        } else {

            $this->warning++;

            $this->line(
                "      Migration Morph: ⚠️ {$morphName} tidak terdeteksi"
            );
        }
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
                    preg_quote($table, '/') .
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
    | MIGRATION COLUMN
    |--------------------------------------------------------------------------
    */

    protected function migrationContainsColumn(
        string $table,
        string $column
    ): bool {

        foreach (
            $this->migrationCache
            as $content
        ) {

            if (
                !preg_match(
                    '/Schema::create\([\'"]' .
                    preg_quote($table, '/') .
                    '[\'"]/',
                    $content
                )
            ) {
                continue;
            }

            /*
            |----------------------------------------------------------------------
            | foreignId()
            |----------------------------------------------------------------------
            */

            if (
                preg_match(
                    '/->foreignId\([\'"]' .
                    preg_quote($column, '/') .
                    '[\'"]\)/',
                    $content
                )
            ) {
                return true;
            }

            /*
            |----------------------------------------------------------------------
            | foreignIdFor()
            |----------------------------------------------------------------------
            */

            if (
                preg_match(
                    '/->foreignIdFor\(/',
                    $content
                ) &&
                str_contains(
                    $content,
                    $column
                )
            ) {
                return true;
            }

            /*
            |----------------------------------------------------------------------
            | unsignedBigInteger()
            |----------------------------------------------------------------------
            */

            if (
                preg_match(
                    '/->(?:unsignedBigInteger|bigInteger|integer|uuid|string)\([\'"]' .
                    preg_quote($column, '/') .
                    '[\'"]\)/',
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
    | ROUTES
    |--------------------------------------------------------------------------
    */

    protected function checkRoutes(): void
    {
        $this->info(
            '[2] ROOT ROUTE → CONTROLLER → METHOD → MODEL → VIEW'
        );

        $this->line(
            '------------------------------------------------------------'
        );

        $routes =
            collect(Route::getRoutes())
                ->filter(
                    function ($route) {

                        return str_starts_with(
                            ltrim(
                                $route->uri(),
                                '/'
                            ),
                            'root'
                        );
                    }
                );

        if ($routes->isEmpty()) {

            $this->warning++;

            $this->warn(
                '⚠️ Tidak ditemukan Root Route.'
            );

            $this->newLine();

            return;
        }

        foreach ($routes as $route) {

            $this->checkRoute($route);
        }

        $this->newLine();
    }

    /*
    |--------------------------------------------------------------------------
    | SINGLE ROUTE
    |--------------------------------------------------------------------------
    */

    protected function checkRoute($route): void
    {
        $methods =
            implode(
                '|',
                array_diff(
                    $route->methods(),
                    ['HEAD']
                )
            );

        $uri =
            '/' .
            ltrim(
                $route->uri(),
                '/'
            );

        $uses =
            $route->getAction('uses');

        $routeName =
            $route->getName();

        $this->line(
            "Route: {$methods} {$uri}"
        );

        if ($routeName) {

            $this->line(
                "   Name:       {$routeName}"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CLOSURE
        |--------------------------------------------------------------------------
        */

        if ($uses instanceof Closure) {

            $this->warning++;

            $this->line(
                '   Controller: ⚠️ Closure'
            );

            $this->newLine();

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CONTROLLER ACTION
        |--------------------------------------------------------------------------
        */

        if (!is_string($uses)) {

            $this->error++;

            $this->line(
                '   Controller: ❌ Action tidak valid'
            );

            $this->newLine();

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | INVOKABLE
        |--------------------------------------------------------------------------
        */

        if (!str_contains($uses, '@')) {

            $controllerClass = $uses;

            $method = '__invoke';

        } else {

            [
                $controllerClass,
                $method
            ] = explode(
                '@',
                $uses,
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CONTROLLER
        |--------------------------------------------------------------------------
        */

        if (!class_exists($controllerClass)) {

            $this->error++;

            $this->line(
                "   Controller: ❌ {$controllerClass}"
            );

            $this->newLine();

            return;
        }

        $this->ok++;

        $this->line(
            "   Controller: ✅ {$controllerClass}"
        );

        /*
        |--------------------------------------------------------------------------
        | METHOD
        |--------------------------------------------------------------------------
        */

        try {

            $reflection =
                new ReflectionClass(
                    $controllerClass
                );

            if (
                !$reflection->hasMethod(
                    $method
                )
            ) {

                $this->error++;

                $this->line(
                    "   Method:     ❌ {$method}()"
                );

                $this->newLine();

                return;
            }

            $reflectionMethod =
                $reflection->getMethod(
                    $method
                );

            if (
                !$reflectionMethod->isPublic()
            ) {

                $this->error++;

                $this->line(
                    "   Method:     ❌ {$method}() bukan public"
                );

                $this->newLine();

                return;
            }

            $this->ok++;

            $this->line(
                "   Method:     ✅ {$method}()"
            );

            /*
            |--------------------------------------------------------------------------
            | MODEL
            |--------------------------------------------------------------------------
            */

            $this->checkControllerModels(
                $reflectionMethod
            );

            /*
            |--------------------------------------------------------------------------
            | VIEW / REDIRECT
            |--------------------------------------------------------------------------
            */

            $this->checkControllerResponse(
                $reflectionMethod
            );

        } catch (Throwable $e) {

            $this->error++;

            $this->line(
                "   Method:     ❌ {$method}()"
            );

            $this->line(
                "      Error: {$e->getMessage()}"
            );
        }

        $this->newLine();
    }

    /*
    |--------------------------------------------------------------------------
    | CONTROLLER MODEL CHECK
    |--------------------------------------------------------------------------
    */

    protected function checkControllerModels(
        ReflectionMethod $method
    ): void {

        $foundModels = [];

        /*
        |--------------------------------------------------------------------------
        | ROUTE MODEL BINDING
        |--------------------------------------------------------------------------
        */

        foreach (
            $method->getParameters()
            as $param
        ) {

            $type =
                $param->getType();

            if (
                $type &&
                !$type->isBuiltin()
            ) {

                $className =
                    $type->getName();

                if (
                    in_array(
                        $className,
                        $this->models
                    )
                ) {

                    $foundModels[] =
                        class_basename(
                            $className
                        );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SOURCE CODE
        |--------------------------------------------------------------------------
        */

        $file =
            $method->getFileName();

        if (
            $file &&
            File::exists($file)
        ) {

            $start =
                $method->getStartLine() - 1;

            $length =
                $method->getEndLine() -
                $start;

            $lines =
                array_slice(
                    file($file),
                    $start,
                    $length
                );

            $sourceCode =
                implode(
                    '',
                    $lines
                );

            foreach (
                $this->models
                as $modelClass
            ) {

                $shortName =
                    class_basename(
                        $modelClass
                    );

                if (
                    preg_match(
                        '/\b' .
                        preg_quote(
                            $shortName,
                            '/'
                        ) .
                        '::/',
                        $sourceCode
                    ) ||
                    preg_match(
                        '/\bnew\s+' .
                        preg_quote(
                            $shortName,
                            '/'
                        ) .
                        '\b/',
                        $sourceCode
                    )
                ) {

                    $foundModels[] =
                        $shortName;
                }
            }
        }

        $foundModels =
            array_unique(
                $foundModels
            );

        /*
        |--------------------------------------------------------------------------
        | OUTPUT
        |--------------------------------------------------------------------------
        */

        if (
            empty($foundModels)
        ) {

            $this->line(
                '   Model:      ➖ Tidak menggunakan model'
            );

            return;
        }

        foreach (
            $foundModels
            as $modelName
        ) {

            $this->ok++;

            $this->line(
                "   Model:      ✅ {$modelName}"
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CONTROLLER RESPONSE CHECK
    |--------------------------------------------------------------------------
    */

    protected function checkControllerResponse(
        ReflectionMethod $method
    ): void {

        $file =
            $method->getFileName();

        if (
            !$file ||
            !File::exists($file)
        ) {

            $this->warning++;

            $this->line(
                '   Response:   ⚠️ Source tidak dapat dibaca'
            );

            return;
        }

        $start =
            $method->getStartLine() - 1;

        $length =
            $method->getEndLine() -
            $start;

        $lines =
            array_slice(
                file($file),
                $start,
                $length
            );

        $source =
            implode(
                '',
                $lines
            );

        $foundResponse =
            false;

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        preg_match_all(
            "/return\s+view\(\s*['\"]([^'\"]+)['\"]/",
            $source,
            $viewMatches
        );

        foreach (
            $viewMatches[1] ?? []
            as $viewName
        ) {

            $foundResponse = true;

            $this->checkView(
                $viewName
            );
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN REDIRECT ROUTE
        |--------------------------------------------------------------------------
        */

        preg_match_all(
            "/redirect\(\)\s*->\s*route\(\s*['\"]([^'\"]+)['\"]/",
            $source,
            $redirectMatches
        );

        foreach (
            $redirectMatches[1] ?? []
            as $routeName
        ) {

            $foundResponse = true;

            $this->checkRedirectRoute(
                $routeName
            );
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN REDIRECT BACK
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $source,
                'redirect()->back()'
            )
        ) {

            $foundResponse = true;

            $this->ok++;

            $this->line(
                '   Redirect:   ✅ back()'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TIDAK ADA VIEW / REDIRECT
        |--------------------------------------------------------------------------
        */

        if (!$foundResponse) {

            $this->line(
                '   Response:   ➖ Tidak ada return view/redirect yang terdeteksi'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW CHECK
    |--------------------------------------------------------------------------
    */

    protected function checkView(
        string $viewName
    ): void {

        $viewPath =
            resource_path(
                'views/' .
                str_replace(
                    '.',
                    '/',
                    $viewName
                ) .
                '.blade.php'
            );

        if (
            File::exists($viewPath)
        ) {

            $this->ok++;

            $relativePath =
                'resources/views/' .
                str_replace(
                    '.',
                    '/',
                    $viewName
                ) .
                '.blade.php';

            $this->line(
                "   View:       ✅ {$viewName}"
            );

            $this->line(
                "   File:       ✅ {$relativePath}"
            );

        } else {

            /*
            |----------------------------------------------------------------------
            | View belum dibuat = WARNING
            |---------------------------------------------------------------------- 
            */

            $this->warning++;

            $relativePath =
                'resources/views/' .
                str_replace(
                    '.',
                    '/',
                    $viewName
                ) .
                '.blade.php';

            $this->line(
                "   View:       ⚠️ {$viewName}"
            );

            $this->line(
                "   File:       ⚠️ {$relativePath} belum ditemukan"
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REDIRECT ROUTE CHECK
    |--------------------------------------------------------------------------
    */

    protected function checkRedirectRoute(
        string $routeName
    ): void {

        $route =
            Route::getRoutes()
                ->getByName(
                    $routeName
                );

        if ($route) {

            $this->ok++;

            $this->line(
                "   Redirect:   ✅ route('{$routeName}')"
            );

            $this->line(
                "      Target:  /{$route->uri()}"
            );

        } else {

            $this->error++;

            $this->line(
                "   Redirect:   ❌ route('{$routeName}') tidak ditemukan"
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    protected function summary(): void
    {
        $this->info(
            '============================================================'
        );

        $this->info(
            'PROJECT CHECK SUMMARY'
        );

        $this->info(
            '============================================================'
        );

        $rootRoutesCount =
            collect(Route::getRoutes())
                ->filter(
                    fn ($route) =>
                    str_starts_with(
                        ltrim(
                            $route->uri(),
                            '/'
                        ),
                        'root'
                    )
                )
                ->count();

        $this->line(
            'Model Terdaftar  : ' .
            count($this->models)
        );

        $this->line(
            'Root Routes      : ' .
            $rootRoutesCount
        );

        $this->line(
            '✅ OK             : ' .
            $this->ok
        );

        $this->line(
            '⚠️ Warning        : ' .
            $this->warning
        );

        $this->line(
            '❌ Error          : ' .
            $this->error
        );

        $this->newLine();

        /*
        |--------------------------------------------------------------------------
        | FINAL STATUS
        |--------------------------------------------------------------------------
        */

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
                '⚠️ BACKEND TERHUBUNG. WARNING HANYA PADA BAGIAN YANG BELUM LENGKAP.'
            );

        } else {

            $this->error(
                '❌ DITEMUKAN ERROR PADA STRUKTUR PROJECT.'
            );
        }

        $this->newLine();
    }
}