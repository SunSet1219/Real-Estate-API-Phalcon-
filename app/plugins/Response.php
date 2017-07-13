<?

class Response
{
    public static function show($data){
        $data = array_merge(['status' => 'success'], $data);
        $response = new \Phalcon\Http\Response();
        $response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');
        $response->setJsonContent($data);
        $response->send();
        die();
    }
    public static function json($data){
        $response = new \Phalcon\Http\Response();
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');
        $response->setJsonContent($data);
        $response->send();
        die();
    }
    public static function allow(){
        $response = new \Phalcon\Http\Response();
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');
        $response->sendHeaders();
    }

    public static function ok(){
        $response = new \Phalcon\Http\Response();
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');
        $response->setJsonContent(['status' => 'success']);
        $response->send();
    }
    public static function error($code, $message){
        $response = new \Phalcon\Http\Response();
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');
        $response->setJsonContent([
            'status' => 'error',
            'code'  => $code,
            'message' => $message
        ]);
        $response->send();
        die();
    }
}
