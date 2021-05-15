@extends('layouts.app')

@section('title')
    @if(!empty($recipe)) {{ $recipe->name }} Recipe | @endif
@endsection

@section('content')
    @if(!empty($recipe))
        @include('partials._recipe', [
            'recipe'    => $recipe
        ])
    @else 
        No recipe to display.
    @endif
@endsection
