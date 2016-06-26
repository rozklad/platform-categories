<?php namespace Sanatorium\Categories\Repositories\Category;

use Cartalyst\Support\Traits;
use Illuminate\Container\Container;
use Symfony\Component\Finder\Finder;
use DB;

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
			// Resluggify
			if ( method_exists($category, 'resluggify') )
				$category->fill($data)->resluggify()->save();
			else
				$category->fill($data)->save();

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
			// Resluggify
            if ( method_exists($category, 'resluggify') )
            	$category->fill($data)->resluggify()->save();
			else
				$category->fill($data)->save();

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

	/**
	 * Because MySQL is not made for hierarchical data by default,
	 * but we use them for nice-urls, this function is going to produce
	 * query like this:
	 *  SELECT t1.slug AS level1, t2.slug as level2, t3.slug as level3, t4.slug as level4, t5.slug as level5
	 * 	FROM _categories AS t1
	 * 	LEFT JOIN _categories AS t2 ON t2.parent = t1.id
	 * 	LEFT JOIN _categories AS t3 ON t3.parent = t2.id
	 * 	LEFT JOIN _categories AS t4 ON t4.parent = t3.id
	 * 	LEFT JOIN _categories AS t5 ON t5.parent = t4.id
	 * 	WHERE t1.parent = 0;
	 *
	 * The result looks like this:
	 * 	  	0 => "ortopedicke-pomucky"
	 * 		1 => "ortopedicke-pomucky/krcni-limce"
	 * 		2 => "ortopedicke-pomucky/krcni-limce/krcni-limec-adams"
	 *
	 * @param int $category_depth
	 * @param int $root_depth
	 */
	public function getAllUrls($category_depth = 5, $root_depth = 1)
	{
		return $this->container['cache']->rememberForever('sanatorium.categories.category.urls', function() use ($category_depth, $root_depth)
		{
			$output = [];

			$table = $this->createModel()->getTable();

			$categories = DB::table( $table . " AS t".$root_depth );

			$select = [];

			for ( $i = $root_depth; $i <= $category_depth; $i++ )
			{
				$select[] = "t{$i}.slug AS level{$i}";

				if ( $i > $root_depth )
				{
					$categories->leftJoin($table . " AS t{$i}", "t{$i}.parent", "=", "t".($i-1).".id");
				}
			}

			$categories->select($select);

			$categories->where("t{$root_depth}.parent", 0);

			foreach( $categories->get() as $categoryRow )
			{
				$row = [];
				foreach ( $categoryRow as $index => $slug )
				{
					$row[] = $slug;
					if ( $slug )
						$output[implode('/', $row)] = implode('/', $row);
				}
			}

			return $output;
		});

	}

	public function getAllUrlsAndNamesTree($category_depth = 5, $root_depth = 1)
	{
		$categories = $this->getAllUrlsAndNames($category_depth, $root_depth);

		$output = [];

		foreach( $categories as $key => $value )
		{

		}

		return $output;
	}

	public function getAllUrlsAndNames($category_depth = 5, $root_depth = 1)
	{
		return $this->container['cache']->rememberForever('sanatorium.categories.category.urls_and_names', function() use ($category_depth, $root_depth)
		{
			$output = [];

			$table = $this->createModel()->getTable();

			$categories = DB::table( $table . " AS t".$root_depth );

			$select = [];

			// Select slugs
			for ( $i = $root_depth; $i <= $category_depth; $i++ )
			{
				$select[] = "t{$i}.slug AS level{$i}";

				if ( $i > $root_depth )
				{
					$categories->leftJoin($table . " AS t{$i}", "t{$i}.parent", "=", "t".($i-1).".id");
				}
			}

			$categories->leftJoin("attributes AS attributes", function($join){
				$join->on(DB::raw('1'), '=', DB::raw('1'));
			});

			$select[] = "attributes.id AS category_attribute_id";

			// Select titles
			for ( $i = $root_depth; $i <= $category_depth; $i++ )
			{
				$select[] = "vals{$i}.value AS title{$i}";

				$categories->leftJoin("attribute_values AS vals{$i}", function($join) use ($i) {
					$join->on("attributes.id", "=", "vals{$i}.attribute_id")
						->on("vals{$i}.entity_id", "=", "t{$i}.id");
				});
			}

			$categories->select($select);

			$categories->where("t{$root_depth}.parent", 0)
				->where('attributes.slug', "=", 'category_title');

			// @todo: make fluid depth
			foreach( $categories->get() as $categoryRow )
			{
				$slug = [];
				$title = null;

				$last_level = $output;

				$i = 1;

				$level_slug = $categoryRow->{'level'.$i};
				$level_title = $categoryRow->{'title'.$i};

				if ( $level_slug )
				{
					if ( !isset($output[$level_slug]) )
					{
						$output[$level_slug] = [
							'title' => $level_title,
							'children' => []
						];
					}
				}

				$i = 2;

				$sublevel_slug = $categoryRow->{'level'.$i};
				$sublevel_title = $categoryRow->{'title'.$i};

				if ( $level_slug )
				{
					if ( !isset($output[$level_slug]) )
					{
						$output[$level_slug]  = [
							'title' => $level_title,
							'children' => []
						];
					}

					if ( !isset($output[$level_slug]['children'][$sublevel_slug]) )
					{
						$output[$level_slug]['children'][$sublevel_slug] = [
							'title' => $sublevel_title
						];
					}
				}

			}

			return $output;
		});

	}

}
