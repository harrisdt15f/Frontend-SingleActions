<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-27 16:26:07
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-27 16:30:16
 */
namespace App\Http\SingleActions\Frontend;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class FrontendAuthLogoutAction
{
    use AuthenticatesUsers;
    /**
     * Login user and create token
     * @param  FrontendApiMainController  $contll
     * @param  $request
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, $request): JsonResponse
    {
        $throtleKey = Str::lower($this->username() . '|' . $request->ip());
        $request->session()->invalidate();
        $this->limiter()->clear($throtleKey);
        $contll->currentAuth->logout();
        $contll->currentAuth->invalidate();
        return $contll->msgOut(true); //'Successfully logged out'
    }
}
