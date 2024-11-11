<?php

namespace App\Models;
use App\Models\Like;
use App\Models\Photo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'usertype',
        'description',
        'profile_image', // Add this line
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship: A user has many photos.
     */
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
    public function getProfileImageUrlAttribute()
{
    if ($this->profile_image) {
        return Storage::disk('public')->url($this->profile_image);
    }
    return '/default-profile-image.jpg'; // Default image if no profile image is set
}
public function likes()
{
    return $this->belongsToMany(Photo::class, 'likes')->withTimestamps();
}
public function comments()
{
    return $this->hasMany(Comment::class);
}


}
