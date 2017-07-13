<?php

use Phalcon\Mvc\Controller;

class SliderController extends Controller
{

	public function indexAction()
	{
		$items = Slider::query()
			->columns([
				'id'		=> 'Slider.id',
				'title' 	=> 'Slider.title',
				'subtitle' 	=> 'Slider.subtitle',
				'image' 	=> 'File.path'
			])
			->join('File')
			->execute()
			->toArray();

        Response::show([
            'items' => $items
        ]);
	}
}
