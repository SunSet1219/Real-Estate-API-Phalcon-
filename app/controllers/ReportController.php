<?php

use Phalcon\Mvc\Controller;

class ReportController extends Controller
{
	public function addAction()
	{
        $report = new Report();
        $report->property_id = $_POST['property_id'];
        $report->text = $_POST['text'];
        $report->timestamp = time();
		$report->save();
		
	    Response::ok();
	}

}
