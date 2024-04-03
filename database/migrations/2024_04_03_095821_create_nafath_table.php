<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nafath', function (Blueprint $table) {
            $table->id();
            $table->string('national_id'); //->unique()
            $table->string('service');
            $table->string('locale');
            $table->string('request_id');
            $table->string('trans_id',);
            $table->string('random_token');
            $table->string('jwt_token')->nullable();
            $table->string('decoded_jwt_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nafath');
    }
};
