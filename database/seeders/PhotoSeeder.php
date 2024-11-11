<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Photo;
use App\Models\User;

class PhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();  // Assuming you have users in the `users` table

        foreach ($users as $user) {
            Photo::create([
                'src' => 'https://picsum.photos/400/600',  // Use placeholder images for testing
                'alt' => 'Sample Photo',
                'user_id' => $user->id,
            ]);
        }
    }
}
