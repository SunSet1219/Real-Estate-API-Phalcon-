<?php

use Phalcon\Mvc\Controller;

class CreaController extends Controller
{

	public function newAction(){
		$crea = new Crea();
		$crea->init();
		$properties = $crea->getProperties();
		foreach ($properties as $data) {
			if(	!isset($data['Price']) ||
				!isset($data['TransactionType']) ||
				!isset($data['Building']['SizeInterior']) ||
				!isset($data['Address']) ||
				!isset($data['Address']['City']) ||
				!isset($data['Address']['Province']))
				continue;
			if(empty($data['Building']['SizeInterior']))
				continue;

			//need to check geo postition
				if(!$geo = $crea->geo($data['Address']))
					continue;

			//need to check photos available
				$photos = $crea->getPhotos((int)$data["@attributes"]['ID']);
				if(count($photos)<0)
					continue;

			$property = new Property();
			$property->lat = $geo['lat'];
			$property->lng = $geo['lng'];

			$property->id = (int)$data["@attributes"]['ID'];
			$property->title = $data['Address']['StreetAddress'].", ".$data['Address']['City'].", ".$data['Address']['Province'];
			$property->subtitle = $data['Address']['StreetAddress']." ".$data['Address']['PostalCode'];

			$property->price = $data['Price'];
			$property->bed = $data['Building']['BedroomsTotal'] || 0;
			$property->bath = $data['Building']['BathroomTotal'] || 0;

			$property->status_id = $crea->getId('status', $data['TransactionType']);
			$property->type_id = $crea->getId('type', $data['PropertyType']);
			$property->city_id = $crea->getId('city', $data['Address']['City']);
			$property->province_id = $crea->getId('province', $data['Address']['Province']);

			if (strpos($data['Building']['SizeInterior'], 'sqft') !== false) {
				$sqft = trim(explode("sqft", $data['Building']['SizeInterior'])[0]);
				$property->sqft = (int)$sqft;
			}else{
				$sqft = trim(explode("m2", $data['Building']['SizeInterior'])[0]);
				$property->sqft = (int)((float)$sqft*10.7639);
			}
			// $property->featured = "N";
			$property->user_id = 1;
			$property->date_create = time();
			$property->date_update = time();
			$property->description = $data['PublicRemarks'];
			$property->zipcode = $data['Address']['PostalCode'];
			$property->save();

			$agent = new Agent();
			$agent->id = $property->id;
			$agent->property_id = $property->id;
			$agent->name = $data['AgentDetails']['Name'];
			$agent->phone = $data['AgentDetails']['Phones']['Phone'];
			$agent->address = $data['AgentDetails']['Office']['Address']['StreetAddress'];
			$agent->city = $data['AgentDetails']['Office']['Address']['City'];
			$agent->phones = implode(", ", $data['AgentDetails']['Office']['Phones']['Phone']);
			$agent->save();

			$features = explode(",", $data['Features']);
			foreach ($features as $feature) {
				$f = new Feature();
				$f->name = trim($feature);
				$f->property_id = $property->id;
				$f->save();
			}

			$first = true;
			foreach($photos as $photo)
			{
				if((!isset($photo['Content-ID']) || !isset($photo['Object-ID'])) ||
					(is_null($photo['Content-ID']) || is_null($photo['Object-ID'])) ||
					($photo['Content-ID'] == 'null' || $photo['Object-ID'] == 'null')){
					continue;
				}

				$path = "/public/upload/".$property->id."_".$photo['Object-ID'].".jpg";
				$photo_check = Photo::findByPath($path)->toArray();
				if(!empty($photo_check))
					continue;

				$destination = $_SERVER['DOCUMENT_ROOT']."/public/upload/".$property->id."_".$photo['Object-ID'].".jpg";

				file_put_contents($destination, $photo['Data']);
				$photo_new = new Photo();
				$photo_new->property_id = $property->id;
				$photo_new->path = $path;
				$photo_new->save();

				if($first){
					$property->photo_id = $photo_new->id;
					$property->save();
					$first = false;
				}
			}
		}
		$crea->next();
		echo $crea->offset."/".$crea->totalAvailable;
		if($crea->offset < $crea->totalAvailable){
			echo '<script>setTimeout(function() { window.location = "http://idsrealty-api.fgeekdemos.org/crea/new/?offset='.$crea->offset.'" }, 1000);</script>';
		}
	}

	public function dropAction(){
		Property::find()->delete();
		Feature::find()->delete();
		Photo::find()->delete();
		Agent::find()->delete();
	}
}
