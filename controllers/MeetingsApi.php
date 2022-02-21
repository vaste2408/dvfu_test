<?php
//ДЛЯ СОБРАНИЯ

class MeetingsApi extends Rest
{
    public $apiName = 'meetings';
    private $MetDB;
    private $ForumDB;

    public function __construct() {
        $this->MetDB = new MeetingDB();
        $this->ForumDB = new ForumDB(); //это коллекция собраний сотрудника
        parent::__construct();
    }

    /**
     * Метод GET
     * Вывод списка всех записей
     * http://ДОМЕН/meetings
     * @return string
     */
    public function indexAction()
    {
        //TODO если есть доп нагрузка параметрами, то надо фильтровать список, но пока сойдёт и так
        $data = $this->MetDB->read_all();
        if($data){
            return $this->response($data, 200);
        }
        return $this->response('Data not found', 404);
    }

    /**
     * Метод GET
     * Просмотр отдельной записи (по id)
     * http://ДОМЕН/meetings/1
     * @return string
     */
    public function viewAction()
    {
        //id должен быть первым параметром после /employees/x
        $id = array_shift($this->requestUri);

        if($id){
            $data = $this->MetDB->read_one($id);
            if($data){
                return $this->response($data, 200);
            }
        }
        return $this->response('Data not found', 404);
    }

    /**
     * Метод POST
     * Создание новой записи
     * http://ДОМЕН/meetings + параметры запроса starts_at, ends_at
     * @return string
     */
    public function createAction()
    {
        $starts_at = $this->requestParams['starts_at'] ?? '';
        $ends_at = $this->requestParams['ends_at'] ?? '';
        if($starts_at && $ends_at){
            $new = new Meeting($starts_at, $ends_at);
            if($this->MetDB->store($new)){
                return $this->response('Data saved.', 200);
            }
        }
        return $this->response("Saving error", 500);
    }

    /**
     * Метод PUT
     * Обновление отдельной записи (по ее id)
     * http://ДОМЕН/meetings/1 + параметры запроса starts_at, ends_at
     * @return string
     */
    public function updateAction()
    {
        $parse_url = parse_url($this->requestUri[0]);
        $id = $parse_url['path'] ?? null;

        if(!$id || !$dbdata = $this->MetDB->read_one($id)){
            return $this->response("Meeting with id = $id not found", 404);
        }

        $starts_at = $this->requestParams['starts_at'] ?? $dbdata['starts_at'];
        $ends_at = $this->requestParams['ends_at'] ?? $dbdata['ends_at'];

        // обновилось хоть одно из полей - можно апдейтить
        if($starts_at != $dbdata['starts_at'] || $ends_at != $dbdata['ends_at']){
            if($this->MetDB->update($id, array('starts_at' => $starts_at, 'ends_at' => $ends_at))){
                return $this->response('Data updated.', 200);
            }
        }
        return $this->response("Update error", 400);
    }

    /**
     * Метод DELETE
     * Удаление отдельной записи (по ее id)
     * http://ДОМЕН/meetings/1
     * @return string
     */
    public function deleteAction()
    {
        $parse_url = parse_url($this->requestUri[0]);
        $id = $parse_url['path'] ?? null;

        if(!$id || !$this->MetDB->read_one($id)){
            return $this->response("Meeting with id = $id not found", 404);
        }
        if($this->MetDB->delete($id)){
            return $this->response('Data deleted.', 200);
        }
        return $this->response("Delete error", 500);
    }

    /**
     * Метод POST
     * Внесение сотрудника на собрание
     * http://ДОМЕН/meetings/{id}/add_employee + параметры запроса id_employee
     * @return string
     */
    public function add_employee(){
        if ($this->method != 'POST')
            return $this->response("Method should be of POST type", 404);

    }
}