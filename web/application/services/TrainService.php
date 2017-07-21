<?php
Yaf_Loader::import(APP_PATH.'/application/services/BaseService.php');
/**
 * Created by PhpStorm.
 * User: owen
 * Date: 2017/6/19
 * Time: 下午2:56
 */
class TrainService extends BaseService
{
    private $nodes = array();
    private $arcs  = array();
    private $object = array();


    public function __construct()
    {

    }

    public function index(){

        $this->mGraph();

    }
    public function mData(){

    }
    public function mGraph(){

        $line =  $this->getModel('LineModel');
        $site =  $this->getModel('SiteModel');
        $swap =  $this->queryFactory();



        array_walk($site,function(& $s){
            $this->nodes[] = 'L'.$s['line_id'].'S'.$s['site'];
        });

        $this->createArc($site);
        $this->createSwapArc($swap);
        $this->floyd();
        echo json_encode($this->arcs);

        //echo json_encode($r);
        //echo 'line ==============='.json_encode($this->nodes);

    }

    public function createSwapArc($swaps){
        foreach($swaps as $swap) {
            foreach ($swaps as $cswap) {
                if ($swap['name'] == $cswap['name'] && $swap['line_id'] != $cswap['line_id']) {
                    $swap_name = 'L' . $swap['line_id'] . 'S' . $swap['site'];
                    $cswap_name = 'L' . $cswap['line_id'] . 'S' . $cswap['site'];
                    $this->arcs[$swap_name][$cswap_name] = 1;
                    $this->arcs[$cswap_name][$swap_name] = 1;
                }
            }
        }

    }


    public function createArc($site){
        $this->arcs['T10S1']['T10S45'] = 3;
        $this->arcs['T10S45']['T10S1'] = 3;
        $this->arcs['T2S1']['T2S18'] = 3;
        $this->arcs['T2S18']['T2S1'] = 3;
        foreach($site as $s){
            $s_name = 'L'.$s['line_id'].'S'.$s['site'];
            foreach($site as $cs){

                $cs_name = 'L'.$cs['line_id'].'S'.$cs['site'];


                if($s['line_id'] == $cs['line_id']){
                    if($s['site'] == $cs['site']){
                        $this->arcs[$s_name][$cs_name] = 0;
                    }elseif(abs($s['site']-$cs['site']) == 1){
                        $this->arcs[$s_name][$cs_name] = 3;
                        $this->arcs[$cs_name][$s_name] = 3;
                    }else{
                        $this->arcs[$s_name][$cs_name] = 1061109567;
                        $this->arcs[$cs_name][$s_name] = 1061109567;
                    }
                }else{
                    //无穷大标示0x3f3f3f3f,换算10进制为1061109567
                    $this->arcs[$s_name][$cs_name] = 1061109567;
                    $this->arcs[$cs_name][$s_name] = 1061109567;
                }
            }
        }
    }

    public function floyd(){
        $path = array();
        $distance = array();

        for($j = 0; $j < count($this->nodes); $j ++) {
            for ($i = 0; $i < count($this->nodes); $i++) {
                if($i !=$j){
                    for ($k = 0; $k < count($this->nodes); $k++) {
                        if ($i != $k && $k !=$j && $this->arcs[$this->nodes[$i]][$this->nodes[$j]]< 1061109567 && $this->arcs[$this->nodes[$j]][$this->nodes[$k]]< 1061109567 && $this->arcs[$this->nodes[$i]][$this->nodes[$k]] > $this->arcs[$this->nodes[$i]][$this->nodes[$j]] + $this->arcs[$this->nodes[$j]][$this->nodes[$k]]) {
//                        $path[$this->nodes[$i]][$this->nodes[$k]] = $path[$this->nodes[$i]][$this->nodes[$j]];
                            $this->arcs[$this->nodes[$k]][$this->nodes[$i]] = $this->arcs[$this->nodes[$i]][$this->nodes[$j]] + $this->arcs[$this->nodes[$j]][$this->nodes[$k]];
                            $this->arcs[$this->nodes[$i]][$this->nodes[$k]] = $this->arcs[$this->nodes[$i]][$this->nodes[$j]] + $this->arcs[$this->nodes[$j]][$this->nodes[$k]];
                            $object['start'] = $this->nodes[$k];
                            $object['end'] = $this->nodes[$i];
                            $object['distance'] = $this->arcs[$this->nodes[$i]][$this->nodes[$j]] + $this->arcs[$this->nodes[$j]][$this->nodes[$k]];
                            $object['time'] = $this->arcs[$this->nodes[$i]][$this->nodes[$j]] + $this->arcs[$this->nodes[$j]][$this->nodes[$k]];
                            $this->insert("RelaModel",$object);

                            $object['start'] = $this->nodes[$i];
                            $object['end'] = $this->nodes[$k];
                            $object['distance'] = $this->arcs[$this->nodes[$i]][$this->nodes[$j]] + $this->arcs[$this->nodes[$j]][$this->nodes[$k]];
                            $object['time'] = $this->arcs[$this->nodes[$i]][$this->nodes[$j]] + $this->arcs[$this->nodes[$j]][$this->nodes[$k]];
                            $this->insert("RelaModel",$object);

                        }
                    }
                }
            }
        }

    }

}