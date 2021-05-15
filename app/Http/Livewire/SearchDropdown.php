<?php

namespace App\Http\Livewire;

use App\Models\Recipe;
use App\Models\Item;
use Livewire\Component;
use Illuminate\Support\Str;

class SearchDropdown extends Component
{
    // these are set and/or returned to the livewire component's view
    public $search = '';
    public $search_results = [];

    public function render()
    {

        // don't make a request until we have 3 or more letters typed
        if (strlen($this->search) >= 3) {

            // do search request with livewire data from view
            $recipe_results = Recipe::where('name', 'like', '%'.$this->search.'%')
                ->orWhere('true_name', 'like', '%'.$this->search.'%')
                ->get();
            $item_results = Item::where('name', 'like', '%'.$this->search.'%')
                ->orWhere('true_name', 'like', '%'.$this->search.'%')
                ->get();

            $raw_search_results = $recipe_results->merge($item_results)->sortBy('name');

            if (!empty($raw_search_results)) {
                // add table info so we know how to label and what routes to link
                $raw_search_results = $raw_search_results->map(function ($result) {
                    return collect($result)->merge([
                        'result_type' => $result->getTable(),
                        'type_name' => ucwords(Str::singular($result->getTable()))
                    ]);
                });

                $this->search_results = $raw_search_results->take(8)->toArray();
            } // endif empty search result
        }

        return view('livewire.search-dropdown');
    } // end render()
}
