<?php

namespace App\Http\SingleActions\Frontend\Homepage;

use App\Models\Admin\Notice\FrontendMessageNoticesContent;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Admin\Activity\FrontendActivityContent;

class HomepageActivityListAction
{
    protected $model;

    /**
     * @param  FrontendActivityContent  $frontendActivityContent
     */
    public function __construct(FrontendActivityContent $frontendActivityContent)
    {
        $this->model = $frontendActivityContent;
    }

    /**
     * 首页 公告|站内信 列表
     * @param FrontendApiMainController $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll,$inputDatas): JsonResponse
    {
        $searchAbleFields = ['title', 'type', 'status', 'admin_name', 'is_time_interval'];
        $orderFields = 'sort';
        $orderFlow = 'asc';
        $contll->type=$inputDatas['type'];
        $datas = $contll->generateSearchQuery($this->model, $searchAbleFields, 0, null, null, $orderFields, $orderFlow);
        return $contll->msgOut(true, $datas);
    }


}
