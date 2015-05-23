<?php
/**
 * file: TestControllerBase.php
 *
 *
 * @author troy
 * @version 1.0     time: 2015-5-21
 */

define("ROOT_PATH",  dirname(__FILE__) . '/..');
define("APPLICATION_PATH", ROOT_PATH . '/application');
define("LIB_PATH", APPLICATION_PATH . '/library');
define("COMMON_PATH", APPLICATION_PATH . '/common');

class TestController extends PHPUnit_Extensions_Database_TestCase {
    private $__application = null;
    public function __construct() {
        $this->__application = Yaf_Registry::get('Application');
        if ($this->__application == null) {
            $this->__application = new Yaf_Application(ROOT_PATH."/conf/application.ini");
            Yaf_Registry::set('Application', $this->__application);
            Yaf_Registry::set('config', $this->__application->getConfig());
        }
    }

    public function getConnection() {
        return $this->createDefaultDBConnection(DB_Manager::getLink(), 'paekdusanTest');
    }

    public function getDataSet() {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/init.xml');
    }

    protected function getJsonResponse($url, $params=array()) {
        $buff = explode('/', $url);
        if (count($buff) != 2) throw new Exception("explode('/', '$url') is not 2");

        $request = new Yaf_Request_Simple("CLI", "Index", $buff[0], $buff[1], $params);
        $response = $this->__application->getDispatcher()->returnResponse(true)->dispatch($request);
        return $response->getBody();
    }

    protected function getArrayResponse($url, $params=array()) {
        $buff = explode('/', $url);
        if (count($buff) != 2) throw new Exception("explode('/', '$url') is not 2");

        $request = new Yaf_Request_Simple("CLI", "Index", $buff[0], $buff[1], $params);
        $response = $this->__application->getDispatcher()->returnResponse(true)->dispatch($request);
        return json_decode($response->getBody(), true);
    }
}
