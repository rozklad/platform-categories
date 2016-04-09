<?php namespace Sanatorium\Categories\Controllers\Admin;

use Platform\Access\Controllers\AdminController;
use Sanatorium\Categories\Repositories\Category\CategoryRepositoryInterface;

class CategoriesController extends AdminController {

	/**
	 * {@inheritDoc}
	 */
	protected $csrfWhitelist = [
		'executeAction',
	];

	/**
	 * The Categories repository.
	 *
	 * @var \Sanatorium\Categories\Repositories\Categories\CategoriesRepositoryInterface
	 */
	protected $categories;

	/**
	 * Holds all the mass actions we can execute.
	 *
	 * @var array
	 */
	protected $actions = [
		'delete',
		'enable',
		'disable',
	];

	/**
	 * Constructor.
	 *
	 * @param  \Sanatorium\Categories\Repositories\Category\CategoryRepositoryInterface  $categories
	 * @return void
	 */
	public function __construct(CategoryRepositoryInterface $categories)
	{
		parent::__construct();

		$this->categories = $categories;
	}

	/**
	 * Display a listing of categories.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		return view('sanatorium/categories::categories.index');
	}

	/**
	 * Datasource for the categories Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
	{
		$data = $this->categories->grid();

		$columns = [
			'*',
		];

		$settings = [
			'sort'      => 'created_at',
			'direction' => 'desc',
		];

		$transformer = function($element)
		{
			$element->edit_uri = route('admin.sanatorium.categories.categories.edit', $element->id);

			return $element;
		};

		return datagrid($data, $columns, $settings, $transformer);
	}

	/**
	 * Show the form for creating new categories.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return $this->showForm('create');
	}

	/**
	 * Handle posting of the form for creating new categories.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store()
	{
		return $this->processForm('create');
	}

	/**
	 * Show the form for updating categories.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function edit($id)
	{
		return $this->showForm('update', $id);
	}

	/**
	 * Handle posting of the form for updating categories.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update($id)
	{
		return $this->processForm('update', $id);
	}

	/**
	 * Remove the specified categories.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($id)
	{
		$type = $this->categories->delete($id) ? 'success' : 'error';

		$this->alerts->{$type}(
			trans("sanatorium/categories::categories/message.{$type}.delete")
		);

		return redirect()->route('admin.sanatorium.categories.categories.all');
	}

	/**
	 * Executes the mass action.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function executeAction()
	{
		$action = request()->input('action');

		if (in_array($action, $this->actions))
		{
			foreach (request()->input('rows', []) as $row)
			{
				$this->categories->{$action}($row);
			}

			return response('Success');
		}

		return response('Failed', 500);
	}

	/**
	 * Shows the form.
	 *
	 * @param  string  $mode
	 * @param  int  $id
	 * @return mixed
	 */
	protected function showForm($mode, $id = null)
	{
		// Do we have a categories identifier?
		if (isset($id))
		{
			if ( ! $category = $this->categories->find($id))
			{
				$this->alerts->error(trans('sanatorium/categories::categories/message.not_found', compact('id')));

				return redirect()->route('admin.sanatorium.categories.categories.all');
			}
		}
		else
		{
			$category = $this->categories->createModel();
		}

		// List all categories for parent selection
		$categories = $this->categories->all();

		// Show the page
		return view('sanatorium/categories::categories.form', compact('mode', 'category', 'categories'));
	}

	/**
	 * Processes the form.
	 *
	 * @param  string  $mode
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	protected function processForm($mode, $id = null)
	{
		// Store the categories
		list($messages) = $this->categories->store($id, request()->all());

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			$this->alerts->success(trans("sanatorium/categories::categories/message.success.{$mode}"));

			return redirect()->route('admin.sanatorium.categories.categories.all');
		}

		$this->alerts->error($messages, 'form');

		return redirect()->back()->withInput();
	}

}
