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
        'Check Model, Migration, Table, Relationship, Controller, Route, Role Access, Method dan View';

    protected int $ok = 0;
    protected int $warning = 0;
    protected int $error = 0;

    protected array $migrationCache = [];

    /*
    |--------------------------------------------------------------------------
    | MODULE CONFIGURATION
    |--------------------------------------------------------------------------
    |
    | controllers:
    | Controller yang dimiliki module/model.
    |
    | route_prefixes:
    | Prefix nama route yang dimiliki module.
    |
    | route_controllers:
    | Mapping controller untuk route utama.
    |
    | nested_route_controllers:
    | Mapping controller untuk nested resource.
    |
    */

    protected array $modules = [

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        User::class => [

            'controllers' => [
                \App\Http\Controllers\Root\UserController::class,
            ],

            'route_prefixes' => [
                'root.users',
            ],

            'route_controllers' => [

                'root.users' => [
                    \App\Http\Controllers\Root\UserController::class,
                ],
            ],

            'roles' => [
                'root',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | COMPANY
        |--------------------------------------------------------------------------
        */

        Company::class => [

            'controllers' => [
                \App\Http\Controllers\Root\CompanyController::class,
                \App\Http\Controllers\Company\CompanyController::class,
            ],

            'route_prefixes' => [
                'root.companies',
                'company.profile',
            ],

            'route_controllers' => [

                'root.companies' => [
                    \App\Http\Controllers\Root\CompanyController::class,
                ],

                'company.profile' => [
                    \App\Http\Controllers\Company\CompanyController::class,
                ],
            ],

            'roles' => [
                'root',
                'company',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | INTERN
        |--------------------------------------------------------------------------
        */

        Intern::class => [

            'controllers' => [
                \App\Http\Controllers\Root\InternController::class,
            ],

            'route_prefixes' => [
                'root.interns',
            ],

            'route_controllers' => [

                'root.interns' => [
                    \App\Http\Controllers\Root\InternController::class,
                ],
            ],

            'roles' => [
                'root',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | INTERNSHIP PROGRAM
        |--------------------------------------------------------------------------
        */

        InternshipProgram::class => [

            'controllers' => [
                \App\Http\Controllers\Root\InternshipProgramController::class,
                \App\Http\Controllers\Company\InternshipProgramController::class,
            ],

            'route_prefixes' => [
                'root.internship-programs',
                'company.internship-programs',
            ],

            'route_controllers' => [

                'root.internship-programs' => [
                    \App\Http\Controllers\Root\InternshipProgramController::class,
                ],

                'company.internship-programs' => [
                    \App\Http\Controllers\Company\InternshipProgramController::class,
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | NESTED ROUTE CONTROLLERS
            |--------------------------------------------------------------------------
            |
            | Route nested dari InternshipProgram mempunyai controller
            | masing-masing.
            |
            */

            'nested_route_controllers' => [

                'company.internship-programs.banners' => [
                    \App\Http\Controllers\Company\InternshipProgramBannerController::class,
                ],

                'company.internship-programs.positions' => [
                    \App\Http\Controllers\Company\InternshipPositionController::class,
                ],

                'company.internship-programs.registrations' => [
                    \App\Http\Controllers\Company\InternshipRegistrationController::class,
                ],

                'company.internship-programs.participants' => [
                    \App\Http\Controllers\Company\InternshipParticipantController::class,
                ],
            ],

            'roles' => [
                'root',
                'company',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | INTERNSHIP PROGRAM BANNER
        |--------------------------------------------------------------------------
        */

        InternshipProgramBanner::class => [

            'controllers' => [
                \App\Http\Controllers\Root\InternshipProgramBannerController::class,
                \App\Http\Controllers\Company\InternshipProgramBannerController::class,
            ],

            'route_prefixes' => [
                'root.internship-program-banners',
                'company.internship-programs.banners',
            ],

            'route_controllers' => [

                'root.internship-program-banners' => [
                    \App\Http\Controllers\Root\InternshipProgramBannerController::class,
                ],

                'company.internship-programs.banners' => [
                    \App\Http\Controllers\Company\InternshipProgramBannerController::class,
                ],
            ],

            'roles' => [
                'root',
                'company',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | INTERNSHIP POSITION
        |--------------------------------------------------------------------------
        */

        InternshipPosition::class => [

            'controllers' => [
                \App\Http\Controllers\Root\InternshipPositionController::class,
                \App\Http\Controllers\Company\InternshipPositionController::class,
            ],

            'route_prefixes' => [
                'root.internship-positions',
                'company.internship-programs.positions',
            ],

            'route_controllers' => [

                'root.internship-positions' => [
                    \App\Http\Controllers\Root\InternshipPositionController::class,
                ],

                'company.internship-programs.positions' => [
                    \App\Http\Controllers\Company\InternshipPositionController::class,
                ],
            ],

            'roles' => [
                'root',
                'company',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | INTERNSHIP REGISTRATION
        |--------------------------------------------------------------------------
        */

        InternshipRegistration::class => [

            'controllers' => [
                \App\Http\Controllers\Root\InternshipRegistrationController::class,
                \App\Http\Controllers\Company\InternshipRegistrationController::class,
            ],

            'route_prefixes' => [
                'root.internship-registrations',
                'company.internship-programs.registrations',
            ],

            'route_controllers' => [

                'root.internship-registrations' => [
                    \App\Http\Controllers\Root\InternshipRegistrationController::class,
                ],

                'company.internship-programs.registrations' => [
                    \App\Http\Controllers\Company\InternshipRegistrationController::class,
                ],
            ],

            'roles' => [
                'root',
                'company',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | INTERNSHIP PARTICIPANT
        |--------------------------------------------------------------------------
        */

        InternshipParticipant::class => [

            'controllers' => [
                \App\Http\Controllers\Root\InternshipParticipantController::class,
                \App\Http\Controllers\Company\InternshipParticipantController::class,
            ],

            'route_prefixes' => [
                'root.internship-participants',
                'company.internship-programs.participants',
            ],

            'route_controllers' => [

                'root.internship-participants' => [
                    \App\Http\Controllers\Root\InternshipParticipantController::class,
                ],

                'company.internship-programs.participants' => [
                    \App\Http\Controllers\Company\InternshipParticipantController::class,
                ],
            ],

            'roles' => [
                'root',
                'company',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | CERTIFICATE
        |--------------------------------------------------------------------------
        */

        Certificate::class => [

            'controllers' => [
                \App\Http\Controllers\Root\CertificateController::class,
            ],

            'route_prefixes' => [
                'root.certificates',
            ],

            'route_controllers' => [

                'root.certificates' => [
                    \App\Http\Controllers\Root\CertificateController::class,
                ],
            ],

            'roles' => [
                'root',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | WORK
        |--------------------------------------------------------------------------
        */

        Work::class => [

            'controllers' => [
                \App\Http\Controllers\Root\WorkController::class,
            ],

            'route_prefixes' => [
                'root.works',
            ],

            'route_controllers' => [

                'root.works' => [
                    \App\Http\Controllers\Root\WorkController::class,
                ],
            ],

            'roles' => [
                'root',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | WORK PHOTO
        |--------------------------------------------------------------------------
        */

        WorkPhoto::class => [

            'controllers' => [
                \App\Http\Controllers\Root\WorkPhotoController::class,
            ],

            'route_prefixes' => [
                'root.work-photos',
            ],

            'route_controllers' => [

                'root.work-photos' => [
                    \App\Http\Controllers\Root\WorkPhotoController::class,
                ],
            ],

            'roles' => [
                'root',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | WORK MEMBER
        |--------------------------------------------------------------------------
        */

        WorkMember::class => [

            'controllers' => [
                \App\Http\Controllers\Root\WorkMemberController::class,
            ],

            'route_prefixes' => [
                'root.work-members',
            ],

            'route_controllers' => [

                'root.work-members' => [
                    \App\Http\Controllers\Root\WorkMemberController::class,
                ],
            ],

            'roles' => [
                'root',
            ],
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

        $this->checkUserCompanyConnection();

        $this->checkRoleAccessMatrix();

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
            'MODEL → MIGRATION → TABLE → RELATIONSHIP → CONTROLLER → ROUTE → ROLE → VIEW'
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

        $modelName = class_basename($modelClass);

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

        if ($this->migrationExistsForTable($table)) {

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

        if (Schema::hasTable($table)) {

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

        $this->line(
            'Relationships'
        );

        $relationshipCount =
            $this->checkRelationships(
                $model
            );

        if ($relationshipCount === 0) {

            $this->line(
                '  └─ Tidak ada relationship'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CONTROLLERS
        |--------------------------------------------------------------------------
        */

        $this->line('');

        $this->line(
            'Controllers'
        );

        foreach ($config['controllers'] as $controller) {

            if (class_exists($controller)) {

                $this->ok++;

                $this->line(
                    '  ├─ ' .
                    class_basename($controller) .
                    ' : ✅'
                );

                $this->checkControllerMethods(
                    $controller
                );

            } else {

                $this->error++;

                $this->line(
                    '  ├─ ' .
                    class_basename($controller) .
                    ' : ❌ TIDAK DITEMUKAN'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ROUTES
        |--------------------------------------------------------------------------
        */

        $this->line('');

        $this->line(
            'Routes'
        );

        $routeCount = 0;

        foreach ($config['route_prefixes'] as $routePrefix) {

            $routes =
                $this->getModuleRoutes(
                    $routePrefix
                );

            if ($routes->isEmpty()) {

                $this->warning++;

                $this->line(
                    "  ├─ {$routePrefix} : ⚠️ TIDAK DITEMUKAN"
                );

                continue;
            }

            foreach ($routes as $route) {

                $routeCount++;

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

                /*
                |--------------------------------------------------------------------------
                | ROUTE → CONTROLLER
                |--------------------------------------------------------------------------
                */

                $allowedControllers =
                    $this->getAllowedRouteControllers(
                        $route,
                        $routePrefix,
                        $config
                    );

                $this->checkRouteController(
                    $route,
                    $allowedControllers
                );

                /*
                |--------------------------------------------------------------------------
                | ROLE
                |--------------------------------------------------------------------------
                */

                $this->checkRouteRole(
                    $route,
                    $routePrefix,
                    $config['roles'] ?? []
                );
            }
        }

        if ($routeCount === 0) {

            $this->error++;

            $this->line(
                '  └─ Tidak ada route module yang ditemukan.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | VIEWS
        |--------------------------------------------------------------------------
        */

        $this->line('');

        $this->line(
            'Views'
        );

        foreach ($config['route_prefixes'] as $routePrefix) {

            $this->checkViews(
                $routePrefix
            );
        }

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
                    |--------------------------------------------------------------------------
                    | TARGET TABLE
                    |--------------------------------------------------------------------------
                    */

                    $tableStatus =
                        Schema::hasTable(
                            $relatedTable
                        )
                            ? '✅'
                            : '❌';

                    if ($tableStatus === '❌') {
                        $this->error++;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | FK
                    |--------------------------------------------------------------------------
                    */

                    $fkStatus = true;

                    if ($relation instanceof BelongsTo) {

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
                    |--------------------------------------------------------------------------
                    | OUTPUT
                    |--------------------------------------------------------------------------
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

                    if ($status === '❌') {

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
        string $controller
    ): void {

        try {

            $reflection =
                new ReflectionClass($controller);

            $methods =
                collect(
                    $reflection->getMethods(
                        ReflectionMethod::IS_PUBLIC
                    )
                )
                ->filter(
                    function ($method) use ($reflection) {

                        return
                            $method->class ===
                            $reflection->getName() &&
                            !str_starts_with(
                                $method->getName(),
                                '__'
                            );
                    }
                )
                ->pluck('name')
                ->values()
                ->all();

            if (empty($methods)) {

                $this->warning++;

                $this->line(
                    '  └─ Methods : ⚠️ tidak ada public method'
                );

                return;
            }

            foreach ($methods as $methodName) {

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
    | GET ALLOWED ROUTE CONTROLLERS
    |--------------------------------------------------------------------------
    |
    | Mencari controller berdasarkan route prefix yang paling spesifik.
    |
    | Contoh:
    |
    | company.internship-programs.banners.index
    |
    | akan menggunakan:
    |
    | Company\InternshipProgramBannerController
    |
    | bukan:
    |
    | Company\InternshipProgramController
    |
    */

    protected function getAllowedRouteControllers(
        $route,
        string $routePrefix,
        array $config
    ): array {

        $routeName =
            $route->getName();

        /*
        |--------------------------------------------------------------------------
        | NESTED ROUTE CONTROLLERS
        |--------------------------------------------------------------------------
        */

        $nestedControllers =
            $config['nested_route_controllers'] ?? [];

        if ($routeName && !empty($nestedControllers)) {

            $prefixes =
                array_keys(
                    $nestedControllers
                );

            /*
            |--------------------------------------------------------------------------
            | Prefix terpanjang diperiksa terlebih dahulu.
            |--------------------------------------------------------------------------
            */

            usort(
                $prefixes,
                fn ($a, $b) =>
                    strlen($b) <=> strlen($a)
            );

            foreach ($prefixes as $nestedPrefix) {

                if (
                    $routeName === $nestedPrefix ||
                    str_starts_with(
                        $routeName,
                        $nestedPrefix . '.'
                    )
                ) {

                    return
                        $nestedControllers[
                            $nestedPrefix
                        ];
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | NORMAL ROUTE
        |--------------------------------------------------------------------------
        */

        return
            $config['route_controllers'][$routePrefix]
            ?? [];
    }

    /*
    |--------------------------------------------------------------------------
    | ROUTE → CONTROLLER
    |--------------------------------------------------------------------------
    */

    protected function checkRouteController(
        $route,
        array $allowedControllers
    ): void {

        $action =
            $route->getAction('uses');

        /*
        |--------------------------------------------------------------------------
        | Controller action tidak berupa string
        |--------------------------------------------------------------------------
        */

        if (is_array($action)) {

            $controller =
                $action[0] ?? null;

            $method =
                $action[1] ?? null;

            if (
                is_object($controller) &&
                $method
            ) {

                $controller =
                    get_class($controller);
            }

        } elseif (is_string($action)) {

            if (!str_contains($action, '@')) {
                return;
            }

            [
                $controller,
                $method
            ] = explode(
                '@',
                $action,
                2
            );

        } else {

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Controller tidak ditemukan
        |--------------------------------------------------------------------------
        */

        if (
            !is_string($controller) ||
            !class_exists($controller)
        ) {

            $this->error++;

            $this->line(
                "  │    Controller : ❌ " .
                ($controller ?: 'tidak diketahui')
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Controller tidak terdaftar
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $controller,
                $allowedControllers,
                true
            )
        ) {

            $this->error++;

            $this->line(
                "  │    Controller : ❌ tidak sesuai route module"
            );

            $this->line(
                "  │    Actual     : {$controller}"
            );

            if (!empty($allowedControllers)) {

                $this->line(
                    "  │    Expected   : " .
                    implode(
                        ', ',
                        $allowedControllers
                    )
                );
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Method
        |--------------------------------------------------------------------------
        */

        if (
            $method &&
            !method_exists(
                $controller,
                $method
            )
        ) {

            $this->error++;

            $this->line(
                "  │    Method     : ❌ {$method}() tidak ditemukan"
            );

            return;
        }

        $this->ok++;

        $this->line(
            "  │    Controller : ✅ " .
            class_basename($controller) .
            ($method ? "@{$method}" : '')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE DETECTION
    |--------------------------------------------------------------------------
    */

    protected function detectRoleFromRoutePrefix(
        string $routePrefix
    ): ?string {

        if (str_starts_with($routePrefix, 'root.')) {
            return 'root';
        }

        if (str_starts_with($routePrefix, 'company.')) {
            return 'company';
        }

        if (str_starts_with($routePrefix, 'intern.')) {
            return 'intern';
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | ROUTE ROLE ACCESS
    |--------------------------------------------------------------------------
    */

    protected function checkRouteRole(
        $route,
        string $routePrefix,
        array $allowedRoles
    ): void {

        $expectedRole =
            $this->detectRoleFromRoutePrefix(
                $routePrefix
            );

        if (empty($allowedRoles)) {

            $this->warning++;

            $this->line(
                '  │    Role       : ⚠️ belum dikonfigurasi'
            );

            return;
        }

        $middleware =
            $route->gatherMiddleware();

        $middlewareText =
            implode(
                ' ',
                $middleware
            );

        $detectedRoles = [];

        foreach (
            ['root', 'company', 'intern']
            as $role
        ) {

            if (
                preg_match(
                    '/(^|[\s|:,])role:' .
                    preg_quote($role, '/') .
                    '($|[\s|,])/',
                    $middlewareText
                )
            ) {

                $detectedRoles[] = $role;

                continue;
            }

            foreach ($middleware as $item) {

                $clean =
                    strtolower(
                        trim(
                            explode(
                                ':',
                                $item,
                                2
                            )[0]
                        )
                    );

                if ($clean === $role) {
                    $detectedRoles[] = $role;
                }
            }
        }

        $detectedRoles =
            array_values(
                array_unique(
                    $detectedRoles
                )
            );

        /*
        |--------------------------------------------------------------------------
        | ROLE TERDETEKSI
        |--------------------------------------------------------------------------
        */

        if (!empty($detectedRoles)) {

            $invalidRoles =
                array_diff(
                    $detectedRoles,
                    $allowedRoles
                );

            if (!empty($invalidRoles)) {

                $this->error++;

                $this->line(
                    '  │    Role       : ❌ role tidak diizinkan'
                );

                $this->line(
                    '  │    Allowed    : ' .
                    implode(', ', $allowedRoles)
                );

                $this->line(
                    '  │    Detected   : ' .
                    implode(', ', $detectedRoles)
                );

                return;
            }

            $this->ok++;

            $this->line(
                '  │    Role       : ✅ ' .
                implode(', ', $detectedRoles)
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Tidak ada middleware role
        |--------------------------------------------------------------------------
        */

        $this->warning++;

        $this->line(
            '  │    Role       : ⚠️ middleware role tidak terdeteksi'
        );

        $this->line(
            '  │    Expected   : ' .
            implode(', ', $allowedRoles)
        );

        if ($expectedRole) {

            $this->line(
                '  │    Prefix     : ' .
                strtoupper($expectedRole)
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | USER ↔ COMPANY
    |--------------------------------------------------------------------------
    */

    protected function checkUserCompanyConnection(): void
    {
        $this->line('');

        $this->line(
            '============================================================'
        );

        $this->info(
            'USER ↔ COMPANY CONNECTION'
        );

        $this->line(
            '------------------------------------------------------------'
        );

        /*
        |--------------------------------------------------------------------------
        | USER → COMPANY
        |--------------------------------------------------------------------------
        */

        $user = new User();

        if (!method_exists($user, 'company')) {

            $this->error++;

            $this->line(
                '  ├─ User::company() : ❌ tidak ditemukan'
            );

        } else {

            try {

                $relation =
                    $user->company();

                if (!$relation instanceof HasOne) {

                    $this->error++;

                    $this->line(
                        '  ├─ User::company() : ❌ bukan HasOne'
                    );

                } else {

                    $related =
                        $relation->getRelated();

                    if (!($related instanceof Company)) {

                        $this->error++;

                        $this->line(
                            '  ├─ User::company() : ❌ target bukan Company'
                        );

                    } else {

                        $foreignKey =
                            $relation->getForeignKeyName();

                        $this->line(
                            '  ├─ User::company() → Company : ✅'
                        );

                        $this->line(
                            "  │    FK : {$foreignKey}"
                        );

                        if (
                            Schema::hasColumn(
                                $related->getTable(),
                                $foreignKey
                            )
                        ) {

                            $this->ok++;

                            $this->line(
                                "  │    Column : ✅ companies.{$foreignKey}"
                            );

                        } else {

                            $this->error++;

                            $this->line(
                                "  │    Column : ❌ companies.{$foreignKey}"
                            );
                        }
                    }
                }

            } catch (Throwable $e) {

                $this->error++;

                $this->line(
                    '  └─ User::company() : ❌ gagal diperiksa'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | COMPANY → USER
        |--------------------------------------------------------------------------
        */

        $company = new Company();

        if (!method_exists($company, 'user')) {

            $this->error++;

            $this->line(
                '  ├─ Company::user() : ❌ tidak ditemukan'
            );

        } else {

            try {

                $relation =
                    $company->user();

                if (!$relation instanceof BelongsTo) {

                    $this->error++;

                    $this->line(
                        '  ├─ Company::user() : ❌ bukan BelongsTo'
                    );

                } else {

                    $related =
                        $relation->getRelated();

                    if (!($related instanceof User)) {

                        $this->error++;

                        $this->line(
                            '  ├─ Company::user() : ❌ target bukan User'
                        );

                    } else {

                        $foreignKey =
                            $relation->getForeignKeyName();

                        $this->line(
                            '  ├─ Company::user() → User : ✅'
                        );

                        $this->line(
                            "  │    FK : {$foreignKey}"
                        );

                        if (
                            Schema::hasColumn(
                                $company->getTable(),
                                $foreignKey
                            )
                        ) {

                            $this->ok++;

                            $this->line(
                                "  │    Column : ✅ companies.{$foreignKey}"
                            );

                        } else {

                            $this->error++;

                            $this->line(
                                "  │    Column : ❌ companies.{$foreignKey}"
                            );
                        }
                    }
                }

            } catch (Throwable $e) {

                $this->error++;

                $this->line(
                    '  └─ Company::user() : ❌ gagal diperiksa'
                );
            }
        }

        $this->newLine();
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE ACCESS MATRIX
    |--------------------------------------------------------------------------
    */

    protected function checkRoleAccessMatrix(): void
    {
        $this->line(
            '============================================================'
        );

        $this->info(
            'ROLE ACCESS MATRIX'
        );

        $this->line(
            '------------------------------------------------------------'
        );

        $roles = [
            'root',
            'company',
            'intern',
        ];

        foreach ($roles as $role) {

            $this->line('');

            $this->info(
                strtoupper($role)
            );

            $this->line(
                '  ----------------------------------------------------------'
            );

            $found = false;

            foreach ($this->modules as $modelClass => $config) {

                $modelName =
                    class_basename($modelClass);

                foreach (
                    $config['route_prefixes']
                    as $routePrefix
                ) {

                    $routes =
                        $this->getModuleRoutes(
                            $routePrefix
                        );

                    if ($routes->isEmpty()) {
                        continue;
                    }

                    $allowedRoles =
                        $config['roles'] ?? [];

                    if (
                        in_array(
                            $role,
                            $allowedRoles,
                            true
                        )
                    ) {

                        $found = true;

                        $this->ok++;

                        $this->line(
                            "  ├─ {$modelName}"
                        );

                        $this->line(
                            "  │    Route : {$routePrefix}"
                        );

                        $this->line(
                            "  │    Access: ✅ DIIZINKAN"
                        );

                    } else {

                        $this->line(
                            "  ├─ {$modelName}"
                        );

                        $this->line(
                            "  │    Route : {$routePrefix}"
                        );

                        $this->line(
                            "  │    Access: 🔒 TIDAK DIIZINKAN"
                        );
                    }
                }
            }

            if (!$found) {

                $this->warning++;

                $this->line(
                    '  └─ ⚠️ Belum ada route yang terdaftar untuk role ini.'
                );
            }
        }

        $this->newLine();
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
                    (
                        $name === $routeNamePrefix ||
                        str_starts_with(
                            $name,
                            $routeNamePrefix . '.'
                        )
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

            $action =
                $route->getAction('uses');

            if (!is_string($action)) {
                continue;
            }

            if (!str_contains($action, '@')) {
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

            if (!class_exists($controller)) {
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

                    if (isset($checked[$view])) {
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

                    if (File::exists($viewPath)) {

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

        if (empty($checked)) {

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

        if (!class_exists($modelClass)) {
            return '❌ MODEL';
        }

        $model =
            new $modelClass();

        $table =
            $model->getTable();

        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        if (!Schema::hasTable($table)) {
            return '❌ TABLE';
        }

        /*
        |--------------------------------------------------------------------------
        | CONTROLLERS
        |--------------------------------------------------------------------------
        */

        foreach ($config['controllers'] as $controller) {

            if (!class_exists($controller)) {
                return '❌ CONTROLLER';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ROUTES
        |--------------------------------------------------------------------------
        */

        $hasRoute = false;

        foreach ($config['route_prefixes'] as $routePrefix) {

            if (
                !$this->getModuleRoutes(
                    $routePrefix
                )->isEmpty()
            ) {

                $hasRoute = true;

                break;
            }
        }

        if (!$hasRoute) {
            return '❌ ROUTE';
        }

        /*
        |--------------------------------------------------------------------------
        | ROUTE CONTROLLER VALIDATION
        |--------------------------------------------------------------------------
        */

        foreach ($config['route_prefixes'] as $routePrefix) {

            $routes =
                $this->getModuleRoutes(
                    $routePrefix
                );

            foreach ($routes as $route) {

                $action =
                    $route->getAction('uses');

                if (!is_string($action)) {
                    continue;
                }

                if (!str_contains($action, '@')) {
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

                if (!class_exists($controller)) {
                    return '❌ CONTROLLER';
                }

                /*
                |--------------------------------------------------------------------------
                | IMPORTANT:
                | Ambil controller berdasarkan route paling spesifik.
                |--------------------------------------------------------------------------
                */

                $allowedControllers =
                    $this->getAllowedRouteControllers(
                        $route,
                        $routePrefix,
                        $config
                    );

                if (
                    !in_array(
                        $controller,
                        $allowedControllers,
                        true
                    )
                ) {

                    return '❌ CONTROLLER ROUTE';
                }

                if (
                    !method_exists(
                        $controller,
                        $methodName
                    )
                ) {

                    return '❌ METHOD';
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VIEWS
        |--------------------------------------------------------------------------
        */

        foreach ($config['route_prefixes'] as $routePrefix) {

            foreach (
                $this->getModuleRoutes(
                    $routePrefix
                ) as $route
            ) {

                $action =
                    $route->getAction('uses');

                if (
                    !is_string($action) ||
                    !str_contains($action, '@')
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

                if (!class_exists($controller)) {
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

                        if (!File::exists($viewPath)) {
                            return '⚠️ VIEW BELUM LENGKAP';
                        }
                    }

                } catch (Throwable $e) {

                    continue;
                }
            }
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
            $this->migrationCache as $content
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
            'Roles Checked   : root, company, intern'
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
                '🎉 PROJECT STRUCTURE & ROLE ACCESS PERFECTLY LINKED!'
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