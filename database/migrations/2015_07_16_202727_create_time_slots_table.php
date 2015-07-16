<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_slots', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('showtime_id')->unsigned();
            $table->foreign('showtime_id')->references('id')->on('showtimes');
            $table->string('time', 20);
            $table->integer('showId')->unique();
            $table->string('location');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('time_slots');
    }
}
