<?php
interface iRestViewAction
{
    /**
     * Метод GET
     * Просмотр отдельной записи (по id)
     * http://ДОМЕН/контроллер/{id}
     * @return string
     */
    public function viewAction();
}