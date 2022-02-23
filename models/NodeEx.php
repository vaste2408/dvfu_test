<?php

/**
 * ЭТО КЛАСС ДЛЯ УЗЛОВ ГРАФА.
 */
class NodeEx {
    public $id;
    public $name;
    public $starts;
    public $ends;
    public $paths; //куда можно перейти

    public function __construct($id, $name, $starts, $ends){
        $this->id = $id;
        $this->name = $name;
        $this->starts = $starts;
        $this->ends = $ends;
        $this->paths = [];
    }

    public function add_path ($id) {
        $this->paths[] = $id;
    }
}