<?php namespace Sanatorium\Categories\Providers;

use Cartalyst\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class CategoryServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		// Register the attributes namespace
		$this->app['platform.attributes.manager']->registerNamespace(
			$this->app['Sanatorium\Categories\Models\Category']
		);

		// Register the menu category type
        $this->app['platform.menus.manager']->registerType(
            $this->app['platform.menus.types.category']
        );

		// Register category as category
        AliasLoader::getInstance()->alias('Category', 'Sanatorium\Categories\Models\Category');  

		// Subscribe the registered event handler
		$this->app['events']->subscribe('sanatorium.categories.category.handler.event');

		// Register the Blade @categories widget.
		$this->registerBladeCategoriesWidget();

		// Register cviebrock/eloquent-sluggable
		$this->registerCviebrockEloquentSluggablePackage();
	}

	/**
	 * Register cviebrock/eloquent-sluggable
	 * @return
	 */
	protected function registerCviebrockEloquentSluggablePackage() {
		$serviceProvider = 'Cviebrock\EloquentSluggable\SluggableServiceProvider';

		if (!$this->app->getProvider($serviceProvider)) {
			$this->app->register($serviceProvider);
		}
	}


	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		// Register the repository
		$this->bindIf('sanatorium.categories.category', 'Sanatorium\Categories\Repositories\Category\CategoryRepository');

		// Register the data handler
		$this->bindIf('sanatorium.categories.category.handler.data', 'Sanatorium\Categories\Handlers\Category\CategoryDataHandler');

		// Register the event handler
		$this->bindIf('sanatorium.categories.category.handler.event', 'Sanatorium\Categories\Handlers\Category\CategoryEventHandler');

		// Register the validator
		$this->bindIf('sanatorium.categories.category.validator', 'Sanatorium\Categories\Validator\Category\CategoryValidator');
	
		// Register the menus 'category' type
        $this->bindIf('platform.menus.types.category', 'Sanatorium\Categories\Menus\CategoryType', true, false);
	}

	/**
     * Register the Blade @categories widget.
     *
     * @return void
     */
	public function registerBladeCategoriesWidget()
	{
        $this->app['blade.compiler']->directive('categories', function ($value) {
            return "<?php echo Widget::make('sanatorium/categories::category.show', array$value); ?>";
        });
	}

	/**
	 * Function used for integrity checks
	 */
	public static function checkEloquentSluggable()
	{
		$sluggableTrait = 'Cviebrock\EloquentSluggable\SluggableTrait';

		/**
		 * Dependency is not available
		 */
		if ( !class_exists($sluggableTrait) ) 
			return false; 

		return true;
	}

}
