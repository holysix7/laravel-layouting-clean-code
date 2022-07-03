<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppLayoutBodiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_layout_bodies', function (Blueprint $table) {
            $table->id();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->boolean('isactive')->default(true);
            $table->integer('app_layout_id')->nullable();
            $table->string('stroke')->nullable();
            $table->integer('left')->nullable();
            $table->integer('top')->nullable();
            $table->integer('stroke_width')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_layout_bodies');
    }
}
