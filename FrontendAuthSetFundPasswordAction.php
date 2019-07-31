<?php

namespace App\Http\SingleActions\Frontend;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\User\FrontendUsersSpecificInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class FrontendAuthSetFundPasswordAction
{
    /**
     * 用户设置资金密码
     * @param  FrontendApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, $inputDatas): JsonResponse
    {
        if ($contll->partnerUser->fund_password !== null) {
            return $contll->msgOut(false, [], '100013');
        }
        if ($inputDatas['password'] !== $inputDatas['confirm_password']) {
            return $contll->msgOut(false, [], '100008');
        }
        try {
            $partnerUserEloq = $contll->partnerUser;
            $partnerUserEloq->fund_password = Hash::make($inputDatas['password']);
            $partnerUserEloq->save();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误妈，错误信息］
            return $this->msgOut(false, [], $sqlState, $msg);
        }
    }
}
