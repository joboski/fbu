<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFormRequest;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $productTags = [];
        foreach ($products as $product) {
            $tags = $product->tags;
            foreach ($tags as $tag) {
                $productTags[$tag->name][] = $product;
            }
        }

        return view('index')->with('items', $productTags);
    }

    public function create()
    {
        return view('form')->with([
            'formRequest' => new ProductFormRequest
        ]);
    }

    public function new(ProductFormRequest $request)
    {
        $product = new Product();
        $product->name = $request->get('name');
        $product->description = $request->get('description');

        if ($product->save() && $request->get('tags')) {
            $this->saveTags($request->get('tags'), $product);
        }
        
        return redirect('/products')->with('status', 'The product was saved');
    }

    public function delete($id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return redirect('products/')
                ->withErrors('Product does not exists.');
        }

        // remove from pivot first
        $product->tags()->wherePivot('product_id', '=', $id)->detach();

        Product::destroy($id);

        return redirect('/products')->with('status', 'The product was deleted');
    }

    private function saveTags($tags, $product)
    {
        $tagObject = new Tag();
        $inputtedTags = explode(',', $tags);
        $storedTags = $tagObject->pluck('name')->toArray();
        $tagIds = [];

        foreach ($inputtedTags as $tag) {
            if (!in_array($tag, $storedTags)) {
                $tagObject->name = trim($tag);
                $tagObject->save();
                $tagIds[] = $tagObject->id;
            } else {
                $tag = $this->findBy($tagObject, 'name', $tag);
                $tagIds[] = $tag->id;
            }
        }

        $product->tags()->sync($tagIds);
    }

    private function findBy($model, $attribute, $value)
    {
        return $this
            ->getBaseQueryForSearch($model, $attribute, $value)
            ->first();
    }

    private function getBaseQueryForSearch($model, $attribute, $value, $operation = '=', $existingQuery = null)
    {
        $query = (! empty($existingQuery)) ? $existingQuery : $model->newQuery();
        if (is_array($value)) {
            if ($operation === '!=') {
                return $query->whereNotIn($attribute, $value);
            }
            return $query->whereIn($attribute, $value);
        }

        return $query->where($attribute, $operation, $value);
    }
}
