<?php namespace Sanatorium\Categories\Models;

use Cartalyst\Attributes\EntityInterface;
use Illuminate\Database\Eloquent\Model;
use Platform\Attributes\Traits\EntityTrait;
use Cartalyst\Support\Traits\NamespacedEntityTrait;
use Cviebrock\EloquentSluggable\SluggableTrait;
use URL;
use StorageUrl;

class Category extends Model implements EntityInterface
{

	use EntityTrait, NamespacedEntityTrait, SluggableTrait;

	protected $sluggable = [
		'build_from' => 'category_title',
		'save_to'    => 'slug',
	];

	/**
	 * {@inheritDoc}
	 */
	protected $table = 'categories';

	/**
	 * {@inheritDoc}
	 */
	protected $guarded = [
		'id',
	];

	/**
	 * {@inheritDoc}
	 */
	protected $with = [
		'values.attribute',
	];

	/**
	 * {@inheritDoc}
	 */
	protected static $entityNamespace = 'sanatorium/categories.category';

	public function parents()
	{
		return $this->belongsTo('Sanatorium\Categories\Models\Category', 'parent', 'id')->with('parents');
	}

	public function children()
	{
		return $this->hasMany('Sanatorium\Categories\Models\Category', 'parent', 'id');
	}

	public function getParentsRecursive()
	{
		if ( $this->parent()->first() )
			return array_merge($this->parent()->first()->getParentsRecursive(), [$this->parent()->first()]);
		else
			return [];
	}

	public static function getFlatParents($cat)
	{
		if ( $cat['parents'] )
			return array_merge(self::getFlatParents($cat['parents']), [$cat['slug']]);
		else
			return [$cat['slug']];
	}

	public function getUrlAttribute()
	{
		$cat = Category::with('parents')->find($this->id)->toArray();

		// @todo make recursive call on parents
		return '/' . implode('/', self::getFlatParents($cat));
	}

	/*
        public function getUrlAttribute()
        {
            return route('sanatorium.categories.categories.view.'.$this->slug);
        }
      */

	public static function getClassesStringBySlug($slug = null)
	{
		return $slug;
	}

	/**
	 * Returns result by path segments
	 *
	 * @param  array $segments Array of path segments like (male, shirts)
	 * @return [type]           [description]
	 */
	public static function getByPath($segments)
	{
		$current = null;

		$model = get_called_class();

		foreach ( $segments as $key => $trace )
		{
			if ( $current )
				$current = $model::where('slug', $trace)->where('parent', $current->id)->first();
			else
				$current = $model::where('slug', $trace)->first();
		}

		return $current;
	}

	protected $cover_attribute = 'category_icon';

	public $cover_object;

	public $cover_image;

	public function getCategoryIconUrlAttribute()
	{
		if ( !$this->{$this->cover_attribute} )	// @todo: thumbnail
			return null;

		$medium = app('platform.media')->find($this->{$this->cover_attribute});

		if ( !is_object($medium) )
			return null;

		$this->cover_object = $medium;
		$this->cover_image = StorageUrl::url($medium->path);

		return $this->cover_image;

	}
}
