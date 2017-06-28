<?php
/**
 * Created by PhpStorm.
 * User: owen
 * Date: 2017/6/8
 * Time: 下午5:42
 */
define("APP_PATH",  realpath(dirname(__FILE__) . '/../')); /* 指向public的上一级 */
$app  = new Yaf_Application(APP_PATH . "/conf/application.ini");
$app->bootstrap()->run();


