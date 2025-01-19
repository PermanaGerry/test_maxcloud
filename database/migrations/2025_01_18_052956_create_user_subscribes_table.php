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
        Schema::create('user_subscribes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("user_id")
                ->nullable(false)
                ->foreign("user_id")
                ->references("id")
                ->on("users");
            $table->bigInteger("package_subscribes_id")
                ->nullable(false)
                ->foreign("package_subscribes_id")
                ->references("id")
                ->on("package_subscribes");
            $table->boolean("is_active")->default(true);
            $table->timestamps();
            // create indexes
            $table->index("user_id");
            $table->index("package_subscribes_id");
            $table->index(["user_id", "package_subscribes_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscribes');
    }
};
