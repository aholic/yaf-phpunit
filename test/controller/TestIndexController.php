<?php
/**
 * file: TestIndexController.php
 */

include_once dirname(__FILE__) . '/../TestController.php';

class TestIndexController extends TestController {
    public function testIndexAction() {
        $response = $this->getArrayResponse('Index/index');
        $this->assertEquals(array('flag' => true, 'msg' => 'hello world!', 'data' => array('hello world!')), $response);
    }
}
