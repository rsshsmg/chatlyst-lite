<?php
// Diagnostic script to check job_title_id column status

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Setup database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== Database Schema Check ===\n";
echo "Checking people table structure...\n\n";

// Check if people table exists
if (!Capsule::schema()->hasTable('people')) {
    echo "❌ people table does not exist\n";
    exit;
}

// Get column information
$columns = Capsule::select('DESCRIBE people');
$jobTitleColumn = null;

foreach ($columns as $column) {
    if ($column->Field === 'job_title_id') {
        $jobTitleColumn = $column;
        break;
    }
}

if ($jobTitleColumn) {
    echo "✅ job_title_id column found\n";
    echo "Type: {$jobTitleColumn->Type}\n";
    echo "Null: {$jobTitleColumn->Null}\n";
    echo "Key: {$jobTitleColumn->Key}\n";
    echo "Default: " . ($jobTitleColumn->Default ?? 'NULL') . "\n";
    echo "Extra: {$jobTitleColumn->Extra}\n";

    if ($jobTitleColumn->Null === 'NO') {
        echo "\n❌ ISSUE: Column is NOT NULLABLE\n";
        echo "This explains the integrity constraint violation\n";
    } else {
        echo "\n✅ Column is nullable as expected\n";
    }
} else {
    echo "❌ job_title_id column not found\n";
}

echo "\n=== Testing Insert ===\n";
try {
    $result = Capsule::table('people')->insert([
        'id' => \Illuminate\Support\Str::uuid(),
        'full_name' => 'Test Person',
        'gender' => 'm',
        'job_title_id' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✅ Insert with null job_title_id succeeded\n";
} catch (\Exception $e) {
    echo "❌ Insert failed: " . $e->getMessage() . "\n";
}

echo "\n=== Recommended Actions ===\n";
echo "1. If column is NOT NULLABLE, run: php artisan migrate:fresh\n";
echo "2. Or create a new migration to alter the column:\n";
echo "   php artisan make:migration make_job_title_nullable_in_people_table\n";
echo "3. Add this to the new migration:\n";
echo "   \$table->foreignId('job_title_id')->nullable()->change();\n";
