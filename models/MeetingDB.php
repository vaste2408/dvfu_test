<?php
/**
 * Created by PhpStorm.
 * User: vasiliev.aa
 * Date: 21.02.2022
 * Time: 14:02
 */

class MeetingDB extends DBObject
{
    const table = 'Meetings';

    public function __construct(){
        parent::__construct(self::table);
    }

    public function store(Meeting $met) {
        return $this->create(array('starts_at' => $met->starts_at, 'ends_at' => $met->ends_at, 'name' => $met->name, 'active' => $met->active));
    }
}