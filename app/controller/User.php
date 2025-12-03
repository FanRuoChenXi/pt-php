<?php

namespace app\controller;

class User extends Base
{
    public function getUserList()
    {
        return $this->callModel();
    }

    public function getUser()
    {
        return $this->callModel();
    }

    public function addUser()
    {
        return $this->callModel();
    }

    public function updateUser()
    {
        return $this->callModel();
    }

    public function deleteUser()
    {
        return $this->callModel();
    }
}