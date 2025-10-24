<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // Always show admin dashboard without authentication
        return $this->adminDashboard();
    }
    
    private function basicDashboard()
    {
        $data = [
            'user' => null,
            'accountCreated' => null,
            'userPermissions' => [],
            'userRoles' => [],
        ];
        
        return view('dashboard.basic', $data);
    }

    public function userDashboard()
    {
        // Data untuk user dashboard without authentication
        $data = [
            'user' => null,
            'totalUsers' => User::count(),
            'todayLogins' => AuditLog::where('action', 'Login')
                ->whereDate('created_at', today())
                ->count(),
            'myLoginHistory' => collect([]),
            'recentActivity' => AuditLog::latest()
                ->take(10)
                ->get(),
            'accountCreated' => null,
            'lastLogin' => 'Sistem tanpa login',
        ];
        
        return view('user.dashboard', $data);
    }

    public function adminDashboard()
    {
        // Data untuk admin dashboard
        $data = [
            'totalUsers' => User::count(),
            'totalAdmins' => User::role('super_admin')->count(),
            'totalRegularUsers' => User::whereDoesntHave('roles', function($query) {
                $query->where('name', 'super_admin');
            })->count(),
            'todayRegistrations' => User::whereDate('created_at', today())->count(),
            'thisWeekRegistrations' => User::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),
            'thisMonthRegistrations' => User::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'todayLogins' => AuditLog::where('action', 'Login')
                ->whereDate('created_at', today())
                ->count(),
            'totalAuditLogs' => AuditLog::count(),
            'recentUsers' => User::latest()->take(5)->get(),
            'recentActivity' => AuditLog::with('user')->latest()->take(10)->get(),
            
            // Hidrologi Data
            'totalHidrologiJobs' => \App\Models\HidrologiJobs::count(),
            'completedJobs' => \App\Models\HidrologiJobs::where('status', 'completed')->count(),
            'runningJobs' => \App\Models\HidrologiJobs::whereIn('status', ['pending', 'running', 'processing'])->count(),
            'failedJobs' => \App\Models\HidrologiJobs::where('status', 'failed')->count(),
            'totalFiles' => \App\Models\HidrologiFile::count(),
            'todayJobs' => \App\Models\HidrologiJobs::whereDate('created_at', today())->count(),
            'thisWeekJobs' => \App\Models\HidrologiJobs::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),
            'thisMonthJobs' => \App\Models\HidrologiJobs::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'recentJobs' => \App\Models\HidrologiJobs::with('user')->latest()->take(5)->get(),
            
            'hidrologiGrowthData' => $this->getHidrologiGrowthData(),
            'jobStatusData' => $this->getJobStatusData(),
        ];
        
        return view('admin.dashboard', $data);
    }
    
    private function getUserGrowthData()
    {
        $months = [];
        $userCounts = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $userCounts[] = $count;
        }
        
        return [
            'months' => $months,
            'userCounts' => $userCounts
        ];
    }
    
    private function getLoginStats()
    {
        $days = [];
        $loginCounts = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('M d');
            $count = AuditLog::where('action', 'Login')
                ->whereDate('created_at', $date)
                ->count();
            $loginCounts[] = $count;
        }
        
        return [
            'days' => $days,
            'loginCounts' => $loginCounts
        ];
    }
    
    private function getHidrologiGrowthData()
    {
        $months = [];
        $jobCounts = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            $count = \App\Models\HidrologiJobs::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $jobCounts[] = $count;
        }
        
        return [
            'months' => $months,
            'jobCounts' => $jobCounts
        ];
    }
    
    private function getJobStatusData()
    {
        $days = [];
        $completedCounts = [];
        $failedCounts = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('M d');
            
            $completed = \App\Models\HidrologiJobs::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->count();
            $completedCounts[] = $completed;
            
            $failed = \App\Models\HidrologiJobs::where('status', 'failed')
                ->whereDate('created_at', $date)
                ->count();
            $failedCounts[] = $failed;
        }
        
        return [
            'days' => $days,
            'completedCounts' => $completedCounts,
            'failedCounts' => $failedCounts
        ];
    }
}