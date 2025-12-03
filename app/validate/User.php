<?php

namespace app\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'user_id' => 'require|integer',
        'role_id' => 'require|integer',
        'user|用户名' => 'require',
        'password|密码' => 'require'
    ];
    protected $message = [];
    protected $scene = [
        'getUserList' => [''],
        'getUser' => ['user_id'],
        'addUser' => ['user', 'password', 'role_id'],
        'updateUser' => ['user_id'],
        'deleteUser' => ['user_id']
    ];
}