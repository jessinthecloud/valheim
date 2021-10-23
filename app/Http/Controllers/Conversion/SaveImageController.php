<?php

namespace App\Http\Controllers\Conversion;

use App\Http\Controllers\Controller;
use App\Http\ImageFetcher;
use App\Models\Items\Craftables\Items\CraftableItem;
use App\Models\Items\Item;
use Illuminate\Support\Str;

class SaveImageController extends Controller
{
    public function __invoke(ImageFetcher $fetcher)
    {
        $skippers = ['long1', 'boar attack1', 'Bonemass heart', 'bow', 'brute sword', 'brute taunt', 'Bukeperries', 'Cape of Odin', 'CAPE TEST', 'cheat sledge', 'cheat sword', 'claw', 'club', 'cold ball', 'dragon breath', 'dragon claw left', 'dragon claw right', 'dragur axe', 'fireballattack', 'fart', 'elder heart', 'Growth trophy', 'hat', 'heal', 'Hood of Odin', 'iron plate armor', 'jaws', 'log', 'lox bite', 'Mead horn of Odin', 'One hand ground slam', 'Rancid remains trophy', 'scream', 'serpent bite', 'serpent taunt', 'shaman attack', 'slap', 'slime throw', 'spawn', 'spike attack', 'spike sweep', 'spikes', 'stag attack1', 'stag attack2', 'stamina greydwarf', 'stamina troll', 'stamina wraith', 'throw stone', 'unarmed', 'wolf attack1', 'wolf attack2', 'wolf attack3', 'wraith melee', 'yagluth thing'];
    
        // images, pieces, piece table, crafting station
        $items = Item::whereNull('image')
            // exclude meta/customize items
            ->whereNotIn('shared_data_id', [169,173,177,181,185,189,193,197,201,205,209,765,769,773,777,781,785,789,793,797,801,805,809,813,817,821])
            ->whereNotIn('name', $skippers)
            ->whereNotIn('slug', ['goblin-torch'])
            ->orderBy('name')->get();
        
        $items->each(function($item) use($fetcher) {
            
            // check recipe enabled if exists
            /*$tmp = new CraftableItem($item->toArray());
            if($tmp->hasRecipes()){
                $tmp->recipes->each(function($recipe) use (&$item) {
                    if( !$recipe->enabled ){
                        // skip if have recipes and not enabled
                        $item->disable = true;
                    }                    
                });
//                dd($tmp, $tmp->hasRecipes(), $tmp->recipes->filter()->all(), $item);
            }*/
       
            // items like "Bronze Plate Leggings" should be plural 
            $name = Str::snake((Str::startsWith($item->slug, 'armor-')) ? $item->name : Str::singular($item->name));

            $name = Str::contains($name, ['seed', 'berry', 'fragment', 'nail', 'coin', 'entrail', 'feather', 'wrap', 'sausages', 'scrap']) ? Str::plural($name) : $name;
            
            // handle names with ":" in them
            if(Str::contains($name, ':')){
                $prefix = ucwords(Str::before($name, ':_'));
                $suffix = ucwords(Str::after($name, ':_'));
                $name = $prefix . ':_' . $suffix;
            }
            
            $image = $fetcher->fetch($name);
            if( !$image ){
                // if fail, skip

                dump('db name: '.$item->name);
                dump($name);

                return;
            }
            $item->image = basename($image); //'app/public/images/'.basename($image);
            $item->save();
            sleep(0.2);
        });
    }
}