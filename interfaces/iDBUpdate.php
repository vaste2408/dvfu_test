<?php
/**
 * Created by PhpStorm.
 * User: vasiliev.aa
 * Date: 21.02.2022
 * Time: 13:55
 */

interface iDBUpdate
{
    public function update($id, $data = array());
}