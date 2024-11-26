<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('flying_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->boolean('is_active')->default(true); // Track if the game is active
            $table->decimal('final_multiplier', 8, 2)->nullable(); // Store final multiplier
            $table->timestamps();
        });
}

    public function down()
    {
    Schema::dropIfExists('flying_histories');
    }

};
