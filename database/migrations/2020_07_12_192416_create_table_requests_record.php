<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRequestsRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests_record', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');
            $table->uuid('uuid_group');
            $table->text('input');
            $table->text('trace');
            $table->char('http_status', 3)->nullable()->index();
            $table->integer('attempts')->default(0);

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
        Schema::drop('requests_record');
    }
}
