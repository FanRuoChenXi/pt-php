<?php

namespace app\model;

use think\facade\Config;
use think\facade\Db;

class UserCenter
{
    public function login($params)
    {
        $password = $params['password'];
        // 用户验证
        $field = 'u.id,u.user,u.password,u.nick_name,r.role_name,r.role_sort,r.permission_edit';
        $where[] = ['u.user', '=', $params['user']];
        $where[] = ['u.del_flag', '<>', Config::get('common.delete_flag')];
        $where[] = ['r.del_flag', '<>', Config::get('common.delete_flag')];
        $user = Db::connect('mysql')
            ->table('user')
            ->alias('u')
            ->join('role r', 'u.role_id = r.id')
            ->where($where)
            ->field($field)
            ->find();
        if (!$user) {
            return ['status' => false, 'message' => '用户不存在'];
        }
        $password_verify = password_verify($password, $user['password']);
        if (!$password_verify) {
            return ['status' => false, 'message' => '密码错误'];
        }
        unset($user['password']);
        setToken($user);
        // 菜单
        $user['role_sort'] = json_decode($user['role_sort'], true);
        $menu_list = Db::connect('mysql')
            ->table('menu')
            ->whereIn('id', $user['role_sort'])
            ->field('name,path')
            ->select();
        $user['menu'] = $menu_list;
        return ['status' => true, 'message' => '登录成功', 'data' => $user];
    }

    public function logout()
    {
        deleteToken();
        return ['status' => true, 'message' => '退出登录成功', 'data' => []];
    }

    public function getUserInfo()
    {
        $user_info = getUserCache();
        $user_info['role_sort'] = json_decode($user_info['role_sort'], true);
        $menu_list = Db::connect('mysql')
            ->table('menu')
            ->whereIn('id', $user_info['role_sort'])
            ->field('name,path')
            ->select();
        $user_info['menu'] = $menu_list;
        return ['status' => true, 'message' => '', 'data' => $user_info];
    }
}