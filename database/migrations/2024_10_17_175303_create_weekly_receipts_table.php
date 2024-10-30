<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeeklyReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weekly_receipts', function (Blueprint $table) {
            $table->id();
            $table->text('admin');
            $table->enum('type', ['stock', 'po'])->default('stock');
            $table->text('id_material');
            $table->integer('qty');
            $table->text('id_unit');
            $table->decimal('price', 10,2);
            $table->decimal('total', 10,2);
            $table->text('information')->nullable();
            $table->date('purchase_date');
            $table->enum('status', ['pending', 'purchased'])->default('pending');
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
        Schema::dropIfExists('weekly_receipts');
    }
}
