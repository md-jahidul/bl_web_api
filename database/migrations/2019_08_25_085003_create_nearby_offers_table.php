<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNearbyOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nearby_offers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('vendor');
            $table->string('location');
            $table->string('type');
            $table->string('offer');
            $table->string('image');
            $table->string('offer_code');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nearby_offers');
    }
}
