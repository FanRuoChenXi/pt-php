<?php

namespace app\model;

use think\facade\Db;

class User
{
    // 用户列表
    public function getUserList($params)
    {
        $field = 'id,user,create_time,update_time';
        $where = [['del_flag', '<>', config('common.delete_flag')]];
        !empty($params['user']) ? array_push($where, ['user', 'like', '%' . $params['user'] . '%']) : null;
        $user_list = Db::table('user')
            ->where($where)
            ->field($field)
            ->paginate(['list_rows' => $params['pageSize'], 'page' => $params['pageNum']])
            ->toArray();
        $data = empty($user_list['data']) ? [] : ['total' => $user_list['total'], 'rows' => $user_list['data']];
        return ['status' => true, 'message' => '', 'data' => $data];
    }

    // 用户详情
    public function getUser($params)
    {
        // 用户
        $field = 'id,user,create_time,update_time';
        $user = Db::table('user')
            ->where('id', $params['user_id'])
            ->where('del_flag', '<>', config('common.delete_flag'))
            ->field($field)
            ->find();
        if (!$user) {
            return ['status' => false, 'message' => '用户不存在'];
        }
        // 角色
//        $role_id = Db::table('user_role')
//            ->where('user_id', $params['id'])
//            ->value('role_id');
//        $role_list = Db::table('role')
//            ->where('del_flag', '<>', config('common.delete_flag'))
//            ->field('id,role_name,role_key')
//            ->select();
        return ['status' => true, 'message' => '', 'data' => $user];
    }

    // 新增用户
    public function addUser($params)
    {
        // 验证用户是否存在
        $user = Db::table('user')
            ->where('user', $params['user'])
            ->where('del_flag', '<>', config('common.delete_flag'))
            ->find();
        if ($user) {
            return ['status' => false, 'message' => '用户已存在'];
        }
        // 验证角色是否存在
        $role = Db::table('role')
            ->where('id', $params['role_id'])
            ->where('del_flag', '<>', config('common.delete_flag'))
            ->find();
        if (!$role) {
            return ['status' => false, 'message' => '角色不存在'];
        }
        // 新增数据
        $password = password_hash($params['password'], PASSWORD_BCRYPT);
        $insertData = [
            'user' => $params['user'],
            'nick_name' => $params['nick_name'],
            'password' => $password,
            'role_id' => $params['role_id'],
        ];
//        Db::startTrans();
//        try {
//            // 新增用户
//            $user_id = Db::table('user')->insertGetId($insertData);
//            // 关联角色
//            Db::table('user_role')->insert(['user_id' => $user_id, 'role_id' => $params['roleId']]);
//            Db::commit();
//        } catch (\Exception $e) {
//            // 回滚事务
//            Db::rollback();
//            return ['ok' => false, 'msg' => '新增用户失败', 'code' => 4002, 'data' => ['line' => $e->getLine(), 'message' => $e->getMessage(), 'file' => $e->getFile()]];
//        }
        Db::table('user')->insert($insertData);
        return ['status' => true, 'message' => '新增用户成功', 'data' => []];
    }

    // 修改用户
    public function updateUser($params)
    {
        // 验证角色
//        if (!empty($params['roleId'])) {
//            $role = Db::table('role')
//                ->where('id', $params['roleId'])
//                ->where('del_flag', '<>', config('common.delete_flag'))
//                ->find();
//            if (!$role) {
//                return ['ok' => false, 'msg' => '角色不存在', 'code' => 1001, 'data' => []];
//            }
//        }
        // 更新数据
        $update_data = [];
        !empty($params['user']) ? $update_data['user'] = $params['user'] : null;
        !empty($params['nick_name']) ? $update_data['nick_name'] = $params['nick_name'] : null;
        !empty($params['password']) ? $update_data['password'] = password_hash($params['password'], PASSWORD_BCRYPT) : null;
//        Db::startTrans();
//        try {
//            // 更新用户
//            $user_id = Db::table('user')->update($edit_data);
//            // 关联角色
//            if (!empty($params['roleId'])) {
//                Db::table('user_role')
//                    ->where('user_id', $params['id'])
//                    ->update(['role_id' => $params['roleId']]);
//            }
//            Db::commit();
//        } catch (\Exception $e) {
//            // 回滚事务
//            Db::rollback();
//            return ['ok' => false, 'msg' => '修改用户失败', 'code' => 4002, 'data' => ['line' => $e->getLine(), 'message' => $e->getMessage(), 'file' => $e->getFile()]];
//        }
        Db::table('user')
            ->where('id', $params['user_id'])
            ->update($update_data);
        return ['status' => true, 'message' => '更新用户成功', 'data' => []];
    }

    // 删除用户
    public function deleteUser($params)
    {
        $delete_data = [
            'id' => $params['user_id'],
            'del_flag' => config('common.delete_flag')
        ];
        Db::table('user')->update($delete_data);
        return ['status' => true, 'message' => '删除用户成功', 'data' => []];
    }
}