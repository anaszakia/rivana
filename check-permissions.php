<?php
/**
 * Check Current User Permissions
 * Jalankan: php check-permissions.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECK USER PERMISSIONS ===\n\n";

// Get current logged in user (simulation)
$user = \App\Models\User::first();

if (!$user) {
    echo "❌ Tidak ada user di database!\n";
    echo "   Buat user dulu dengan: php artisan tinker\n";
    echo "   User::create(['name'=>'Test','email'=>'test@test.com','password'=>bcrypt('password')]);\n\n";
    exit(1);
}

echo "User: {$user->name} ({$user->email})\n\n";

// Check if Spatie permission is installed
if (method_exists($user, 'hasPermissionTo')) {
    echo "✓ Spatie Permission installed\n\n";
    
    // Check hidrologi permissions
    $permissions = [
        'view hidrologi',
        'create hidrologi',
        'edit hidrologi',
        'delete hidrologi',
        'download hidrologi files'
    ];
    
    echo "Checking permissions:\n";
    foreach ($permissions as $permission) {
        $has = $user->hasPermissionTo($permission);
        $icon = $has ? '✓' : '✗';
        $status = $has ? 'YES' : 'NO';
        echo "  $icon $permission: $status\n";
    }
    
    echo "\n";
    
    // Check roles
    $roles = $user->roles()->pluck('name')->toArray();
    if (count($roles) > 0) {
        echo "User Roles:\n";
        foreach ($roles as $role) {
            echo "  - $role\n";
        }
    } else {
        echo "⚠️  User tidak punya role!\n";
    }
    
    echo "\n";
    
    // Check if user can access hidrologi
    $canCreate = $user->hasPermissionTo('create hidrologi');
    
    if (!$canCreate) {
        echo "❌ User TIDAK BISA submit job!\n\n";
        echo "SOLUSI:\n";
        echo "1. Assign permission ke user:\n";
        echo "   php artisan tinker\n";
        echo "   \$user = User::first();\n";
        echo "   \$user->givePermissionTo('view hidrologi');\n";
        echo "   \$user->givePermissionTo('create hidrologi');\n\n";
        
        echo "2. Atau assign role:\n";
        echo "   php artisan tinker\n";
        echo "   \$user = User::first();\n";
        echo "   \$user->assignRole('admin');\n\n";
        
        echo "3. Atau comment middleware di routes/web.php:\n";
        echo "   // ->middleware('permission:create hidrologi')\n\n";
    } else {
        echo "✓ User BISA submit job!\n\n";
    }
    
} else {
    echo "⚠️  Spatie Permission tidak terinstall atau tidak configured\n\n";
}

// Check if routes are registered
echo "Checking routes:\n";
$routes = \Illuminate\Support\Facades\Route::getRoutes();
$hidrologiRoutes = array_filter(iterator_to_array($routes), function($route) {
    return str_contains($route->getName() ?? '', 'hidrologi');
});

foreach ($hidrologiRoutes as $route) {
    echo "  ✓ {$route->getName()}\n";
}

echo "\n=== CHECK SELESAI ===\n";
