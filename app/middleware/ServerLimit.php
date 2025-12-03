<?php

namespace app\middleware;

use think\facade\Cache;

class ServerLimit
{
    /**
     * @param $request
     * @param \Closure $next
     * @return \think\response\Json
     */
    // 每分钟最大请求数
    protected $limit = 60;
    // 限流时间窗口（秒）
    protected $expire = 60;

    public function handle($request, \Closure $next)
    {
        //得到当前IP
        $ip = $request->ip();
        $key = 'rate_limit:' . $ip;

        // 使用 Redis 计数
        $count = Cache::get($key, 0);

        if ($count >= $this->limit) {
            // 超出限制，返回错误响应
            return show(config('common.status.error'), '请求过于频繁，请稍后再试', [], config('common.http_status.internal_error'));
        }

        // 自增 + 设置过期时间
        if ($count == 0) {
            Cache::set($key, 1, $this->expire);
        } else {
            Cache::inc($key);
        }

        // 继续执行请求
        return $next($request);
    }
}