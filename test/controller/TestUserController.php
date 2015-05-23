<?php
/**
 * file: TestUserController.php
 */
include_once dirname(__FILE__) . '/../TestController.php';

class TestUserController extends TestController {
    public function testGetUserInfoAction() {
        $expectedUserInfo = array(
            1 => array('userId' => '1', 'userName' => 'ahoLic'),
            2 => array('userId' => '2', 'userName' => 'kost'),
        );

        $requestUserID = array(1, 2);
        foreach ($requestUserID as $userID) {
            $response = $this->getArrayResponse('User/getUserInfo', $userID);
            $this->assertEquals($expectedUserInfo[$userID], $response);
        }
    }
}
