<?php

namespace app\validate;

use think\Validate;

class UserCenter extends Validate
{
    protected $rule = [
        'user|用户名' => 'require',
        'password|密码' => 'require',
    ];
    protected $message = [
    ];
    protected $scene = [
        'login' => ['user', 'password'],
        'logout' => [''],
        'getUserInfo' => [''],
    ];
}