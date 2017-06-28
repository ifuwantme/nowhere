<?php
Yaf_Loader::import(APP_PATH.'/application/models/BaseModel.php');
/**
 * Created by PhpStorm.
 * User: owen
 * Date: 2017/6/19
 * Time: 下午2:51
 */
class SiteModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->_table  = 'nw_subwaystation';
        $this->_entity = array('line_id','site');
        $this->_var = "select a.* from nw_subwaystation A where (select count(*) from nw_subwaystation B where A.name=B.name)>1 order by name";
    }
}