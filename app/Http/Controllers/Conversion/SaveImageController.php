<?php

namespace App\Http\Controllers\Conversion;

use App\Http\Controllers\Controller;
use App\Http\ImageFetcher;
use App\Models\Items\Craftables\Items\CraftableItem;
use App\Models\Items\Craftables\Pieces\Piece;
use App\Models\Items\Item;
use App\Models\Tools\CraftingStation;
use App\Models\Tools\PieceTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SaveImageController extends Controller
{
    protected ImageFetcher $fetcher;

    public function __invoke(ImageFetcher $fetcher)
    {
        $this->fetcher = $fetcher;

        $this->getCraftingStationImages();

        $this->getPieceTableImages();

        $this->getItemImages();

        $this->getPieceImages();

    }

    /**
     *
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     */
    protected function getItemImages()
    {
        $skippers = [
            'name' => [
                'long1', 'boar attack1', 'Bonemass heart', 'bow', 'brute sword', 'brute taunt', 'Bukeperries', 'Cape of Odin', 'CAPE TEST', 'cheat sledge', 'cheat sword', 'claw', 'club', 'cold ball', 'dragon breath', 'dragon claw left', 'dragon claw right', 'dragur axe', 'fireballattack', 'fart', 'elder heart', 'Growth trophy', 'hat', 'heal', 'Hood of Odin', 'iron plate armor', 'jaws', 'log', 'lox bite', 'Mead horn of Odin', 'One hand ground slam', 'Rancid remains trophy', 'scream', 'serpent bite', 'serpent taunt', 'shaman attack', 'slap', 'slime throw', 'spawn', 'spike attack', 'spike sweep', 'spikes', 'stag attack1', 'stag attack2', 'stamina greydwarf', 'stamina troll', 'stamina wraith', 'throw stone', 'unarmed', 'wolf attack1', 'wolf attack2', 'wolf attack3', 'wraith melee', 'yagluth thing'
                ],
            'shared_data_id' => [169,173,177,181,185,189,193,197,201,205,209,765,769,773,777,781,785,789,793,797,801,805,809,813,817,821],
            'slug' => ['goblin-torch']
        ];
        
        $plural_criteria = ['seed', 'berry', 'fragment', 'nail', 'coin', 'entrail', 'feather', 'wrap', 'sausages', 'scrap'];

        // images, pieces, piece table, crafting station
        $this->getMissingImages(Item::class, $skippers, $plural_criteria);        
    }

    /**
     *
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     */
    protected function getPieceImages()
    {
        $skippers = [
            'name' => [],
            'shared_data_id' => [],
            'slug' => []
        ];

        $plural_criteria = [];

        // images, pieces, piece table, crafting station
        $this->getMissingImages(Piece::class, $skippers, $plural_criteria);
    }

    /**
     *
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     */
    protected function getPieceTableImages()
    {
        $skippers = [
            'name' => [],
            'shared_data_id' => [],
            'slug' => []
        ];

        $plural_criteria = [];

        // images, pieces, piece table, crafting station
        $this->getMissingImages(PieceTable::class, $skippers, $plural_criteria);
    }

    /**
     *
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     */
    protected function getCraftingStationImages()
    {
        $skippers = [
            'name' => [],
            'shared_data_id' => [],
            'slug' => []
        ];

        $plural_criteria = [];

        // images, pieces, piece table, crafting station
        $this->getMissingImages(CraftingStation::class, $skippers, $plural_criteria);
    }

    /**
     * @param string                 $model
     * @param array                  $skippers
     * @param array                  $plural_criteria
     *
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     */
    protected function getMissingImages(string $model, array $skippers, array $plural_criteria)
    {
        // images, pieces, piece table, crafting station
        $query = $model::whereNull('image');
        
        foreach($skippers as $column => $values){
            // exclude invalid items
            $query->whereNotIn($column, $values);
        }
        
        $items = $query->orderBy('name')->get();
        
        $items->each(function($item) use ($plural_criteria) {
            $this->prepareDbData($item, $plural_criteria);
        });
    }

    /**
     * @param                        $item
     * @param array                  $plural_criteria
     *
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     */
    protected function prepareDbData($item, array $plural_criteria)
    {
        // items like "Bronze Plate Leggings" should be plural 
        $name = Str::snake((Str::startsWith($item->slug, 'armor-')) ? $item->name : Str::singular($item->name));

        $name = Str::contains($name, $plural_criteria) ? Str::plural($name) : $name;

        // handle names with ":" in them
        if(Str::contains($name, ':')){
            $prefix = ucwords(Str::before($name, ':_'));
            $suffix = ucwords(Str::after($name, ':_'));
            $name = $prefix . ':_' . $suffix;
        }

        $this->getAndSaveImage($item, $name);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $item
     * @param string                              $name
     *
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     */
    protected function getAndSaveImage(Model $item, string $name)
    {
        $image = $this->fetcher->fetch($name);
        if( !$image ){

            dump('-- FAILED -- db name: '.$item->name);
            dump($name);

            // if fail, skip
            return;
        }
        
        $item->image = basename($image); //'app/public/images/'.basename($image);
        $item->save();
        
        sleep(0.2);
    }
}