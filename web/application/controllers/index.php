<?php
Yaf_Loader::import(APP_PATH.'/application/services/TrainService.php');


/**
 * Created by PhpStorm.
 * User: owen
 * Date: 2017/6/8
 * Time: 下午5:51
 */


class IndexController extends Yaf_Controller_Abstract{

    protected function init(){
        $this->trainService = new TrainService();
    }
    public function indexAction(){

        $message = $this->trainService->index();
        echo $message;
        $this->getView()->assign("content", $message);
        
    }

    public function pageAction(){
        echo 'abc';

        $this->getView()->assign("content","abc");
    }
}