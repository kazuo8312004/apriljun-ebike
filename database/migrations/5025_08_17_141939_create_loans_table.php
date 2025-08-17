<?php
// database/migrations/2024_01_09_000009_create_loans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_number')->unique();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('user_id')->constrained(); // Staff who processed
            $table->foreignId('nwow_unit_id')->constrained();
            $table->string('borrower_name');
            $table->string('borrower_phone');
            $table->text('borrower_address');
            $table->string('borrower_id_type'); // Government ID, Driver's License, etc.
            $table->string('borrower_id_number');
            $table->date('loan_date');
            $table->date('expected_return_date');
            $table->date('actual_return_date')->nullable();
            $table->decimal('collateral_amount', 10, 2)->default(0);
            $table->text('loan_purpose')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'returned', 'overdue', 'lost'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loans');
    }
};
