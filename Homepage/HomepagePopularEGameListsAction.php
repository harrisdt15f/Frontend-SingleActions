<?php

namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Game\EGame\FrontendPopularEGameList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HomepagePopularEGameListsAction
{
    protected $model;

    /**
     * @param  FrontendPopularEGameList  $frontendPopularEGameList
     */
    public function __construct(FrontendPopularEGameList $frontendPopularEGameList)
    {
        $this->model = $frontendPopularEGameList;
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
