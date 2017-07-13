<?php

use Phalcon\Mvc\Controller;

class SavedsearchController extends Controller
{
	public function indexAction()
	{
        $user = User::access();
		$tmp = SavedSearch::query()
			->where('SavedSearch.user_id = :user_id:', ['user_id' => $user['id']])
			->execute()
			->toArray();

		$saved_search = [];
		foreach ($tmp as $ss) {
			$saved_search[] = [
				'id'   => $ss['id'],
				'name' => $ss['name'],
				'query' => [
					'query'  => $ss['query'],
					'status' => $ss['status'],
					'type'   => $ss['type'],
					'lat'    => $ss['lat'],
					'lng'    => $ss['lng'],
				]
			];
		}

		Response::show([
			'items' => $saved_search
		]);
	}

	public function deleteAction()
	{
        $user = User::access();

		$saved_search = SavedSearch::findFirstById($_POST['id']);
		$saved_search->delete();
		Response::show([]);
	}

	public function addAction()
	{
        $user = User::access();

		$saved_search = new SavedSearch();
		$saved_search->user_id = $user['id'];
		$saved_search->name = $_POST['name'];
		$saved_search->query = $_POST['query']['query'];
		$saved_search->status = $_POST['query']['status'];
		$saved_search->type = $_POST['query']['type'];
		$saved_search->lat = $_POST['query']['lat'];
		$saved_search->lng = $_POST['query']['lng'];
		$saved_search->save();
		if ($saved_search->save() === false) {
		    echo "Мы не можем сохранить робота прямо сейчас: \n";

		    $messages = $saved_search->getMessages();

		    foreach ($messages as $message) {
		        echo $message, "\n";
		    }
		}
		Response::show([]);
	}
}
