<?php

/**
 * КЛАСС ДЛЯ ПОЛУЧЕНИЯ ИЗ БД ОБЪЕДИНЕНИЯ ВСЕХ 3Х СУЩНОСТЕЙ
 * ОН НЕ УМЕЕТ РЕАЛИЗОВЫВАТЬ CUD, поэтому наследуем от базового класса
 */
class AgregateForumsDB extends DBObject
{
    public function __construct($table = 'AgregateView'){
        parent::__construct($table);
    }

    public function all_employee_meetings ($emp_id, $day = null) {
        $args = Array('id_employee' => $emp_id);
        if ($day)
            $args['DATE(starts_at)'] = $day;
        return $this->read_all($args);
    }
}