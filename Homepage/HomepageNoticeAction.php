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

class HomepageNoticeAction
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
     * 首页 公告|站内信 列表
     * @param FrontendApiMainController $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        $noticeEloq = $this->model::select('show_num', 'status')->where('en_name', 'notice')->first();
        if ($noticeEloq->status !== 1) {
            return $contll->msgOut(false, [], '100400');
        }
        $eloqM = new FrontendMessageNotice();
        $contll->inputs['receive_user_id'] = $contll->partnerUser->id ?? null;
        $searchAbleFields = ['status', 'receive_user_id'];
        $fixedJoin = 1;
        $withTable = 'messageContent';
        $withSearchAbleFields = ['type'];
        $data = $contll->generateSearchQuery($eloqM, $searchAbleFields, $fixedJoin, $withTable, $withSearchAbleFields, $orderFields = 'id', $orderFlow = 'desc');
        return $contll->msgOut(true, $data);
    }
}
