<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-25 11:13:31
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 20:38:48
 */
namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Admin\Homepage\FrontendLotteryRedirectBetList;
use App\Models\Admin\Homepage\FrontendPageBanner;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HompagePopularLotteriesAction
{
    protected $model;

    /**
     * @param  FrontendAllocatedModel  $frontendAllocatedModel
     */
    public function __construct(FrontendAllocatedModel $frontendAllocatedModel)
    {
        $this->model = $frontendAllocatedModel;
    }

    /**
     * 热门彩票一
     * @param  FrontendApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        if (Cache::has('popular_lotteries')) {
            $datas = Cache::get('popular_lotteries');
        } else {
            $datas = FrontendLotteryRedirectBetList::webPopularLotteriesCache();
        }
        return $contll->msgOut(true, $datas);
    }

}
