<?php
/**
 * file: User.php
 */

include_once 'Base.php';

class UserController extends BaseController {
    public function getUserInfoAction() {
        $userID = $this->request('userID');

        if ($userID === '') return $this->response(false, 'wrong parameters');

        $rst = UserModel::getUserInfoByID($userID);
        if ($rst === false) return $this->response(false, 'error occurred when query database');

        return $this->response(true, 'ok', $rst);
    }
}
