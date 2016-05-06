<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCityListingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_listing', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('listing_id');
            $table->integer('city_id');

            $table->index('listing_id');
            $table->index('city_id');
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
        Schema::drop('city_listing');
    }
}
