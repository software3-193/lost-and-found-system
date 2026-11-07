<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 10); // lost | found
            $table->string('title', 120);
            $table->string('category', 60);
            $table->text('description');
            $table->string('location', 150);
            $table->date('item_date');
            $table->string('status', 20)->default('pending'); // pending | published | rejected | returned
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index(['status', 'category', 'item_date']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
