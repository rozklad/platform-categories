<?php namespace Sanatorium\Categories\Traits;

trait CategoryTrait
{

    public function categories()
    {
        return $this->morphToMany('Sanatorium\Categories\Models\Category', 'categorized', 'shop_categorized');
    }

}