<?php
/**
 * Połaczenie PDO.
 *
 * @author Krzysztof Wałek
 */

class qDbConnect
{
    // Multiple UTF8
    public static $utf8 = false;

    protected $link = 0;
    protected $connect = true;
    protected $driver = 'none';

    /** @var string ostatni błąd */
    protected $error = 'NoError';
    protected $errorCode = false;
    protected $prefix = '';
    protected $log = false;
    protected $result = 0;
    protected $rows = 0;
    /** @var int licznik ilości zapytań */
    protected $totalCount = 0;
    protected $totalTime = 0;
    protected $name = '';

    /** @var string Ostatnie zapytanie */
    protected $sqlText = '';
    protected $connectName = '';

    /** @var string|false zapytanie przez prepare */
    protected $prepare = false;

    /** @var array parametry do prepare */
    protected $params = array();

    protected $statement = false;

    /**
     * Connect constructor.
     *
     * @param            $driver
     * @param            $host
     * @param            $name
     * @param            $user
     * @param            $pass
     * @param bool|false $port
     */
    public function __construct($driver, $host, $name, $user, $pass)
    {
        list($host, $port) = qString::explodeList(':', $host, 2, false);
        $this->driver = $driver;
        $connect = $driver.':host='.$host.';dbname='.$name;
        if ($port) {
            $connect .= ';port='.$port;
        }
        try {
            $this->link = new PDO($connect, $user, $pass);
            $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->error = 'Brak połączenia z bazą danych. '.$e->getMessage();
            $this->errorCode = $e->getCode();
            echo $this->error;
            exit;
        }
        $this->connect = true;
        if (false === $this->link) {
            $this->error = 'NoHost';
            $this->errorCode = '99999';
            $this->connect = false;
        }
        $this->name = $name;
    }

    public function execute()
    {
        $this->query($this->prepare, $this->params);
    }

    public function query($prepare, $params = array(), $logError = true)
    {
        $this->prepare = $prepare;
        $this->params = $params;
        $this->numRows = false;

        ++$this->totalCount;

        $this->error = 'NoError';
        $this->errorCode = false;

        if (!$params) {
            $params = [];
        }
        $realSql = $this->sqlDebug($prepare, $params);
        $this->sqlText = $realSql;
        try {
            $this->statement = $this->link->prepare($prepare);
            $this->result = $this->statement->execute($params);
            if ('mysql' == $this->driver) {
                $this->numRows = $this->statement->rowCount();
            }
            $this->error = 'NoError';
            $this->errorCode = false;
            $this->result = true;
        } catch (PDOException $e) {
            $this->error = 'PDO Error: '.$e->getMessage();
            $this->errorCode = $e->getCode();
            if ($logError) {
                qLog::write("Błąd zapytania POD:\n".$realSql."\n".$this->error);
            }
            $this->result = false;
        }

        return $this->result;
    }

    /**
     * Zwraca kod błędu ostatniego zapytania.
     */
    public function errorCode()
    {
        return $this->errorCode;
    }

    /**
     * Ustawienie bazy danych w tryb UTF8.
     */
    public function utf8()
    {
        if (!static::$utf8) {
            $this->query('SET NAMES UTF8');
            static::$utf8 = true;
        }
    }

    /**
     * Zwaraca ilość zaoytań typu exec.
     *
     * @return int
     */
    public function totalCount()
    {
        return $this->totalCount;
    }

    /**
     * Zwraca czas wszystkich zapytań.
     *
     * @return number
     */
    public function totalTime()
    {
        return $this->totalTime;
    }

    /**
     * Zwaraca nazwę polaczenia.
     *
     * @param $connectName
     */
    public function setConnectName($connectName)
    {
        $this->connectName = $connectName;
    }

    /**
     * Zwraca objekt realizujączy zapytania.
     *
     * @param bool|false $sql
     * @param array      $params
     *
     * @return SqlMysqlSelect
     */
    public function select($sql = false, $params = array())
    {
        $this->prepare = $sql;
        $this->params = $params;
        $this->queryPrefix($this->prepare, $this->params);

        return $this;
    }

    public function name()
    {
        return $this->name;
    }

    /*
     * Funkcje pomocnicze
     */

