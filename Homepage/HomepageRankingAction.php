<?php

/**
 * @Author: Fish
 * @Date:   2019-07-3 14:35:55
 */
namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HomepageRankingAction
{
    protected $model;

    /**
     * @param  Project  $projectModel
     */
    public function __construct(Project $projectModel)
    {
        $this->model = $projectModel;
    }

    /**
     * 首页中奖排行榜
     * @param  FrontendApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        if (Cache::has('homepageRanking')) {
            $rankingE = Cache::get('homepageRanking');
        } else {
            $status = FrontendAllocatedModel::select('status')->where('en_name', 'winning.ranking')->first();
            if ($status->status !== 1) {
                return $contll->msgOut(false, [], '100400');
            }
            $rankingE = $this->model::select('username', 'bonus')->where('bonus','>', '0')->orderBy('bonus','DESC')->limit(100)->get()->toArray();
            Cache::forever('homepageRanking', $rankingE);
        }
        return $contll->msgOut(true, $rankingE);
    }
}
