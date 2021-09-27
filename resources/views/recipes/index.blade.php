@extends('layouts.app')

@section('title')
    Recipes |
@endsection

@section('content')

    <x-listings.entity-listing
        :entities="$recipes"
        :entityName="'recipe'"
        :routeName="'recipes.show'"
        :paginator="$paginator"
    ></x-listings.entity-listing>

@endsection
