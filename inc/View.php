<?php

namespace Web;


use Db\ModelView;

class View
{
    use ModelView;

    const AJAX_ACTIONS = ['menuList', 'ratingList'];
    const RATING_COLUMNS = ['position' => '##', 'name' => 'Название', 'year' => 'Год', 'calc_ball' => 'Расчетный балл', 'votes' => 'Голосов', 'avg_ball' => 'Средний балл'];

    function __construct()
    {
        $this->initDatabase();
    }

    public function response()
    {
        if (isset($_SERVER['CONTENT_TYPE']) && ($_SERVER['CONTENT_TYPE'] == 'application/json')) {
            $params = json_decode(file_get_contents('php://input'));
            $this->resposeJSON($params);
        } else {
            $this->responseHTML();
        }
    }

    private function responseHTML()
    {
        header('Content-Type: text/html; charset=UTF-8');
        readfile(TEMPLATE_DIRECTORY . 'index.tpl.html');
    }

    private function resposeJSON($params)
    {
        $result = [];
        if (is_object($params)) {
            $params = (array)$params;
        }
        if (in_array($params['action'], self::AJAX_ACTIONS)) {
            $result = $this->{$params['action']}(isset($params['params']) ? (array)$params['params'] : null);
        }
        header('Content-Type: application/json; charset=UTF-8');
        print json_encode($result);
    }

    private function menuList(): array
    {
        return $this->getMenuItems();
    }

    private function ratingList($params): array
    {
        $filmTypeId = isset($params['filmType']) ? $params['filmType'] : 1;
        $loadId = isset($params['loadId']) ? $params['loadId'] : $this->getLastLoadId();
        return ['items' => $this->getRatingList($filmTypeId, $loadId), 'header' => self::RATING_COLUMNS];
    }
}