<?php

use Phalcon\Mvc\Controller;

class UserController extends Controller
{
    public function checkAction()
	{
        $user = User::access();
        Response::show([
			'username' => $user['login']
		]);
	}

    public function registerAction()
	{
        $email = $_REQUEST['email'];
        $password = $_REQUEST['password'];
        $login = $_REQUEST['login'];

        $user = User::query()
                ->inWhere("login", [$login])
                ->execute()
                ->toArray();

		if($user)
			Response::error("u409", "Login is already registered");

        $user = User::query()
                ->inWhere("email", [$email])
                ->execute()
                ->toArray();

		if($user)
			Response::error("u409", "Email is already registered");


        $user = new User();
		$user->email = $email;
		$user->password = md5($password);
        $user->login = $login;
        $user->token = md5($email.$password."salt").time();
        $user->save();
		Response::show([
            'token' => $user->token,
            'username' => $user->login
		]);
	}

	public function loginAction()
	{
        $login = $_POST['login'];
        $password = $_POST['password'];

        Request::validate(['login', 'password']);

		$user = User::findFirstByLogin($login);
		if(!$user)
			Response::error("u404", "Invalid user or password");

		if($user->password != md5($password))
			Response::error("u404", "Invalid user or password");

		Response::show([
			'token' => $user->token,
            'username' => $user->login
		]);
	}


}
