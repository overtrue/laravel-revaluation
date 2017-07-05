<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->unsignedInteger('total');
            $table->unsignedInteger('paid_in');
            $table->unsignedInteger('postage');
            $table->timestamps();
        });

        \DB::table('orders')->insert([
            'title' => 'order1',
            'total' => 1234500,
            'postage' => 1,
            'paid_in' => 1234566,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
