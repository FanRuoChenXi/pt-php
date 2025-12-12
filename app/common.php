<?php
// 应用公共文件
use think\App;
use think\response\Json;

/**
 * 通用化API数据格式输出
 * @param $status *业务代码
 * @param string $message
 * @param array $data *返回数据
 * @param int $httpStatus *HTTP状态码
 * @return Json
 */
function show($status, string $message = '', array $data = [], int $httpStatus = 200): Json
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
 * @param $user
 * @return void
 * @throws \Random\RandomException
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
 * @return array
 */
function checkToken(): array
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
 * @return mixed|object|App
 */
function getUserCache()
{
    $token = cookie('PT_TOKEN');
    return cache("pt_{$token}");
}

/**
 * curl get请求
 * @param $url
 * @param string $user_agent
 * @param string $Authorization
 * @return mixed
 */
function curl_get($url, string $user_agent = '', string $Authorization = '')
{
    $headerArray = [
        'Content-type' => 'application/json; charset=utf-8',
        'Accept' => 'application/json',
        'User-Agent' => !empty($user_agent) ? $user_agent : '',
        'Authorization' => !empty($Authorization) ? $Authorization : '',
    ];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
    $result = curl_exec($curl);
    curl_close($curl);
    return json_decode($result, true);
}

/**
 * curl post请求
 * @param $url
 * @param $data
 * @param string $user_agent
 * @param string $Authorization
 * @param int $time_out
 * @return mixed
 */
function curl_post($url, $data, string $user_agent = '', string $Authorization = '', int $time_out = 100)
{
    $headerArray = [
        'Content-type' => 'application/json; charset=utf-8',
        'Accept' => 'application/json',
        'User-Agent' => !empty($user_agent) ? $user_agent : '',
        'Authorization' => !empty($Authorization) ? $Authorization : '',
    ];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, $time_out);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    curl_close($curl);
    return json_decode($result, true);
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
 * 删除文件
 * @param $file_url
 * @return array
 */
function deleteFile($file_url): array
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