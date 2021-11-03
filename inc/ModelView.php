<?php


namespace Db;

/**
 * Trait ModelView
 * @package Db
 */
trait ModelView
{
    /**
     * @var Database
     */
    private Database $db;

    public function initDatabase()
    {
        $this->db = new Database();
    }

    /**
     * @return array
     */
    public function getMenuItems(): array
    {
        return $this->db->getArray('Select id,name from film_types');
    }

    /**
     * @param int $filmTypeId
     * @param int $loadId
     * @param string $orderBy
     * @return array
     */
    public function getRatingList(int $filmTypeId, int $loadId, string $orderBy): array
    {
        $dir = WEB_IMAGE_DIRECTORY;
        $sql = <<<SQL
Select r.id as rating_id, f.id, r.position, f.name, f.year, r.calc_ball, r.votes, r.avg_ball, concat('$dir',f.cover) as cover
from rates as r join films as f on f.id = r.film_id 
where r.film_type_id = $filmTypeId
  and r.load_id = $loadId
order by $orderBy 
SQL;

        return $this->db->getArray($sql);
    }

    /**
     * @return int
     */
    public function getLastLoadId(): int
    {
        $query = $this->db->query('Select max(id) as id from loads');
        $result = 0;
        if ($query->num_rows) {
            $result = $query->fetch_assoc()['id'];
        }
        return $result;
    }

    /**
     * @return int
     */
    public function getFirstFilmTypeId(): int
    {
        $query = $this->db->query('Select min(id) as id from film_types');
        $result = 1;
        if ($query->num_rows) {
            $result = intval($query->fetch_assoc()['id']);
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getLoadsList(): array
    {
        return $this->db->getArray('Select l.id, date_format(l.dt,\'%d.%m.%Y %H:%i\') as dt from loads as l order by l.dt desc');
    }

    /**
     * @param $filmId
     * @return array
     */
    public function getFilmInfo($filmId): array
    {
        $filmId = intval($filmId);
        $sql = <<<SQL
Select f.name, f.year, concat('/images/', f.cover) as cover, f.description, r.calc_ball, r.votes,r.avg_ball
from rates as r join films as f ON f.id = r.film_id join loads l on r.load_id = l.id where f.id = $filmId  
order by l.dt desc limit 1
SQL;
        $result = $this->db->getArray($sql);
        return reset($result);
    }
}