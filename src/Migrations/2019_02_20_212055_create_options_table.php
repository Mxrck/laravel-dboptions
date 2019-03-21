<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 192);
            $table->text('value');
            $table->boolean('autoload')->default(false);
            $table->boolean('public')->default(false);
            $table->nullableMorphs('optionable', 'optionable_unique');

            $table->index(['optionable_id', 'optionable_type']);
            $table->unique(['key', 'optionable_type', 'optionable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('options');
    }
}
