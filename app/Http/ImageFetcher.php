<?php

namespace App\Http;

use Illuminate\Support\Str;
use PHPHtmlParser\Dom;
use Illuminate\Support\Facades\Http;

class ImageFetcher 
{
    /**
     * 
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     */
    public function fetch(string $item_name)
    {
        $image_url = $this->fetchImageHtmlString($item_name);
        
        return $image_url ? $this->saveImage($image_url) : false;
    }

    /**
     * @param string $item_name
     *
     * @return mixed
     *              
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     */
    public function fetchImageHtmlString(string $item_name)
    {
        $base_uri = 'https://valheim.fandom.com';
        $uri_path = '/api.php?format=json&action=parse';
        $uri = $base_uri.$uri_path;
        // $item_name = 'Ancient_bark_spear';
        // example: https://valheim.fandom.com/api.php?format=json&action=parse&page=sausages

        $response = Http::get($uri, [
            'action' => 'parse',
            'page' => $item_name,
            'format'=> 'json'
        ]);
        
        if($response->failed()){
            abort($response->status(), 'Request to '.$uri.' failed.');
        }
        
        if(property_exists($response->object(), 'error')){
//            dump('======= ERROR ',$response->object(),  '=======');
            return false;
        }
        $obj = $response->object();
        $response = $obj->parse;
        
        // https://github.com/paquettg/php-html-parser
        $dom = new Dom;
        $dom->loadStr(stripslashes(reset($response->text)));
        $imageNode = $dom->find('.pi-image-thumbnail')[0];
        if(null === $imageNode){
//            dump('======= ERROR ',$obj, $dom->find('.pi-image-thumbnail'),  '=======');
            return false;
        }
        return $imageNode->getTag()->getAttribute('src')->getValue();
        
        /*return '<img
                    src="'.$imageNode->getTag()->getAttribute('src')->getValue().'"
                    srcset="'.$imageNode->getTag()->getAttribute('srcset')->getValue().'"
                    alt="'.$title.' Thumbnail"
                    class="'.$imageNode->getTag()->getAttribute('class')->getValue().'"
                    data-image-key="'.$imageNode->getTag()->getAttribute('data-image-key')->getValue().'"
                    data-image-name="'.$imageNode->getTag()->getAttribute('data-image-name')->getValue().'"
                />';*/
    }

    public function saveImage($image)
    {
        $file_name = Str::beforeLast($image, '.') . '.' . Str::before(Str::afterLast($image, '.'), '/');
        $name = storage_path('app/public/images/'.basename($file_name));

        $file = fopen ($image, "rb");

        if ($file) {
            $newf = fopen ($name, "a"); // to overwrite existing file

            if ($newf) {
                while ( !feof( $file ) ) {
                    fwrite( $newf, fread( $file, 1024 * 8 ), 1024 * 8 );
                }

//                dump($newf);
                
                fclose($file);
                fclose($newf);
                
                return $name;
            }
            fclose($file);
        }
        return false;
    }
}
