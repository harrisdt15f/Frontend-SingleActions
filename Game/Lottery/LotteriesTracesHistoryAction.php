<?php

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
        $searchAbleFields = ['user_id', 'lottery_sign', 'status'];
        $fixedJoin = 1;
        $withTable = 'traceLists';
        $withSearchAbleFields = ['project_serial_number', 'issue'];
        $orderFields = 'id';
        $orderFlow = 'desc';
        $data = $contll->generateSearchQuery(
            $eloqM,
            $searchAbleFields,
            $fixedJoin,
            $withTable,
            $withSearchAbleFields,
            $orderFields,
            $orderFlow
        );
        return $contll->msgOut(true, $data);
    }
}
