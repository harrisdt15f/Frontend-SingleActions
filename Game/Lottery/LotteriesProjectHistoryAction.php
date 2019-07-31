<?php

namespace App\Http\SingleActions\Frontend\Game\Lottery;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class LotteriesProjectHistoryAction
{
    /**
     * 游戏-下注历史
     * @param  FrontendApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, $inputDatas): JsonResponse
    {
        $eloqM = new Project();
        $contll->inputs['user_id'] = $contll->partnerUser->id;
        $searchAbleFields = ['lottery_sign', 'user_id'];
        $orderFields = 'id';
        $orderFlow = 'desc';
        $data = $contll->generateSearchQuery($eloqM, $searchAbleFields, 0, null, null, $orderFields, $orderFlow);
        return $contll->msgOut(true, $data);
    }
}
