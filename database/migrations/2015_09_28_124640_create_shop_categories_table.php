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

		$attributeNamespace = Sanatorium\Categories\Models\Category::getEntityNamespace();

		$defaultAttributes = [
			[
				'name' 			=> 'Category title',
				'slug' 			=> 'category_title',
				'description' 	=> 'Category title',
				'type' 			=> 'input',
				'enabled' 		=> 1,
				'namespace' 	=> $attributeNamespace
			],
			[
				'name' 			=> 'Category description',
				'slug' 			=> 'category_description',
				'description' 	=> 'Category description',
				'type' 			=> 'textarea',
				'enabled' 		=> 1,
				'namespace' 	=> $attributeNamespace
			],
			[
				'name' 			=> 'Category long description',
				'slug' 			=> 'category_long_description',
				'description'	=> 'Category long description for category detail page',
				'type' 			=> 'wysiwyg',
				'enabled' 		=> 1,
				'namespace' 	=> $attributeNamespace
			],
			[
				'name' 			=> 'Category icon',
				'slug' 			=> 'category_icon',
				'description'	=> 'Category icon image',
				'type' 			=> 'image',
				'enabled' 		=> 1,
				'namespace' 	=> $attributeNamespace
			],
		];

		// Create the attributes
        $attribute = app('Platform\Attributes\Repositories\AttributeRepositoryInterface');

        foreach( $defaultAttributes as $defaultAttribute ) {
        	$attribute->create($defaultAttribute);
        }
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('categories');

		$attribute = app('Platform\Attributes\Repositories\AttributeRepositoryInterface');

		$attributeNamespace = Sanatorium\Categories\Models\Category::getEntityNamespace();

        $attributeIds = $attribute->createModel()->where('namespace', $attributeNamespace)->lists('id');

        foreach ($attributeIds as $id) {
            $attribute->delete($id);
        }
	}

}
