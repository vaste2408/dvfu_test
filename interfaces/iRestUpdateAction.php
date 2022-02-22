<?php
interface iRestUpdateAction
{
    /**
     * Метод PUT
     * Обновление отдельной записи (по ее id)
     * http://ДОМЕН/контроллер/{id} + параметры запроса
     * @return string
     */
    public function updateAction();
}