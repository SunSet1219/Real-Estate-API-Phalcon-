<?
class Crea
{
    public $RETS;
    public $offset;
    public $step = 15;
    public $totalAvailable = 0;
    public $settings;

    public $province = [];
    public $city = [];
    public $status = [];
    public $type = [];

    public function init(){
        require($_SERVER['DOCUMENT_ROOT']."/app/plugins/crea/PHRets_CREA.php");

        if(isset($_GET['offset']))
            $this->offset = $_GET['offset'];
        else
            $this->offset = 0;

        $this->RETS = new PHRets();
        $this->RETS->Connect("http://data.crea.ca/Login.svc/Login", "QmDKV50roV3BaqkPpZVfceCr", "e20BggigKuQbLvOeQk2wOrXU");
        $this->RETS->AddHeader("RETS-Version", "RETS/1.7.2");
        $this->RETS->AddHeader('Accept', '/');
        $this->RETS->SetParam('compression_enabled', true);

        $params = array("Limit" => 1, "Format" => "STANDARD-XML", "Count" => 1);
        $this->settings = "(LastUpdated=" . date('Y-m-d', strtotime("-48 hours")) . ")";

        $results = $this->RETS->SearchQuery("Property", "Property", $this->settings, $params);
        $this->totalAvailable = $results["Count"];

        $provinces = Province::find();
        foreach ($provinces as $province)
            $this->province[$province->name] = $province->id;

        $cities = City::find();
        foreach ($cities as $city)
            $this->city[$city->name] = $city->id;

        $statuses = Status::find();
        foreach ($statuses as $status)
            $this->status[$status->name] = $status->id;

        $types = Type::find();
        foreach ($types as $type)
            $this->type[$type->name] = $type->id;

    }

    public function geo($address){
        $geo = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".str_replace(" ", "+", $address['StreetAddress'].", ".$address['City'].", ".$address['Province']));
        $data = json_decode($geo, true);
        if($data['status']=="OK"){
            return $data['results'][0]['geometry']['location'];
        }
        if($data['status']=="OVER_QUERY_LIMIT"){
            usleep(500000);
            return $this->geo($address);
        }
        return false;
    }

    public function getPhotos($listing){
        $photos = $this->RETS->GetObject("Property", "LargePhoto", $listing, '*');
        return $photos;
    }

    public function getId($object, $name){
        switch ($object) {
            case 'city':
                if(isset($this->city[$name])){
                    return $this->city[$name];
                } else {
                    $entity = new City();
                    $entity->name = $name;
                    $entity->save();
                    $this->city[$name] = $entity->id;
                    return $entity->id;
                }
                break;
            case 'type':
                if(isset($this->type[$name])){
                    return $this->type[$name];
                } else {
                    $entity = new Type();
                    $entity->name = $name;
                    $entity->save();
                    $this->type[$name] = $entity->id;
                    return $entity->id;
                }
                break;
            case 'province':
                if(isset($this->province[$name])){
                    return $this->province[$name];
                } else {
                    $entity = new Province();
                    $entity->name = $name;
                    $entity->save();
                    $this->province[$name] = $entity->id;
                    return $entity->id;
                }
                break;
            case 'status':
                if(isset($this->status[$name])){
                    return $this->status[$name];
                } else {
                    $entity = new Status();
                    $entity->name = $name;
                    $entity->color = "#000";
                    $entity->save();
                    $this->status[$name] = $entity->id;
                    return $entity->id;
                }
                break;

            default:
                # code...
                break;
        }
    }

    public function next(){
        $this->offset = $this->offset + $this->step;
    }

    public function getProperties(){
        $params = array("Limit" => $this->step, "Format" => "STANDARD-XML", "Offset" => $this->offset);
        $properties = $this->RETS->SearchQuery("Property", "Property", $this->settings, $params);
        return $properties['Properties'];
    }

    public function photos($property_id){
        $photos = $RETS->GetObject("Property", "LargePhoto", $property_id, '*');
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

				$destination = $_SERVER['DOCUMENT_ROOT']."/public/upload/".$property_id."_".$photo['Object-ID'].".jpg";
				file_put_contents($destination, $photo['Data']);

				$file = new File();
				$file->path = "/public/upload/".$property_id."_".$photo['Object-ID'].".jpg";
				$file->sort = "100";
				$file->type = "image";
				$file->description = "parse";
				$file->save();

				$pf = new PropertyFiles();
				$pf->file_id = $file->id;
				$pf->property_id = $property_id;
				$pf->save();

				if($count==0){
					$property->photo_id = $file->id;
					$property->save();
				}

				$count++;
			}
        }
    }

    public function disconnect(){
        $this->RETS->Disconnect();
    }
}
//
// function downloadPhotos($listingID)
// {
// 	global $RETS, $RETS_PhotoSize, $debugMode;
//
// 	if(!$downloadPhotos)
// 	{
// 		if($debugMode) error_log("Not Downloading Photos");
// 		return;
// 	}
//
// 	$photos = $RETS->GetObject("Property", $RETS_PhotoSize, $listingID, '*');
//
// 	if(!is_array($photos))
// 	{
// 		if($debugMode) error_log("Cannot Locate Photos");
// 		return;
// 	}
//
// 	if(count($photos) > 0)
// 	{
// 		$count = 0;
// 		foreach($photos as $photo)
// 		{
// 			if(
// 				(!isset($photo['Content-ID']) || !isset($photo['Object-ID']))
// 				||
// 				(is_null($photo['Content-ID']) || is_null($photo['Object-ID']))
// 				||
// 				($photo['Content-ID'] == 'null' || $photo['Object-ID'] == 'null')
// 			)
// 			{
// 				continue;
// 			}
//
// 			$listing = $photo['Content-ID'];
// 			$number = $photo['Object-ID'];
// 			$destination = $listingID."_".$number.".jpg";
// 			$photoData = $photo['Data'];
//
// 			/* @TODO SAVE THIS PHOTO TO YOUR PHOTOS FOLDER
// 			 * Easiest option:
// 			 * 	file_put_contents($destination, $photoData);
// 			 * 	http://php.net/function.file-put-contents
// 			 */
//
// 			$count++;
// 		}
//
// 		if($debugMode)
// 			error_log("Downloaded ".$count." Images For '".$listingID."'");
// 	}
// 	elseif($debugMode)
// 		error_log("No Images For '".$listingID."'");
//
// 	// For good measure.
// 	if(isset($photos)) $photos = null;
// 	if(isset($photo)) $photo = null;
// }


/* This script, by default, will output something like this:

Connecting to RETS as '[YOUR RETS USERNAME]'...
-----GETTING ALL ID's-----
-----81069 Found-----
-----Get IDs For 0 to 100. Mem: 0.7MB-----
-----Get IDs For 100 to 200. Mem: 3.7MB-----
-----Get IDs For 200 to 300. Mem: 4.4MB-----
-----Get IDs For 300 to 400. Mem: 4.9MB-----
-----Get IDs For 400 to 500. Mem: 3.4MB-----
*/

?>
