<?php

namespace app\middleware;

class CheckLogin
{
    /**
     * 登录验证
     * @param \think\Request $request
     * @param \Closure $next
     * @return Response|\think\response\Json
     */
    public function handle($request, \Closure $next)
    {
        $request_uri = $_SERVER['REQUEST_URI'];
        $pathinfo = explode('?', $request_uri)[0];
        $white_list = [
            '/userLogin'
        ]; // 白名单，不用登录
        if (!in_array($pathinfo, $white_list)) {
            $result = checkToken();
            if ($result['status'] != config('common.status.success')) {
                return show($result['status'], $result['message']);
            }
        }
        return $next($request);
    }
}
