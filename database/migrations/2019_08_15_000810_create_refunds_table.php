<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTimeTz('date')->nullable();
            $table->string('type', 30);
            $table->text('description');
            $table->decimal('value', 10, 2);
            $table->string('receipt', 20)->nullable($value = true);
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('block')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refunds');
    }
}
