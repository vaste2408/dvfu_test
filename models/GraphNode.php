<?php
// ДЛЯ ПОСТРОЕНИЯ ГРАФА
class Node {
    private $id;
    private $start;
    private $end;
    private $parent_node;

    public function __construct($id, $start, $end, $parent_node = null){
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
        $this->parent_node = $parent_node;
    }

    public function set_parent($parent_node) {
        $this->parent_node = $parent_node;
    }
}