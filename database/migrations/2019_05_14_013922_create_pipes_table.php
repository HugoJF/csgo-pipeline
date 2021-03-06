<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePipesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pipes', function (Blueprint $table) {
			$table->bigIncrements('id');

			$table->boolean('active');

			$table->string('key');
			$table->unsignedInteger('limit');
			$table->boolean('pop_on_limit')->default(false);

			$table->text('description');

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
		Schema::dropIfExists('pipes');
	}
}
