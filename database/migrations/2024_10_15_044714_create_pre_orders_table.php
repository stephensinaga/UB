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
            $table->decimal('total_price', 10,2);
            $table->enum('payment', ['cash', 'transfer'])->nullable();
            $table->decimal('cash', 10,2)->nullable();
            $table->text('transfer_img')->nullable();
            $table->enum('progress', ['pending', 'inProgress', 'done'])->default('pending');
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
