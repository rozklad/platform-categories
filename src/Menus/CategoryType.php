<?php

namespace Sanatorium\Categories\Menus;

use Platform\Menus\Models\Menu;
use Platform\Menus\Types\AbstractType;
use Platform\Menus\Types\TypeInterface;

class CategoryType extends AbstractType implements TypeInterface
{
    /**
     * Holds all the available categorys.
     *
     * @var \Sanatorium\Categories\Models\Category
     */
    protected $categories = null;

    /**
     * {@inheritDoc}
     */
    public function getIdentifier()
    {
        return 'category';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'Category';
    }

    /**
     * {@inheritDoc}
     */
    public function getFormHtml(Menu $child = null)
    {
        $categories = $this->getCategories();

        return $this->view->make("sanatorium/categories::types/form", compact('child', 'categories'));
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplateHtml()
    {
        $categories = $this->getCategories();

        return $this->view->make("sanatorium/categories::types/template", compact('categories'));
    }

    /**
     * {@inheritDoc}
     */
    public function afterSave(Menu $child)
    {
        $data = $child->getAttributes();

        if ($categoryId = array_get($data, 'category_id')) {
            $category = $this->app['sanatorium.categories.category']->find($categoryId);

            $child->page_id = $categoryId;

            $child->uri = $category->url;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function beforeDelete(Menu $child)
    {
    }

    /**
     * Return all the available categorys.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getCategories()
    {
        if (! is_null($this->categories)) {
            return $this->categories;
        }

        $categories = $this->app['sanatorium.categories.category']->findAll();

        foreach ($categories as $category) {
            $category->uri = $category->url === '/' ? null : $category->url;
        }

        return $this->categories = $categories;
    }
}
