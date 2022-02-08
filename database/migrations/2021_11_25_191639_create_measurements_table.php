<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeasurementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measurements', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('unit');
            $table->integer('netto')->nullable();
            $table->integer('brutto')->nullable();
            $table->string('product');
            $table->string('plate')->nullable();
            $table->string('customer')->nullable();
            $table->string('driver')->nullable();
            $table->string('notes')->nullable();
            $table->foreignId('modified_by_id')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('measurements');
    }
}
