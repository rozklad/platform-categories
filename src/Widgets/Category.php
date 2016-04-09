<?php namespace Sanatorium\Categories\Widgets;

class Category {

	public function show($object, $root = 0, $id = 'default-tree')
	{
		// Get Categories
		$this->categories = app('Sanatorium\Categories\Repositories\Category\CategoryRepositoryInterface');

		$categories = $this->categories->tree();

		// Get object categories
		$active_categories = $object->categories->pluck('id')->toArray();

		return view('sanatorium/categories::widgets/tree', compact('categories', 'object', 'root', 'id', 'active_categories'));
	}

}
