<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-25 10:45:55
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-28 15:58:42
 */
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
        $searchAbleFields = ['lottery_sign'];
        $data = $contll->generateSearchQuery($eloqM, $searchAbleFields);
        return $contll->msgOut(true, $data);
    }
}
