<?php

use Phalcon\Mvc\Controller;

class SearchController extends Controller
{
	public function autocompleteAction(){
		if($_POST['query']=="")
			Response::show([
	            'items' => []
	        ]);

		$autocomplete = [];
		$properties = [];

		$tmp = Property::query()
			->columns([
				'name' => 'Property.title'
			])
			->distinct(true)
			->join('Status', 'Property.status_id = Status.id')
			->join('Type', 'Property.type_id = Type.id')
			->where("Property.title LIKE '%{$_POST['query']}%'");
		if($_POST['type'] == "commercial" || $_POST['type'] == "residential"){
			$tmp->andWhere("Status.code = :status: AND Type.code = :type:", ['status' => $_POST['status'], 'type' => $_POST['type']]);
		}else{
			$tmp->andWhere("Status.code = :status:", ['status' => $_POST['status']]);
		}
		$tmp = $tmp->execute()->toArray();

		foreach ($tmp as $item)
			$properties[trim($item['name'])] = $item['name'];

		$tmp = Property::query()
			->columns([
				'_name' => 'City.name'
			])
			->distinct(true)
			->join('City', 'Property.city_id = City.id')
			->join('Status', 'Property.status_id = Status.id')
			->join('Type', 'Property.type_id = Type.id')
			->where("City.name LIKE '%{$_POST['query']}%'");
		if($_POST['type'] == "commercial" || $_POST['type'] == "residential"){
			$tmp->andWhere("Status.code = :status: AND Type.code = :type:", ['status' => $_POST['status'], 'type' => $_POST['type']]);
		}else{
			$tmp->andWhere("Status.code = :status:", ['status' => $_POST['status']]);
		}
		$tmp = $tmp->execute()->toArray();

		foreach ($tmp as $item)
			$autocomplete[] = $item['_name'];

		$tmp = Property::query()
			->columns([
				'_name' => 'Province.name'
			])
			->distinct(true)
			->join('Province', 'Property.province_id = Province.id')
			->join('Status', 'Property.status_id = Status.id')
			->join('Type', 'Property.type_id = Type.id')
			->where("Province.name LIKE '%{$_POST['query']}%'");
		if($_POST['type'] == "commercial" || $_POST['type'] == "residential"){
			$tmp->andWhere("Status.code = :status: AND Type.code = :type:", ['status' => $_POST['status'], 'type' => $_POST['type']]);
		}else{
			$tmp->andWhere("Status.code = :status:", ['status' => $_POST['status']]);
		}
		$tmp = $tmp->execute()->toArray();

		foreach ($tmp as $item)
			$autocomplete[] = $item['_name'];

		$properties = array_values($properties);
		shuffle($properties);
		$properties = array_slice($properties, 0, 20);
	
		$result = array_merge($autocomplete, $properties);

		Response::show([
            'items' => $result
        ]);
	}

	// public function indexAction()
	// {
    //     $data = [];
    //     $data['statuses'] = array_merge([['id' => '0', 'name' => 'All Status']], PropertyStatus::find()->toArray());
    //     $data['types'] = array_merge([['id' => '0', 'name' => 'All Types']], PropertyType::find()->toArray());
	// 	$data['features'] = PropertyFeature::find()->toArray();
	// 	$data['cities'] = array_merge([['id' => '0', 'name' => 'All Provinces']], City::find()->toArray());
	//
    //     Response::show([
    //         'data' => $data
    //     ]);
	// }
	//
	// public function areasAction(){
	// 	Response::show([
    //         'areas' => array_merge([['id' => '0', 'name' => 'All Cities']], CityArea::findByCityId($_POST['city_id'])->toArray())
    //     ]);
	// }
	//
	// public function subtitleAction(){
	// 	$subtitles = Property::query()
	// 		->columns("subtitle")
	// 		->distinct(true)
	// 		->limit(20)
	// 		->where("subtitle LIKE '%{$_POST['subtitle']}%'")
	// 		->execute()
	// 		->toArray();
	//
	// 	$items = [];
	// 	foreach ($subtitles as $s) {
	// 		$items[] = $s['subtitle'];
	// 	}
	// 	Response::show([
    //         'items' => $items
    //     ]);
	// }

}
