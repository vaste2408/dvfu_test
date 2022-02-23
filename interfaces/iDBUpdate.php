<?php

/**
 * сохранение объекта в БД
 */
interface iDBUpdate
{
    public function update($where, $data = array());
}