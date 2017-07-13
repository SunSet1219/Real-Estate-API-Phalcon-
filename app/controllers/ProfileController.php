<?php

use Phalcon\Mvc\Controller;

class ProfileController extends Controller
{
    public function indexAction()
	{
        $user = User::access();
        $profile = User::query()
            ->columns([
                'agency' => 'User.agency',
                'name' => 'User.name',
                'email' => 'User.email',
                'location' => 'User.location',
                'public' => 'User.public',
                'email' => 'User.email',
                'website' => 'User.website',
                'bio' => 'User.bio',
                'photo_id' => 'User.photo_id',
                'photo_path' => 'Photo.path',
                'logo_id' => 'User.logo_id',
                'logo_path' => 'Logo.path'
            ])
            ->where("User.id = :user_id:", ['user_id' => $user['id']])
            ->leftJoin("File", 'Photo.id = User.photo_id', 'Photo')
            ->leftJoin("File", 'Logo.id = User.logo_id', 'Logo')
            ->execute()
            ->toArray();

        Response::show($profile[0]);
	}

    public function userAction()
	{
        $user = User::access();
        $profile = User::query()
            ->columns([
                'agency' => 'User.agency',
                'name' => 'User.name',
                'email' => 'User.email',
                'location' => 'User.location',
                'public' => 'User.public',
                'email' => 'User.email',
                'website' => 'User.website',
                'bio' => 'User.bio',
                'photo_id' => 'User.photo_id',
                'photo_path' => 'Photo.path',
                'logo_id' => 'User.logo_id',
                'logo_path' => 'Logo.path'
            ])
            ->where("User.id = :public: or User.public = :public:", ['public' => $_POST['public_id']])
            ->leftJoin("File", 'Photo.id = User.photo_id', 'Photo')
            ->leftJoin("File", 'Logo.id = User.logo_id', 'Logo')
            ->execute()
            ->toArray();
        if(!$profile)
            Response::error("404", 'not found');
            
        Response::show($profile[0]);
	}

    public function saveAction()
	{
        $user = User::access();
        $profile = User::findFirstByToken($_POST['token']);
        $profile->agency = $_POST['profile']['agency'];
        $profile->name = $_POST['profile']['name'];
        $profile->email = $_POST['profile']['email'];
        $profile->location = $_POST['profile']['location'];
        $profile->public = $_POST['profile']['public'];
        $profile->email = $_POST['profile']['email'];
        $profile->website = $_POST['profile']['website'];
        $profile->bio = $_POST['profile']['bio'];
        if($_POST['profile']['photo_id']!='')
            $profile->photo_id = $_POST['profile']['photo_id'];
        if($_POST['profile']['logo_id']!='')
            $profile->logo_id = $_POST['profile']['logo_id'];
        if ($profile->save() === false) {
            echo "Мы не можем сохранить робота прямо сейчас: \n";

            $messages = $profile->getMessages();

            foreach ($messages as $message) {
                echo $message, "\n";
            }
        }
        Response::ok();
	}



}
