@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Welcome, {{ $user->name }}!</h1>
                <p class="text-gray-600 mt-1">Here's your dashboard overview</p>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <p class="text-sm text-gray-500">Member since</p>
                    <p class="text-sm font-medium">{{ $accountCreated }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- User Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Roles Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Your Roles</h3>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-shield text-green-600"></i>
                </div>
            </div>
            <div class="space-y-2">
                @forelse($userRoles as $role)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-shield-alt mr-2"></i>
                        {{ ucfirst($role) }}
                    </span>
                @empty
                    <p class="text-gray-500 text-sm">No roles assigned</p>
                @endforelse
            </div>
        </div>

        <!-- Permissions Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Your Permissions</h3>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-key text-blue-600"></i>
                </div>
            </div>
            <div class="max-h-40 overflow-y-auto">
                <div class="space-y-1">
                    @forelse($userPermissions as $permission)
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            {{ ucwords(str_replace('_', ' ', $permission)) }}
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No permissions assigned</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @can('edit profile')
                <a href="{{ route('profile.edit') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-user-edit text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Edit Profile</p>
                        <p class="text-sm text-gray-500">Update your information</p>
                    </div>
                </a>
            @endcan

            @can('view users')
                <a href="{{ route('users.index') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-users text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">View Users</p>
                        <p class="text-sm text-gray-500">Browse user directory</p>
                    </div>
                </a>
            @endcan

            @can('view audit logs')
                <a href="{{ route('audit.index') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-clipboard-check text-purple-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Audit Logs</p>
                        <p class="text-sm text-gray-500">View system activity</p>
                    </div>
                </a>
            @endcan
        </div>

        @if(!auth()->user()->can('edit profile') && !auth()->user()->can('view users') && !auth()->user()->can('view audit logs'))
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-info-circle text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500">No actions available at the moment.</p>
                <p class="text-sm text-gray-400 mt-1">Contact your administrator for more permissions.</p>
            </div>
        @endif
    </div>
</div>
@endsection