<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('columns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('status_name');
            $table->timestamps();
        });

        DB::table('columns')->insert([
            ['title' => 'To Do', 'status_name' =>'todo'],
            ['title' => 'In Progress', 'status_name' => 'inProgress'],
            ['title' => 'Done', 'status_name' => 'done'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('columns');
    }
}
