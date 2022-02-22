<?php

/*
 * Этот класс в наглую стырен с просторов интернета.
 * Придумывать свой алгоритм обхода графа не имеет смысла.
 */
class Dijkstra {
    protected $startingNode;
    protected $endingNode;
    protected $graph;
    protected $paths = array();
    protected $solution = false;

    /**
     * Instantiates a new algorithm, requiring a graph to work with.
     *
     * @param Graph $graph
     */
    public function __construct(Graph $graph) {
        $this->graph = $graph;
    }

    /**
     * Returns the distance between the starting and the ending point.
     *
     * @return integer
     */
    public function getDistance() {
        if (! $this->isSolved()) {
            throw new Exception("Cannot calculate the distance of a non-solved algorithm:\nDid you forget to call ->solve()?");
        }
        return $this->getEndingNode()->getPotential();
    }

    /**
     * Gets the node which we are pointing to.
     *
     * @return Node
     */
    public function getEndingNode() {
        return $this->endingNode;
    }

    /**
     * Returns the solution in a human-readable style.
     * @param boolean $longest
     * @return string
     */
    public function getLiteralPath($longest = true) {
        $path = $this->solve($longest);
        $literal = '';
        foreach ( $path as $p ) {
            $literal .= "{$p->name} - ";
        }
        return substr($literal, 0, count($literal) - 4);
    }

    /**
     * Reverse-calculates the shortest path of the graph thanks the potentials
     * stored in the nodes.
     *
     * @return Array
     */
    public function getShortestPath() {
        $path = array();
        $node = $this->getEndingNode();
        while ( $node->getId() != $this->getStartingNode()->getId() ) {
            $path[] = $node;
            $node = $node->getPotentialFrom();
        }
        $path[] = $this->getStartingNode();
        return array_reverse($path);
    }

    public function getLongestPath() {
        $path = array();
        $node = $this->getEndingNode();
        while ( $node->getId() != $this->getStartingNode()->getId() ) {
            $path[] = $node;
            $node = $node->getPotentialFrom();
        }
        $path[] = $this->getStartingNode();
        return $path;
    }

    /**
     * Retrieves the node which we are starting from to calculate the shortest path.
     *
     * @return Node
     */
    public function getStartingNode() {
        return $this->startingNode;
    }

    /**
     * Sets the node which we are pointing to.
     *
     * @param Node $node
     */
    public function setEndingNode(Node $node) {
        $this->endingNode = $node;
    }

    /**
     * Sets the node which we are starting from to calculate the shortest path.
     *
     * @param Node $node
     */
    public function setStartingNode(Node $node) {
        $this->paths[] = array($node);
        $this->startingNode = $node;
    }

    /**
     * Solves the algorithm and returns the shortest path as an array.
     * @param boolean $longest
     * @return Array
     */
    public function solve($longest = true) {
        if (! $this->getStartingNode() || ! $this->getEndingNode()) {
            throw new Exception("Cannot solve the algorithm without both starting and ending nodes");
        }
        $this->calculatePotentials($this->getStartingNode());
        if ($longest)
            $this->solution = $this->getShortestPath();
        else
            $this->solution = $this->getLongestPath();
        return $this->solution;
    }

    /**
     * Recursively calculates the potentials of the graph, from the
     * starting point you specify with ->setStartingNode(), traversing
     * the graph due to Node's $connections attribute.
     *
     * @param Node $node
     */
    protected function calculatePotentials(Node $node) {
        $connections = $node->getConnections();
        $sorted = array_flip($connections);
        krsort($sorted);
        foreach ( $connections as $id => $distance ) {
            $v = $this->getGraph()->getNode($id);
            $v->setPotential($node->getPotential() + $distance, $node);
            foreach ( $this->getPaths() as $path ) {
                $count = count($path);
                if ($path[$count - 1]->getId() === $node->getId()) {
                    $this->paths[] = array_merge($path, array($v));
                }
            }
        }
        $node->markPassed();
        // Get loop through the current node's nearest connections
        // to calculate their potentials.
        foreach ( $sorted as $id ) {
            $node = $this->getGraph()->getNode($id);
            if (! $node->isPassed()) {
                $this->calculatePotentials($node);
            }
        }
    }

    /**
     * Returns the graph associated with this algorithm instance.
     *
     * @return Graph
     */
    protected function getGraph() {
        return $this->graph;
    }

    /**
     * Returns the possible paths registered in the graph.
     *
     * @return Array
     */
    protected function getPaths() {
        return $this->paths;
    }

    /**
     * Checks wheter the current algorithm has been solved or not.
     *
     * @return boolean
     */
    protected function isSolved() {
        return ( bool ) $this->solution;
    }
}