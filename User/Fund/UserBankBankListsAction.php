<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-25 19:39:45
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 16:23:11
 */
namespace App\Http\SingleActions\Frontend\User\Fund;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Admin\Fund\FrontendSystemBank;
use Illuminate\Http\JsonResponse;

class UserBankBankListsAction
{
    /**
     * 添加银行卡时选择的银行列表
     * @param  FrontendApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        $data = FrontendSystemBank::select('id', 'title', 'code')->where([
            ['pay_type', 1],
            ['status', 1],
        ])->get();
        return $contll->msgOut(true, $data);
    }
}
