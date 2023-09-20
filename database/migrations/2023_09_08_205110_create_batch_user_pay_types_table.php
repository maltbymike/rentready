<?php

use App\Models\Payroll;
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
        Schema::create('payroll_batch_user_pay_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_batch_user_id');
            $table->foreign('payroll_batch_user_id')->references('id')->on('payroll_batch_user');
            $table->foreignIdFor(Payroll\PayType::class)->constrained();
            $table->decimal('value', 6, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_batch_user_pay_type');
    }
};
