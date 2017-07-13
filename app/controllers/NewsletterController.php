<?php

use Phalcon\Mvc\Controller;

class NewsletterController extends Controller
{

	public function addAction()
	{
        $newsletter = new NewsLetter();
        $newsletter->date = time();
        $newsletter->email = $_POST['email'];
        $newsletter->save();

        Response::show([]);
	}
}
