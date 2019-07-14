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
        if (Cache::has('popularLotteries')) {
            $datas = Cache::get('popularLotteries');
        } else {
            $lotteriesEloq = $this->model::select('show_num', 'status')->where('en_name', 'popularLotteries.one')->first();
            if ($lotteriesEloq->status !== 1) {
                return $contll->msgOut(false, [], '100400');
            }
            $dataEloq = FrontendLotteryRedirectBetList::select('id', 'lotteries_id', 'pic_path')->with(['lotteries' => function ($query) {
                $query->select('id', 'day_issue', 'en_name');
            }])->orderBy('sort', 'asc')->limit($lotteriesEloq->show_num)->get();
            $datas = [];
            foreach ($dataEloq as $key => $dataIthem) {
                $datas[$key]['en_name'] = $dataIthem->lotteries->en_name;
                $datas[$key]['pic_path'] = $dataIthem->pic_path;
                $datas[$key]['day_issue'] = $dataIthem->lotteries->day_issue;
            }
            Cache::forever('popularLotteries', $datas);
        }
        return $contll->msgOut(true, $datas);
    }

}
