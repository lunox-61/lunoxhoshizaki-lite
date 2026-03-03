<?php

use LunoxHoshizaki\Database\Schema\Schema;
use LunoxHoshizaki\Database\Schema\Blueprint;

class CreateFlightsTable
{
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('flights');
    }
}