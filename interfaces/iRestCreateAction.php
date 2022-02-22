<?php
interface iRestCreateAction
{
    /**
     * Метод POST
     * Создание новой записи
     * http://ДОМЕН/контроллер + параметры запроса
     * @return string
     */
    public function createAction();
}