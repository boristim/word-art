<?php


namespace Db;

use mysqli;
use Memcache;
use Exception;

/**
 * Class Database
 * @package Db
 */
class Database extends mysqli
{
    /**
     * @var Memcache
     */
    private Memcache $memcache;

    /**
     * Database constructor.
     * @throws Exception
     */
    public function __construct()
    {
        @parent::__construct(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        if ($this->connect_errno) {
            throw new Exception("Database error: " . $this->connect_error);
        }
        $this->set_charset(DB_CHARSET);
        $this->memcache = new Memcache();
        $this->memcache->connect(MEMCACHE_HOST, MEMCACHE_PORT);
    }

    /**
     * @param string $sql
     * @param int $cachePeriod
     * @return array
     */
    public function getArray(string $sql, $cachePeriod = MEMCACHE_PERIOD): array
    {
        $key = md5($sql);
        $result = $this->memcache->get($key);
        if (!$result) {
            $query = $this->query($sql);
            if ($query->num_rows) {
                $result = $query->fetch_all(MYSQLI_ASSOC);
                $this->memcache->set($key, $result, 0, $cachePeriod);
            }
        }

        return $result;
    }
}