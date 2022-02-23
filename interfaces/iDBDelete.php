<?php

/**
 * удаление объекта из БД
 */
interface iDBDelete
{
    public function delete($where, $soft = true);
}