    public function sql()
    {
        return $this->sqlText;
    }

    public function connected()
    {
        return $this->connect;
    }

    public function error($newError = false)
    {
        if ($newError) {
            $this->error = $newError;
        }

        return $this->error;
    }

    public function prefix($aSharp = false)
    {
        if (false === $aSharp) {
            return $this->prefix;
        }
        $this->prefix = $aSharp;
    }

    public function setLog($log)
    {
        $this->log = $log;
    }

    /*
     * Funkcje transakcji
     */

    public function beginTransaction()
    {
        return $this->query('BEGIN TRANSACTION');
    }

    public function commit()
    {
        return $this->query('COMMIT TRANSACTION');
    }

    public function rollBack()
    {
        return $this->query('ROLLBACK TRANSACTION');
    }

    /*
     * FUNKCJE WEWNĘTRZNE
     */

    public function preparePrefix($sql)
    {
        return str_replace(' `#', ' `'.$this->prefix, $sql);
    }

    public function queryPrefix($sql, $params = array())
    {
        return $this->query($this->preparePrefix($sql), $params);
    }

    public function affectedRows()
    {
        //return mysql_affected_rows($this->link);
        return $this->statement->rowCount();
    }

    public function freeResult()
    {
        if (0 != $this->result) {
            mysql_free_result($this->result);
            $this->result = 0;
        }
    }

    public function lastInsertId()
    {
        return $this->select('SELECT LAST_INSERT_ID()')->result();
    }

    public function lastInsertPk()
    {
        return $this->select('SELECT LAST_INSERT_ID()')->result();
    }

    public function insert($table, array $fields, $ignore = false, $logError = true)
    {
        if ('#' == substr($table, 0, 1)) {
            $table = $this->prefix.substr($table, 1);
        }
        $cols = array();
        $vals = array();
        $bind = array();
        foreach ($fields as $col => $val) {
            $cols[] = '`'.$col.'`';
            if ($val instanceof \qPdoExpr) {
                $vals[] = $val->__toString();
            }
            else {
                $vals[] = ':'.$col;
                $bind[':'.$col] = $val;
            }
        }
        $sql = 'INSERT ';
        if ($ignore) {
            $sql .= ' IGNORE';
        }
        $sql .= ' INTO `'.$table.'` ('.implode(', ', $cols).') '
                .'VALUES ('.implode(', ', $vals).')';
        $this->query($sql, $bind, $logError);

        return $this->numRows;
    }

    public function update(string $table, array $fields, array $where)
    {
        if ('#' == substr($table, 0, 1)) {
            $table = $this->prefix.substr($table, 1);
        }
        $sets = array();
        $keys = array();
        $bind = array();
        foreach ($fields as $col => $val) {
            if ($val instanceof \qPdoExpr) {
                $sets['`' . $col . '`'] = $val->__toString();
            } elseif (is_null($val)) {
                $sets['`' . $col . '`'] = 'null';
            } else {
                $sets['`'.$col.'`'] = ':'.$col;
                $bind[':'.$col] = $val;
            }
        }
        if ($where instanceof \qPdoExpr) {
            $where = $where->__toString();
        } else {
            foreach ($where as $col => $val) {
                if ($val instanceof \qPdoExpr) {
                    $keys['`'.$col.'`'] = $val->__toString();
                } else {
                    $keys['`' . $col . '`'] = ':__' . $col . '__';
                    $bind[':__' . $col . '__'] = $val;
                }
            }
        }
        $sql = 'UPDATE `'.$table.'` SET';
        $ii = 0;
        foreach ($sets as $col => $val) {
            if ($ii > 0) {
                $sql .= ',';
            }
            $sql .= ' '.$col.' = '.$val;
            ++$ii;
        }
        if (count($keys) > 0) {
            $sql .= ' WHERE';
            $ii = 0;
            foreach ($keys as $col => $val) {
                if ($ii > 0) {
                    $sql .= ' AND';
                }
                $sql .= ' '.$col.' = '.$val;
                ++$ii;
            }
        } elseif (is_string($where)) {
            $sql .= ' WHERE '.$where;
        }

        $res = $this->query($sql, $bind);
        if (!$res) {
            return true;
        } else {
            return $res;
        }
    }

