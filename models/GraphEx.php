<?php
//ГРАФ ЗАГРУЖЕННОСТИ

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
}