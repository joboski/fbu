@extends('layout.app')

@section('content')

	@if (session('status'))
	    <div class="alert-success">
	        {{ session('status') }}
	    </div>
	@endif

	<h1>Current Products</h1>

	@if (count($items) > 0)
	    <ul>
	        @foreach ($items as $key => $products)
	        	<li><h2>{{ $key }}</h2></li>
	        	<ul>
	        		@foreach ($products as $product)
		            <li>
		                <h2>{!! $product->name !!}</h2>
		                <p>{{ $product->description }}</p>
		                <form action="{{ route('product.delete', $product->id) }}" method="POST">
		                    @csrf
		                    @method('DELETE')
		                    <button type="submit">delete</button>
		                </form>
		            </li>
		            @endforeach
	            </ul>
	        @endforeach
	    </ul>
	@else
	    <p><em>No products have been created yet.</em></p>
	@endif
@endsection