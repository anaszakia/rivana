# 📚 Guide: Menambahkan Menu Baru dengan Permission System

## 🎯 Overview
Panduan lengkap untuk menambahkan menu baru dalam sistem Laravel dengan Spatie Permission, mulai dari permission, route, controller, hingga tampilan UI.

---

## 📋 Table of Contents
1. [Prerequisites](#prerequisites)
2. [Step 1: Menambahkan Permission](#step-1-menambahkan-permission)
3. [Step 2: Membuat Migration (Jika Diperlukan)](#step-2-membuat-migration-jika-diperlukan)
4. [Step 3: Membuat Model](#step-3-membuat-model)
5. [Step 4: Membuat Controller](#step-4-membuat-controller)
6. [Step 5: Menambahkan Routes](#step-5-menambahkan-routes)
7. [Step 6: Membuat Views](#step-6-membuat-views)
8. [Step 7: Update Sidebar Navigation](#step-7-update-sidebar-navigation)
9. [Step 8: Testing & Debugging](#step-8-testing--debugging)
10. [Best Practices](#best-practices)

---

## Prerequisites

### Sistem Requirements:
- ✅ Laravel Framework
- ✅ Spatie Laravel Permission Package
- ✅ AlpineJS & TailwindCSS (untuk UI)
- ✅ Database sudah setup dengan permission tables

---

## Step 1: Menambahkan Permission

### 1.1 Update RolePermissionSeeder

```php
// database/seeders/RolePermissionSeeder.php

public function run(): void
{
    // Reset cached roles and permissions
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    // Existing permissions...
    $permissions = [
        // User Management
        'view users',
        'create users',
        'edit users',
        'delete users',
        
        // NEW MENU PERMISSIONS - Contoh: Products
        'view products',      // Melihat daftar produk
        'create products',    // Membuat produk baru
        'edit products',      // Edit produk
        'delete products',    // Hapus produk
        'manage products',    // Permission khusus untuk management
        
        // NEW MENU PERMISSIONS - Contoh: Categories
        'view categories',
        'create categories',
        'edit categories',
        'delete categories',
        
        // Existing permissions...
    ];

    // Create permissions
    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
    }

    // Assign all permissions to super_admin role
    $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
    $superAdminRole->givePermissionTo(Permission::all());
}
```

### 1.2 Jalankan Seeder

```bash
# Jalankan seeder untuk update permissions
php artisan db:seed --class=RolePermissionSeeder

# Clear permission cache
php artisan permission:cache-reset

# Verify permissions
php artisan permission:show
```

---

## Step 2: Membuat Migration (Jika Diperlukan)

### 2.1 Buat Migration untuk Table Baru

```bash
# Contoh: membuat table products
php artisan make:migration create_products_table
```

### 2.2 Define Migration Structure

```php
// database/migrations/xxxx_xx_xx_create_products_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

### 2.3 Jalankan Migration

```bash
php artisan migrate
```

---

## Step 3: Membuat Model

### 3.1 Generate Model

```bash
php artisan make:model Product
```

### 3.2 Define Model Properties

```php
// app/Models/Product.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
```

---

## Step 4: Membuat Controller

### 4.1 Generate Resource Controller

```bash
php artisan make:controller ProductController --resource
```

### 4.2 Implement Controller Methods

```php
// app/Http/Controllers/ProductController.php

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Search and pagination
        $keyword = $request->query('search');

        $products = Product::query()
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($q2) use ($keyword) {
                    $q2->where('name', 'like', "%{$keyword}%")
                       ->orWhere('description', 'like', "%{$keyword}%")
                       ->orWhere('category', 'like', "%{$keyword}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->appends(['search' => $keyword]);

        return view('admin.products.index', compact('products', 'keyword'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product berhasil ditambahkan!');
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product berhasil dihapus!');
    }
}
```

---

## Step 5: Menambahkan Routes

### 5.1 Update web.php dengan Permission Middleware

```php
// routes/web.php

// Authenticated routes - Permission based access control
Route::middleware(['auth', 'log.sensitive'])->group(function () {
    
    // Existing routes...
    
    // NEW MENU ROUTES - Products Management
    Route::get('/products', [ProductController::class, 'index'])
        ->middleware('permission:view products')
        ->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])
        ->middleware('permission:create products')
        ->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])
        ->middleware('permission:create products')
        ->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])
        ->middleware('permission:view products')
        ->name('products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
        ->middleware('permission:edit products')
        ->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])
        ->middleware('permission:edit products')
        ->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->middleware('permission:delete products')
        ->name('products.destroy');
        
    // Atau bisa menggunakan resource route dengan middleware:
    // Route::resource('products', ProductController::class)
    //     ->middleware([
    //         'index' => 'permission:view products',
    //         'create' => 'permission:create products',
    //         'store' => 'permission:create products',
    //         'show' => 'permission:view products',
    //         'edit' => 'permission:edit products',
    //         'update' => 'permission:edit products',
    //         'destroy' => 'permission:delete products',
    //     ]);
});
```

### 5.2 Verify Routes

```bash
# Check routes
php artisan route:list --name=products
```

---

## Step 6: Membuat Views

### 6.1 Create Views Directory

```bash
mkdir -p resources/views/admin/products
```

### 6.2 Index View (Listing)

```blade
{{-- resources/views/admin/products/index.blade.php --}}

@extends('layouts.app')
@section('title', 'Manajemen Produk')

@section('content')
<div x-data="{ 
    openCreate: false,
    editModals: {},
    detailModals: {},
    toggleModal(type, id = null) {
        if (type === 'create') {
            this.openCreate = !this.openCreate;
        } else {
            this[type + 'Modals'][id] = !this[type + 'Modals'][id];
        }
    }
}" class="space-y-6">

    {{-- HEADER --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manajemen Produk</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola data produk sistem</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                {{-- Search Form --}}
                <form method="GET" action="{{ route('products.index') }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari produk..." 
                           class="border-gray-300 rounded-lg px-4 py-2.5 text-sm w-64 focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg">
                        Cari
                    </button>
                </form>
                
                {{-- Add Button --}}
                @can('create products')
                <button @click="toggleModal('create')" class="bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Tambah Produk
                </button>
                @endcan
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Harga</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Stok</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->category ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $product->formatted_price }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $product->stock }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            {{-- Action buttons dengan permission --}}
                            <button @click="toggleModal('detail', {{ $product->id }})" class="text-blue-600 hover:text-blue-700">
                                <i class="fas fa-eye"></i>
                            </button>
                            @can('edit products')
                            <button @click="toggleModal('edit', {{ $product->id }})" class="text-yellow-600 hover:text-yellow-700">
                                <i class="fas fa-edit"></i>
                            </button>
                            @endcan
                            @can('delete products')
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700" onclick="return confirm('Yakin hapus?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada data produk
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($products->hasPages())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        {{ $products->links() }}
    </div>
    @endif

    {{-- MODALS akan ditambahkan di sini --}}
</div>
@endsection
```

### 6.3 Create/Edit Forms

```blade
{{-- resources/views/admin/products/create.blade.php --}}

@extends('layouts.app')
@section('title', 'Tambah Produk')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Tambah Produk Baru</h1>
        
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                    <input type="text" name="name" value="{{ old('name') }}" 
                           class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <input type="text" name="category" value="{{ old('category') }}" 
                           class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                    <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0"
                           class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stok</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0"
                           class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    @error('stock')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="4" 
                          class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mt-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Produk Aktif</span>
                </label>
            </div>
            
            <div class="flex justify-end space-x-3 mt-8">
                <a href="{{ route('products.index') }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Batal
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
```

---

## Step 7: Update Sidebar Navigation

### 7.1 Tambahkan Menu di Sidebar

```blade
{{-- resources/views/partials/sidebar.blade.php --}}

<!-- Existing menu items... -->

{{-- Products Menu --}}
@can('view products')
<li class="relative">
    <a href="{{ route('products.index') }}" 
       class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors rounded-lg {{ request()->routeIs('products.*') ? 'bg-blue-50 text-blue-600 border-r-2 border-blue-600' : '' }}">
        <i class="fas fa-box-open w-5 h-5 mr-3"></i>
        <span>Produk</span>
    </a>
</li>
@endcan

{{-- Submenu untuk Products (Optional) --}}
@canany(['view products', 'create products'])
<li class="relative">
    <div x-data="{ open: {{ request()->routeIs('products.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-box-open w-5 h-5 mr-3"></i>
                <span>Produk</span>
            </div>
            <i class="fas fa-chevron-down transform transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        
        <ul x-show="open" x-transition class="ml-8 mt-2 space-y-1">
            @can('view products')
            <li>
                <a href="{{ route('products.index') }}" 
                   class="block px-4 py-2 text-sm text-gray-600 hover:text-blue-600 rounded-lg {{ request()->routeIs('products.index') ? 'text-blue-600 font-medium' : '' }}">
                    Daftar Produk
                </a>
            </li>
            @endcan
            
            @can('create products')
            <li>
                <a href="{{ route('products.create') }}" 
                   class="block px-4 py-2 text-sm text-gray-600 hover:text-blue-600 rounded-lg {{ request()->routeIs('products.create') ? 'text-blue-600 font-medium' : '' }}">
                    Tambah Produk
                </a>
            </li>
            @endcan
        </ul>
    </div>
</li>
@endcanany
```

---

## Step 8: Testing & Debugging

### 8.1 Test Permissions

```bash
# Check permissions
php artisan permission:show

# Clear caches
php artisan permission:cache-reset
php artisan route:cache
php artisan view:clear
```

### 8.2 Test Routes

```bash
# List specific routes
php artisan route:list --name=products

# Test route accessibility
php artisan tinker
>>> route('products.index')
>>> auth()->user()->can('view products')
```

### 8.3 Debugging Common Issues

```php
// Debug permission di Blade
@dump(auth()->user()->getAllPermissions()->pluck('name'))

// Debug di Controller
dd(auth()->user()->can('view products'));

// Check specific permission
if (!auth()->user()->can('view products')) {
    abort(403, 'Unauthorized action.');
}
```

---

## Best Practices

### 🔐 Security Best Practices

1. **Always Use Permission Middleware**:
   ```php
   Route::get('/products', [ProductController::class, 'index'])
       ->middleware('permission:view products');
   ```

2. **Double Check di Controller**:
   ```php
   public function index()
   {
       $this->authorize('view products');
       // atau
       if (!auth()->user()->can('view products')) {
           abort(403);
       }
   }
   ```

3. **Consistent Permission Naming**:
   - Format: `{action} {resource}` (view users, create products)
   - Gunakan lowercase dan underscore
   - Specific dan descriptive

### 🎨 UI/UX Best Practices

1. **Permission-Based UI Elements**:
   ```blade
   @can('create products')
       <button>Tambah Produk</button>
   @endcan
   ```

2. **Graceful Degradation**:
   - Hide elements yang tidak accessible
   - Show appropriate messages
   - Provide alternative actions

3. **Consistent Styling**:
   - Follow existing design patterns
   - Use consistent icon sets
   - Maintain responsive design

### 📝 Documentation Best Practices

1. **Document New Permissions**:
   - Update permission documentation
   - Include role mappings
   - Add testing instructions

2. **Code Comments**:
   ```php
   // Check if user can manage products
   // This includes view, create, edit, delete permissions
   if (auth()->user()->can('manage products')) {
       // Allow full access
   }
   ```

---

## 🔍 Troubleshooting

### Common Errors & Solutions

#### 1. **"Permission does not exist"**
```bash
# Solution: Run seeder dan clear cache
php artisan db:seed --class=RolePermissionSeeder
php artisan permission:cache-reset
```

#### 2. **"Route not found"**
```bash
# Solution: Clear route cache
php artisan route:cache
php artisan route:list --name=products
```

#### 3. **"403 Forbidden"**
```php
// Check user permissions
dd(auth()->user()->getAllPermissions()->pluck('name'));

// Check role assignments
dd(auth()->user()->getRoleNames());
```

#### 4. **"View not found"**
```bash
# Ensure view file exists
ls resources/views/admin/products/

# Clear view cache
php artisan view:clear
```

---

## 📚 Additional Resources

### Documentation Links:
- [Spatie Permission Documentation](https://spatie.be/docs/laravel-permission)
- [Laravel Route Model Binding](https://laravel.com/docs/routing#route-model-binding)
- [Laravel Authorization](https://laravel.com/docs/authorization)

### Useful Commands:
```bash
# Permission management
php artisan permission:show
php artisan permission:cache-reset

# Development helpers
php artisan route:list
php artisan make:controller ControllerName --resource
php artisan make:model ModelName -m
php artisan make:seeder SeederName

# Debugging
php artisan tinker
php artisan log:clear
```

---

## ✅ Checklist Template

Gunakan checklist ini setiap kali menambahkan menu baru:

- [ ] ✅ Permissions ditambahkan di RolePermissionSeeder
- [ ] ✅ Migration dibuat (jika diperlukan)
- [ ] ✅ Model dibuat dan configured
- [ ] ✅ Controller dibuat dengan proper validation
- [ ] ✅ Routes ditambahkan dengan permission middleware
- [ ] ✅ Views dibuat (index, create, edit, show)
- [ ] ✅ Sidebar navigation updated
- [ ] ✅ Permission cache cleared
- [ ] ✅ Routes tested
- [ ] ✅ UI permissions tested
- [ ] ✅ Error handling implemented
- [ ] ✅ Documentation updated

---

**🎯 Happy Coding! Semoga panduan ini membantu dalam pengembangan sistem yang secure dan terstruktur!**