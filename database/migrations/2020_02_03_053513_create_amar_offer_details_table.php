<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmarOfferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amar_offer_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('details_en')->nullable();
            $table->string('details_bn')->nullable();
            $table->tinyInteger('type')->comment('1=internet,2=voice,3=bundle');
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
        Schema::dropIfExists('amar_offer_details');
    }
}
