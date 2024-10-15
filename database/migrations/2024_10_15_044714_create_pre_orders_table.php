<?php

use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_orders', function (Blueprint $table) {
            $table->id();
            $table->text('customer');
            $table->text('customer_contact');
            $table->text('keterangan')->nullable();
            $table->text('total_price');
            $table->enum('payment', ['cash', 'transfer'])->nullable();
            $table->text('changes')->nullable();
            $table->text('transfer_img')->nullable();
            $table->enum('status', ['payment', 'done'])->default('payment');
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
        Schema::dropIfExists('pre_orders');
    }
}
