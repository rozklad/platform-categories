<?php namespace Sanatorium\Categories\Repositories\Category;

interface CategoryRepositoryInterface {

	/**
	 * Returns a dataset compatible with data grid.
	 *
	 * @return \Sanatorium\Categories\Models\Category
	 */
	public function grid();

	/**
	 * Returns all the categories entries.
	 *
	 * @return \Sanatorium\Categories\Models\Category
	 */
	public function findAll();

	/**
	 * Returns a categories entry by its primary key.
	 *
	 * @param  int  $id
	 * @return \Sanatorium\Categories\Models\Category
	 */
	public function find($id);

	/**
	 * Determines if the given categories is valid for creation.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	public function validForCreation(array $data);

	/**
	 * Determines if the given categories is valid for update.
	 *
	 * @param  int  $id
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	public function validForUpdate($id, array $data);

	/**
	 * Creates or updates the given categories.
	 *
	 * @param  int  $id
	 * @param  array  $input
	 * @return bool|array
	 */
	public function store($id, array $input);

	/**
	 * Creates a categories entry with the given data.
	 *
	 * @param  array  $data
	 * @return \Sanatorium\Categories\Models\Category
	 */
	public function create(array $data);

	/**
	 * Updates the categories entry with the given data.
	 *
	 * @param  int  $id
	 * @param  array  $data
	 * @return \Sanatorium\Categories\Models\Category
	 */
	public function update($id, array $data);

	/**
	 * Deletes the categories entry.
	 *
	 * @param  int  $id
	 * @return bool
	 */
	public function delete($id);

}
