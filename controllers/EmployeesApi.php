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
        $this->ForumDB = new AgregateForumsDB(); //это коллекция собраний сотрудника
        parent::__construct();
    }

    /*========================================================
     * DISCLAIMER здесь не хватает валидации входящих параметров.
     * Я о ней не забыл, просто не вижу смысла её здесь писать,
     * поскольку способов валидации очень много,
     * и какой выбрать имеено Вам - решать не мне
     * (мы обычно используем form_validation)
     ========================================================*/

    public function indexAction()
    {
        //TODO если есть доп нагрузка параметрами, то надо фильтровать список, но пока сойдёт и так
        $emps = $this->EmpDB->read_all();
        if($emps){
            return $this->response($emps, 200);
        }
        return $this->response('Data not found', 404);
    }

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
            if($this->EmpDB->update_by_id($emp_id, array('fio' => $fio, 'department' => $department))){
                return $this->response('Data updated.', 200);
            }
        }
        return $this->response("Update error", 400);
    }

    public function deleteAction()
    {
        $parse_url = parse_url($this->requestUri[0]);
        $emp_id = $parse_url['path'] ?? null;

        if(!$emp_id || !$this->EmpDB->read_one($emp_id)){
            return $this->response("Employee with id = $emp_id not found", 404);
        }
        if($this->EmpDB->delete_by_id($emp_id)){
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
        return $this->response("Employee with id = $id not found", 404);
    }

    //функция поиска самого плотного расписания
    private function maxMeetingsDensity ($emp_id, $date = null) {
        $date = $date ? $date : date(self::_DATE_FORMAT); //не передали дату - берём "сегодня"
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

        // ФОРМИРУЕМ ГРАФЫ
        $graphs = []; $graph = null;
        foreach ($my_meetings as $ind => $met) {
            $cur_el_start = date_create_from_format(self::_DATETIME_FORMAT, $met['starts_at'])->format('H:i');
            $cur_el_end = date_create_from_format(self::_DATETIME_FORMAT, $met['ends_at'])->format('H:i');
            $prev_start = $cur_el_start;
            $prev_el_end = $cur_el_end;
            $new_node = new Node($met['id_meeting'], $met['name'], $cur_el_start, $cur_el_end);
            // СИТУАЦИИ:
            // Первые за день собрания начинаются одновременно
            if ($cur_el_start == $min_time_start){
                $graph = new Graph();
                $graph->add_node($new_node);
                $graphs.push($graph);
            }
            else{ //двинулись дальше
                foreach ($graphs as $gr){ //в каждом из существующих графов надо проводить пути
                    $add = false;
                    foreach ($gr->nodes as $nd){ //каждый узел в графе может соединиться с новым элементом
                        if ($cur_el_start >= $nd->ends){ //время начала нового элемента больше или такое же как время окончания предыдущего
                            $gr->add_path($nd, $new_node);
                            $add = true;
                        }
                    }
                    if ($add) //добавляем в граф только связанные точки
                        $gr->add_node($new_node);
                }
            }
        }
        $maximal_length = 0; //текущий максимум
        $maximal_paths = []; //на случай одинаково длинных путей

        foreach ($graphs as $graph){
            $algo = new Dijkstra ($graph); //ВОТ ЗДЕСЬ МАГИЯ
            $max_path = $algo->maximal_path(); //Ищем самый длинный путь
            $max_count = count($max_path); //сколько узлов в пути
            if ($max_count >= $maximal_length){ //самый длинный путь из найденых
                $maximal_paths[] = $max_path;
                $maximal_length = $max_count;
            }
        }
        return $maximal_paths; // искомое расписание с максимальной загрузкой сотрудника

    }
    /*
    $routes = array();
    $routes[] = array('from'=>'a', 'to'=>'b', 'price'=>100);
    $routes[] = array('from'=>'c', 'to'=>'d', 'price'=>300);
    $routes[] = array('from'=>'b', 'to'=>'c', 'price'=>200);
    $routes[] = array('from'=>'a', 'to'=>'d', 'price'=>900);
    $routes[] = array('from'=>'b', 'to'=>'d', 'price'=>300);

    printShortestPath('a', 'd', $routes);
     */

    function printShortestPath($graph, $from_name, $to_name, $routes) {
        foreach ($routes as $route) {
            $from = $route['from'];
            $to = $route['to'];
            $price = $route['price'];
            if (! array_key_exists($from, $graph->getNodes())) {
                $from_node = new Node($from);
                $graph->add($from_node);
            } else {
                $from_node = $graph->getNode($from);
            }
            if (! array_key_exists($to, $graph->getNodes())) {
                $to_node = new Node($to);
                $graph->add($to_node);
            } else {
                $to_node = $graph->getNode($to);
            }
            $from_node->connect($to_node, $price);
        }

        $g = new Dijkstra($graph);
        $start_node = $graph->getNode($from_name);
        $end_node = $graph->getNode($to_name);
        $g->setStartingNode($start_node);
        $g->setEndingNode($end_node);
        echo "From: " . $start_node->getId() . "\n";
        echo "To: " . $end_node->getId() . "\n";
        echo "Route: " . $g->getLiteralShortestPath() . "\n";
        echo "Total: " . $g->getDistance() . "\n";
    }
}