<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        // Create a dummy user for display purposes since no authentication
        $dummyUser = (object) [
            'name' => 'RIVANA User',
            'email' => 'sistem@rivana.app'
        ];
        return view('profile', compact('dummyUser'));
    }

    public function update(Request $request)
    {
        // Since there's no authentication, just return success message
        $request->validate([
            'name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|max:255',
            'password' => 'nullable|min:8|confirmed',
        ], [
            'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
        ]);
        
        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
