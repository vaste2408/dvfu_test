<?php
/**
БАЗОВЫЙ КЛАСС ОБЪЕКТА БД
 */
abstract class DBObject implements iDBRead //все объекты БД вроде как умеют реализовывать чтение
{
    protected $db_connect;
    protected $table;

    public function __construct($table) {
        //TODO это можно сделать параметрами класса и передавать их в конструкторе, но пока пусть так
        $this->db_connect = new mysqli('localhost', 'root', '', 'test');
        $this->table = $table;
        if(mysqli_connect_error()){
            die("Database Connection Failed" . mysqli_connect_error() . mysqli_connect_errno());
        }
    }

    /**
     * СЛУЖЕБНЫЙ САНИТАЙЗЕР для экранирования спец-символов
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
    public function form_where_condition($where){
        $args = $this->args_into_query_args($where);
        $where_q = " WHERE ". implode(' AND ', $args);
        return $where_q;
    }

    /**
     * функция преобразования массива типа key-value в массив key='value'
     * @param array $args
     * @return array
     */
    public function args_into_query_args ($args = array()) {
        $ret = array();
        foreach ($args as $key => $value) {
            $ret[] = "$key = '$value'";
        }
        return $ret;
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