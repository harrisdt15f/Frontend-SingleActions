<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-28 16:07:01
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-28 16:09:16
 */
namespace App\Http\SingleActions\Frontend\Game\Lottery;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\LotteryTrace;
use Illuminate\Http\JsonResponse;

class LotteriesTracesHistoryAction
{
    /**
     * 游戏-追号历史
     * @param  FrontendApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, $inputDatas): JsonResponse
    {
        $eloqM = new LotteryTrace();
        $contll->inputs['user_id'] = $contll->partnerUser->id;
        $searchAbleFields = ['lottery_sign', 'user_id'];
        $fixedJoin = 1;
        $withTable = 'traceLists';
        $withSearchAbleFields = [];
        $orderFields = 'id';
        $orderFlow = 'desc';
        $data = $contll->generateSearchQuery($eloqM, $searchAbleFields, $fixedJoin, $withTable, $withSearchAbleFields, $orderFields, $orderFlow);
        return $contll->msgOut(true, $data);
    }
}
