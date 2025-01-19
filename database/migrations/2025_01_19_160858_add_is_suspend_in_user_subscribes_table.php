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
        Schema::table('user_subscribes', function (Blueprint $table) {
            $table->string("vps")->nullable(true)->after("package_subscribes_id");
            $table->dateTime("expired_at")->nullable(true)->after("vps");
            $table->boolean("is_suspend")->default(false)->after("expired_at");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscribes', function (Blueprint $table) {
            $table->dropColumn("vps");
            $table->dropColumn("expired_at");
            $table->dropColumn("is_suspend");
        });
    }
};
