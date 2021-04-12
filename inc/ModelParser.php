<?php


namespace Db;

use Exception;
use Generator;

/**
 * Trait ModelParser
 * @package Db
 */
trait ModelParser
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
     * @return Generator
     */
    public function newLoad(): Generator
    {
        static $loadId = 0;
        if (!$loadId) {
            $this->db->query('Insert into loads set dt=now()');
            $loadId = $this->db->insert_id;
        }

        $query = $this->db->query('Select id,url from film_types')->fetch_all(MYSQLI_ASSOC);
        foreach ($query as $row) {
            yield $row['id'] => ['url' => $row['url'], 'loadId' => $loadId];
        }
    }

    /**
     * @param $values
     * @return int|mixed
     */
    public function getFilmId($values)
    {
        $result = 0;
        $query = $this->db->query('Select id from films where word_art_id = ' . intval($values['word_art_id']));
        if ($query->num_rows) {
            $result = $query->fetch_assoc()['id'];
        }
        return $result;
    }

    /**
     * @param array $values
     * @return int|string
     */
    public function saveFilm(array $values)
    {
        static $name, $year, $description, $cover, $word_art_id, $insertStmt;
        if (!isset($insertStmt)) {
            $insertStmt = $this->db->prepare('Insert into films (name,year,description,cover,word_art_id)values(?,?,?,?,?)');
            $insertStmt->bind_param('sissi', $name, $year, $description, $cover, $word_art_id);
        }
        extract($values);
        $insertStmt->execute();
        return $this->db->insert_id;
    }

    /**
     * @param array $values
     * @return int|string
     * @throws Exception
     */
    public function saveRate(array $values)
    {
        static $load_id, $film_id, $film_type_id, $position, $calc_ball, $votes, $avg_ball, $insertStmt;
        if (!isset($insertStmt)) {
            $insertStmt = $this->db->prepare('Insert into rates (load_id, film_id, film_type_id, position, calc_ball, votes, avg_ball) values (?,?,?,?,?,?,?)');
            $insertStmt->bind_param('iiisdid', $load_id, $film_id, $film_type_id, $position, $calc_ball, $votes, $avg_ball);
        }
        extract($values);
        $insertStmt->execute();
        if ($this->db->errno) {
            throw new Exception('Model error: ' . $this->db->error);
        }
        return $this->db->insert_id;
    }
}