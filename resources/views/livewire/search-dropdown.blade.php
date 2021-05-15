<div class="relative" x-data="{ isVisible: true}" @click.away="isVisible=false">
    {{-- wire model will update the value of the property on the class
    search is public property on the class
    debounce - prevent requests within certain time 
        (to prevent too many as you type) --}}
    <input wire:model.debounce.300ms="search" 
    type="text" class="bg-gray-800 text-sm rounded-full px-3 pl-8 py-1 w-64" placeholder="Search"
    {{-- 
    on keydown event anywhere, check to see if it was /
    if so, focus the element "search" that was returned from any x-ref 
    DOES NOT WORK IN FIREFOX
    --}}
    x-ref="search"
    @keydown.window="
        if(event.keycode == 191){
            event.preventDefault();
            $refs.search.focus();
        }
    "
    {{-- 
    below are event listeners that set the var from x-data when fired
    
    @focus - bring the dropdown back (x-show on the list below) 
    @keydown.escape.window - if escape is pressed anywhere, bring dd back
    @keydown - if any key is pressed inside the input, bring dd back
    @keydown.shift.tab - if shift and tab are pressed (move focus backwards) then hide the dd
    --}}
    @focus="isVisible=true" 
    @keydown.escape.window="isVisible=false"
    @keydown="isVisible=true"
    @keydown.shift.tab="isVisible=false"

    > <!-- end search input tag -->

    <!-- magnifying glass icon -->
    <div class="absolute top-0 flex items-center h-full ml-2">
        <svg class="fill-current text-gray-400 w-4" viewBox="0 0 24 24"><path class="heroicon-ui" d="M16.32 14.9l5.39 5.4a1 1 0 01-1.42 1.4l-5.38-5.38a8 8 0 111.41-1.41zM10 16a6 6 0 100-12 6 6 0 000 12z"/></svg>
    </div>

    <!-- tailwind spinner was deprecated, need replacement -->
    {{-- <svg wire:loading class="animate-spin h-5 w-5 mr-3 top-0 right-0 absolute mr-4 mt-3" viewBox="0 0 24 24"></svg> --}}
 
    <!-- dynamic drop down -->
    @if(strlen($search) >= 3)
        {{-- don't show until we've typed a search --}}
        <div class="absolute z-50 bg-gray-800 text-xs rounded w-64 mt-2" x-show.transition.opacity.duration.250="isVisible">
            @if(count($search_results) > 0)
                <ul>
                    @foreach($search_results as $result)
                        <li class="border-b border-gray-700">
                            <a href="{{ route($result['result_type'].'.show', $result['slug']) }}" class="block hover:bg-gray-700 px-3 py-3 flex items-center transition ease-in-out duartion-150"
                            {{-- if we are on last item,
                            if press tab (focus off), hide dropdown --}}
                            @if($loop->last)
                                @keydown.tab="isVisible=false"
                            @endif
                            >
                                {{-- @if(!empty($result['cover_url']))
                                    <img src="{{ $result['cover_url'] }}" alt="Cover Art" class="w-10">
                                @endif --}}
                                <span class="ml-4">{{ $result['name'] }} - <strong>{{ $result['type_name'] }}</strong></span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="px-3 py-3">No Results for <em>{{ $search }}</em></div>
            @endif
        </div>
    @endif
</div>
