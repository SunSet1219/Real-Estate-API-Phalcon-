<?php

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{

	public function indexAction()
	{

		echo "asdasd";
		echo Phalcon\Version::get();

		$this->metatag->setTitle('Phalcon MetaTags Service');

        $this->metatag->setCustom("charset", ['charset' => 'UTF-8']);
        $this->metatag->setCustom("http", ['http-equiv' => 'content-type', 'content' => 'text/html; charset"=UTF-8']);

        $this->metatag->setByName("description", 'phal"con php metatags');

        $this->metatag->setByProperty("og:description", "When Great Minds Don’t Think Alike");
	}
}
