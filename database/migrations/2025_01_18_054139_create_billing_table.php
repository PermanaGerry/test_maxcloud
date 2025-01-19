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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("account_id")->nullable(false)->foreign("account_id")->references("id")->on("accounts");
            $table->bigInteger("user_subscribes_id")->nullable(false)->foreign("user_subscribes_id")->references("id")->on("user_subscribes");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing');
    }
};
