<?php

use Phalcon\Mvc\Controller;

class FeaturedController extends Controller
{
	public function indexAction(){
		$featured = Property::query()
			->columns([
				'id'		=> 'Property.id',
				'title' 	=> 'Property.title',
				'subtitle' 	=> 'Property.subtitle',
				'photo'		=> 'Preview.path',
				'photos'	=> 'COUNT(Photos.id)',
				'price'		=> 'Property.price',
				'bed' 		=> 'Property.bed',
				'bath' 		=> 'Property.bath',
				'sqft'		=> 'Property.sqft',
			])
			->limit(12)
			->groupBy(['Property.id'])
			->join('Photo', 'Property.photo_id = Preview.id', 'Preview')
			->join('Photo', 'Property.id = Photos.property_id', 'Photos')
			->execute()
			->toArray();

        Response::show([
            'items' => $featured
        ]);
	}
}
