<?php

namespace app\controller;

class UserCenter extends Base
{
    public function login()
    {
        return $this->callModel();
    }

    public function logout()
    {
        return $this->callModel();
    }

    public function getUserInfo()
    {
        return $this->callModel();
    }
}