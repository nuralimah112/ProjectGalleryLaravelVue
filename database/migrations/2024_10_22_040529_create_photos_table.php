<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID as the primary key
            $table->string('src'); // Path or URL to the photo
            $table->string('alt'); // Alternative text for accessibility
            $table->foreignId('user_id') // Foreign key to the users table
                ->constrained() // References the users table automatically
                ->onDelete('cascade'); // Cascade delete photos if the user is deleted
            $table->timestamps(); // Created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('photos'); // Drops the table if it exists
    }
}
