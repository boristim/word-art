<?php

namespace Web;


use Db\ModelView;

/**
 * Class View
 * @package Web
 */
class View
{
    use ModelView;

    const AJAX_ACTIONS = ['menuList', 'ratingList', 'loadsSelect', 'filmInfo'];
    const RATING_COLUMNS = ['position' => '##', 'name' => 'Название', 'year' => 'Год', 'calc_ball' => 'Расчетный балл', 'votes' => 'Голосов', 'avg_ball' => 'Средний балл'];

    /**
     * View constructor.
     */
    function __construct()
    {
        $this->initDatabase();
    }

    public function response()
    {
        if (isset($_SERVER['CONTENT_TYPE']) && ($_SERVER['CONTENT_TYPE'] == 'application/json')) {
            $this->responseJSON();
        } else {
            $this->responseHTML();
        }
    }

    private function responseHTML()
    {
        header('Content-Type: text/html; charset=UTF-8');
        include TEMPLATE_DIRECTORY . 'index.tpl.php';
    }

    private function responseJSON()
    {
        $params = (array)json_decode(file_get_contents('php://input'));
        if (in_array($params['action'], self::AJAX_ACTIONS)) {
            $p = null;
            if (isset($params['url'])) {
                $p = explode('/', trim($params['url'], '/'));
            }
            $result = $this->{$params['action']}($p);
            header('Content-Type: application/json; charset=UTF-8');
            print json_encode($result);
        } else {
            $this->responseHTML();
        }
    }

    /**
     * @return array
     */
    private function menuList(): array
    {
        return $this->getMenuItems();
    }

    /**
     * @param array $params
     * @return array
     */
    private function ratingList(array $params): array
    {
        $filmTypeId = $params[1] ?? $this->getFirstFilmTypeId();
        $loadId = $params[0] ?? $this->getLastLoadId();
        if ($loadId <= 0) {
            $loadId = $this->getLastLoadId();
        }
        $fields = array_keys(self::RATING_COLUMNS);
        $orderBy = $fields[0];
        if (isset($params[2])) {
            $orderFieldId = abs(intval($params[2])) - 1;
            if (isset($fields[$orderFieldId])) {
                $orderBy = $fields[$orderFieldId];
                if ($params[2] < 0) {
                    $orderBy = "$orderBy desc";
                }
            }
        }
        return ['items' => $this->getRatingList($filmTypeId, $loadId, $orderBy), 'header' => self::RATING_COLUMNS];
    }

    /**
     * @return array
     */
    private function loadsSelect(): array
    {
        return $this->getLoadsList();
    }

    /**
     * @param array $filmId
     * @return array
     */
    private function filmInfo(array $filmId): array
    {
        return $this->getFilmInfo(reset($filmId));
    }
}