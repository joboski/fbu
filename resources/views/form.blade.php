@extends('layout.app')

@section('content')

    @if (session('status'))
        <div class="alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif

    <h2>New product</h2>
    <form action="/products/new" method="POST">
        @csrf
        <input type="text" value="{{  old('name', $formRequest->name) }}" name="name" placeholder="name" /><br />
        <textarea name="description" placeholder="description">
            {{  old('description', $formRequest->description) }}
        </textarea><br />
        <input type="text" name="tags" value="{{  old('tags', $formRequest->name) }}" placeholder="tags" /><br />
        <button type="submit">Submit</button>
    </form>

@endsection
