<?php

namespace app;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\ClassNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\RouteNotFoundException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制
        $returnCode = config("common.status.error");
        $returnData = [];

        if ($e instanceof ValidateException) {
            $returnMessage = $e->getError();
            $httpStatus = config("status.http_status.validate_error");
        } else if (($e instanceof ClassNotFoundException || $e instanceof RouteNotFoundException) || ($e instanceof HttpException && $e->getStatusCode() == 404)) {
            $returnMessage = env('app_debug') ? $e->getMessage() : '当前请求资源不存在，请稍后再试';
            $httpStatus = config("common.http_status.not_found");
        } else {
            $returnMessage = env('app_debug') ? $e->getMessage() : '系统异常，请稍后再试';
            $httpStatus = config("common.http_status.internal_error");
        }
        if (request()->isAjax()) {
            return show($returnCode, $returnMessage, $returnData, $httpStatus);
        }
        // 其他错误交给系统处理
        return parent::render($request, $e);
    }
}
