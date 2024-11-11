<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;


class PhotoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isAdmin = auth()->user()->usertype === 'admin';
        $users = $isAdmin ? User::all(['id', 'name']) : [];
        $photos = Photo::with('user:id,name,profile_image', 'likes')
     ->get()
     ->map(function ($photo) use ($user) {
         return [
             'id' => $photo->id,
             'src' => $photo->src,
             'profile' => $photo->user->profile_image ? asset('storage/' . $photo->user->profile_image) : 'https://via.placeholder.com/100',
             'uploader' => $photo->user->name, // Ensure this is correctly set
             'profileLink' => route('account', $photo->user->id),
             'likes_count' => $photo->likes_count,
             'usertype' => auth()->user()->usertype, // Ensure 'usertype' is included in the response
             'liked_by_user' => $photo->isLikedByUser($user),
         ];
     });

            
    
        return Inertia::render('Dashboard', [
            'users' => User::where('usertype', 'user')->get(),

            'photos' => $photos,
            'auth' => ['user' => $user],
            'isAdmin' => $isAdmin, // Pass admin status to the frontend
            'users' => $users, // Pass users to the frontend if admin
        ]);
    }
    
    
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'src' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Only allow image uploads
            'alt' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255', // Use 'description' here
        ]);

        // Store the uploaded image and create a photo record
        $path = $request->file('src')->store('photos', 'public');

        $photo = Photo::create([
            'src' => $path,
            'alt' => $request->alt,
            'description' => $request->description, // Store the 'description' here
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('dashboard')->with('message', 'Photo uploaded successfully!');
    }
    


    public function destroy(Photo $photo)
{
    // Ensure the user is the one who uploaded the photo
    if ($photo->user_id !== auth()->id()) {
        return redirect()->route('profile.show', ['name' => auth()->user()->name])
                         ->with('error', 'You are not authorized to delete this photo.');
    }

    // Delete the photo file from storage
    Storage::delete($photo->src);

    // Delete the photo record from the database
    $photo->delete();

    // Redirect back to the user's profile page after deletion
    return redirect()->route('profile.show', ['name' => auth()->user()->name])
                     ->with('success', 'Photo deleted successfully.');
}

// Update Photo Description
public function update(Request $request, $id)
{
    $photo = Photo::findOrFail($id);

    // Vaslidate the description
    $request->validate([
        'description' => 'nullable|string|max:255',
    ]);

    // Update the descriptionk
    $photo->update([
        'description' => $request->description,
    ]);

    return redirect()->route('profile.show', ['name' => auth()->user()->name])
                     ->with('message', 'Description updated successfully!');
}

public function like($photoId)
{
    $photo = Photo::findOrFail($photoId);
    $user = Auth::user();

    // Toggle the like (add if it doesn’t exist, remove if it does)
    $user->likes()->toggle($photo);

    // Return a JSON response with the updated like count and status
    return response()->json([
        'likes_count' => $photo->likes()->count(),
        'liked_by_user' => $user->likes->contains($photoId),
    ]);
}

    // Method untuk menampilkan halaman detail foto
    public function photoshow($photoId)
    {
        $photo = Photo::with('user', 'likes')->findOrFail($photoId); // Mengambil foto dengan status like
        $user = Auth::user();
    
        // Menambahkan informasi apakah foto sudah di-like oleh user
        $liked_by_user = $user ? $user->likes->contains($photoId) : false;
    
        return Inertia::render('PhotoDetail', [
            'photo' => [
                'id' => $photo->id,
                'src' => $photo->src,
                'description' => $photo->description,
                'profile' => $photo->user->profile_image,
                'uploader' => $photo->user->name,
                'profileLink' => route('profile.show', $photo->user->id),
                'likes_count' => $photo->likes_count,
                'liked_by_user' => $liked_by_user,
            ]
        ]);
    }
    // PhotoController.php

public function destroyPhoto(Photo $photo)
{
    // Check if the current user is an admin or the owner of the photo
    if (auth()->user()->usertype === 'admin' || auth()->id() === $photo->user_id) {
        $photo->delete();
        return redirect()->route('dashboard')->with('success', 'Photo deleted successfully');
    }

    return redirect()->route('dashboard')->with('error', 'You are not authorized to delete this photo');
}

}