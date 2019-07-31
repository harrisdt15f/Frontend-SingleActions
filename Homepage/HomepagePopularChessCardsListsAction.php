<?php

namespace App\Http\SingleActions\Frontend\Homepage;

use App\Http\Controllers\FrontendApi\FrontendApiMainController;
use App\Models\Game\ChessCards\FrontendPopularChessCardsList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HomepagePopularChessCardsListsAction
{
    protected $model;

    /**
     * @param  FrontendPopularChessCardsList  $frontendPopularChessCardsList
     */
    public function __construct(FrontendPopularChessCardsList $frontendPopularChessCardsList)
    {
        $this->model = $frontendPopularChessCardsList;
    }

    /**
     * 首页 热门棋牌
     * @param  FrontendApiMainController $contll
     * @return JsonResponse
     * @todo   还未开发完成，临时用来首页展示的数据。
     */
    public function execute(FrontendApiMainController $contll): JsonResponse
    {
        $data = $this->model::select('chess_card_id', 'name', 'icon')->orderBy('sort', 'asc')->get()->toArray();
        return $contll->msgOut(true, $data);
    }
}
