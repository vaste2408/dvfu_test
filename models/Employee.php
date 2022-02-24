<?php

/**
 * КЛАСС СОТРУДНИКА
 */
class Employee implements iCheckSelfCompleteness
{
    //TODO здесь можно в пхп 7.4+ чётко типизировать параметры, но у меня старая IDE и обновлять слишком накладно
    protected $id;
    protected $fio;
    protected $department;
    protected $active = 1; //все записи по умолчанию активны

    //TODO без чёткой типизации надо бы написать для сетеров валидатор, но пока опустим этот момент
    public function getFio()
    {
        return $this->fio;
    }

    public function setFio($fio)
    {
        $this->fio = $fio;
    }

    public function getDepartment()
    {
        return $this->department;
    }

    public function setDepartment($department)
    {
        $this->department = $department;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function __construct($id = null){
        if ($id)
            $this->setId($id);
    }

    /**
     * Служебная функция самопроверки объекта на полноту сведений
     * Его бы расширить, чтоб умел возвращать, каких сведений не хватает
     * но для базового использования пойдёт и так
     * @return bool
     */
    public function check_completeness(){
        return $this->getId() != null && $this->getActive() != null && !empty($this->getFio()) && !empty($this->getDepartment());
    }
}