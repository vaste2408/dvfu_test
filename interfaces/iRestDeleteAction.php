<?php
interface iRestDeleteAction
{
    /**
     * Метод DELETE
     * Удаление отдельной записи (по ее id)
     * http://ДОМЕН/контроллер/{id}
     * @return string
     */
    public function deleteAction();
}