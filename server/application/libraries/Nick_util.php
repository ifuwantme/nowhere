<?php

/**
 * Created by PhpStorm.
 * User: owen
 * Date: 2017/7/29
 * Time: 下午12:52
 */
class Nick_util
{
    public function __construct() {
        $this->CI=& get_instance();

    }


    public function encode($name){
        $station = $this->CI->db->query("select * from nw_subwaystation where name='".$name."'")->result();

        $nick = 'L'.$station[0]->line_id.'S'.$station[0]->site;

        return $nick;
    }

    public function decode($nick){
        $n = explode('S',$nick);
        $line = substr($n[0],1);
        $sta  = $n[1];

        $station = $this->CI->db->query("select * from nw_subwaystation where line_id='".$line."' and site='".$sta."'")->result();
        return $station[0]->name;
    }
}