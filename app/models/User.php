<?php

use Phalcon\Mvc\Model;

class User extends Model
{
	public static function access(){
		if(!isset($_POST['token']) || $_POST['token']==""){
			Response::error('u412', 'token not found');
		}
		$user = User::findFirstByToken($_POST['token']);
		if(!$user)
			Response::error("u404", "bad access token");

		return $user->ToArray();
	}

}
