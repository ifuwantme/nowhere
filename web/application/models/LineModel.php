<?php
Yaf_Loader::import(APP_PATH.'/application/models/BaseModel.php');
/**
 * Created by PhpStorm.
 * User: owen
 * Date: 2017/6/19
 * Time: 下午2:51
 */
class LineModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->_table = 'nw_subwayline';
        $this->_entity = array('line_num','name');
    }
}