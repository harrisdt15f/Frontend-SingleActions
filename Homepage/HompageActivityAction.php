<?php

namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Lib\Common\CacheRelated;
use App\Models\Admin\Activity\FrontendActivityContent;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use Illuminate\Http\JsonResponse;

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
     * @param  int $type 活动所属端 1 网页 2手机端
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll, int $type): JsonResponse
    {
        $activityEloq = $this->model::select('show_num', 'status')->where('en_name', 'activity')->first();
        if ($activityEloq === null || $activityEloq->status !== 1) {
            return $contll->msgOut(false, [], '100400');
        }
        $tags = 'homepage';
        $redisKey = $type == 1 ? 'homepage_activity_hot_web' : 'homepage_activity_hot_app';
        $data = CacheRelated::getTagsCache($tags, $redisKey);
        if ($data === false) {
            $data = FrontendActivityContent::select('id', 'title', 'content', 'preview_pic_path', 'redirect_url')
                ->where('status', 1)
                ->where('type', $type)
                ->orderBy('sort', 'asc')
                ->limit($activityEloq->show_num)
                ->get()
                ->toArray();
            CacheRelated::setTagsCache($tags, $redisKey, $data);
        }
        return $contll->msgOut(true, $data);
    }
}
