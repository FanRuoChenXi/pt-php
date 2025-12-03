<?php
// 应用公共文件
use think\facade\Db;

/**
 * 通用化API数据格式输出
 * @param $status 业务代码
 * @param string $message
 * @param $data 返回数据
 * @param $httpStatus HTTP状态码
 * @return \think\response\Json
 */
function show($status, string $message = '', $data = [], $httpStatus = 200)
{
    $result = [
        "status" => $status,
        "message" => $message,
        "data" => $data
    ];
    return json($result, $httpStatus);
}

/**
 * 设置token
 * @param $data
 * @return void
 */
function setToken($user)
{
    $rand_str = bin2hex(random_bytes(16));
    $timestamp = time();
    $token = sha1($timestamp . $user['id'] . $rand_str);
    cookie('PT_TOKEN', $token);
    cache("pt_{$token}", $user, 3600 * 24);
}

/**
 * 验证token
 * @return false|mixed|object|\think\App
 */
function checkToken()
{
    $token = cookie('PT_TOKEN');
    if (empty($token)) {
        return ['status' => config('common.status.not_login'), 'message' => '用户未登录'];
    }
    $user = cache("pt_{$token}");
    if (empty($user)) {
        return ['status' => config('common.status.not_login'), 'message' => '用户未登录'];
    }
    // 如果有返回则重新缓存计算cookie时间
    cookie('PT_TOKEN', $token);
    cache("pt_{$token}", $user, 3600 * 24);
    return ['status' => config('common.status.success')];
}

/**
 * 删除token
 * @return void
 */
function deleteToken()
{
    $token = cookie('PT_TOKEN');
    cache("pt_{$token}", null);
}

/**
 * 获取用户缓存信息
 * @return mixed|object|\think\App
 */
function getUserCache()
{
    $token = cookie('PT_TOKEN');
    return cache("pt_{$token}");
}

/**
 * 格式化北京时间
 * @param string $date
 * @return string
 * @throws Exception
 */
function dateFormat(string $date)
{
    // 创建DateTime对象并指定时区为UTC
    $dateTime = new DateTime($date, new DateTimeZone('UTC'));
    // 转换为北京时间（东八区）
    $dateTime->setTimezone(new DateTimeZone('Asia/Shanghai'));
    // 输出转换后的时间
    return $dateTime->format('Y-m-d H:i:s');
}

/**
 * array转变为得到key-value
 * @param $list
 * @param $id_name
 * @return array
 */
function getKeyValue($list, $id_name)
{
    $key_value = [];
    foreach ($list as $item) {
        $key_value[$item[$id_name]] = $item;
    }
    return $key_value;
}

/**
 * 注入数据 用于更新、新增数据
 * @param array $inject_data
 * @param array $inject_list
 * @param $params
 * @return array
 */
function injectData(array $inject_data, array $inject_list, $params): array
{
    foreach ($inject_list as $item) {
        if (!empty($params[$item])) {
            $inject_data[$item] = $params[$item];
        }
    }
    return $inject_data;
}

/**
 * 处理默认日期
 * @param array $check_data
 * @return array
 */
function checkDefaultDate(array $check_data): array
{
    foreach ($check_data as $key => $item) {
        $item = $item == '2000-01-01' ? '' : $item;
        $check_data[$key] = $item;
    }
    return $check_data;
}

/**
 * 原生sql查询
 * @param array $sql
 * @param array $arr
 * @return mixed
 */
function querySql(array $sql, array $arr)
{
    $data_sql = $sql['sql'];
    $where_sql = $sql['where'];
    $page_sql = $sql['page'];
    $where_arr = $arr['where'];
    $page_arr = $arr['page'];
    if (!empty($where_arr)) {
        $list = Db::query($data_sql . $where_sql . $page_sql, array_merge($page_arr, $where_arr));
    } else {
        $list = Db::query($data_sql . $where_sql . $page_sql, $page_arr);
    }
    return $list;
}

/**
 * 删除文件
 * @param $file_url
 * @return array
 */
function deleteFile($file_url)
{
    // 要删除的文件路径
    $root_path = dirname(root_path());
    $file = $root_path . '/pm_upload/' . $file_url;

    $result = [];
    if (is_file($file)) {
        if (@unlink($file)) {
            $result['status'] = config('common.status.success');
            $result['message'] = "文件已成功删除";
        } else {
            $result['status'] = config('common.status.error');
            $result['message'] = "无法删除该文件";
        }
    } else {
        $result['status'] = config('common.status.error');
        $result['message'] = "指定的文件不存在";
    }

    return $result;
}