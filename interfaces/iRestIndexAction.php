<?php

interface iRestIndexAction
{
    /**
     * Метод GET
     * Вывод списка всех записей
     * http://ДОМЕН/контроллер
     * @return string
     */
    public function indexAction();
}