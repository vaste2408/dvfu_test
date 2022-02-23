<?php

/**
 * КЛАСС ДЛЯ ПОДПИСИ СОТРУДНИКОВ НА СОБРАНИЕ В БД
 */
class ForumDB extends DBCRUD
{
    public function __construct($table = 'Forums'){
        parent::__construct($table);
    }

    public function store(Forum $forum) {
        $success = 1;
        foreach ($forum->employees_ids as $emp){
            //если хоть одна завершилась неуспехом, по-идее, надо откатить всё
            $success = $success && $this->create(array('id_employee' => $emp, 'id_meeting' => $forum->meeting_id));
        }
        return $success;
    }
}