<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-25 10:26:38
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-25 10:30:42
 */
namespace App\Http\SingleActions\Frontend\Game\Lottery;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Game\Lottery\LotteryList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class LotteriesLotteryInfoAction
{
    /**
     * 游戏 彩种详情
     * @param  FrontendApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        $data = [];
        $redisKey = 'frontend.lottery.lotteryInfo';
        if (Cache::has($redisKey)) {
            $data = Cache::get($redisKey);
        } else {
            $lotteryModel = new LotteryList();
            $data = $lotteryModel->lotteryInfoCache();
        }
        return $contll->msgOut(true, $data);
    }
}
