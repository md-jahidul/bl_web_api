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
        Schema::create('page_component_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('component_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('key')->nullable();
            $table->text('value_en')->nullable();
            $table->text('value_bn')->nullable();
            $table->integer('group')->default(0);
            $table->timestamps();
            // $table->foreign('component_id')
            //     ->references('id')
            //     ->on('page_components')
            //     ->onDelete('cascade')
            //     ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_component_data');
    }
};
