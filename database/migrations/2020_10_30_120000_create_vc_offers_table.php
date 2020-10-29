<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVcOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vc_offers', function (Blueprint $table) {
            $table->string('id', 255)->unique();
            $table->mediumText('credential');
            $table->dateTime('expires');
            $table->boolean('issued')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vc_offers');
    }
}

