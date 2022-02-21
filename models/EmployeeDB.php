<?php
/**
 * Created by PhpStorm.
 * User: vasiliev.aa
 * Date: 21.02.2022
 * Time: 14:00
 */

class EmployeeDB extends DBObject
{
    const table = 'Employees';

    public function __construct(){
        parent::__construct(self::table);
    }

    public function store(Employee $emp) {
        return $this->create(array('fio' => $emp->fio, 'department' => $emp->department, 'active' => $emp->active));
    }

    // по-идее, надо аналогично делать методы на апдейт и делит, но для простоты далее в коде будет использоваться метод базового класса без построения объектов Employee
}