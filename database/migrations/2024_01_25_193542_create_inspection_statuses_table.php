<?php

use App\Models\Product\InspectionStatus;
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
        Schema::create('product_inspection_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->timestamps();
        });

        $statuses = collect([
            [
                'name' => 'Not Started',
            ],
            [
                'name' => 'In Progress',
            ],
            [
                'name' => 'Completed',
            ],
        ]);

        $statuses->each(function (array $status) {
            InspectionStatus::create($status);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_inspection_statuses');
    }
};
