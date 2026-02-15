<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->index(['state', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['offer_id', 'state']);
            $table->index(['offer_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropIndex(['state', 'created_at']);
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['offer_id', 'state']);
            $table->dropIndex(['offer_id', 'created_at']);
        });
    }
};
