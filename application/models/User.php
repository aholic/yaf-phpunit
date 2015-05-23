<?php

class UserModel {
    public static function getUserInfoByID($userID) {
        return DB_Manager::select('user', array('userID' => $userID));
    }
}
