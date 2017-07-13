<?

class Request
{
    public static function debug($object = false){
        if(!$object){
            $object = $_POST;
        }
        $response = new \Phalcon\Http\Response();
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');
        $response->setJsonContent($object);
        $response->send();
    }

    public static function setDefault($fields){
        if(!isset($_POST))
            $_POST = [];
        foreach ($fields as $field => $value) {
            if(!isset($_POST[$field]) || $_POST[$field]==""){
                $_POST[$field] = $value;
            }
        }
    }

    public static function validate($fields){
        $fieldsString = "";
        foreach ($fields as $field) {
            if(!isset($_POST[$field]) || $_POST[$field]==""){
                $fieldsString = $fieldsString.$field.",";
            }
        }
        if($fieldsString!=""){
            $fieldsString = rtrim($fieldsString, ",");
            Response::error('404', "fields: {$fieldsString}  --not found");
        }
    }
}
