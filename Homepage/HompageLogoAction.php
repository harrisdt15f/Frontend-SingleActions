<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-25 11:37:24
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 20:38:24
 */
namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HompageLogoAction
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
     * 首页LOGO
     * @param  FrontendApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        if (Cache::has('homepageLogo')) {
            $data = Cache::get('homepageLogo');
        } else {
            $logoEloq = $this->model::select('value', 'status')->where('en_name', 'logo')->first();
            if (is_null($logoEloq)) {
                //#######################################################
                return $contll->msgOut(false, [], '100400');
            }
            if ($logoEloq->status !== 1) {
                return $contll->msgOut(false, [], '100400');
            }
            $data['value'] = $logoEloq->value;
            Cache::forever('homepageLogo', $data);
        }
        return $contll->msgOut(true, $data);
    }
}
