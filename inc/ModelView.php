<?php


namespace Db;

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
    private function getMenuItems(): array
    {
        return $this->db->getArray('Select id,name from film_types');
    }

    private function getRatingList(int $filmTypeId, int $loadId): array
    {
        $filmTypeId = intval($filmTypeId);
        $loadId = intval($loadId);
        $sql = "SELECT f.id,r.position,f.name,f.year,r.calc_ball,r.votes,r.avg_ball FROM rates r JOIN films f ON f.id = r.film_id where r.film_type_id = $filmTypeId and r.load_id = $loadId";
        return $this->db->getArray($sql);
    }

    private function getLastLoadId()
    {
        $query = $this->db->query('Select max(id) id from loads');
        $result = 0;
        if ($query->num_rows) {
            $result = $query->fetch_assoc()['id'];
        }
        return $result;
    }
}