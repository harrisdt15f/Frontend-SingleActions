<?php

namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Admin\Notice\FrontendMessageNotice;
use App\Models\Admin\Notice\FrontendMessageNoticesContent;
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
        $data = [];
        if ($contll->inputs['type'] == FrontendMessageNoticesContent::TYPE_NOTICE) {
            $data = $this->getNoticeList($contll);
        } elseif ($contll->inputs['type'] == FrontendMessageNoticesContent::TYPE_MESSAGE) {
            $data = $this->getMessageList($contll);
        }
        return $contll->msgOut(true, $data);
    }

    //公告列表
    public function getNoticeList($contll)
    {
        $eloqM = new FrontendMessageNoticesContent();
        $searchAbleFields = ['type'];
        $orderFields = 'id';
        $orderFlow = 'desc';
        return $contll->generateSearchQuery($eloqM, $searchAbleFields, 0, null, null, $orderFields, $orderFlow);
    }

    //站内信列表
    public function getMessageList($contll)
    {
        $eloqM = new FrontendMessageNotice();
        $contll->inputs['receive_user_id'] = $contll->partnerUser->id ?? null;
        $searchAbleFields = ['status', 'receive_user_id'];
        $fixedJoin = 1;
        $withTable = 'messageContent';
        $withSearchAbleFields = ['type'];
        $orderFields = 'id';
        $orderFlow = 'desc';
        return $contll->generateSearchQuery($eloqM, $searchAbleFields, $fixedJoin, $withTable, $withSearchAbleFields, $orderFields, $orderFlow);
    }
}
