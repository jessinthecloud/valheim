@props(['title', 'entities', 'entityName', 'paginator', 'routeName'])

<h1 class="w-full text-4xl mb-4">{{ $title }}</h1>

@if(!empty($entities))
    {{--paging links--}}
    <div class="paging-wrapper w-full mt-4 mb-10 lg:mt-0">
        {{ $paginator->links() }}
    </div>

    @foreach($entities as $entity)
        <x-listings.listing-link
            :routeName="$routeName"
            :entity="$entity"
            :entityName="$entityName"
        ></x-listings.listing-link>
    @endforeach

    {{--paging links--}}
    <div class="paging-wrapper w-full mt-8">
        {{ $paginator->links() }}
    </div>
@else
    No {{ Str::plural($entityName) }} to display.
@endif
