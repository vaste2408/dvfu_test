<?php

/**
 * КЛАСС СОТРУДНИКОВ ДЛЯ ВЗАИМОДЕЙСТВИЯ С БД
 */
class EmployeeDB extends DBCRUD
{
    public function __construct($table = 'Employees'){
        parent::__construct($table);
    }

    public function store(Employee $emp) {
        return $this->create(array('fio' => $emp->fio, 'department' => $emp->department, 'active' => $emp->active));
    }

    // по-идее, надо аналогично делать методы на апдейт и делит, но для простоты далее в коде будет использоваться метод базового класса без построения объектов Employee
}