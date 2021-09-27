@extends('layouts.app')

@section('title')
    Items |
@endsection

@section('content')

    <x-listings.entity-listing
            :entities="$items"
            :entityName="'item'"
            :routeName="'items.show'"
            :paginator="$paginator"
    ></x-listings.entity-listing>

@endsection
