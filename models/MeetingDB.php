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
        return $this->create(array('starts_at' => $met->getStartsAt(), 'ends_at' => $met->getEndsAt(), 'name' => $met->getName(), 'active' => $met->getActive()));
    }

    public function change(Meeting $met) {
        if (!$met->check_completeness())
            return false;
        return $this->update_by_id($met->getId(), array('starts_at' => $met->getStartsAt(), 'ends_at' => $met->getEndsAt(), 'name' => $met->getName()));
    }
}