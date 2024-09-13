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
            $table->string('cashier');
            $table->string('customer');
            $table->bigInteger('grandtotal');
            $table->enum('payment', ['cash','transfer']);
            $table->bigInteger('cash');
            $table->bigInteger('changes');
            $table->string('transfer_image')->nullable();
            $table->enum('status', ['print', 'pending', 'checkout']);
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