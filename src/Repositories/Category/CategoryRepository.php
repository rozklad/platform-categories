<?php namespace Sanatorium\Categories\Repositories\Category;

use Cartalyst\Support\Traits;
use Illuminate\Container\Container;
use Symfony\Component\Finder\Finder;

class CategoryRepository implements CategoryRepositoryInterface {

	use Traits\ContainerTrait, Traits\EventTrait, Traits\RepositoryTrait, Traits\ValidatorTrait;

	/**
	 * The Data handler.
	 *
	 * @var \Sanatorium\Categories\Handlers\Category\CategoryDataHandlerInterface
	 */
	protected $data;

	/**
	 * The Eloquent categories model.
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * Constructor.
	 *
	 * @param  \Illuminate\Container\Container  $app
	 * @return void
	 */
	public function __construct(Container $app)
	{
		$this->setContainer($app);

		$this->setDispatcher($app['events']);

		$this->data = $app['sanatorium.categories.category.handler.data'];

		$this->setValidator($app['sanatorium.categories.category.validator']);

		$this->setModel(get_class($app['Sanatorium\Categories\Models\Category']));
	}

	/**
	 * {@inheritDoc}
	 */
	public function grid()
	{
		return $this
			->createModel();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAll()
	{
		return $this->container['cache']->rememberForever('sanatorium.categories.category.all', function()
		{
			return $this->createModel()->get();
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function find($id)
	{
		return $this->container['cache']->rememberForever('sanatorium.categories.category.'.$id, function() use ($id)
		{
			return $this->createModel()->find($id);
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForCreation(array $input)
	{
		return $this->validator->on('create')->validate($input);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpdate($id, array $input)
	{
		return $this->validator->on('update')->validate($input);
	}

	/**
	 * {@inheritDoc}
	 */
	public function store($id, array $input)
	{
		return ! $id ? $this->create($input) : $this->update($id, $input);
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(array $input)
	{
		// Create a new category
		$category = $this->createModel();

		// Fire the 'sanatorium.categories.category.creating' event
		if ($this->fireEvent('sanatorium.categories.category.creating', [ $input ]) === false)
		{
			return false;
		}

		// Prepare the submitted data
		$data = $this->data->prepare($input);

		// Validate the submitted data
		$messages = $this->validForCreation($data);

		// Check if the validation returned any errors
		if ($messages->isEmpty())
		{
			// Save the category
			$category->fill($data)->save();

			// Resluggify
            if ( method_exists($category, 'resluggify') )
            	$category->resluggify()->save();

			// Fire the 'sanatorium.categories.category.created' event
			$this->fireEvent('sanatorium.categories.category.created', [ $category ]);
		}

		return [ $messages, $category ];
	}

	/**
	 * {@inheritDoc}
	 */
	public function update($id, array $input)
	{
		// Get the category object
		$category = $this->find($id);

		// Fire the 'sanatorium.categories.category.updating' event
		if ($this->fireEvent('sanatorium.categories.category.updating', [ $category, $input ]) === false)
		{
			return false;
		}

		// Prepare the submitted data
		$data = $this->data->prepare($input);

		// Validate the submitted data
		$messages = $this->validForUpdate($category, $data);

		// Check if the validation returned any errors
		if ($messages->isEmpty())
		{
			// Update the category
			$category->fill($data)->save();

			// Resluggify
            if ( method_exists($category, 'resluggify') )
            	$category->resluggify()->save();

			// Fire the 'sanatorium.categories.category.updated' event
			$this->fireEvent('sanatorium.categories.category.updated', [ $category ]);
		}

		return [ $messages, $category ];
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		// Check if the category exists
		if ($category = $this->find($id))
		{
			// Fire the 'sanatorium.categories.category.deleted' event
			$this->fireEvent('sanatorium.categories.category.deleted', [ $category ]);

			// Delete the category entry
			$category->delete();

			return true;
		}

		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function enable($id)
	{
		$this->validator->bypass();

		return $this->update($id, [ 'enabled' => true ]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function disable($id)
	{
		$this->validator->bypass();

		return $this->update($id, [ 'enabled' => false ]);
	}

	/**
	 * Category tree
	 * @return [type] [description]
	 */
	public function tree($root = 0)
	{
		$result = [];

		$categories = $this->createModel()->where('parent', $root)->get();

		foreach( $categories as $category ) {

			$category->children = $this->tree($category->id);

			$result[] = $category;

		}

		return $result;
	}

}
