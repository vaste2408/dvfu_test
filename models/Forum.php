<?php
/**
КЛАСС ПОДПИСАНИЯ СОТРУДНИКОВ НА СОБРАНИЯ
 */

class Forum
{
    public $meeting_id;
    public $employees_ids;

    //ЗДЕСЬ ПО-ИДЕЕ МОЖНО СДЕЛАТЬ КОНСТРУКТОР НА ОБЪЕКТАХ, А НЕ НА АЙДИШНИКАХ
    public function create_forum ($meeting_id, $employees_ids = []){
        $this->meeting_id = $meeting_id;
        $this->employees_ids = $employees_ids;
    }
}