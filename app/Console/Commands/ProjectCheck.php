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

    protected $description = 'Check Model, Table, Relationship, Foreign Key, Migration, Route, Controller, Method, dan Model Controller';

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

        // Cache migration files untuk optimasi performa (menghindari baca file berulang-ulang)
        $this->loadMigrations();

        $this->header();
        $this->checkModels();
        $this->checkRoutes();
        $this->summary();

        return $this->error > 0 ? self::FAILURE : self::SUCCESS;
    }

    protected function loadMigrations(): void
    {
        $migrationPath = database_path('migrations');
        if (File::isDirectory($migrationPath)) {
            foreach (File::allFiles($migrationPath) as $file) {
                $this->migrationCache[$file->getFilename()] = File::get($file->getPathname());
            }
        }
    }

    protected function header(): void
    {
        $this->newLine();
        $this->info('============================================================');
        $this->info('YUHLEZ MAGANG — PROJECT CHECK');
        $this->info('MODEL → TABLE → RELATIONSHIP → FK → MIGRATION → ROUTE → CONTROLLER');
        $this->info('============================================================');
        $this->newLine();
    }

    protected function checkModels(): void
    {
        $this->info('[1] MODEL → TABLE → RELATIONSHIP → FOREIGN KEY → MIGRATION');
        $this->line('------------------------------------------------------------');

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
                $this->line('❌ ' . class_basename($modelClass) . ' — Model tidak ditemukan');
                return;
            }

            /** @var Model $model */
            $model = new $modelClass();
            $modelName = class_basename($modelClass);
            $table = $model->getTable();

            $this->ok++; // Model exists

            if (!Schema::hasTable($table)) {
                $this->error++;
                $this->line("❌ {$modelName} → {$table}");
                $this->line("   Table: ❌ tidak ditemukan di database");
                return;
            }

            $this->ok++; // Table exists

            $migrationFound = $this->migrationExistsForTable($table);
            if ($migrationFound) {
                $this->ok++;
            } else {
                $this->warning++;
            }

            $this->line("✅ {$modelName} → {$table}");
            $this->line('   Migration: ' . ($migrationFound ? '✅' : '⚠️ (File tidak ditemukan)'));

            $this->checkRelationships($model);

        } catch (Throwable $e) {
            $this->error++;
            $this->line("❌ {$modelClass}");
            $this->line("   Error: {$e->getMessage()}");
        }
    }

    protected function checkRelationships(Model $model): void
    {
        $reflection = new ReflectionClass($model);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->class !== get_class($model) || $method->getNumberOfParameters() > 0) {
                continue;
            }

            try {
                $relation = $method->invoke($model);

                if (
                    !$relation instanceof BelongsTo &&
                    !$relation instanceof HasMany &&
                    !$relation instanceof HasOne &&
                    !$relation instanceof MorphMany &&
                    !$relation instanceof BelongsToMany
                ) {
                    continue;
                }

                $relationName = $method->getName();
                $relatedModel = $relation->getRelated();
                $relatedClass = get_class($relatedModel);
                $relatedTable = $relatedModel->getTable();

                if (!class_exists($relatedClass)) {
                    $this->error++;
                    $this->line("   ❌ {$relationName}() → " . class_basename($relatedClass) . " (Model tidak ditemukan)");
                    continue;
                }

                $tableStatus = Schema::hasTable($relatedTable) ? '✅' : '❌';
                if ($tableStatus === '✅') $this->ok++; else $this->error++;

                $type = class_basename(get_class($relation));
                $this->line("   {$tableStatus} {$relationName}() → " . class_basename($relatedClass) . " [{$type}]");
                $this->line("       Table Target: {$tableStatus} {$relatedTable}");

                // Pengecekan FK berdasarkan jenis relasi
                if ($relation instanceof BelongsTo) {
                    $this->checkForeignKey($model->getTable(), $relation->getForeignKeyName());
                } elseif ($relation instanceof HasMany || $relation instanceof HasOne) {
                    $this->checkForeignKey($relatedTable, $relation->getForeignKeyName());
                } elseif ($relation instanceof BelongsToMany) {
                    $pivotTable = $relation->getTable();
                    $pivotStatus = Schema::hasTable($pivotTable) ? '✅' : '❌';
                    if ($pivotStatus === '✅') $this->ok++; else $this->error++;
                    $this->line("       Pivot Table: {$pivotStatus} {$pivotTable}");
                } elseif ($relation instanceof MorphMany) {
                    $this->checkMorphKeys($relatedTable, $relation->getForeignKeyName(), $relation->getMorphType());
                }

            } catch (Throwable $e) {
                continue; // Method bukan relasi atau gagal dieksekusi
            }
        }
    }

    protected function checkForeignKey(string $table, string $foreignKey): void
    {
        $exists = Schema::hasColumn($table, $foreignKey);
        if ($exists) {
            $this->ok++;
            $this->line("       FK Column: ✅ {$foreignKey}");
        } else {
            $this->error++;
            $this->line("       FK Column: ❌ {$foreignKey}");
        }

        $migrationFound = $this->migrationContainsColumn($table, $foreignKey);
        if ($migrationFound) {
            $this->ok++;
            $this->line("       Migration FK: ✅");
        } else {
            $this->warning++;
            $this->line("       Migration FK: ⚠️ (Tidak terdeteksi di file migration)");
        }
    }

    protected function checkMorphKeys(string $table, string $idColumn, string $typeColumn): void
    {
        $idExists = Schema::hasColumn($table, $idColumn);
        $typeExists = Schema::hasColumn($table, $typeColumn);

        if ($idExists && $typeExists) {
            $this->ok += 2;
            $this->line("       Morph Columns: ✅ {$idColumn} & {$typeColumn}");
        } else {
            $this->error++;
            $this->line("       Morph Columns: ❌ Missing in database");
        }

        if ($this->migrationContainsColumn($table, $idColumn)) { // Biasanya terdeteksi via morphs()
            $this->ok++;
            $this->line("       Migration Morph: ✅");
        } else {
            $this->warning++;
            $this->line("       Migration Morph: ⚠️");
        }
    }

    protected function migrationExistsForTable(string $table): bool
    {
        foreach ($this->migrationCache as $content) {
            if (preg_match('/Schema::create\([\'"]' . $table . '[\'"]/', $content)) {
                return true;
            }
        }
        return false;
    }

    protected function migrationContainsColumn(string $table, string $column): bool
    {
        // Ubah _id menjadi nama morph (misal: notifiable_id -> notifiable)
        $morphName = str_replace('_id', '', $column);

        foreach ($this->migrationCache as $content) {
            if (!preg_match('/Schema::create\([\'"]' . $table . '[\'"]/', $content)) {
                continue;
            }

            if (
                preg_match('/->(?:foreignId|foreignIdFor|string|text|uuid|unsignedBigInteger|bigInteger|integer)\([\'"]' . preg_quote($column, '/') . '[\'"]\)/', $content) ||
                preg_match('/->(?:morphs|nullableMorphs)\([\'"]' . preg_quote($morphName, '/') . '[\'"]\)/', $content)
            ) {
                return true;
            }
        }
        return false;
    }

    protected function checkRoutes(): void
    {
        $this->info('[2] ROOT ROUTE → CONTROLLER → METHOD → MODEL');
        $this->line('------------------------------------------------------------');

        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return str_starts_with(ltrim($route->uri(), '/'), 'root');
        });

        if ($routes->isEmpty()) {
            $this->warning++;
            $this->warn('⚠️ Tidak ditemukan Root Route (prefix "root").');
            $this->newLine();
            return;
        }

        foreach ($routes as $route) {
            $this->checkRoute($route);
        }
        $this->newLine();
    }

    protected function checkRoute($route): void
    {
        $methods = implode('|', array_diff($route->methods(), ['HEAD']));
        $uri = '/' . ltrim($route->uri(), '/');
        $uses = $route->getAction('uses');

        $this->line("Route: {$methods} {$uri}");

        if ($uses instanceof Closure) {
            $this->line('   Controller: ⚠️ Closure (Melewatkan pengecekan controller)');
            $this->warning++;
            $this->newLine();
            return;
        }

        if (!is_string($uses)) {
            $this->line('   Controller: ❌ Action tidak valid');
            $this->error++;
            $this->newLine();
            return;
        }

        // Handle Invokable Controllers
        if (!str_contains($uses, '@')) {
            $controllerClass = $uses;
            $method = '__invoke';
        } else {
            [$controllerClass, $method] = explode('@', $uses, 2);
        }

        if (!class_exists($controllerClass)) {
            $this->line("   Controller: ❌ {$controllerClass} (Tidak ditemukan)");
            $this->error++;
            $this->newLine();
            return;
        }

        $this->line("   Controller: ✅ {$controllerClass}");
        $this->ok++;

        try {
            $reflection = new ReflectionClass($controllerClass);
            if (!$reflection->hasMethod($method)) {
                $this->line("   Method:     ❌ {$method}() tidak ditemukan");
                $this->error++;
                $this->newLine();
                return;
            }

            $reflectionMethod = $reflection->getMethod($method);
            if (!$reflectionMethod->isPublic()) {
                $this->line("   Method:     ❌ {$method}() bukan public");
                $this->error++;
                $this->newLine();
                return;
            }

            $this->line("   Method:     ✅ {$method}()");
            $this->ok++;

            $this->checkControllerModels($reflectionMethod);

        } catch (Throwable $e) {
            $this->line("   Method:     ❌ {$method}() - Error: {$e->getMessage()}");
            $this->error++;
        }
        $this->newLine();
    }

    protected function checkControllerModels(ReflectionMethod $method): void
    {
        $foundModels = [];

        // 1. Cek dari Route Model Binding (Parameters)
        foreach ($method->getParameters() as $param) {
            $type = $param->getType();
            if ($type && !$type->isBuiltin()) {
                $className = $type->getName();
                if (in_array($className, $this->models)) {
                    $foundModels[] = class_basename($className);
                }
            }
        }

        // 2. Cek dari Source Code method (Query Builder / Instansiasi)
        $file = $method->getFileName();
        if ($file && File::exists($file)) {
            $start = $method->getStartLine() - 1;
            $length = $method->getEndLine() - $start;
            $lines = array_slice(file($file), $start, $length);
            $sourceCode = implode('', $lines);

            foreach ($this->models as $modelClass) {
                $shortName = class_basename($modelClass);
                // Regex mencari pemanggilan statis (Model::) atau instansiasi (new Model)
                if (preg_match('/\b' . preg_quote($shortName, '/') . '::/', $sourceCode) || 
                    preg_match('/\bnew\s+' . preg_quote($shortName, '/') . '\b/', $sourceCode)) {
                    $foundModels[] = $shortName;
                }
            }
        }

        $foundModels = array_unique($foundModels);

        if (empty($foundModels)) {
            $this->line('   Model:      ➖ Tidak terdeteksi menggunakan model');
            return;
        }

        foreach ($foundModels as $modelName) {
            $this->line("   Model:      ✅ {$modelName}");
            $this->ok++;
        }
    }

    protected function summary(): void
    {
        $this->info('============================================================');
        $this->info('SUMMARY');
        $this->info('============================================================');

        $rootRoutesCount = collect(Route::getRoutes())->filter(
            fn ($route) => str_starts_with(ltrim($route->uri(), '/'), 'root')
        )->count();

        $this->line('Model Terdaftar  : ' . count($this->models));
        $this->line('Root Routes      : ' . $rootRoutesCount);
        $this->line('✅ OK            : ' . $this->ok);
        $this->line('⚠️ Warning       : ' . $this->warning);
        $this->line('❌ Error         : ' . $this->error);
        $this->newLine();

        if ($this->error === 0 && $this->warning === 0) {
            $this->info('🎉 BACKEND STRUCTURE PERFECTLY LINKED!');
        } elseif ($this->error === 0) {
            $this->warn('⚠️ Struktur OK, namun ada beberapa warning (Biasanya di struktur Migration).');
        } else {
            $this->error('❌ Ditemukan ERROR (Table/Class/Method tidak sinkron)! Silahkan perbaiki struktur backend.');
        }
        $this->newLine();
    }
}