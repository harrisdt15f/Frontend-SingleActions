<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-25 11:55:08
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 20:38:15
 */
namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HompageIcoAction
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
     * 前台网站头ico
     * @param  FrontendApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        if (Cache::has('homepage_ico')) {
            $data = Cache::get('homepage_ico');
        } else {
            $icoEloq = $this->model::select('value', 'status')->where('en_name', 'frontend.ico')->first();
            if ($icoEloq === null || $icoEloq->status !== 1) {
                return $contll->msgOut(false, [], '100400');
            }
            $data = $icoEloq->value;
            Cache::forever('homepage_ico', $data);
        }
        return $contll->msgOut(true, $data);
    }
}
