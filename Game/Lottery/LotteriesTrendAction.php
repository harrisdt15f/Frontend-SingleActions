<?php

namespace App\Http\SingleActions\Frontend\Game\Lottery;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Game\Lottery\LotteryIssue;
use Illuminate\Http\JsonResponse;

class LotteriesTrendAction
{
    private const TREND_RANGE = 100;//规定可取的范围

    /**
     * 彩种走势
     * @param  FrontendApiMainController $contll
     * @param  array $inputDatas
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, array $inputDatas): JsonResponse
    {
        $lottery_id = $inputDatas['lottery_id'];
        $range = $inputDatas['range'];
        if ($range > self::TREND_RANGE) {
            return $contll->msgOut(false, [], '100319');
        }

        $data = LotteryIssue::getTrend($lottery_id, $range);

        return $contll->msgOut(true, $data);
    }
}
