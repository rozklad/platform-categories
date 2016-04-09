<?php namespace Sanatorium\Categories\Handlers\Category;

use Illuminate\Events\Dispatcher;
use Sanatorium\Categories\Models\Category;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class CategoryEventHandler extends BaseEventHandler implements CategoryEventHandlerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('sanatorium.categories.category.creating', __CLASS__.'@creating');
		$dispatcher->listen('sanatorium.categories.category.created', __CLASS__.'@created');

		$dispatcher->listen('sanatorium.categories.category.updating', __CLASS__.'@updating');
		$dispatcher->listen('sanatorium.categories.category.updated', __CLASS__.'@updated');

		$dispatcher->listen('sanatorium.categories.category.deleted', __CLASS__.'@deleted');
	}

	/**
	 * {@inheritDoc}
	 */
	public function creating(array $data)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function created(Category $category)
	{
		$this->flushCache($category);
	}

	/**
	 * {@inheritDoc}
	 */
	public function updating(Category $category, array $data)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function updated(Category $category)
	{
		$this->flushCache($category);
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleted(Category $category)
	{
		$this->flushCache($category);
	}

	/**
	 * Flush the cache.
	 *
	 * @param  \Sanatorium\Categories\Models\Category  $category
	 * @return void
	 */
	protected function flushCache(Category $category)
	{
		$this->app['cache']->forget('sanatorium.categories.category.all');

		$this->app['cache']->forget('sanatorium.categories.category.'.$category->id);
	}

}
