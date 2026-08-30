<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation as BaseRelation;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphedByMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use Throwable;

class ModelList extends Command
{
    protected $signature = 'model:list';

    protected $description = 'Mengecek Model, Table, Relationship, Foreign Key, dan Migration';

    protected int $ok = 0;
    protected int $warning = 0;
    protected int $error = 0;

    public function handle()
    {
        $this->newLine();
        $this->info('MODEL → TABLE → RELATIONSHIP → FOREIGN KEY → MIGRATION');
        $this->newLine();

        /*
        |--------------------------------------------------------------------------
        | 1. Ambil semua tabel dari database
        |--------------------------------------------------------------------------
        */
        $tables = $this->getDatabaseTables();

        /*
        |--------------------------------------------------------------------------
        | 2. Ambil semua migration yang tersedia
        |--------------------------------------------------------------------------
        */
        $migrationTables = $this->getMigrationTables();

        /*
        |--------------------------------------------------------------------------
        | 3. Ambil semua Model
        |--------------------------------------------------------------------------
        */
        $modelPath = app_path('Models');

        if (!File::exists($modelPath)) {
            $this->error('❌ Folder app/Models tidak ditemukan.');
            return self::FAILURE;
        }

        $files = File::files($modelPath);

        $totalModels = 0;
        $totalRelations = 0;

        foreach ($files as $file) {

            if ($file->getExtension() !== 'php') {
                continue;
            }

            $className = 'App\\Models\\' . $file->getFilenameWithoutExtension();

            if (!class_exists($className)) {
                continue;
            }

            try {
                $reflection = new ReflectionClass($className);

                if (
                    $reflection->isAbstract() ||
                    !$reflection->isSubclassOf(Model::class)
                ) {
                    continue;
                }

                $totalModels++;

                /** @var Model $model */
                $model = new $className();

                $table = $model->getTable();

                /*
                |--------------------------------------------------------------------------
                | MODEL → TABLE
                |--------------------------------------------------------------------------
                */

                if (in_array($table, $tables)) {
                    $tableStatus = '✅';
                    $this->ok++;
                } else {
                    $tableStatus = '❌';
                    $this->error++;
                }

                /*
                |--------------------------------------------------------------------------
                | MIGRATION
                |--------------------------------------------------------------------------
                */

                if (in_array($table, $migrationTables)) {
                    $migrationStatus = '✅';
                    $this->ok++;
                } else {
                    $migrationStatus = '⚠️';
                    $this->warning++;
                }

                $this->line(
                    "{$tableStatus} {$reflection->getShortName()} → {$table}"
                );

                $this->line(
                    "   Migration: {$migrationStatus}"
                );

                /*
                |--------------------------------------------------------------------------
                | RELATIONSHIPS
                |--------------------------------------------------------------------------
                */

                foreach ($reflection->getMethods() as $method) {

                    if (
                        $method->isStatic() ||
                        $method->isConstructor() ||
                        $method->getNumberOfParameters() > 0 ||
                        $method->getDeclaringClass()->getName() !== $className
                    ) {
                        continue;
                    }

                    try {

                        $result = $method->invoke($model);

                        if (!$result instanceof Relation) {
                            continue;
                        }

                        $totalRelations++;

                        $relationName = $method->getName();

                        $relationType = class_basename($result);

                        $relatedModel = $result->getRelated();

                        $relatedClass = get_class($relatedModel);

                        $relatedModelName = class_basename($relatedModel);

                        $relatedTable = $relatedModel->getTable();

                        /*
                        |--------------------------------------------------------------------------
                        | RELATED TABLE CHECK
                        |--------------------------------------------------------------------------
                        */

                        if (in_array($relatedTable, $tables)) {
                            $relatedTableStatus = '✅';
                            $this->ok++;
                        } else {
                            $relatedTableStatus = '❌';
                            $this->error++;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | FOREIGN KEY CHECK
                        |--------------------------------------------------------------------------
                        */

                        $foreignKey = null;
                        $foreignKeyExists = null;

                        if ($result instanceof BelongsTo) {

                            $foreignKey = $result->getForeignKeyName();

                            $foreignKeyExists = $this->columnExists(
                                $table,
                                $foreignKey
                            );

                        } elseif ($result instanceof HasOneOrMany) {

                            $foreignKey = $result->getForeignKeyName();

                            $foreignKeyExists = $this->columnExists(
                                $relatedTable,
                                $foreignKey
                            );

                        } elseif ($result instanceof BelongsToMany) {

                            $foreignKey = $result->getForeignPivotKeyName();

                            $pivotTable = $result->getTable();

                            $foreignKeyExists = $this->columnExists(
                                $pivotTable,
                                $foreignKey
                            );

                        } elseif ($result instanceof MorphOne) {

                            $foreignKey = $result->getForeignKeyName();

                            $foreignKeyExists = $this->columnExists(
                                $relatedTable,
                                $foreignKey
                            );

                        } elseif ($result instanceof MorphMany) {

                            $foreignKey = $result->getForeignKeyName();

                            $foreignKeyExists = $this->columnExists(
                                $relatedTable,
                                $foreignKey
                            );

                        } elseif ($result instanceof MorphTo) {

                            $foreignKey = $result->getForeignKeyName();

                            $foreignKeyExists = $this->columnExists(
                                $table,
                                $foreignKey
                            );

                        } elseif ($result instanceof MorphToMany) {

                            $foreignKey = $result->getForeignPivotKeyName();

                            $pivotTable = $result->getTable();

                            $foreignKeyExists = $this->columnExists(
                                $pivotTable,
                                $foreignKey
                            );

                        } elseif ($result instanceof MorphedByMany) {

                            $foreignKey = $result->getForeignPivotKeyName();

                            $pivotTable = $result->getTable();

                            $foreignKeyExists = $this->columnExists(
                                $pivotTable,
                                $foreignKey
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | STATUS RELATIONSHIP
                        |--------------------------------------------------------------------------
                        */

                        if ($foreignKeyExists === true) {

                            $relationStatus = '✅';
                            $this->ok++;

                        } elseif ($foreignKeyExists === false) {

                            $relationStatus = '❌';
                            $this->error++;

                        } else {

                            $relationStatus = '⚠️';
                            $this->warning++;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | OUTPUT
                        |--------------------------------------------------------------------------
                        */

                        $this->line(
                            "   {$relationStatus} {$relationName}() → {$relatedModelName} [{$relationType}]"
                        );

                        $this->line(
                            "      Table: {$relatedTableStatus} {$relatedTable}"
                        );

                        if ($foreignKey !== null) {

                            $fkTable = $table;

                            if (
                                $result instanceof HasOneOrMany ||
                                $result instanceof MorphOne ||
                                $result instanceof MorphMany
                            ) {
                                $fkTable = $relatedTable;
                            }

                            if (
                                $result instanceof BelongsToMany ||
                                $result instanceof MorphToMany ||
                                $result instanceof MorphedByMany
                            ) {
                                $fkTable = $result->getTable();
                            }

                            $this->line(
                                "      FK: {$relationStatus} {$foreignKey} on {$fkTable}"
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | MIGRATION CHECK RELATED TABLE
                        |--------------------------------------------------------------------------
                        */

                        if (in_array($relatedTable, $migrationTables)) {

                            $this->line(
                                "      Migration: ✅ {$relatedTable}"
                            );

                            $this->ok++;

                        } else {

                            $this->line(
                                "      Migration: ⚠️ {$relatedTable} tidak ditemukan di migration"
                            );

                            $this->warning++;
                        }

                    } catch (Throwable $e) {

                        /*
                        |--------------------------------------------------------------------------
                        | RELATIONSHIP ERROR
                        |--------------------------------------------------------------------------
                        */

                        $this->line(
                            "   ❌ {$method->getName()}() → Error membaca relationship"
                        );

                        $this->line(
                            "      {$e->getMessage()}"
                        );

                        $this->error++;
                    }
                }

                $this->newLine();

            } catch (Throwable $e) {

                $this->line(
                    "❌ {$file->getFilename()} → {$e->getMessage()}"
                );

                $this->error++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $this->info('========================================');
        $this->info('SUMMARY');
        $this->info('========================================');

        $this->line("Total Model       : {$totalModels}");
        $this->line("Total Relationship : {$totalRelations}");
        $this->line("✅ OK              : {$this->ok}");
        $this->line("⚠️ Warning         : {$this->warning}");
        $this->line("❌ Error           : {$this->error}");

        $this->newLine();

        if ($this->error === 0) {

            $this->info('🎉 Tidak ditemukan error pada Model / Relationship / Foreign Key.');

        } else {

            $this->error(
                '⚠️ Masih ada relationship atau foreign key yang perlu diperiksa.'
            );
        }

        return $this->error > 0
            ? self::FAILURE
            : self::SUCCESS;
    }

    /*
    |--------------------------------------------------------------------------
    | CEK KOLOM DATABASE
    |--------------------------------------------------------------------------
    */

    protected function columnExists(
        string $table,
        string $column
    ): bool {

        try {

            return DB::getSchemaBuilder()
                ->hasColumn($table, $column);

        } catch (Throwable $e) {

            return false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | AMBIL SEMUA TABLE DATABASE
    |--------------------------------------------------------------------------
    */

    protected function getDatabaseTables(): array
    {
        $connection = DB::getDriverName();

        try {

            if ($connection === 'mysql') {

                return collect(
                    DB::select('SHOW TABLES')
                )->map(function ($table) {

                    return array_values(
                        get_object_vars($table)
                    )[0];

                })->toArray();
            }

            if ($connection === 'sqlite') {

                return DB::table('sqlite_master')
                    ->where('type', 'table')
                    ->pluck('name')
                    ->toArray();
            }

            if ($connection === 'pgsql') {

                return DB::table('information_schema.tables')
                    ->where('table_schema', 'public')
                    ->pluck('table_name')
                    ->toArray();
            }

        } catch (Throwable $e) {

            return [];
        }

        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | AMBIL TABLE DARI FILE MIGRATION
    |--------------------------------------------------------------------------
    */

    protected function getMigrationTables(): array
    {
        $migrationPath = database_path('migrations');

        if (!File::exists($migrationPath)) {
            return [];
        }

        $tables = [];

        foreach (File::files($migrationPath) as $file) {

            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = File::get($file->getPathname());

            /*
            | create('table_name')
            */

            preg_match_all(
                "/Schema::create\(\s*['\"]([^'\"]+)['\"]/",
                $content,
                $matches
            );

            if (!empty($matches[1])) {

                foreach ($matches[1] as $table) {
                    $tables[] = $table;
                }
            }

            /*
            | createIfNotExists('table_name')
            */

            preg_match_all(
                "/Schema::createIfNotExists\(\s*['\"]([^'\"]+)['\"]/",
                $content,
                $matches
            );

            if (!empty($matches[1])) {

                foreach ($matches[1] as $table) {
                    $tables[] = $table;
                }
            }
        }

        return array_unique($tables);
    }
}