<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
 'auth' => [
            'user' => Auth::user(), // Ensure you're getting the authenticated user
        ],
        ]);
    }
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Fill user data with validated request
        $user->fill($request->validated());

        // Handle profile image upload if present
        if ($request->hasFile('profile_image')) {
            // Validate the image (you can adjust rules as needed)
            $request->validate([
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Store the image in the 'profile_images' folder
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path; // Save the path to the user model
        }

        // Check if the email field is dirty and reset the verification timestamp
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Save the user
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updatePhoto(Request $request)
{
    $request->validate([
        'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $user = $request->user();

    if ($request->hasFile('profile_image')) {
        $path = $request->file('profile_image')->store('profile_images', 'public');
        $user->profile_image = $path; // Save the path to the user model
        $user->save(); // Save user after updating profile_image
    }

    return response()->json(['status' => 'Profile picture updated successfully.']);
}

public function show($name, Request $request)
{
    $user = User::where('name', $name)->firstOrFail();
    $photos = $user->photos;

    return Inertia::render('Profile/Show', [
        'user' => $user,
        'photos' => $photos,
        'authUser' => auth()->user(), // Add the authenticated user to the page
    ]);
}
// Add this method to delete a user
// AccountController.php

public function getUsers()
{
    // Assuming you want to return all users except admins, modify the logic as needed.
    $users = User::where('usertype', 'user')->get();

    return response()->json($users);
}
public function destroyUser($id)
{
    // Check if the current user is an admin
    if (auth()->user()->usertype === 'admin') {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('dashboard')->with('success', 'User deleted successfully');
    }

    return redirect()->route('dashboard')->with('error', 'You are not authorized to delete this user');
}



}