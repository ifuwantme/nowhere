<?php

/**
 * Created by PhpStorm.
 * User: owen
 * Date: 2017/6/19
 * Time: 下午2:19
 */
class BaseModel
{
    protected $_table   = null;
    protected $_index   = null;
    protected $_entity  = null;
    protected $_var     = null;

    public function __construct()
    {
        $this->_db = Yaf_Registry::get('_db');

    }

    public function get($columns,$where){

        $result = $this->_db->select($this->_table,$columns,$where);

        return $result;
    }

    public function getAll(){

        $result = $this->_db->select($this->_table,$this->_entity,null);

        return $result;
    }

    public function originQuery($query){
        if($query){
            $result = $this->_db->query($query)->fetchAll();
        }else{
            $result = $this->_db->query($this->_var)->fetchAll();
        }


        return $result;
    }

    public function save($data){
        $result = $this->_db->insert($this->_table,$data);

        return $result;
    }

}