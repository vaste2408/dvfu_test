<?php

/**
КЛАСС СОТРУДНИКА
 */
class Employee
{
    protected $id;
    public $fio;
    public $department;
    public $active = 1;

    public function __construct($fio, $department, $active = 1){
        $this->fio = $fio;
        $this->department = $department;
        $this->active = $active;
    }
}