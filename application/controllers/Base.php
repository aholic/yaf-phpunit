<?php
/**
 * file: Base.php
 */


class BaseController extends Yaf_Controller_Abstract {
    public function init() {
        Yaf_dispatcher::getInstance()->disableView();
    }

    protected function response($flag, $msg = '', $data = '') {
        return $this->getResponse()->setBody(json_encode(array('flag' => $flag, 'msg' => $msg, 'data' => $data)));
    }

    protected function request($key, $defaultVal = '') {
        return $this->getRequest()->getPost($key, $this->getRequest()->get($key, $defaultVal));
    }
}
