<?php

namespace app\controller;

use app\BaseController;

class Base extends BaseController
{
    /**
     * 全局验证前端数据
     * @return \think\response\Json
     */
    public function callModel()
    {
        $params = request()->param();
        $class = request()->controller();
        $action = request()->action();

        $params['pageNum'] = $params['pageNum'] ?? 1;
        $params['pageSize'] = $params['pageSize'] ?? 10;
        // 验证字段
        try {
            validate($class)->scene($action)->check($params);
            // 调用业务代码
            $model = '\app\model\\' . $class;
            $result = (new $model())->$action($params);
            if (is_array($result) && isset($result['status'])) {
                if ($result['status']) {
                    return show(config('common.status.success'), $result['message'], $result['data']);
                } else {
                    return show(config('common.status.error'), $result['message'], [], config('common.http_status.internal_error'));
                }
            } else {
                return json($result);
            }
        } catch (\Exception $e) {
            return show(config('common.status.error'), $e->getMessage(), ['line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }
}