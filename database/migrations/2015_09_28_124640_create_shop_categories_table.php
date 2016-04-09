<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('parent')->default('0');
			$table->string('slug');
			$table->timestamps();
		});

		$defaultAttributes = [
			[
				'name' 		=> 'Category title',
				'slug' 		=> 'category_title',
				'type' 		=> 'input',
				'enabled' 	=> 1
			],
			[
				'name' 		=> 'Category description',
				'slug' 		=> 'category_description',
				'type' 		=> 'textarea',
				'enabled' 	=> 1
			],
			[
				'name' 		=> 'Category long description',
				'slug' 		=> 'category_long_description',
				'type' 		=> 'textarea',
				'enabled' 	=> 1
			],
		];

		$attributes = app('platform.attributes');

		$attributes->fill($defaultAttributes);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('categories');
	}

}
