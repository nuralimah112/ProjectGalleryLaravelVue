<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    // Method to show the user's account information
    public function show($id)
    {
        // Fetch the user by their ID
        $user = User::findOrFail($id); // Use findOrFail to handle missing users gracefully
        
        // Pass the user data to the Inertia view
        return Inertia::render('Account/Show', [
            'user' => $user, // You can customize the data as needed
            'isCurrentUser' => Auth::id() === $user->id, // Check if the logged-in user is viewing their own account
        ]);
    }
}
