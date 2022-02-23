<?php

/**
КЛАСС, РЕАЛИЗУЮЩИЙ CRUD В БД
 */
abstract class DBCRUD extends DBObject implements iDBCreate, iDBUpdate, iDBDelete
{
    /**
     * ШАБЛОН НА СОЗДАНИЕ
     * @param array $data
     * @return bool
     */
    public function create ($data = array()) {
        $table_columns = implode(',', array_keys($data));
        $table_value = implode("','", $data);

        $query = "INSERT INTO $this->table ($table_columns) VALUES('$table_value')";
        $res = mysqli_query($this->db_connect, $query);
        if($res){
            return true;
        }else{
            return false;
        }
    }

    /**
     * ШАБЛОН НА РЕДАКТИРОВАНИЕ
     * @param array $where - массив k-v условий
     * @param array $data
     * @return bool
     */
    public function update($where, $data = array()){
        $args = $this->args_into_query_args($data);

        $update = "UPDATE  `$this->table` SET " . implode(',', $args) . $this->form_where_condition($where);
        $res = mysqli_query($this->db_connect, $update);
        if($res){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $id
     * @param array $data
     * @return bool
     */
    public function update_by_id ($id, $data = array()) {
        return $this->update(array('id' => $id), $data);
    }

    /**
     * ШАБЛОН НА УДАЛЕНИЕ
     * @param array $where - условие удаления
     * @param boolean $soft - мягкое удаление
     * @return bool
     */
    public function delete($where, $soft = true){
        if ($soft)
            return $this->update($where, array('active' => false));

        $delete = "DELETE FROM `". $this->table ."` " . $this->form_where_condition($where);
        $res = mysqli_query($this->db_connect, $delete);
        if($res){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $id
     * @param bool $soft
     * @return bool
     */
    public function delete_by_id ($id, $soft = true) {
        return $this->update(array('id' => $id), $soft);
    }
}