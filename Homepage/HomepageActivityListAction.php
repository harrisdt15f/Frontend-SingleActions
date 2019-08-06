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
     * 活动列表
     * @param FrontendApiMainController $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll,$inputDatas): JsonResponse
    {
        $contll->inputs['type']=$inputDatas['type'];
        $searchAbleFields = ['title','type', 'id','status', 'admin_name', 'is_time_interval'];
        $orderFields = 'sort';
        $orderFlow = 'asc';
        $datas = $contll->generateSearchQuery($this->model, $searchAbleFields, 0, null, null, $orderFields, $orderFlow);
        return $contll->msgOut(true, $datas);
    }


}
