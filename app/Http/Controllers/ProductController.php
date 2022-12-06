<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\Product;
use app\Models\Tag;

class ProductController extends Controller
{
    public function index()
    {
        return view('products');
    }

    public function new(Request $request)
    {
        $validator = $request->validate([
            'name' => 'bail|required|unique:products|max:64',
            'description' => 'required'
        ]);

        if ($validator->fails()) {

            return redirect('products/new')
                ->withErrors($validator)
                ->withInputs();
        }

        $validated = $validator->validated();

        Product::insert([
            'name' => $validated['name'],
            'description' => $validated['description']
        ]);

        if ($validated['tags']) {
            $this->saveTags($validated['tags']);
        }
        
        return redirect('/products')->with('status', 'The product was saved');
    }

    public function delete($id)
    {
        $isExistingProduct = Product::find($id);
        
        if (!$isExistingProduct) {
            return redirect('products/')
                ->withErrors('Product does not exists.');
        }

        Product::destroy($id);

        return redirect('/products')->with('status', 'The product was deleted');
    }

    private function saveTags($tags)
    {
        $storedTags = Tag::all();
        $arrTags = explode(',', $tags);

        foreach ($arrTags as $tag) {
            if (!in_array($tag, $storedTags)) {
                Tag::insert(['name' => $tag]);
            }
        }
    }
}
