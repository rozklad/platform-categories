<?php

use Illuminate\Foundation\Application;
use Cartalyst\Extensions\ExtensionInterface;
use Cartalyst\Settings\Repository as Settings;
use Cartalyst\Permissions\Container as Permissions;

return [

  /*
  |--------------------------------------------------------------------------
  | Name
  |--------------------------------------------------------------------------
  |
  | This is your extension name and it is only required for
  | presentational purposes.
  |
  */

  'name' => 'Categories',

  /*
  |--------------------------------------------------------------------------
  | Slug
  |--------------------------------------------------------------------------
  |
  | This is your extension unique identifier and should not be changed as
  | it will be recognized as a new extension.
  |
  | Ideally, this should match the folder structure within the extensions
  | folder, but this is completely optional.
  |
  */

  'slug' => 'sanatorium/categories',

  /*
  |--------------------------------------------------------------------------
  | Author
  |--------------------------------------------------------------------------
  |
  | Because everybody deserves credit for their work, right?
  |
  */

  'author' => 'Sanatorium',

  /*
  |--------------------------------------------------------------------------
  | Description
  |--------------------------------------------------------------------------
  |
  | One or two sentences describing the extension for users to view when
  | they are installing the extension.
  |
  */

  'description' => 'Categories',

  /*
  |--------------------------------------------------------------------------
  | Version
  |--------------------------------------------------------------------------
  |
  | Version should be a string that can be used with version_compare().
  | This is how the extensions versions are compared.
  |
  */

  'version' => '3.0',

  /*
  |--------------------------------------------------------------------------
  | Requirements
  |--------------------------------------------------------------------------
  |
  | List here all the extensions that this extension requires to work.
  | This is used in conjunction with composer, so you should put the
  | same extension dependencies on your main composer.json require
  | key, so that they get resolved using composer, however you
  | can use without composer, at which point you'll have to
  | ensure that the required extensions are available.
  |
  */

  'require' => [
    'platform/menus',
    'platform/attributes',
  ],

  /*
  |--------------------------------------------------------------------------
  | Autoload Logic
  |--------------------------------------------------------------------------
  |
  | You can define here your extension autoloading logic, it may either
  | be 'composer', 'platform' or a 'Closure'.
  |
  | If composer is defined, your composer.json file specifies the autoloading
  | logic.
  |
  | If platform is defined, your extension receives convetion autoloading
  | based on the Platform standards.
  |
  | If a Closure is defined, it should take two parameters as defined
  | bellow:
  |
  | object \Composer\Autoload\ClassLoader      $loader
  | object \Illuminate\Foundation\Application  $app
  |
  | Supported: "composer", "platform", "Closure"
  |
  */

  'autoload' => 'composer',

  /*
  |--------------------------------------------------------------------------
  | Service Providers
  |--------------------------------------------------------------------------
  |
  | Define your extension service providers here. They will be dynamically
  | registered without having to include them in app/config/app.php.
  |
  */

  'providers' => [

    'Sanatorium\Categories\Providers\CategoryServiceProvider',

  ],

  /*
  |--------------------------------------------------------------------------
  | Routes
  |--------------------------------------------------------------------------
  |
  | Closure that is called when the extension is started. You can register
  | any custom routing logic here.
  |
  | The closure parameters are:
  |
  | object \Cartalyst\Extensions\ExtensionInterface  $extension
  | object \Illuminate\Foundation\Application        $app
  |
  */

  'routes' => function(ExtensionInterface $extension, Application $app)
  {
    Route::group([
        'prefix'    => admin_uri().'/categories/categories',
        'namespace' => 'Sanatorium\Categories\Controllers\Admin',
      ], function()
      {
        Route::get('/' , ['as' => 'admin.sanatorium.categories.categories.all', 'uses' => 'CategoriesController@index']);
        Route::post('/', ['as' => 'admin.sanatorium.categories.categories.all', 'uses' => 'CategoriesController@executeAction']);

        Route::get('grid', ['as' => 'admin.sanatorium.categories.categories.grid', 'uses' => 'CategoriesController@grid']);

        Route::get('create' , ['as' => 'admin.sanatorium.categories.categories.create', 'uses' => 'CategoriesController@create']);
        Route::post('create', ['as' => 'admin.sanatorium.categories.categories.create', 'uses' => 'CategoriesController@store']);

        Route::get('{id}'   , ['as' => 'admin.sanatorium.categories.categories.edit'  , 'uses' => 'CategoriesController@edit']);
        Route::post('{id}'  , ['as' => 'admin.sanatorium.categories.categories.edit'  , 'uses' => 'CategoriesController@update']);

        Route::delete('{id}', ['as' => 'admin.sanatorium.categories.categories.delete', 'uses' => 'CategoriesController@delete']);
      });

    Route::group([
      'prefix'    => 'categories/categories',
      'namespace' => 'Sanatorium\Categories\Controllers\Frontend',
    ], function()
    {
      Route::get('/', ['as' => 'sanatorium.categories.categories.index', 'uses' => 'CategoriesController@index']);
    });

    $categories = app('sanatorium.categories.category')->getAllUrls();

    foreach ( $categories as $category_url ) {
      Route::get($category_url, 'Sanatorium\Categories\Controllers\Frontend\CategoriesController@categoryBySlug');     # category page
    }

    foreach ( $categories as $category_url ) {
      Route::get($category_url . '/{slug}', ['as' => 'sanatorium.categories.product.view', 'uses' => 'Sanatorium\Shop\Controllers\Frontend\ProductsController@productBySlug']);     # product detail
    }

    if ( empty( $categories ) ) {
      Route::get('product/{slug}', ['as' => 'sanatorium.categories.product.view', 'uses' => 'Sanatorium\Shop\Controllers\Frontend\ProductsController@productBySlug']); # product detail
    }

  },

  /*
  |--------------------------------------------------------------------------
  | Database Seeds
  |--------------------------------------------------------------------------
  |
  | Platform provides a very simple way to seed your database with test
  | data using seed classes. All seed classes should be stored on the
  | `database/seeds` directory within your extension folder.
  |
  | The order you register your seed classes on the array below
  | matters, as they will be ran in the exact same order.
  |
  | The seeds array should follow the following structure:
  |
  | Vendor\Namespace\Database\Seeds\FooSeeder
  | Vendor\Namespace\Database\Seeds\BarSeeder
  |
  */

  'seeds' => [

  ],

  /*
  |--------------------------------------------------------------------------
  | Permissions
  |--------------------------------------------------------------------------
  |
  | Register here all the permissions that this extension has. These will
  | be shown in the user management area to build a graphical interface
  | where permissions can be selected to allow or deny user access.
  |
  | For detailed instructions on how to register the permissions, please
  | refer to the following url https://cartalyst.com/manual/permissions
  |
  */

  'permissions' => function(Permissions $permissions)
  {
    $permissions->group('category', function($g)
    {
      $g->name = 'Categories';

      $g->permission('category.index', function($p)
      {
        $p->label = trans('sanatorium/categories::categories/permissions.index');

        $p->controller('Sanatorium\Categories\Controllers\Admin\CategoriesController', 'index, grid');
      });

      $g->permission('category.create', function($p)
      {
        $p->label = trans('sanatorium/categories::categories/permissions.create');

        $p->controller('Sanatorium\Categories\Controllers\Admin\CategoriesController', 'create, store');
      });

      $g->permission('category.edit', function($p)
      {
        $p->label = trans('sanatorium/categories::categories/permissions.edit');

        $p->controller('Sanatorium\Categories\Controllers\Admin\CategoriesController', 'edit, update');
      });

      $g->permission('category.delete', function($p)
      {
        $p->label = trans('sanatorium/categories::categories/permissions.delete');

        $p->controller('Sanatorium\Categories\Controllers\Admin\CategoriesController', 'delete');
      });
    });
  },

  /*
  |--------------------------------------------------------------------------
  | Widgets
  |--------------------------------------------------------------------------
  |
  | Closure that is called when the extension is started. You can register
  | all your custom widgets here. Of course, Platform will guess the
  | widget class for you, this is just for custom widgets or if you
  | do not wish to make a new class for a very small widget.
  |
  */

  'widgets' => function()
  {

  },

  /*
  |--------------------------------------------------------------------------
  | Settings
  |--------------------------------------------------------------------------
  |
  | Register any settings for your extension. You can also configure
  | the namespace and group that a setting belongs to.
  |
  */

  'settings' => function(Settings $settings, Application $app)
  {

  },

  /*
  |--------------------------------------------------------------------------
  | Menus
  |--------------------------------------------------------------------------
  |
  | You may specify the default various menu hierarchy for your extension.
  | You can provide a recursive array of menu children and their children.
  | These will be created upon installation, synchronized upon upgrading
  | and removed upon uninstallation.
  |
  | Menu children are automatically put at the end of the menu for extensions
  | installed through the Operations extension.
  |
  | The default order (for extensions installed initially) can be
  | found by editing app/config/platform.php.
  |
  */

  'menus' => [

    'admin' => [
      [
        'class' => 'fa fa-list-ul',
        'name' => 'Categories',
        'uri' => 'categories/categories',
        'regex' => '/:admin\/categories\/category/i',
        'slug' => 'admin-sanatorium-categories-category',
      ],
    ],
    'main' => [

    ],
  ],

  /*
  |--------------------------------------------------------------------------
  | Integrity
  |--------------------------------------------------------------------------
  |
  */


  'integrity' => [

    [

      'name' => 'cviebrock\eloquent-sluggable is available',
      'test' => ['Sanatorium\Categories\Providers\CategoryServiceProvider', 'checkEloquentSluggable']
    
    ]

  ],

];
