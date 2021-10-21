@props(['title', 'entities', 'entityName', 'routeName', 'paginator'])

<x-layouts.app>
    <x-slot name="title">
        {{ $title }}
    </x-slot>

    <x-listings.entity-listing
            :entities="$entities"
            :entityName="$entityName"
            :routeName="$routeName"
            :paginator="$paginator"
    ></x-listings.entity-listing>
</x-layouts.app>