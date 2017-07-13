<?php

use Phalcon\Mvc\Controller;

class FavoriteController extends Controller
{
	public function indexAction()
	{
        $user = User::access();
		$favorites = Favorite::query()
			->columns([
				'id' => 'Favorite.id',
				'name' => 'Favorite.name',
				'count' => 'COUNT(FavoriteProperty.property_id)',
			])
			->groupBy('Favorite.id')
			->orderBy('Favorite.id')
			->where('Favorite.user_id = :user_id:', ['user_id' => $user['id']])
			->leftJoin('FavoriteProperty', 'Favorite.id = FavoriteProperty.favorite_id')
			->execute()
			->toArray();
		Response::show([
			'items' => $favorites
		]);
	}

	public function addAction()
	{
        $user = User::access();

		if((int)$_POST['favorite_id']==0){
			$favorite = new Favorite();
			$favorite->name = $_POST['favorite_name'];
			$favorite->user_id = $user['id'];
			$favorite->save();

			$fav_id = $favorite->id;
		}else{
			$fav_id = (int)$_POST['favorite_id'];
		}

		if(isset($_POST['property_id'])){
			$check = FavoriteProperty::query()
				->where("favorite_id = :favorite_id:", ['favorite_id' => $fav_id])
				->andWhere("property_id = :property_id:", ['property_id' => (int)$_POST['property_id']])
				->execute()
				->toArray();
			if($check)
				Response::show([]);


			$fp = new FavoriteProperty();
			$fp->favorite_id = $fav_id;
			$fp->property_id = $_POST['property_id'];
			$fp->save();
		}
		Response::show([]);
	}

	public function propertiesAction(){
		$user = User::access();

		$fp = FavoriteProperty::query()
			->columns([
				'property_id' => 'FavoriteProperty.property_id',
			])
			->where('FavoriteProperty.favorite_id = :favorite_id:', ['favorite_id' => $_POST['favorite_id']])
			->execute()
			->toArray();

		if(!$fp)
			Response::show([
				'items' => []
			]);

		$properties__ids = [];
		foreach ($fp as $f) {
			$properties__ids[] = $f['property_id'];
		}

		$properties = Property::query()
			->columns([
				'property_id'	=> 'Property.id',
				'title' 	=> 'Property.title',
				'subtitle' 	=> 'Property.subtitle',
				'photo'		=> 'f.path',
				'price'		=> 'Property.price',
				'bed' 		=> 'Property.bed',
				'bath' 		=> 'Property.bath',
				'sqft'		=> 'Property.sqft',
				'type_id'	=> 'Property.type_id',
				'type'		=> 'PropertyType.name',
				'date_sort'	=> 'Property.date_update',
				'date'		=> 'Property.date_update',
				'user'		=> 'User.name',
				'desc' 		=> 'Property.description',
				'coord_x'  	=> 'Property.coord_x',
				'coord_y'	=> 'Property.coord_y',
			])
			->inWhere("Property.id", $properties__ids)
			->join('User', 'Property.user_id = User.id')
			->join('File', 'Property.photo_id = f.id', 'f')
			->join('PropertyType', 'Property.type_id = PropertyType.id')
			->execute()
			->toArray();



		foreach ($properties as &$property) {
			$property['id'] = $property['property_id'];
			unset($property['property_id']);
		}

		$properties = PropertyStatuses::filterByStatus(0, $properties, $properties__ids);

		$photos = [];
		$tmp = Property::query()
			->columns([
				'id'		=> 'Property.id',
				'count' 	=> 'COUNT(File.path)'
			])
			->groupBy("Property.id")
			->join('File')
			->inWhere('File.type', ['image'])
			->execute()
			->toArray();
		foreach ($tmp as $photo) {
			$photos[$photo['id']] = $photo['count'];
		}

		foreach ($properties as &$property) {
			$property["date"] = date("d.m.y", $property["date"]);
			$property['photos'] = (isset($photos[$property['id']])) ? $photos[$property['id']] : "0";
		}

		Response::show([
            'items' => $properties
        ]);
	}
}
