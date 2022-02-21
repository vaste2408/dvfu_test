<?php
/**
 * Created by PhpStorm.
 * User: vasiliev.aa
 * Date: 21.02.2022
 * Time: 14:04
 */

class ForumDB extends DBObject
{
    const table = 'Forums';

    public function __construct(){
        parent::__construct(self::table);
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