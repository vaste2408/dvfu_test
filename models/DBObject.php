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

    //СЛУЖЕБНЫЙ САНИТАЙЗЕР
    public function db_esc($param) {
        $clean = mysqli_real_escape_string($this->db_connect, $param);
        //TODO injection detect
        return $clean;
    }

    //ШАБЛОН НА СОЗДАНИЕ
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

    //ШАБЛОН НА РЕДАКТИРОВАНИЕ
    public function update($id, $data = array()){
        $args = array();

        foreach ($data as $key => $value) {
            $args[] = "$key = '$value'";
        }

        $update = "UPDATE  $this->table SET " . implode(',', $args) ." WHERE id = $id";
        $res = mysqli_query($this->db_connect, $update);
        if($res){
            return true;
        }else{
            return false;
        }
    }

    //ШАБЛОН НА УДАЛЕНИЕ
    public function delete($id){
        return $this->update($id, array('active' => false));
        /* Лучше сделать soft-delete
        $delete = "DELETE FROM `". $this->table ."` WHERE id = $id";
        $res = mysqli_query($this->db_connect, $delete);
        if($res){
            return true;
        }else{
            return false;
        }
        */
    }

    //ШАБЛОН НА ЧТЕНИЕ
    public function read($where = array(), $columns = "*")
    {
        $sql = "SELECT $columns FROM `$this->table` WHERE active = 1";
        if (count($where)) {
            foreach ($where as $cond) {
                $sql .= " AND " . $cond;
            }
            $res = mysqli_query($this->db_connect, $sql);
            $result_array = array();
            while ($r = mysqli_fetch_assoc($res)) {
                $result_array[] = $r;
            }
            return $result_array;
        }
    }

    //это своего рода синтаксический сахар для удобства
    public function read_one ($id){
        return $this->read(array('id' => $id));
    }

    public function read_all ($filters_arr = array()){
        $where = array();
        foreach ($filters_arr as $key => $value) {
            $where[] = "$key = '$value'";
        }
        return $this->read($where);
    }
}