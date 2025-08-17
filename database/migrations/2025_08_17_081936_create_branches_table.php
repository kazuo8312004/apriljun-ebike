<?php
// database/migrations/2024_01_01_000001_create_branches_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // SDO, BTY, NRV, SNT, SanJ
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('manager_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('branches');
    }
};
