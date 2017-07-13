<?php

use Phalcon\Mvc\Controller;

class PropertiesController extends Controller
{
	public function indexAction(){

		$query = Property::query()
			->columns([
				'id'		=> 'Property.id',
				'title' 	=> 'Property.title',
				'subtitle' 	=> 'Property.subtitle',
				'price'		=> 'Property.price',
				'photo'		=> 'Photo.path',
				'featured'	=> 'Property.featured',
				'bed' 		=> 'Property.bed',
				'bath' 		=> 'Property.bath',
				'sqft'		=> 'Property.sqft',
				'type_id'	=> 'Type.id',
				'type_name' => 'Type.name',
				'status_id'	=> 'Status.id',
				'status_name' => 'Status.name',
				'status_color' => 'Status.color',
				'date_sort'	=> 'Property.date_update',
				'date'		=> 'Property.date_update',
				'user'		=> 'User.name',
				'desc' 		=> 'Property.description',
				'lat'  		=> 'Property.lat',
				'lng'		=> 'Property.lng',
			])
			->limit(50)
			->groupBy(['Property.id'])
			->join('User', 'Property.user_id = User.id')
			->join('City', 'Property.city_id = City.id')
			->join('Province', 'Property.province_id = Province.id')
			->join('Status', 'Property.status_id = Status.id')
			->join('Photo', 'Property.photo_id = Photo.id')
			->join('Type', 'Property.type_id = Type.id')
			->where("Status.code = :status:", ['status' => $_POST['status']]);

		if(isset($_POST['beds'])){
			$query->betweenWhere('Property.bed', (int)$_POST['beds'][0], (int)$_POST['beds'][1]);
		}
		if(isset($_POST['price'])){
			$query->betweenWhere('Property.price', (int)$_POST['price'][0], (int)$_POST['price'][1]);
		}
		if(isset($_POST['sqft'])){
			$query->betweenWhere('Property.sqft', (int)$_POST['sqft'][0], (int)$_POST['sqft'][1]);
		}
		if(isset($_POST['baths'])){
			$query->betweenWhere('Property.bath', (int)$_POST['baths'][0], (int)$_POST['baths'][1]);
		}

		if(isset($_POST['type'])){
			if($_POST['type']=="commercial" || $_POST['type']=="residential")
				$query->andWhere("Type.code = :type:", ['type' => $_POST['type']]);
		}

		if(isset($_POST['query'])){
			if(trim($_POST['query']) != ""){
				$query->andWhere("Property.zipcode LIKE '%{$_POST['query']}%' or Province.name LIKE '%{$_POST['query']}%' or Property.title LIKE '%{$_POST['query']}%' or Property.subtitle LIKE '%{$_POST['query']}%' or City.name LIKE '%{$_POST['query']}%'");
			}
		}

		$query = $query->execute()->toArray();

		$items = [];
		$items_ids = [];
		foreach ($query as &$item) {

			$item['date'] = date("d.m.y", $item['date']);
			$item['date_sort'] = date("d.m.y", $item['date_sort']);
			$item['photos'] = [];
			$items[$item['id']] = $item;
			$items_ids[] = $item['id'];
		}

		$photos = Photo::query()
			->columns([
				'property_id' => 'Photo.property_id',
				'path' => 'Photo.path'
			])
			->inWhere('property_id', $items_ids)
			->execute()
			->toArray();

		foreach ($photos as $photo) {
			$items[$photo['property_id']]['photos'][] = $photo['path'];
		}

		Response::show([
            'items' => array_values($items)
        ]);
	}

	// public function indexAction(){
	// 	$query = Property::query()
	// 		->columns([
	// 			'id'		=> 'Property.id',
	// 			'title' 	=> 'Property.title',
	// 			'subtitle' 	=> 'Property.subtitle',
	// 			'photo'		=> 'File.path',
	// 			'price'		=> 'Property.price',
	// 			'bed' 		=> 'Property.bed',
	// 			'bath' 		=> 'Property.bath',
	// 			'sqft'		=> 'Property.sqft',
	// 			'type_id'	=> 'Property.type_id',
	// 			'type'		=> 'PropertyType.name',
	// 			'date_sort'	=> 'Property.date_update',
	// 			'date'		=> 'Property.date_update',
	// 			'user'		=> 'User.name',
	// 			'desc' 		=> 'Property.description',
	// 			'coord_x'  	=> 'Property.coord_x',
	// 			'coord_y'	=> 'Property.coord_y',
	// 		])
	// 		->limit(50)
	// 		->join('User', 'Property.user_id = User.id')
	// 		->join('File', 'Property.photo_id = File.id')
	// 		->join('PropertyType', 'Property.type_id = PropertyType.id');
	//
	// 	if(isset($_POST['subtitle']))
	// 		if(trim($_POST['subtitle']!=""))
	// 			$query->where("subtitle LIKE '%{$_POST['subtitle']}%'");
	//
	// 	if((int)$_POST['city']!=0)
	// 		$query->inWhere('city_id', [(int)$_POST['city']]);
	//
	// 	if((int)$_POST['area']!=0)
	// 		$query->inWhere('area_id', [(int)$_POST['area']]);
	//
	// 	if((int)$_POST['type']!=0)
	// 		$query->inWhere('type_id', [(int)$_POST['type']]);
	//
	// 	if((int)$_POST['bed']!=0){
	// 		$query->andWhere("bed = :bed:", ['bed' => (int)$_POST['bed']]);
	// 	}
	// 	if((int)$_POST['bath']!=0)
	// 		$query->andWhere("bath = :bath:", ['bath' => (int)$_POST['bath']]);
	//
	// 	$query->betweenWhere('price', (int)$_POST['price'][0], (int)$_POST['price'][1]);
	// 	$query->betweenWhere('sqft', (int)$_POST['sqft'][0], (int)$_POST['sqft'][1]);
	// 	$properties = $query->execute()->toArray();
	//
	// 	$properties__ids = [];
	// 	foreach ($properties as $property)
	// 		$properties__ids[] = $property['id'];
	//
	// 	$properties = PropertyStatuses::filterByStatus($_POST['status'], $properties, $properties__ids);
	//
	// 	if(isset($_POST['features']))
	// 		$properties = PropertyFeatures::filterByFeatures($_POST['features'], $properties, $properties__ids);
	//
	// 	$photos = [];
	// 	$tmp = Property::query()
	// 		->columns([
	// 			'id'		=> 'Property.id',
	// 			'count' 	=> 'COUNT(File.path)'
	// 		])
	// 		->groupBy("Property.id")
	// 		->join('File')
	// 		->inWhere('File.type', ['image'])
	// 		->execute()
	// 		->toArray();
	// 	foreach ($tmp as $photo) {
	// 		$photos[$photo['id']] = $photo['count'];
	// 	}
	//
	// 	foreach ($properties as &$property) {
	// 		$property["date"] = date("d.m.y", $property["date"]);
	// 		$property['photos'] = (isset($photos[$property['id']])) ? $photos[$property['id']] : "0";
	// 	}
	//
	// 	Response::show([
    //         'items' => $properties
    //     ]);
	// }

}
