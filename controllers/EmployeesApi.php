<?php
// ДЛЯ СОТРУДНИКА
class EmployeesApi extends Rest
{
    public $apiName = 'employees';
    private $EmpDB;
    private $ForumDB;

    const _DATE_FORMAT = "Y-m-d";
    const _DATETIME_FORMAT = "Y-m-d H:i:s";

    public function __construct() {
        $this->EmpDB = new EmployeeDB();
        $this->ForumDB = new ForumDB(); //это коллекция собраний сотрудника
        parent::__construct();
    }

    /**
     * Метод GET
     * Вывод списка всех записей
     * http://ДОМЕН/employees
     * @return string
     */
    public function indexAction()
    {
        //TODO если есть доп нагрузка параметрами, то надо фильтровать список, но пока сойдёт и так
        $emps = $this->EmpDB->read_all();
        if($emps){
            return $this->response($emps, 200);
        }
        return $this->response('Data not found', 404);
    }

    /**
     * Метод GET
     * Просмотр отдельной записи (по id)
     * http://ДОМЕН/employees/1
     * @return string
     */
    public function viewAction()
    {
        $id = array_shift($this->requestUri);

        if($id){
            $emp = $this->EmpDB->read_one($id);
            if($emp){
                return $this->response($emp, 200);
            }
        }
        return $this->response('Data not found', 404);
    }

    /**
     * Метод POST
     * Создание новой записи
     * http://ДОМЕН/employees + параметры запроса
     * @return string
     */
    public function createAction()
    {
        $fio = $this->requestParams['fio'] ?? '';
        $department = $this->requestParams['department'] ?? '';
        if($fio && $department){
            $emp = new Employee($fio, $department);
            if($this->EmpDB->store($emp)){
                return $this->response('Data saved.', 200);
            }
        }
        return $this->response("Saving error", 500);
    }

    /**
     * Метод PUT
     * Обновление отдельной записи (по ее id)
     * http://ДОМЕН/employees/1 + параметры запроса fio, department
     * @return string
     */
    public function updateAction()
    {
        $parse_url = parse_url($this->requestUri[0]);
        $emp_id = $parse_url['path'] ?? null;

        if(!$emp_id || !$dbemp = $this->EmpDB->read_one($emp_id)){
            return $this->response("Employee with id = $emp_id not found", 404);
        }

        $fio = $this->requestParams['fio'] ?? $dbemp['fio'];
        $department = $this->requestParams['department'] ?? $dbemp['department'];

        // обновилось хоть одно из полей - можно апдейтить
        if($fio != $dbemp['fio'] || $department != $dbemp['department']){
            if($this->EmpDB->update($emp_id, array('fio' => $fio, 'department' => $department))){
                return $this->response('Data updated.', 200);
            }
        }
        return $this->response("Update error", 400);
    }

    /**
     * Метод DELETE
     * Удаление отдельной записи (по ее id)
     * http://ДОМЕН/employees/1
     * @return string
     */
    public function deleteAction()
    {
        $parse_url = parse_url($this->requestUri[0]);
        $emp_id = $parse_url['path'] ?? null;

        if(!$emp_id || !$this->EmpDB->read_one($emp_id)){
            return $this->response("Employee with id = $emp_id not found", 404);
        }
        if($this->EmpDB->delete($emp_id)){
            return $this->response('Data deleted.', 200);
        }
        return $this->response("Delete error", 500);
    }

    /**
     * Метод GET расписание
     * http://ДОМЕН/employees/{id}/schedule
     * @return string
     */
    public function schedule(){
        $id = array_shift($this->requestUri);

        if($id){
            $schedule = $this->maxMeetingsDensity($id);
            if($schedule){
                return $this->response($schedule, 200);
            }
        }
        return $this->response('Data not found', 404);
    }

    private function maxMeetingsDensity ($emp_id, $date = null) {
        $date = $date ? $date : date(self::_DATE_FORMAT);
        /*
        функционально я этого не реализовывал, но будем для облегчения предполагать,
        что все мероприятия сотрудника выстроены по возрастанию стартового времени,
        чтобы не заморачиваться здесь с сортировкой (благо это легко делается индексом или сортировкой в БД)
        */
        // получаем мероприятия на выбранную дату
        $my_meetings = $this->ForumDB->all_employee_meetings($emp_id, $date);
        if (count($my_meetings) == 0)
            return null;

        // определяем минимальное время
        $times = [];
        foreach ($my_meetings as $met) {
            $times.push(date_create_from_format(self::_DATETIME_FORMAT, $met['starts_at']));
        }
        $min_time_start = min($times);

        // формируем стартовые графы
        $graphs = [];
        $graph = [];
        foreach ($my_meetings as $ind => $met) {
            $cur_el_start = date_create_from_format(self::_DATETIME_FORMAT, $met['starts_at'])->format('H:i');
            $cur_el_end = date_create_from_format(self::_DATETIME_FORMAT, $met['ends_at'])->format('H:i');
            $prev_start = $cur_el_start;
            $prev_el_end = $cur_el_end;
            // СИТУАЦИИ:
            //ЭТО ПЕРВЫЙ ЭЛЕМЕНТ
            if ($ind  == 0) {
            }
            //ЭТО НЕ ПЕРВЫЙ ЭЛЕМЕНТ
            else {
                //ОН НАЧАЛСЯ В ОДНО ВРЕМЯ С ПРЕДЫДУЩИМ
                if ($cur_el_start == $prev_start){

                }
                //ОН НАЧАЛСЯ В ДРУГОЕ ВРЕМЯ (ПОЗЖЕ)
                if ($cur_el_start > $prev_start){
                    //ОН НАЧАЛСЯ ПОЗЖЕ, ЧЕМ ЗАКОНЧИТСЯ ПРЕДЫДУЩЕЕ
                    if ($cur_el_start > $prev_el_end){

                    }
                    //НЕТ
                    else{

                    }
                }
            }

            // узел
            $vert = array(
                'id' => $met['id_meeting'],
                'start' => $cur_el_start,
                'end' => $cur_el_end,
                'parent' => null
            );
            // засовываем вершину в граф
            $graph.push($vert);

        }
        $maximal_length = 0; //текущий максимум
        $maximal_paths = []; //на случай одинаково длинных путей
        $maximal_path = array(); //текущий макс путь
        // используем стандартный класс дерева, т.к. он оптимизирован
        foreach ($graphs as $data){
            $tree = new Tree();
            // заполняем дерево данными
            foreach ($data as $item)
            {
                $tree->addItem(
                    $item['id'],
                    $item['parent']
                );
            }
            $algo = new Deixtra ($tree); //ВОТ ЗДЕСЬ МАГИЯ
            $max_path = $algo->maximal_path(); //Ищем самый длинный путь
            $max_count = count($max_path); //сколько узлов в пути
            if ($max_count >= $maximal_length){ //самый длинный путь из найденых
                $maximal_path = $max_path;
                $maximal_paths[] = $max_path;
                $maximal_length = $max_count;
            }
        }
        return $maximal_paths; // искомое расписание с максимальной загрузкой сотрудника

    }
}