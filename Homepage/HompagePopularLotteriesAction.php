<?php

namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Lib\Common\CacheRelated;
use App\Models\Admin\Homepage\FrontendLotteryRedirectBetList;
use Illuminate\Http\JsonResponse;

class HompagePopularLotteriesAction
{

    /**
     * 热门彩票一
     * @param  FrontendApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        $tags = $contll->tags;
        $redisKey = 'popular_lotteries';
        $datas = CacheRelated::getTagsCache($tags, $redisKey);
        if ($datas === false) {
            $datas = FrontendLotteryRedirectBetList::webPopularLotteriesCache();
        }
        return $contll->msgOut(true, $datas);
    }
}
