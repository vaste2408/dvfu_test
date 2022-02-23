<?php

/**
 * КЛАСС СОБРАНИЙ ДЛЯ ВЗАИМОДЕЙСТВИЯ С БД
 */
class MeetingDB extends DBCRUD
{
    public function __construct($table = 'Meetings'){
        parent::__construct($table);
    }

    public function store(Meeting $met) {
        return $this->create(array('starts_at' => $met->starts_at, 'ends_at' => $met->ends_at, 'name' => $met->name, 'active' => $met->active));
    }
}