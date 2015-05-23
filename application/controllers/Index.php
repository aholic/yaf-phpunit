<?php

include_once 'Base.php';

class IndexController extends BaseController {
    public function indexAction() {
        return $this->response(true, 'hello world!', array('hello world!'));
    }
}
