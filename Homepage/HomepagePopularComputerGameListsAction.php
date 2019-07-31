<?php

namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Game\ComputerGame\FrontendPopularComputerGameList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HomepagePopularComputerGameListsAction
{
    protected $model;

    /**
     * @param  FrontendPopularComputerGameList  $frontendPopularComputerGameList
     */
    public function __construct(FrontendPopularComputerGameList $frontendPopularComputerGameList)
    {
        $this->model = $frontendPopularComputerGameList;
    }

    /**
     * 首页 热门电子
     * @param  FrontendApiMainController $contll
     * @return JsonResponse
     * @todo   还未开发完成，临时用来首页展示的数据。
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        $data = $this->model::select('computer_game_id', 'name', 'icon')->orderBy('sort', 'asc')->get()->toArray();
        return $contll->msgOut(true, $data);
    }
}
