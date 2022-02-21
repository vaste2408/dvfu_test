<?php
/**
БАЗОВЫЙ КОНТРОЛЛЕР
 */

abstract class Rest
{
    protected $method = ''; //Get, post, put, delete
    protected $endpoint = ''; //index, view, create, update, delete или что-то своё

    public $apiName = '';
    public $requestUri = [];
    public $requestParams = [];

    public function __construct() {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        //Массив GET параметров разделенных слешем
        $this->requestUri = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
        $this->requestParams = $_REQUEST;

        //Определение метода запроса
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }
    }

    public function run() {
        if(array_shift($this->requestUri) !== $this->apiName){
            throw new RuntimeException('API Not Found', 404);
        }
        //Определение действия для обработки
        $this->endpoint = $this->getEndpoint();

        //Если метод(действие) определен в дочернем классе API
        if (method_exists($this, $this->endpoint)) {
            //Очистка входящих параметров
            $this->cleanInputs();
            return $this->{$this->endpoint}();
        } else {
            throw new RuntimeException('Invalid Method', 405);
        }
    }

    protected function getEndpoint()
    {
        //при структурах типа /controller/{id}/action надо ещё проверять на метод
        //controller был извлечен шагом выше, 0 - {id}, 1 - action
        if ($this->requestUri[1])
            return $this->requestUri[1];
        $method = $this->method;
        switch ($method) {
            case 'GET':
                if($this->requestUri){
                    return 'viewAction';
                } else {
                    return 'indexAction';
                }
                break;
            case 'POST':
                return 'createAction';
                break;
            case 'PUT':
                return 'updateAction';
                break;
            case 'DELETE':
                return 'deleteAction';
                break;
            default:
                return null;
        }
    }

    protected function response($data, $status = 500) {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        return json_encode($data);
    }

    private function requestStatus($code) {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return $status[$code] ? $status[$code] : $status[500];
    }

    private function _cleanData($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanData($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }

    protected function cleanInputs(){
        switch($this->method) {
            case 'DELETE':
            case 'POST':
                $this->requestParams = $this->_cleanData($_POST);
                break;

            case 'GET': case 'PUT':
                $this->requestParams = $this->_cleanData($_GET);
                break;

            default:
                $this->requestParams = null;
                break;
        }
    }

    abstract protected function indexAction();
    abstract protected function viewAction();
    abstract protected function createAction();
    abstract protected function updateAction();
    abstract protected function deleteAction();
}
