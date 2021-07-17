<?php

namespace App\Http;

use PHPHtmlParser\Dom;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ImageFetcher 
{
    public function fetchImageHtmlString(string $item_name)
    {
        $base_uri = 'https://valheim.fandom.com';
        // $uri_path = '/wiki/Category:Food/';
        $uri_path = '/api.php?format=json&action=parse';
        // ?action=parse&page=Lox meat pie&format=json
        // https://valheim.fandom.com/api.php?format=json&action=parse&page=Food
        // https://valheim.fandom.com/api.php?format=json&action=parse&page=Ancient_bark_spear&section=1
        $uri = $base_uri.$uri_path;
        // $item_name = 'Ancient_bark_spear';

        // TODO: store values in database and only make HTTP request periodically
        $response = /*Cache::remember($item_name, 6000, function() use($item_name, $uri) {
            return*/ Http::get($uri, [
                'action' => 'parse',
                'page' => $item_name,
                'format'=> 'json'
            ]);
        // });
        // dump($response->object());

        // fake response out for now
        // same as: https://valheim.fandom.com/api.php?format=json&action=parse&page=sausages
        // $response = json_decode(file_get_contents(storage_path('app/sausages.json')))->parse;
        // $response = json_decode(file_get_contents(storage_path('app/sausages.json')))->parse;
        if($response->failed()){
            abort($response->status(), 'Request to '.$uri.' failed.');
        }
        $response = $response->object()->parse;
        // dump($response);

        $title = $response->title;
        /*dump($title);
        $images = $response->images;
        dump($images);*/
        // $sections = $response->sections;
        // dump($sections);
        // $text = stripslashes(reset($response->text));
        // dump($text);

        // https://github.com/paquettg/php-html-parser
        $dom = new Dom;
        $dom->loadStr(stripslashes(reset($response->text)));
        $imageNode = $dom->find('.pi-image-thumbnail')[0];
        // dump('imageNodes:', $imageNodes);
        // $imageTag = ($imageNodes[0])->getTag();
        // dump('imageTag:', $imageNode->getTag());
        return '<img
                    src="'.$imageNode->getTag()->getAttribute('src')->getValue().'"
                    srcset="'.$imageNode->getTag()->getAttribute('srcset')->getValue().'"
                    alt="'.$title.' Thumbnail"
                    class="'.$imageNode->getTag()->getAttribute('class')->getValue().'"
                    data-image-key="'.$imageNode->getTag()->getAttribute('data-image-key')->getValue().'"
                    data-image-name="'.$imageNode->getTag()->getAttribute('data-image-name')->getValue().'"
                />';
        // dump('image:', $image);
        /*$labelnodes = $dom->find('.pi-data-label');
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

        dump($info);*/
    }
}
