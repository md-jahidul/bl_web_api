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
        Schema::create('page_components', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('status')->default(0);
            $table->timestamps();
            $table->foreign('page_id')
                ->references('id')
                ->on('pages')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_components');
    }
};
