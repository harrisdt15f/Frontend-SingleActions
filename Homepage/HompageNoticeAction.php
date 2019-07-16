<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-25 11:48:22
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 20:38:38
 */
namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Admin\Notice\FrontendMessageNotice;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HompageNoticeAction
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
     * 首页公告列表
     * @param FrontendApiMainController $contll
     * @param $input
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, $input): JsonResponse
    {
        $noticeEloq = $this->model::select('show_num', 'status')->where('en_name', 'notice')->first();
        if (is_null($noticeEloq) || $noticeEloq->status !== 1) {
            return $contll->msgOut(false, [], '100400');
        }
        if (Cache::has('homepageNotice')) {
            $data = Cache::get('homepageNotice');
        } else {
            $eloqM = new FrontendMessageNotice();
            $searchAbleFields = [
                'type',
                'status',
            ];
            $contll->inputs['status'] = 1;
            $contll->inputs['type'] = $input;
            $data = $contll->generateSearchQuery($eloqM, $searchAbleFields, $fixedJoin = 0, $withTable = null, $withSearchAbleFields = null, $orderFields = 'sort', $orderFlow = 'asc');
            Cache::forever('homepageNotice', $data);
        }
        return $contll->msgOut(true, $data);
    }
}
