<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

// User
Route::get('/getUserList', 'User/getUserList');
Route::get('/getUser', 'User/getUser');
Route::post('/addUser', 'User/addUser');
Route::post('/updateUser', 'User/updateUser');
Route::post('/deleteUser', 'User/deleteUser');

// UserCenter
Route::post('/userLogin', 'UserCenter/login');
Route::post('/userLogout', 'UserCenter/logout');
Route::get('/getUserInfo', 'UserCenter/getUserInfo');