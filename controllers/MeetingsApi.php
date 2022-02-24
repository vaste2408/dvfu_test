<?php

/**
 * КОНТРОЛЛЕР СОБРАНИЙ
 */
class MeetingsApi extends Rest
{
    private $MetDB;
    private $ForumDB;

    public function __construct($apiName = 'meetings') {
        $this->apiName = $apiName;
        $this->MetDB = new MeetingDB();
        $this->ForumDB = new ForumDB(); //это коллекция собраний сотрудника
        parent::__construct();
    }

    /*========================================================
     * DISCLAIMER здесь не хватает валидации входящих параметров (как минимум)
     * Я о ней не забыл, просто не вижу смысла её здесь писать,
     * поскольку способов валидации очень много
     ========================================================*/

    public function indexAction()
    {
        //TODO если есть доп нагрузка параметрами, то надо фильтровать список, но пока сойдёт и так
        $data = $this->MetDB->read_all();
        if($data){
            return $this->response($data, 200);
        }
        return $this->response('Data not found', 404);
    }

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

    public function createAction()
    {
        $starts_at = $this->requestParams['starts_at'] ?? '';
        $ends_at = $this->requestParams['ends_at'] ?? '';
        $name = $this->requestParams['name'] ?? '';
        if($starts_at && $ends_at && $name){
            $new = new Meeting();
            $new->setStartsAt($starts_at);
            $new->setEndsAt($ends_at);
            $new->setName($name);
            if($this->MetDB->store($new)){
                return $this->response('Data saved.', 200);
            }
        }
        return $this->response("Saving error", 500);
    }

    public function updateAction()
    {
        $parse_url = parse_url($this->requestUri[0]);
        $id = $parse_url['path'] ?? null;

        if(!$id || !$dbdata = $this->MetDB->read_one($id)){
            return $this->response("Meeting with id = $id not found", 404);
        }

        $met = new Meeting($id);

        $starts_at = $this->requestParams['starts_at'] ?? $dbdata['starts_at'];
        $ends_at = $this->requestParams['ends_at'] ?? $dbdata['ends_at'];
        $name = $this->requestParams['name'] ?? $dbdata['name'];

        $met->setStartsAt($starts_at);
        $met->setEndsAt($ends_at);
        $met->setName($name);

        if (!$met->check_completeness())
            return $this->response("Update error, not all parameters given", 400);

        // обновилось хоть одно из полей - можно апдейтить
        if($starts_at != $dbdata['starts_at'] || $ends_at != $dbdata['ends_at'] || $name != $dbdata['name']){
            if($this->MetDB->change($met)){
                return $this->response('Data updated.', 200);
            }
        }
        else
            return $this->response('No data changing received', 200);
        return $this->response("Update error", 400);
    }

    public function deleteAction()
    {
        $parse_url = parse_url($this->requestUri[0]);
        $id = $parse_url['path'] ?? null;

        if(!$id || !$this->MetDB->read_one($id)){
            return $this->response("Meeting with id = $id not found", 404);
        }
        if($this->MetDB->delete_by_id($id)){
            return $this->response('Data deleted.', 200);
        }
        return $this->response("Delete error", 500);
    }

    /**
     * TODO по-идее, это scope другой сущности, но я не придумал, как это грамотно засунуть в идеологию REST.
     * TODO к тому же эта функция нарушает приницы S и O
     *
     * CRD для сводной таблицы
     * http://ДОМЕН/meetings/{id}/employees + параметры запроса id_employees:array
     * @return string
     */
    public function employees(){
        switch ($this->method){
            case 'GET':
                $data = $this->ForumDB->read_all(array('id_meeting' => $this->requestParams['id_meeting']));
                return $this->response($data, 200);
                break;
            case 'POST':
                $employees = $this->requestParams['id_employees'];
                if (!$employees || !count($employees))
                    return $this->response("No data provided. Expecting array id_employees", 404);
                $forum = new Forum($this->requestParams['id_meeting'], $employees);
                $success = $this->ForumDB->store($forum);
                if (!$success)
                    return $this->response("Error saving data [" . implode(', ', $employees) . "]", 500);
                return $this->response('Data saved.', 200);
                break;
            case 'DELETE':
                $employees = $this->requestParams['id_employees'];
                if (!$employees || !count($employees))
                    return $this->response("No data provided. Expecting array id_employees", 404);
                $success = true;
                foreach ($employees as $emp){
                    $success = $success && $this->ForumDB->delete_by_id($emp, false);
                }
                if (!$success)
                    return $this->response("Error deleting data [" . implode(', ', $employees) . "]", 500);
                return $this->response('Data deleted.', 200);
                break;
            default:
                return $this->response("Unknown method type. Expecting GET / POST / DELETE", 404);
                break;
        }
    }
}