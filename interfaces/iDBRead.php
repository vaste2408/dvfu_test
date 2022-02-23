<?php

/**
 * чтение объекта из БД
 */
interface iDBRead
{
    public function read($where = array(), $columns = "*");
}