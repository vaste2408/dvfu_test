<?php

/**
 * КЛАСС СОБРАНИЯ
 */
class Meeting implements iCheckSelfCompleteness
{
    //TODO здесь можно в пхп 7.4+ чётко типизировать параметры, но у меня старая IDE и обновлять слишком накладно
    protected $id;
    protected $starts_at;
    protected $ends_at;
    protected $name;
    protected $active = 1;

    //TODO без чёткой типизации надо бы написать для сетеров валидатор, но пока опустим этот момент
    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getStartsAt()
    {
        return $this->starts_at;
    }

    public function setStartsAt($starts_at): void
    {
        $this->starts_at = $starts_at;
    }

    public function getEndsAt()
    {
        return $this->ends_at;
    }

    public function setEndsAt($ends_at): void
    {
        $this->ends_at = $ends_at;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getActive(): int
    {
        return $this->active;
    }

    public function setActive(int $active): void
    {
        $this->active = $active;
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
        return $this->getId() != null && $this->getActive() != null && $this->getStartsAt() != null && $this->getEndsAt() != null && !empty($this->getName());
    }
}