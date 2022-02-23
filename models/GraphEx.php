<?php

/**
 * ЭТО КЛАСС ДЛЯ ФОРМИРОВАНИЯ ПРЕДВАРИТЕЛЬНОГО ПУЛА УЗЛОВ
 */
class GraphEx
{
    public $nodes;

    public function __construct()
    {
        $this->nodes = [];
    }

    public function add_node (NodeEx $node){
        $this->nodes[] = $node;
    }

    public function add_path (NodeEx $start, NodeEx $end){
        $start->add_path($end->id);
    }

    public function get_nodes(){
        return $this->nodes;
    }

    public function get_size (){
        return count($this->nodes);
    }

    public function get_root () {
        if ($this->get_size())
            return $this->nodes[0];
        return null;
    }

    public function get_finish () {
        if ($this->get_size())
            return $this->nodes[$this->get_size()-1];
        return null;
    }
}