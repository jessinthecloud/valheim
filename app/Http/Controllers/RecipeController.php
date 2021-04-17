<?php

namespace App\Http\Controllers;

use PHPHtmlParser\Dom;
use App\Models\Recipe;
use App\Http\Controllers\JsonAdapter;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // decode json file to array
        $contents = ['recipes'=>JsonAdapter::decodeJsonFile(
            storage_path('app\recipes.json'),
            true
        )];

        echo "<BR>";
        dump("////////////////////// BEFORE FUNC ////////////////////////////////");
        echo "<BR>";
        /////////////////

        // $recipe = JsonAdapter::recursiveCreateFromArray($contents);
        $recipe = JsonAdapter::createFromArray($contents);

        echo "<BR>";
        dump("/////////////////////// AFTER FUNC ///////////////////////////////");
        echo "<BR>";

        dump($recipe);
        ////////////////////
        // dumping only
        /*$i=0;
        foreach ($contents as $key => $item) {
            dump("key: $key");
            dump($item);
            if (is_int(array_key_first($item))) {
                foreach ($item as $k => $v) {
                    if ($i > 22) {
                        break;
                    }
                    if (is_int($k)) {
                        foreach ($v as $prop => $val) {
                            if ($i > 22) {
                                break;
                            }
                            if (is_int($prop)) {
                            } else {
                                dump("inner key: $prop");
                                dump($val);
                            }
                            dump("=$i===========================================$i=");
                            $i++;
                        }
                    } else {
                        dump("int key: $k");
                        dump($v);
                    }
                    dump("=$i===========================================$i=");
                    $i++;
                }
            }
        }*/
        /*$iterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($contents),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        $i=0;
        foreach ($iterator as $key => $item) {
            if ($i > 22) {
                break;
            }
            dump("key: $key");
            dump($item);
            dump("=$i===========================================$i=");
            $i++;
        }*/

        /*foreach ($iterator as $key => $item) {


            if (is_array($item)) {
                // item is array
                    // if item is numeric array - if (is_int(array_key_first($item)))
                        // current key is object name, item is array of these objects
                    // else item is assoc array
                        // key is object name, item is ARRAY of properties => values
            } else {
                // item is NOT array
                // key is property name, item is property value
                // key => item
            }

            ////////////////////////////////////////////////////////

            if (!is_int($key)) {
                // key is not int
                    // if item is array
                        // if item is numeric array
                            // key is object name, item is array of these objects
                        // else item is assoc array
                            // key is object name, item is ARRAY of property => value
                    // else item is not array
                        // key is property name, item is property value
                        // key => item
            } else {
                // key is int
                    // if item is array
                        // if item is numeric array
                            // PREVIOUS key was object name, item is array of these objects
                        // else item is assoc array
                            // PREVIOUS key was object name, item is ARRAY of property => value
                // ---------------
                    // --- THIS CASE SHOULD NEVER HAPPEN --  else item is not array
                        // key is property name, item is property value
                        // key => item
            }

        }*/
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tester()
    {
        $response = json_decode(file_get_contents(storage_path('app\itemdrops.json')));
        return $response;
        /*dump($response[1]);

        $array = json_decode(file_get_contents(storage_path('app\itemdrops.json')), true);

        $unique_properties = [];
        foreach (new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($array),
            \RecursiveIteratorIterator::SELF_FIRST
        )
          as $key => $value) {
            if (is_string($key)) {
                $unique_properties[]=$key;
            }
        }
        $unique_properties = array_unique($unique_properties);
        foreach ($unique_properties as $prop) {
            echo "$prop <BR>";
        }*/
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function oldIndex()
    {
        $base_uri = 'https://valheim.fandom.com';
        // $uri_path = '/wiki/Category:Food/';
        $uri_path = '/api.php';
        // ?action=parse&page=Lox meat pie&format=json
        // https://valheim.fandom.com/api.php?format=json&action=parse&page=Food
        // https://valheim.fandom.com/api.php?format=json&action=parse&page=sausages&section=1
        $uri = $base_uri.$uri_path;
        // $uri = 'https://google.com';

        // TODO: store values in database and only make HTTP request periodically
        /*Http::get($uri, [
            'action' => 'parse',
            'page' => "sausages",
            'format'=> 'json'
        ]);*/

        // fake response out for now
        // same as: https://valheim.fandom.com/api.php?format=json&action=parse&page=sausages
        $response = json_decode(file_get_contents(storage_path('app/sausages.json')))->parse;

        // dump($response);

        $title = $response->displaytitle;
        dump($title);
        $images = $response->images;
        dump($images);
        $sections = $response->sections;
        dump($sections);
        $text = stripslashes(reset($response->text));
        // dump($text);

        // https://github.com/paquettg/php-html-parser
        $dom = new Dom;
        $dom->loadStr($text);
        $labelnodes = $dom->find('.pi-data-label');
        $valuenodes = $dom->find('.pi-data-value');

        $info = array_map(function ($label, $value) {
            // dump('label: '.$label->text."\nvalue: ".(empty(trim($value->text)) ? $value->getChildren()[0]->text : $value->text));

            if (empty(trim($value->text))) {
                $val = $value->getChildren()[0];
                if ($value->getChildren()[0]->tag->name() === 'a') {
                    return [
                        'label'=>$label->text,
                        'val'=>[
                            'link' => $val->tag->getAttribute('href')->getValue(),
                            'text' => $val->text
                        ]
                    ];
                }
                return ['label'=>$label->text,'val'=>['text' => $val->text]];
            }

            return ['label'=>$label->text, 'val'=>['text'=>$value->text]];
        }, $labelnodes->toArray(), $valuenodes->toArray());

        dump($info);
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
     * Display the specified resource.
     *
     * @param  \App\Models\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function show(Recipe $recipe)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function edit(Recipe $recipe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recipe $recipe)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Recipe $recipe)
    {
        //
    }
}

/**
* PHP's DOM classes are recursive but don't provide an implementation of
* RecursiveIterator. This class provides a RecursiveIterator for looping over DOMNodeList
*/
class DOMNodeRecursiveIterator extends \ArrayIterator implements \RecursiveIterator
{
    public function __construct(\DOMNodeList $node_list)
    {
        $nodes = array();
        foreach ($node_list as $node) {
            $nodes[] = $node;
        }

        parent::__construct($nodes);
    }

    public function getRecursiveIterator()
    {
        return new \RecursiveIteratorIterator($this, \RecursiveIteratorIterator::SELF_FIRST);
    }

    public function hasChildren()
    {
        return $this->current()->hasChildNodes();
    }


    public function getChildren()
    {
        return new self($this->current()->childNodes);
    }
}
