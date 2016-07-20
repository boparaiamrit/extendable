<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomFieldsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('custom_fields', function (Blueprint $table) {
			$table->increments('id');
			$table->string('field_name', 255);
			$table->string('parent_type', 255);
			$table->unsignedInteger('parent_id');
			$table->string('string_value', 255)->nullable();
			$table->double('number_value')->nullable();
			$table->text('text_value')->nullable();
			$table->timestamp('date_value')->nullable();
			
			$table->unique(['field_name', 'parent_type', 'parent_id']);
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('custom_fields', function (Blueprint $table) {
			$table->drop();
		});
	}
}
