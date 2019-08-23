<?php

namespace App\Http\SingleActions\Frontend\Game\Lottery;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Lib\Common\CacheRelated;
use App\Models\Game\Lottery\LotteryList;
use Illuminate\Http\JsonResponse;

class LotteriesLotteryInfoAction
{
    /**
     * 游戏 彩种详情
     * @param  FrontendApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        $tags = 'lottery';
        $redisKey = 'frontend.lottery.lotteryInfo';
        $data = CacheRelated::getTagsCache($tags, $redisKey);
        if ($data === false) {
            $data = LotteryList::lotteryInfoCache();
        }
        return $contll->msgOut(true, $data);
    }
}
