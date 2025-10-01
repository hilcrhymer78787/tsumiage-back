<?php

namespace App\Http\Middleware;

use App\Domains\Shared\LoginInfo\Services\LoginInfoService;
use Closure;
use Illuminate\Http\Request;
use App\Http\Exceptions\AppHttpException;

class CheckToken
{
  public function __construct(
    private readonly LoginInfoService $loginInfoService,
  ) {}
  /**
   * 送信されてきたリクエストの処理
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    $loginInfoModel = $this->loginInfoService->getLoginInfo($request);
    if (!$loginInfoModel) throw new AppHttpException(401, 'トークンが有効期限切れです');
    return $next($request);
  }
}
