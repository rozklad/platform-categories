<?php namespace Sanatorium\Categories\Controllers\Frontend;

use Platform\Foundation\Controllers\Controller;
use Sanatorium\Categories\Models\Category;
use Product;
use Exception;

class CategoriesController extends Controller
{

    /**
     * Return the main view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('sanatorium/categories::index');
    }

    /**
     * @TODO: remake - slug unused
     */
    public function categoryBySlug($slug = null)
    {
        $url = \Request::url();
        $segments = explode('/', $url);

        // First three segments are http (2x) and domain name
        unset($segments[0]);
        unset($segments[1]);
        unset($segments[2]);

        return $this->category(Category::getByPath($segments));
    }

    /**
     * @param Sanatorium\Categories\Models\Category $category
     * @param int                                   $per_page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|null
     */
    public function category($category = null, $per_page = 0)
    {
        try
        {
            if ( !$category )
            {
                throw new Exception('sanatorium/categories: Category was not specified');
            }
        } catch (Exception $e) {
            return redirect()->to('/');
        }

        return view('sanatorium/categories::index', [
            'products' => Product::whereHas('categories', function ($q) use ($category)
            {
                $q->where('shop_categorized.category_id', $category->id);
            })->ordered()->paginate(config('sanatorium-shop.per_page')),
            'category' => $category,
            'per_page' => config('sanatorium-shop.per_page'),
            'per_row'  => config('sanatorium-shop.per_row'),
        ]);
    }
}
