<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'src',
        'user_id',
        'description', // Add this line
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): BelongsToMany
{
    return $this->belongsToMany(User::class, 'likes');
}

public function getLikesCountAttribute(): int
{
    return $this->likes()->count();
}

public function isLikedByUser(User $user): bool
{
    return $this->likes->contains($user);
}

public function comments()
{
    return $this->hasMany(Comment::class);
}

}
