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
        return $this->create(array('fio' => $emp->getFio(), 'department' => $emp->getDepartment(), 'active' => $emp->getActive()));
    }

    public function change(Employee $emp) {
        if (!$emp->check_completeness())
            return false;
        return $this->update_by_id($emp->getId(), array('fio' => $emp->getFio(), 'department' => $emp->getDepartment()));
    }

    // по-идее, надо аналогично делать методы на делит, но для простоты далее в коде будет использоваться метод базового класса без построения объектов Employee
}