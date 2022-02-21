<?php
/**
 * Created by PhpStorm.
 * User: vasiliev.aa
 * Date: 21.02.2022
 * Time: 13:54
 */

interface iDBRead
{
    public function read($where = array(), $columns = "*");
}