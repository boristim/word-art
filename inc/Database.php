<?php


namespace Db;


use mysqli;
use Exception;

class Database extends mysqli
{
    public function __construct()
    {
        @parent::__construct(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        if ($this->connect_error) {
            throw new Exception('Database error: Couldn`t connect to database');
        }
        $this->set_charset(DB_CHARSET);
    }

    public function getArray($sql): array
    {
        $query = $this->query($sql);
        if ($query->num_rows) {
            $ret = $query->fetch_all(MYSQLI_ASSOC);
            _log($ret);
            return $ret;
        }
        return [];
    }
}