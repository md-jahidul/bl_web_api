<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMixedBundleOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mixed_bundle_offers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('internet');
            $table->string('minutes');
            $table->string('sms');
            $table->string('validity');
            $table->string('price');
            $table->string('offer_code');
            $table->string('tag');
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
        Schema::dropIfExists('mixed_bundle_offers');
    }
}
