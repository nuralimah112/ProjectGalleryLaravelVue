<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePhotosTable extends Migration
{
    public function up()
    {
        Schema::table('photos', function (Blueprint $table) {
            // Make the 'alt' column nullable
            $table->string('alt')->nullable()->change();

            // Add a new 'description' column
            $table->text('description')->nullable();
        });
    }

    public function down()
    {
        Schema::table('photos', function (Blueprint $table) {
            // Revert 'alt' column to not nullable
            $table->string('alt')->nullable(false)->change();

            // Drop the 'description' column
            $table->dropColumn('description');
        });
    }
}
