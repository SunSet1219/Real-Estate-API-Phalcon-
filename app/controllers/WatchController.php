<?php

use Phalcon\Mvc\Controller;

class WatchController extends Controller
{
	public function toggleAction()
	{
		$user = User::findFirstByToken($_POST['token']);

        $watch = Watch::query()
            ->where("property_id = :p_id: and user_id = :u_id:", ['p_id' => $_POST['property_id'], 'u_id' => $user->id])
            ->execute()
			->toArray();
        if($watch){
            $w = Watch::find($watch[0]['id']);
			$w->delete();
            Response::show([
                'watch' => 'no'
            ]);
        }else{
            $watch = new Watch();
            $watch->property_id = $_POST['property_id'];
            $watch->user_id = $user->id;
    		$watch->save();
            Response::show([
                'watch' => 'yes'
            ]);
        }
	}

}
