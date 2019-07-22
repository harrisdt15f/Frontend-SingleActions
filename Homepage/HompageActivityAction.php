<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-25 11:33:22
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 20:37:32
 */
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
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        $activityEloq = $this->model::select('show_num', 'status')->where('en_name', 'activity')->first();
        if ($activityEloq === null || $activityEloq->status !== 1) {
            return $contll->msgOut(false, [], '100400');
        }
        if (Cache::has('homepage_activity')) {
            $data = Cache::get('homepage_activity');
        } else {
            $data = FrontendActivityContent::select('id', 'title', 'content', 'preview_pic_path', 'redirect_url')->where('status', 1)->orderBy('sort', 'asc')->limit($activityEloq->show_num)->get()->toArray();
            Cache::forever('homepage_activity', $data);
        }
        return $contll->msgOut(true, $data);
    }
}
