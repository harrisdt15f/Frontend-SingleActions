<?php

namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Admin\Activity\FrontendActivityContent;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HompageActivityAction
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
     * 首页活动列表
     * @param  FrontendApiMainController  $contll
     * @param  $type 活动所属端 1 网页 2手机端
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll,$type): JsonResponse
    {
        $cacheName = $type==1?'homepage_activity_hot_web':'homepage_activity_hot_app';
        $activityEloq = $this->model::select('show_num', 'status')->where('en_name', 'activity')->first();
        if ($activityEloq === null || $activityEloq->status !== 1) {
            return $contll->msgOut(false, [], '100400');
        }
        if (Cache::has($cacheName)) {
            $data = Cache::get($cacheName);
        } else {
            $data = FrontendActivityContent::select('id', 'title', 'content', 'preview_pic_path', 'redirect_url')->where('status', 1)->where('type', $type)->orderBy('sort', 'asc')->limit($activityEloq->show_num)->get()->toArray();
            Cache::forever($cacheName, $data);
        }
        return $contll->msgOut(true, $data);
    }
}
