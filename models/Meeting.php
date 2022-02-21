<?php
/**
КЛАСС СОБРАНИЯ
 */
class Meeting
{
    protected $id;
    public $starts_at;
    public $ends_at;
    public $active = 1;

    public function __construct($starts_at, $ends_at, $active = 1){
        $this->starts_at = $starts_at;
        $this->ends_at = $ends_at;
        $this->active = $active;
    }
}