    /**
     * Usuwanie rekordu (rekordów) w/g wartośći wskazanego pola.
     *
     * @param string $table nazwa tabeli
     * @param array  $where tabela asocjacyjna
     *
     * @return bool rezultat
     */
    public function delete($table, $where)
    {
        // zabezpiecznie - nie usuń przypadkowo wszystkiego
        if (0 == count($where)) {
            return false;
        }
        if ('#' == substr($table, 0, 1)) {
            $table = $this->prefix.substr($table, 1);
        }
        $keys = array();
        $bind = array();

        if ($where instanceof qPdoExpr) {
            $where = $where->__toString();
        } else {
            foreach ($where as $col => $val) {
                if ($val instanceof qPdoExpr) {
                    $keys['`'.$col.'`'] = $val->__toString();
                } else {
                    $keys['`'.$col.'`'] = ':__'.$col.'__';
                    $bind[':__'.$col.'__'] = $val;
                }
            }
        }
        $sql = 'DELETE FROM `'.$table.'` WHERE';
        $ii = 0;

        if (is_string($where)) {
            $sql .= ' '.$where;
        } else {
            foreach ($keys as $col => $val) {
                if ($ii > 0) {
                    $sql .= ' AND';
                }
                $sql .= ' '.$col.' = '.$val;
                ++$ii;
            }
        }
        $res = $this->query($sql, $bind);
        if (!$res) {
            return true;
        } else {
            return $res;
        }
    }

    public function deleteOld($table, $where = '')
    {
        if ('#' == substr($table, 0, 1)) {
            $table = $this->prefix.substr($table, 1);
        }
        $sql = 'DELETE FROM '.'`'.$table.'`';
        if ($where instanceof sqlExpr) {
            $where = $where->__toString();
            $sql .= " WHERE {$where}";
        } else {
            $where = $this->_whereExpr($where);
            if ($where) {
                $sql .= " WHERE ($where)";
            }
        }

        $res = $this->query($sql);
        $select = $this->select('SELECT ROW_COUNT()');

        return $select->result();
    }

    protected function _whereExpr($where)
    {
        if (empty($where)) {
            return $where;
        }
        if (!is_array($where)) {
            $where = array($where);
        }
        foreach ($where as &$term) {
            if ($term instanceof SqlExpr) {
                $term = $term->__toString();
            }
            $term = '('.$term.')';
        }
        $where = implode(' AND ', $where);

        return $where;
    }

    protected function prepare($str)
    {
        //$res = "'".addslashes($str)."'";
        $res = "'".$this->escape($str)."'";

        return $res;
    }

    public function unprepare($row)
    {
        if (false !== $row) {
            foreach ($row as &$value) {
                $value = stripcslashes($value);
            }
        }

        return $row;
    }

    public function escape($str)
    {
        //Log::write($str,mysql_real_escape_string($str, $this->link));
        //return mysql_real_escape_string($str, $this->link); // wtf is this?!?!?!
        $escaped = $this->link->quote($str);
        $escaped = substr($escaped, 1, strlen($escaped) - 2);

        return $escaped;
    }

    public function now()
    {
        $sql = 'SELECT NOW()';
        $select = $this->select($sql);

        return $select->result();
    }

    // SELEKTY

    public function rows($object = true)
    {
        $rows = array();

        if($object) {
            foreach ($this->statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $rows[] = (object)$row;
            }
        }
        else {
            foreach ($this->statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $rows[] = (array)$row;
            }
        }

        $this->statement->closeCursor();
        $this->numRows = count($rows);

        return $rows;
    }

    public function indexRows($cIndex)
    {
        $rows = array();
        foreach ($this->rows() as $row) {
            $rows[$row->$cIndex] = $row;
        }

        return $rows;
    }

    public function row($object = true)
    {
        $row = false;
        $res = $this->statement->fetch(PDO::FETCH_ASSOC);
        if ($res) {
            if($object) {
                $row = (object)$res;
            }
            else {
                $row = (array)$res;
            }
        }
        $this->statement->closeCursor();

        return $row;
    }

    /**
     * Funkcja zwraca pierwszą kolumnę pierwszego wiersza zapytnia.
     */
    public function result()
    {
        $one = false;
        $one = $this->statement->fetchColumn(0);
        $this->statement->closeCursor();

        return $one;
    }

