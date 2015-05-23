<?php
/**
 * file: Manager.php
 *
 *
 * @author troy
 * @version 1.0     time: 2014-9-5
 */

class DB_Manager {
    private $link;
    private static $instance = array();
    protected function __construct() {
        $dbConf = $this->parseConf();
        $this->link = $this->getDbConn($dbConf);

        if ($this->link === null) throw new Exception("Error when getDbConn");
    }
    protected function __clone() {}
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }

    public static function getLink() {
        $thiz = DB_Manager::getInstance();
        return $thiz->link;
    }

    /**
     * <p>$sqlUnits is array of $sqlUnit, and $sqlUnit is as follows:</p>
     * <p></p>
     * <p>$sqlUnit = array('sql' => 'select * from xxx where id = ?', 'vals' => array(1));</p>
     * <p>or</p>
     * <p>$sqlUnit = 'select * from xxx where id = 1';</p>
     * @param $sqlUnits array
     */
    public static function execTrans($sqlUnits) {
        $thiz = DB_Manager::getInstance();
        try {
            $thiz->link->beginTransaction();// 开启事务处

            foreach ($sqlUnits as $sqlUnit) {
                $sqlUnit = $thiz->parseSqlUnit($sqlUnit);
                if(trim($sqlUnit['sql']) == '') continue;
                $st = $thiz->link->prepare($sqlUnit['sql']);
                $res = $st->execute($sqlUnit['vals']);
                if(!$res) {
                    $thiz->link->rollBack();
                    return false;
                }
                $st->closeCursor();
            }

            $thiz->link->commit();// 事务处理结束
        } catch(PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * <p>eg.</p>
     * <p>$sqlUnit = array('sql' => 'select * from xxx where id = ?', 'vals' => array(1));</p>
     * <p>or</p>
     * <p>$sqlUnit = 'select * from xxx where id = 1';</p>
     * @param $sqlUnit array|string
     * @param $isSelect boolean
     */
    public static function execute($sqlUnit, $isSelect=true) {
        $thiz = DB_Manager::getInstance();
        $sqlUnit = $thiz->parseSqlUnit($sqlUnit);

        if (!$sqlUnit['sql']) return false;
        try {
            $st = $thiz->link->prepare($sqlUnit['sql']);
            $res = $st->execute($sqlUnit['vals']);

            //var_dump($sqlUnit);
            if ($res === false) return false;
            if (!$isSelect) {
                if (strpos(strtolower($sqlUnit['sql']), 'insert into') === 0) return $thiz->link->lastInsertId();
                if (strpos(strtolower($sqlUnit['sql']), 'update') === 0) return $st->rowCount();
                if (strpos(strtolower($sqlUnit['sql']), 'delete') === 0) return $st->rowCount();
                return $res;
            }

            return $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * <p>$sqlUnit = array('sql' => 'select * from xxx where id = ?', 'vals' => array(1));</p>
     * <p>or</p>
     * <p>$sqlUnit = 'select * from xxx where id = 1';</p>
     */
    private function parseSqlUnit($sqlUnit) {
        if (is_array($sqlUnit)) {
            $sqlUnit['sql'] = trim($sqlUnit['sql']);
            return $sqlUnit;
        }

        return array('sql' => trim($sqlUnit), 'vals'=> array());
    }

    private function parseConf() {
        $dbConf = Yaf_Registry::get('config')->get('db')->toArray();
        foreach ($dbConf as $k => $v) {
            $dbConf[$k] = trim($v);
        }
        return $dbConf;
    }

    private function getDbConn($dbConf) {
        $conn = null;
        try	{
            $connPara[PDO::ATTR_TIMEOUT] = 3;
            $connPara[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES UTF8;";
            $conn = new PDO('mysql:host='.$dbConf['host'].';port='.$dbConf['port'].';dbname='.$dbConf['db'],$dbConf['user'],$dbConf['pwd'], $connPara);
        } catch(PDOException $e) {
            return null;
        }
        return $conn;
    }

    public static function getInstance() {
        $cls = get_called_class();
        if (!isset(self::$instance[$cls])) {
            self::$instance[$cls] = new static;
        }
        return self::$instance[$cls];
    }

    /**
     * 数据库insert操作
     * @param $table    [string]    表名
     * @param $params   [array]     待插入数据库字段数组
     * @return          执行失败返回false, 成功返回$thiz->link->lastInsertId()
     */
    public static function insert($table, $params) {
        $thiz = DB_Manager::getInstance();

        $keys = array();
        $values = array();

        foreach ($params as $k => $v) {
            $keys[] = $k;
            $values[] = $v;
        }

        $sql = 'insert into `'.$table.'`(`' . implode('`,`', $keys) . '`) values (' . substr(str_pad('', 2*count($keys), ',?'), 1) . ')';

        return $thiz->execute(array('sql' => $sql, 'vals' => $values), false);
    }

    public static function delete($table, $where) {
        $thiz = DB_Manager::getInstance();

        $sql = "delete from $table where 1 = 1 ";
        $vals = array();

        foreach ($where as $k => $v) {
            $sql .= " and `$k` = ? ";
            $vals[] = $v;
        }

        return $thiz->execute(array('sql' => $sql, 'vals' => $vals), false);
    }

    /**
     * 数据库update操作
     * @param $table    [string]    表名
     * @param $params   [array]     待更新数据库字段数组
     * @param $where    [array]     where条件
     * @return          执行失败返回false, 成功返回$st->rowCount()
     */
    public static function update($table, $params, $where) {
        $thiz = DB_Manager::getInstance();

        $sql = 'update `'.$table.'` set ';
        $vals = array();

        foreach ($params as $field => $val) {
            $sql .= "`$field` = ?,";
            $vals[] = $val;
        }
        $sql = substr($sql, 0, strlen($sql) - 1) . ' where ';
        foreach ($where as $field => $val) {
            $sql .= "`$field` = ? and ";
            $vals[] = $val;
        }
        $sql = substr($sql, 0, strlen($sql) - 4);

        return $thiz->execute(array('sql' => $sql, 'vals' => $vals), false);
    }

    /**
     * 数据库select操作
     * @param $table    [string]    表名
     * @param $params   [array]     select的字段
     * @param $where    [array]     where条件
     * @return          执行失败返回false, 成功返回数据集
     */
    public static function select($table, $where = array(), $fields = "*") {
        $thiz = DB_Manager::getInstance();

        if ($fields == "*") {
            $sql = "select * from `$table` where 1 = 1 ";
        } else {
            $sql = 'select `' . implode('`,`', $fields) . "` from `$table`  where 1 = 1 ";
        }

        $vals = array();
        foreach ($where as $k => $v) {
            $sql .= " and `$k` = ?";
            $vals[] = $v;
        }

        return $thiz->execute(array('sql' => $sql, 'vals' => $vals));
    }
}
