<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-26 11:03:18
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 20:08:13
 */
namespace App\Http\SingleActions\Frontend;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\User\FrontendUser;
use App\Models\User\FrontendUsersSpecificInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class FrontendAuthResetSpecificInfosAction
{
    /**
     * 用户设置个人信息
     * @param  FrontendApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, $inputDatas): JsonResponse
    {
        $specificinfoEloq = $contll->partnerUser->specific;
        if ($specificinfoEloq === null) {
            return $this->createSpecificInfo($contll, $inputDatas);
        }
        try {
            $contll->editAssignment($specificinfoEloq, $inputDatas);
            $specificinfoEloq->save();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            return $contll->msgOut(false, [], '100012');
        }
    }

    /**
     * 生成用户个人信息
     * @param  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function createSpecificInfo($contll, $inputDatas): JsonResponse
    {
        DB::beginTransaction();
        try {
            $specificinfoEloq = new FrontendUsersSpecificInfo();
            $specificinfoEloq->fill($inputDatas);
            $specificinfoEloq->save();
            $userEloq = $contll->partnerUser;
            $userEloq->user_specific_id = $specificinfoEloq->id;
            $userEloq->save();
            DB::commit();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            DB::rollback();
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
