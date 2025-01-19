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
        Schema::create('package_subscribes', function (Blueprint $table) {
            $table->id();
            $table->integer("cpu")->nullable(false);
            $table->integer("ram")->nullable(false);
            $table->integer("disk")->nullable(false);
            $table->decimal("monthly_rate", 10, 2)->nullable(false);
            $table->boolean("is_active")->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_subscribes');
    }
};