    public function nextRecord()
    {
        $res = $this->statement->fetch(PDO::FETCH_ASSOC);
        if ($res) {
            $row = (object) $res;
        }

        return $row;
    }

    public function count()
    {
        if (false === $this->numRows) {
            if ('SELECT * FROM' == substr($this->sql, 0, 13)) {
                $old = $this->sql;
                $sql = 'SELECT count(*) FROM'.substr($this->sql, 13);
                $this->query($sql);
                $this->sql = $old;
                $count = 0;
                if (false === $this->result) {
                    return 0;
                } elseif (true === $this->result) {
                    return 0;
                }
                $this->numRows = mysql_num_rows($this->result);
                if ($this->numRows > 0) {
                    $row = $this->nextRecordNum();
                    if (false != $row) {
                        $this->numRows = stripcslashes($row[0]);
                    }
                }
                $this->connect->freeResult();
            } else {
                $this->query($this->sql);
                if (false === $this->result) {
                    return 0;
                } elseif (true === $this->result) {
                    // to nie select
                    return 0;
                }
                $this->numRows = mysql_num_rows($this->result);
                $this->connect->freeResult();
            }
        }

        return $this->numRows;
    }

    /**
     * Usuwa tabelę.
     *
     * @param $table
     *
     * @return bool
     */
    public function dropTable($table)
    {
        $this->query('SET FOREIGN_KEY_CHECKS=0;');
        $res = $this->query('DROP TABLE IF EXISTS `'.$table.'`');
        $this->query('SET FOREIGN_KEY_CHECKS=1;');

        return $res ? true : false;
    }

    private function sqlDebug($prepare, array $params = array())
    {
        $key = null;
        foreach ($params as $key => &$val) {
            if (is_object($val) && $val instanceof \DateTime) {
                $val = $val->format('Y-m-d H:i:s');
            } elseif (null === $val) {
                $val = 'NULL';
            } else {
                $val = '\''.$val.'\'';
            }

            $prepare = preg_replace('/'.$key.'/', $val, $prepare);
        }
//        if (!is_null($key)) {
//            $prepare = preg_replace('/'.$key.'/', "".$val."", $prepare);
//        }

        return $prepare;
    }

    public function setSql($sql)
    {
        $sql = $this->preparePrefix($sql);

        return $this->select($sql, []);
    }

    public function insertOn($table, array $bind, $id = '')
    {
        if ('#' == substr($table, 0, 1)) {
            $table = $this->prefix.substr($table, 1);
        }
        $cols = array();
        $vals = array();
        foreach ($bind as $col => $val) {
            $cols[] = '`'.$col.'`';
            if ($val instanceof sqlExpr) {
                $vals[] = $val->__toString();
            } else {
                $vals[] = $this->prepare($val);
            }
        }
        $sql = 'INSERT ';
        $sql .= ' INTO '
      .'`'.$table.'`'
      .' ('.implode(', ', $cols).') '
      .'VALUES ('.implode(', ', $vals).')';
        $sql .= ' ON DUPLICATE KEY UPDATE ';
        $params = array();
        foreach ($cols as $col) {
            $params[] = $col.'='.current($vals);
            next($vals);
        }
        $sql .= implode(', ', $params);
        if (!empty($id)) {
            $sql .= ", $id = LAST_INSERT_ID($id)\n;";
        }
        $res = $this->queryPrefix($sql);
        if (false === $res) {
            return 0;
        }

        return $this->affectedRows();
    }

    public function pages($limit)
    {
        return ceil($this->count() / $limit);
    }

    public function page($page, $limit = 0, $difference = 0)
    {
        if (0 == $limit) {
            $limit = $this->limit;
        }

        $offset = (($page - 1) * $limit) + $difference;
        if ($offset < 0) {
            $offset = 0;
        }

        $this->prepare .= " LIMIT {$offset}, {$limit}";
        $this->queryPrefix($this->prepare, $this->params);

        return $this->rows();
    }

    public function offUnprepare()
    {
        $this->unprepare = false;
    }

    public function onUnprepare()
    {
        $this->unprepare = true;
    }
}
