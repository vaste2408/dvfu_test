<?php
/**
КЛАСС ДЛЯ ОБЪЕДИНЕНИЯ ВСЕХ 3Х СУЩНОСТЕЙ (скорее всего на базе Materialized View в БД)
 */

class AgregateForumsDB extends DBObject {
    const table = 'AgregateView';

    public function __construct(){
        parent::__construct(self::table);
    }

    public function all_employee_meetings ($emp_id, $day = null) {
        $args = Array('id_employee' => $emp_id);
        if ($day)
            $args['DATE(starts_at)'] = $day;
        return $this->read_all($args);
    }
}