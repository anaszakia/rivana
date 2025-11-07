# Dokumentasi Fitur Bilingual (Multi-bahasa)

## 🌐 Cara Menggunakan Terjemahan di Blade Template

### 1. **Menggunakan Helper `__()` atau `@lang`**

```blade
<!-- Menggunakan helper __(key) -->
<h1>{{ __('messages.dashboard') }}</h1>

<!-- Menggunakan directive @lang -->
<h1>@lang('messages.dashboard')</h1>

<!-- Dengan parameter/placeholder -->
<p>{{ __('messages.welcome_user', ['name' => $user->name]) }}</p>
```

### 2. **Contoh Implementasi di View**

```blade
<!-- Navigation Menu -->
<a href="{{ route('dashboard') }}">
    <i class="fas fa-home"></i>
    {{ __('messages.dashboard') }}
</a>

<a href="{{ route('hidrologi.index') }}">
    <i class="fas fa-water"></i>
    {{ __('messages.hidrologi') }}
</a>

<!-- Buttons -->
<button>{{ __('messages.download') }}</button>
<button>{{ __('messages.delete') }}</button>
<button>{{ __('messages.cancel') }}</button>

<!-- Status Labels -->
<span class="badge">{{ __('messages.completed') }}</span>
<span class="badge">{{ __('messages.processing') }}</span>

<!-- Tables -->
<th>{{ __('messages.location_name') }}</th>
<th>{{ __('messages.start_date') }}</th>
<th>{{ __('messages.end_date') }}</th>
```

### 3. **Menambahkan Terjemahan Baru**

**File: `resources/lang/en/messages.php`**
```php
return [
    'new_key' => 'English Translation',
    'welcome_message' => 'Welcome to our system!',
];
```

**File: `resources/lang/id/messages.php`**
```php
return [
    'new_key' => 'Terjemahan Indonesia',
    'welcome_message' => 'Selamat datang di sistem kami!',
];
```

### 4. **Language Switcher**

Sudah otomatis tersedia di sidebar kiri bawah dengan tampilan:
- 🇮🇩 Bahasa Indonesia
- 🇬🇧 English

### 5. **Cara Kerja**

1. User klik language switcher
2. Request POST ke route `/language/switch`
3. Middleware `SetLocale` otomatis mendeteksi bahasa dari session
4. Semua text dengan `__('messages.key')` akan diterjemahkan

### 6. **Default Locale**

- Default: Bahasa Indonesia (`id`)
- Fallback: English (`en`)
- Dapat diubah di `config/app.php`

### 7. **Pengecekan Bahasa Aktif di Blade**

```blade
@if(app()->getLocale() === 'en')
    <p>English content</p>
@else
    <p>Konten Indonesia</p>
@endif

<!-- Atau -->
{{ app()->getLocale() === 'en' ? 'English' : 'Indonesia' }}
```

### 8. **Best Practices**

✅ Selalu gunakan `__()` untuk semua text yang terlihat user
✅ Gunakan key yang descriptive: `messages.location_name` bukan `msg.ln`
✅ Group berdasarkan kategori (navigation, buttons, status, dll)
✅ Test kedua bahasa sebelum deploy

### 9. **Menambah Bahasa Baru (Opsional)**

Jika ingin menambah bahasa lain (misal: Jepang):

1. Buat folder: `resources/lang/ja`
2. Copy file terjemahan: `resources/lang/ja/messages.php`
3. Tambahkan di middleware: `['en', 'id', 'ja']`
4. Tambahkan opsi di language switcher

### 10. **File-file Penting**

- **Middleware**: `app/Http/Middleware/SetLocale.php`
- **Controller**: `app/Http/Controllers/LanguageController.php`
- **Translations**: `resources/lang/{locale}/messages.php`
- **Language Switcher**: `resources/views/partials/language-switcher.blade.php`
- **Config**: `config/app.php`
- **Route**: `routes/web.php`

---

## 📝 Contoh Penggunaan di Controller

```php
// Di controller, return message dengan terjemahan
return redirect()->back()
    ->with('success', __('messages.delete_success'));

// Dengan parameter
return redirect()->back()
    ->with('success', __('messages.item_deleted', ['name' => $item->name]));
```

## 🎨 Contoh Styling Language Switcher

Sudah di-style dengan:
- Flag emoji (🇮🇩, 🇬🇧)
- Dropdown dengan animasi smooth
- Active state indicator (checkmark)
- Hover effects
- Mobile responsive

---

**Sistem bilingual sudah siap digunakan!** 🎉
