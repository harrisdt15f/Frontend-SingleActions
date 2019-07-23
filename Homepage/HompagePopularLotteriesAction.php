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
        $lotteriesEloq = $this->model::select('show_num', 'status')->where('en_name', 'popular.lotteries.one')->first();
        if ($lotteriesEloq === null) {
            $lotteriesEloq = FrontendAllocatedModel::createPopularLotteries();
        }
        $dataEloq = FrontendLotteryRedirectBetList::select('id', 'lotteries_id', 'lotteries_sign', 'pic_path')->with([
            'lotteries:id,day_issue,en_name,cn_name',
            'issueRule:lottery_id,issue_seconds',
            'currentIssue:lottery_id,issue,end_time',
        ])->orderBy('sort', 'asc')->limit($lotteriesEloq->show_num)->get();
        $datas = [];
        foreach ($dataEloq as $key => $dataIthem) {
            $datas[$key]['cn_name'] = $dataIthem->lotteries->cn_name ?? null;
            $datas[$key]['en_name'] = $dataIthem->lotteries->en_name ?? null;
            $datas[$key]['pic_path'] = $dataIthem->pic_path ?? null;
            $datas[$key]['issue_seconds'] = $dataIthem->issueRule->issue_seconds ?? null;
            $datas[$key]['day_issue'] = $dataIthem->lotteries->day_issue ?? null;
            $datas[$key]['issue'] = $dataIthem->currentIssue->issue ?? null;
            $datas[$key]['end_time'] = $dataIthem->currentIssue->end_time ?? null;
        }
        return $contll->msgOut(true, $datas);
    }

}
