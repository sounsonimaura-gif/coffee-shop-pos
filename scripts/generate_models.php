<?php

/**
 * Bulk-generate Eloquent model stubs for every table in the migration.
 * Used once during scaffolding. Run with: php scripts/generate_models.php
 * After running, key models are hand-tuned (User, Role, Permission, Company, Branch, etc.).
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->bootstrapWith([
    LoadEnvironmentVariables::class,
    LoadConfiguration::class,
    HandleExceptions::class,
    RegisterFacades::class,
    RegisterProviders::class,
    BootProviders::class,
]);

use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Bootstrap\RegisterFacades;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Tables to skip (Laravel auto-managed)
$skip = ['migrations', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs',
    'password_reset_tokens', 'sessions'];

// Hand-written models we shouldn't overwrite
$handWritten = ['users', 'roles', 'permissions', 'companies', 'branches'];

$tables = collect(DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name"))
    ->pluck('name')
    ->reject(fn ($t) => in_array($t, $skip))
    ->values();

// Special class names for plural tables (singularize)
$tableToClass = function (string $table): string {
    // Custom mappings for plural-irregular or compound names
    $map = [
        'permission_role' => 'PermissionRole',
        'branch_user' => 'BranchUser',
    ];
    if (isset($map[$table])) {
        return $map[$table];
    }

    return Str::studly(Str::singular($table));
};

$modelDir = __DIR__.'/../app/Models';
if (! is_dir($modelDir)) {
    mkdir($modelDir, 0755, true);
}

foreach ($tables as $table) {
    $class = $tableToClass($table);
    $file = "$modelDir/$class.php";
    if (in_array($table, $handWritten) && file_exists($file)) {
        echo "Skipping hand-written: $class\n";

        continue;
    }

    $cols = DB::select("PRAGMA table_info('$table')");
    $columnNames = array_map(fn ($c) => $c->name, $cols);

    $fillable = array_diff($columnNames, ['id', 'created_at', 'updated_at', 'deleted_at']);
    $hasSoftDelete = in_array('deleted_at', $columnNames);

    $casts = [];
    foreach ($cols as $c) {
        $name = $c->name;
        $type = strtolower($c->type);
        if ($name === 'id') {
            continue;
        }
        if (str_contains($type, 'tinyint(1)') || str_contains($type, 'boolean')) {
            $casts[$name] = 'boolean';
        } elseif (str_contains($type, 'json') || str_contains($type, 'text') && in_array($name, ['old_values', 'new_values', 'meta', 'failed_job_ids'])) {
            $casts[$name] = 'array';
        } elseif (str_contains($type, 'date') && ! str_contains($type, 'datetime')) {
            $casts[$name] = 'date';
        } elseif (str_contains($type, 'datetime') || str_contains($type, 'timestamp')) {
            if (! in_array($name, ['created_at', 'updated_at', 'deleted_at'])) {
                $casts[$name] = 'datetime';
            }
        } elseif (str_contains($type, 'decimal') || str_contains($type, 'numeric')) {
            $casts[$name] = 'decimal:4';
        } elseif (str_contains($type, 'int')) {
            if (! in_array($name, ['id'])) {
                $casts[$name] = 'integer';
            }
        }
    }

    $fillableStr = implode(",\n        ", array_map(fn ($c) => "'$c'", $fillable));
    $castsStr = implode(",\n        ", array_map(
        fn ($k, $v) => "'$k' => '$v'",
        array_keys($casts),
        array_values($casts)
    ));

    $useSoftDelete = $hasSoftDelete ? "use Illuminate\\Database\\Eloquent\\SoftDeletes;\n" : '';
    $traits = $hasSoftDelete ? '    use HasFactory, SoftDeletes;' : '    use HasFactory;';

    $tableProp = $table === Str::snake(Str::pluralStudly($class)) ? '' : "    protected \$table = '$table';\n";

    $php = <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
{$useSoftDelete}
class {$class} extends Model
{
{$traits}

{$tableProp}    protected \$fillable = [
        {$fillableStr},
    ];

    protected \$casts = [
        {$castsStr},
    ];
}
PHP;

    file_put_contents($file, $php);
    echo "Generated: $class ($table)\n";
}

echo "\nDone.\n";
