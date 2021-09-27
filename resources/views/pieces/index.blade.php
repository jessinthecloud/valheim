@extends('layouts.app')

@section('title')
    Pieces |
@endsection

@section('content')

    <x-listings.entity-listing
        :entities="$pieces"
        :entityName="'piece'"
        :routeName="'pieces.show'"
        :paginator="$paginator"
    ></x-listings.entity-listing>

@endsection
