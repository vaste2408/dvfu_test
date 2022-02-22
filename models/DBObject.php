<?php
/**
БАЗОВЫЙ КЛАСС ОБЪЕКТА БД
 */
abstract class DBObject implements iDBCreate, iDBRead, iDBUpdate, iDBDelete
{
    protected $db_connect;
    protected $table;

    //КОНСТРУКТОР
    public function __construct($table) {
        //TODO это можно сделать параметрами класса и передавать их в конструкторе, но пока пусть так
        $this->db_connect = new mysqli('localhost', 'root', '', 'test');
        $this->table = $table;
        if(mysqli_connect_error()){
            die("Database Connection Failed" . mysqli_connect_error() . mysqli_connect_errno());
        }
    }

    /**
     * СЛУЖЕБНЫЙ САНИТАЙЗЕР
     * @param $param
     * @return string
     */
    public function db_esc($param) {
        $clean = mysqli_real_escape_string($this->db_connect, $param);
        //TODO injection detect
        return $clean;
    }

    /**
     * функция формирования блока условия
     * @param $where
     * @return string
     */
    private function form_where_condition($where){
        $args = $this->args_into_query_args($where);
        $where_q = " WHERE ". implode(' AND ', $args);
        return $where_q;
    }

    /**
     * функция преобразования массива типа key-value в массив key='value'
     * @param array $args
     * @return array
     */
    private function args_into_query_args ($args = array()) {
        $ret = array();
        foreach ($args as $key => $value) {
            $ret[] = "$key = '$value'";
        }
        return $ret;
    }

    //TODO по-хорошему бы написать конструктор формирования запросов, но готового у меня нет, а придумывать пока нет смысла

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

    /**
     * ШАБЛОН НА ЧТЕНИЕ
     * @param array $where
     * @param string $columns
     * @return array
     */
    public function read($where = array(), $columns = "*")
    {
        $where['active'] = 1;

        $sql = "SELECT $columns FROM `$this->table` " . $this->form_where_condition($where);
        $res = mysqli_query($this->db_connect, $sql);
        $result_array = array();
        while ($r = mysqli_fetch_assoc($res)) {
            $result_array[] = $r;
        }
        return $result_array;
    }

    /**
     * @param $id
     * @return array
     */
    public function read_one ($id){
        return $this->read(array('id' => $id));
    }

    /**
     * @param array $where
     * @return array
     */
    public function read_all ($where = array()){
        return $this->read($where);
    }
}