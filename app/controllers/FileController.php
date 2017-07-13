<?php

use Phalcon\Mvc\Controller;

class FileController extends Controller
{

	public function indexAction()
	{
	}

    public function uploadAction($type = 'profile'){
		$response = new \Phalcon\Http\Response();
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader("Access-Control-Allow-Methods", "GET,HEAD,OPTIONS,POST,PUT");
        $response->setHeader("Access-Control-Allow-Headers", "authorization,cache-control,x-requested-with");
        $response->sendHeaders();
        Request::setDefault([ 'sort' => 500, 'description' => '']);
        $_POST['type'] = $type;
        if (!empty($_FILES)) {
            if(!isset($_FILES['file'])){
                Response::error("f400", "file not found");
            }
            $_FILES['file']['name'] = Transform::translit($_FILES['file']['name']);
            $uploadFile = $_FILES['file'];
            $rand_name = time().rand(33,1344);
            $targetFile = $_SERVER['DOCUMENT_ROOT']."/public/upload/".$rand_name.$uploadFile['name'];
            move_uploaded_file($uploadFile['tmp_name'] , $targetFile);

			if($_POST['type']=="image"){
				$image = new Phalcon\Image\Adapter\GD($_SERVER['DOCUMENT_ROOT']."/public/upload/".$rand_name.$uploadFile['name']);
				$image->resize(1920, 1200);
				$image->save($_SERVER['DOCUMENT_ROOT']."/public/upload/".$rand_name.$uploadFile['name'], 70);
			}

            $file = new File();
            $file->path = "/upload/".$rand_name.$uploadFile['name'];
            $file->sort = $_POST['sort'];
            $file->type = $_POST['type'];
            $file->description = $_POST['description'];
            $file->save();
            echo json_encode([
    			'file_id' => $file->id,
				'file_path' => $file->path,
    		]);
        }
    }
}
