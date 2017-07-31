<?php

/**
 * Created by PhpStorm.
 * User: owen
 * Date: 2017/7/27
 * Time: 下午3:15
 */
class BaiduMapApi_util
{
    const AK = "9OAXKOL87ukamPI8VHKjeES5mGnm9bRR";
    //const AK = "Vk5eZTZdD11H3d7NXanwWoa175EtGh3H";
    const SK = "BQ0WfXbXDbZex5QWji3zSt1TvsuPHBwM";
    const BASE_URI = "http://api.map.baidu.com";
    const OUTPUT = "json";




    //API uri
    const DIRECTION = "/direction/v2/";
    const GEOCODER  = "/geocoder/v2/";
    const PLACE = "/place/v2/";

    public function __construct() {
        $this->CI=& get_instance();
        $this->CI->load->model('place_model');
    }



    function getPlace($data,$update=1){
        $address = null;
        //多地址逻辑暂时不写,TODO LIST
        if(is_array($data['address'])){
            foreach($data['address'] as $a){
                $address = $address."$".$a;
            }
        }else{
            $address = $data['address'];
        }
        //http://api.map.baidu.com/place/v2/search?query=银行&location=39.915,116.404&radius=2000&output=xml&ak={您的密钥}

        $aim = SELF::BASE_URI.SELF::PLACE."search?query=".$address."&location=".$data['lat'].",".$data['lng']."&radius=".$data['distance']."&scope=2&output=".SELF::OUTPUT."&ak=".SELF::AK;

        $result = file_get_contents($aim);

        $result = json_decode($result);
        if($update == 0){
            $d = array(
                'name' => $result->results[0]->name,
                'distance' => $result->results[0]->detail_info->distance/110
            );
            return $d;
        }else{
            return json_decode($result);
        }


    }

    function getDirection($start,$end){

        //Todo 线路接口

        if($start['lat']&&$end['lat']) {
            $aim = SELF::BASE_URI . SELF::DIRECTION . "transit?origin=" . $start['lat'] . "," . $start['lng'] . "&destination=" . $end['lat'] . "," . $end['lng'] . "&output=" . SELF::OUTPUT . "&ak=" . SELF::AK . "&tactics_incity=5";

            $result = file_get_contents($aim);
           echo $result;
            $result = json_decode($result);
        }else{
            return 1;
        }

        if($result->result) {
            return $result;
        }else{
            return 1;
        }
    }


    /**
     * 输入地址,得到经纬度返回loc
     * @return array
     */

    function getCode($data){

        $row = $this->CI->db->query("select * from nw_place where name='".$data."'")->result();

        if($row){
            $loc_row = array(
                'name'       => $row[0]->name,
                'lng'        => $row[0]->lng,
                'lat'        => $row[0]->lat,
                'precise'    => $row[0]->precise,
                'confidence' => $row[0]->confidence,
                'level'      => $row[0]->level
            );

            return $loc_row;
        }
//        $querystring_arrays = array (
//            'address' => urlencode($data),
//            'output' => SELF::OUTPUT,
//            'ak' => SELF::AK
//        );

        //"http://api.map.baidu.com/geocoder/v2/?address=".$newdata."&output=json&ak=需自己申请";
//        $aim = SELF::BASE_URI.SELF::GEOCODER."?address=".$data."&output=".SELF::OUTPUT."&ak=".SELF::AK."&sn=";
//        $sn  = $this->caculateAKSN(SELF::AK,SELF::SK,SELF::GEOCODER,$querystring_arrays);
//        echo $aim.$sn;
        $aim = SELF::BASE_URI.SELF::GEOCODER."?address=".$data."-地铁站&output=".SELF::OUTPUT."&ak=".SELF::AK."&city=北京市";

        $result = file_get_contents($aim);

        echo $result;
//        $json_data = json_decode($address_data);
//        $lng = $json_data->result->location->lng;
//        $lat = $json_data->result->location->lat;

        $result = json_decode($result);

        if($result->status !=0){
            $aim = SELF::BASE_URI.SELF::GEOCODER."?address=".$data."地铁站&output=".SELF::OUTPUT."&ak=".SELF::AK."&city=北京市";

            $result = file_get_contents($aim);
            echo 'abc='.$result;
            $result = json_decode($result);

        }



        //{"status":0,"result":{"location":{"lng":116.46827388546248,"lat":39.959990111793079},"precise":1,"confidence":80,"level":"商务大厦"}}
        $loc = array(
            'name'       => $data,
            'lng'        => $result->result->location->lng,
            'lat'        => $result->result->location->lat,
            'precise'    => $result->result->precise,
            'confidence' => $result->result->confidence,
            'level'      => $result->result->level
        );
        if(!$row){

            $this->CI->place_model->save($loc);

        }

        return $loc;
    }


    function caculateAKSN($ak, $sk, $url, $querystring_arrays, $method = 'GET')
    {
        if ($method === 'POST'){
            ksort($querystring_arrays);
        }
        $querystring = http_build_query($querystring_arrays);
        return md5(urlencode($url.'?'.$querystring.$sk));
    }




}