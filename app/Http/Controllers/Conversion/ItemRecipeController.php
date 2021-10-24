<?php

namespace App\Http\Controllers\Conversion;

use App\Http\Controllers\Controller;
use App\Models\Recipes\Recipe;
use Illuminate\Http\Request;

class ItemRecipeController extends Controller
{
    use DoesConversion;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {        
        $paginator = Recipe::orderBy('name', 'asc')->paginate(32);

        $recipes = collect($paginator->items());
        
        return view('recipes.index', 
            compact(
            'recipes',
            'paginator'
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource (by id)
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $recipe = Recipe::with($this->relationsSubQuery())->findOrFail($id);

        $recipe = $this->formatUpgradesForView($this->formatForView($recipe));

        return view('recipes.show', compact('recipe'));
    }

    /**
     * Display the specified resource (by slug)
     *
     * @param  \App\Models\r  $r
     * @return \Illuminate\Http\Response
     */
    public function showSlug($slug)
    {
        $recipe = Recipe::with($this->relationsSubQuery())->where('slug', $slug)->firstOrFail();

        $recipe = $this->formatUpgradesForView($this->formatForView($recipe));

        return view('recipes.show', compact('recipe'));
    }

    protected function relationsSubQuery()
    {
        return [
            'requirements' => function ($query) {
                $query->orderByDesc('amount', SORT_NUMERIC)->orderByDesc('name', SORT_NATURAL|SORT_FLAG_CASE);
            },
        ];
    }

    protected function formatForView($recipe)
    {
        $recipe->name = ucwords($recipe->name);
        $recipe->max_quality = $recipe->creation->sharedData->maxQuality() ?? 1;

        return $recipe;
    }

    protected function formatUpgradesForView($recipe)
    {
        $recipe->name = ucwords($recipe->name);
        $recipe->max_quality = $recipe->creation->sharedData->maxQuality() ?? 1;
        $upgrades = [];
        $sum = [];
        // start at the first upgrade level and determine the required item amounts
        for ($i=2; $i<=$recipe->maxQuality(); $i++) {
            $upgrades[$i]= [
                'station' => $recipe->getRequiredStation($i)->name,
                'station_level' => $recipe->getRequiredStationLevel($i),
            ];
            foreach ($recipe->requirements as $req) {
                $upgrades[$i]['resources'][$req->name]= $req->getAmount($i);
                if (isset($sum[$req->name])) {
                    $sum[$req->name] += $upgrades[$i]['resources'][$req->name];
                } else {
                    $sum[$req->name] = $upgrades[$i]['resources'][$req->name]+$req->amount;
                }
            } // end foreach
        } // end for

        // dump($recipe);
        // dump("max level: ".$recipe->creation->sharedData->maxQuality());
        // dump($upgrades);
        // dump($sum);

        $totals = '';
        foreach ($sum as $item => $amount):
            if ($amount > 0) {
                $totals .= '<strong>'.$amount.'</strong> '.$item.', ';
            }
        endforeach;
        $totals = rtrim($totals, ', ');
        // include max station level in totals
        if (isset($upgrades[$recipe->maxQuality()]) && $upgrades[$recipe->maxQuality()]['station_level'] > 1) {
            $totals .= ' (<strong>Level ' . $upgrades[$recipe->maxQuality()]['station_level'].' '.$upgrades[$recipe->maxQuality()]['station'] . '</strong>)';
        }

        $recipe->upgrades = $upgrades;
        $recipe->totals = $totals;

        unset($upgrades);
        unset($totals);

        return $recipe;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Recipes\Recipe $recipe
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Recipe $recipe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request   $request
     * @param \App\Models\Recipes\Recipe $recipe
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recipe $recipe)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Recipes\Recipe $recipe
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Recipe $recipe)
    {
        //
    }
}
