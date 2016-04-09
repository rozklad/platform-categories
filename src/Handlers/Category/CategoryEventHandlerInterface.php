<?php namespace Sanatorium\Categories\Handlers\Category;

use Sanatorium\Categories\Models\Category;
use Cartalyst\Support\Handlers\EventHandlerInterface as BaseEventHandlerInterface;

interface CategoryEventHandlerInterface extends BaseEventHandlerInterface {

	/**
	 * When a category is being created.
	 *
	 * @param  array  $data
	 * @return mixed
	 */
	public function creating(array $data);

	/**
	 * When a category is created.
	 *
	 * @param  \Sanatorium\Categories\Models\Category  $category
	 * @return mixed
	 */
	public function created(Category $category);

	/**
	 * When a category is being updated.
	 *
	 * @param  \Sanatorium\Categories\Models\Category  $category
	 * @param  array  $data
	 * @return mixed
	 */
	public function updating(Category $category, array $data);

	/**
	 * When a category is updated.
	 *
	 * @param  \Sanatorium\Categories\Models\Category  $category
	 * @return mixed
	 */
	public function updated(Category $category);

	/**
	 * When a category is deleted.
	 *
	 * @param  \Sanatorium\Categories\Models\Category  $category
	 * @return mixed
	 */
	public function deleted(Category $category);

}
