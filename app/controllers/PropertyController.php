<?php

use Phalcon\Mvc\Controller;

class PropertyController extends Controller
{

	public function indexAction()
	{
		$user = User::findFirstByToken($_POST['token']);

		$property = Property::findFirstById($_POST['id'])->toArray();
		$property['photo'] = Photo::findFirstById($property['photo_id'])->toArray()['path'];
		$property['type'] = Type::findFirstById($property['type_id'])->toArray()['name'];
		$property['user'] = User::findFirstById($property['user_id'])->toArray()['name'];
		$property['city'] = City::findFirstById($property['city_id'])->toArray()['name'];
		$property['province'] = Province::findFirstById($property['province_id'])->toArray()['name'];
		$property['agent'] = Agent::findFirstByProperty_id($property['id'])->toArray();

		if(!$user){
			$property['watch'] = "no";
		}else{
			$watch = Watch::query()
	            ->where("property_id = :p_id: AND user_id = :u_id:", ['p_id' => $_POST['id'], 'u_id' => $user->id])
	            ->execute()
				->toArray();
			if($watch){
				$property['watch'] = "yes";
			}
		}
		$property['photos'] =  Photo::query()
					->columns([
						'path' => 'Photo.path'
					])
					->distinct(true)
					->where('Photo.property_id = :id:', ['id' => $_POST['id']])
					->execute()
					->toArray();

		$property['attachments'] = [];
		$property['features'] =  Feature::query()
					->columns([
						'name' => 'Feature.name'
					])
					->where('Feature.property_id = :id:', ['id' => $_POST['id']])
					->execute()
					->toArray();


		unset($property['photo_id'], $property['type_id'], $property['user_id'], $property['city_id'], $property['area_id']);
		Response::show([
			'item' => $property
		]);
	}

}
