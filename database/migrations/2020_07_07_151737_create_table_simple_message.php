<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSimpleMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('simple_message', function (Blueprint $table) {
            $table->integer('from_id');
            $table->integer('to_id');
            $table->string('msg', 200);
            $table->tinyInteger('read')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('readed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('simple_message');
    }
}
