<?php
// database/migrations/2024_01_04_000004_create_nwow_units_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nwow_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->string('chassis_no')->unique();
            $table->string('motor_no')->nullable();
            $table->string('battery_no')->nullable();
            $table->string('controller_no')->nullable();
            $table->string('charger_no')->nullable();
            $table->string('remote_no')->nullable();
            $table->string('color')->nullable();
            $table->date('purchase_date');
            $table->decimal('purchase_price', 10, 2);
            $table->enum('status', ['in_stock', 'sold', 'loaned', 'transferred'])->default('in_stock');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nwow_units');
    }
};
