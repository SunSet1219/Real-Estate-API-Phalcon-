<?php

use Phalcon\Mvc\Model;

class File extends Model
{
    public static function savePhotos($property, $listingID){
        global $RETS, $RETS_PhotoSize, $debugMode;
        $photos = $RETS->GetObject("Property", $RETS_PhotoSize, $listingID, '*');

    	if(!is_array($photos))
    		return;

    	if(count($photos) > 0)
    	{
    		$count = 0;
    		foreach($photos as $photo)
    		{
    			if((!isset($photo['Content-ID']) || !isset($photo['Object-ID'])) ||
    				(is_null($photo['Content-ID']) || is_null($photo['Object-ID'])) ||
    				($photo['Content-ID'] == 'null' || $photo['Object-ID'] == 'null')
                ){
    				continue;
    			}

    			$destination = $_SERVER['DOCUMENT_ROOT']."/public/upload/".$listingID."_".$photo['Object-ID'].".jpg";
    			file_put_contents($destination, $photo['Data']);

                $file = new File();
                $file->path = "/public/upload/".$listingID."_".$photo['Object-ID'].".jpg";
                $file->sort = "100";
                $file->type = "image";
                $file->description = "parse";
                $file->save();

                $pf = new PropertyFiles();
                $pf->file_id = $file->id;
                $pf->property_id = $property->id;

                if($count==0){
                    $property->photo_id = $file->id;
                    $property->save();
                }

                $count++;
    		}
    	}
    }

}
