<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('no_invoice');
            $table->integer('no_meja')->nullable();
            $table->string('cashier')->nullable();
            $table->string('customer')->nullable();
            $table->bigInteger('grandtotal');
            $table->enum('payment', ['cash','transfer'])->nullable();
            $table->bigInteger('cash')->nullable();
            $table->bigInteger('changes')->nullable();
            $table->string('transfer_image')->nullable();
            $table->enum('status', ['debt', 'pending', 'checkout']);
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
        Schema::dropIfExists('main_orders');
    }
}
