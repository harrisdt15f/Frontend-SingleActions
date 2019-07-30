<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-25 11:24:17
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 20:38:53
 */
namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Admin\Homepage\FrontendLotteryFnfBetableList;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class HompagePopularMethodsAction
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
     * 热门彩票二-玩法
     * @param  FrontendApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        $lotteriesEloq = $this->model::select('show_num', 'status')->where('en_name', 'popularLotteries.two')->first();
        if ($lotteriesEloq === null || $lotteriesEloq->status !== 1) {
            return $contll->msgOut(false, [], '100400');
        }
        $methodsEloq = FrontendLotteryFnfBetableList::orderBy('sort', 'asc')->limit($lotteriesEloq->show_num)->with([
            'method',
            'currentIssue:lottery_id,issue,end_time',
        ])->get();
        $datas = [];
        foreach ($methodsEloq as $method) {
            $data = [
                'lotteries_id' => $method->lotteries_id,
                'method_id' => $method->method_id,
                'lottery_name' => $method->method->lottery_name,
                'method_name' => $method->method->method_name,
                'issue' => $method->currentIssue->issue ?? null,
                'end_time' => $method->currentIssue->end_time ?? null,
            ];
            $datas[] = $data;
        }
        return $contll->msgOut(true, $datas);
    }

}
