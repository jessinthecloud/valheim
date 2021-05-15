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
    protected $search_results = [];

    // validation for the properties
    protected $rules = [
        'search' => 'required|string|min:3'
    ];

    // use mount() instead of a class constructor
    public function mount($search='')
    {
        $this->search = $search;
    }

    public function updatedSearch()
    {
        // don't make a request until we have 3 or more letters typed
        if (strlen($this->search) >= 3) {
            $this->validate();
            // $this->search_results = [['name'=>$this->search, 'type_name'=>'items', 'result_type'=>'items', 'slug'=>'nothing']];

            // replace spaces in terms with wildcard to allow forgiving searches
            $term = str_replace(' ', '%', $this->search);

            // do search request with livewire data from view
            $recipe_results = Recipe::where('name', 'like', '%'.$term.'%')
                ->orWhere('true_name', 'like', '%'.$term.'%')
                ->get();
            $item_results = Item::where('name', 'like', '%'.$term.'%')
                ->orWhere('true_name', 'like', '%'.$term.'%')
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
                if ($raw_search_results->count() > 8) {
                    $this->search_results = $raw_search_results->take(8)->toArray();
                } else {
                    $this->search_results = $raw_search_results->toArray();
                }
            } else {
                $this->search_results = [];
            } // endif empty search result
        } else {
            $this->search_results = [];
        }
    }

    public function render()
    {
        return view('livewire.search-dropdown', [
            'search'=> $this->search,
            'search_results'=> $this->search_results,
        ]);
    } // end render()
}
