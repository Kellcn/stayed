<?php
namespace Library;

class Mysql
{
    private $dsn;
    private $dbh;
    private $user;
    private $timeout;
    private $password;

    public $lastSQL = '';

    public function __construct($config = array())
    {
        $this->dsn      = $config['DB_DSN'];
        $this->user     = $config['DB_USER'];
        $this->password = $config['DB_PWD'];
        $this->timeout  = $config['DB_TIME'];
        $this->connect();
    }

    private function connect(){
        if(!$this->dbh){
            $this->dbh = new \PDO($this->dsn, $this->user, $this->password, array(
                // PDO::ATTR_TIMEOUT => $timeout,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ));
        }
    }
    
    public function pdoExec($sql, $param = [])
    {
        $sth = null;
        $this->lastSQL = $sql;
        
        try {
            $sth = $this->dbh->prepare($sql);
            $time_start = microtime(true);
            $sth->execute($param);
            $time = number_format(microtime(true) - $time_start, 8);
            if ($time > 0.3) { // 单位是秒
                \Local\Log::warning('SLOW_SQL[' . $this->lastSQL . '] PARAMS[' .json_encode($param) . '] TIME[' . $time . ']', 'TIME_LOG');
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            \Local\Log::warning('SQL_Error(' . $message . ') SQL['.$this->lastSQL.'] PARAMS[' .json_encode($param) . '] ErrInfo['.$sth->errorInfo()[2].'] code['.intval($sth->errorCode()).']', 'LOG_DPL');
        }
        
        return $sth;
    }

    public function pdoBeginTransaction()
    {
        return $this->dbh->beginTransaction();
    }
    
    public function inTransaction()
    {
        return $this->dbh->inTransaction();
    }
    
    public function pdoRollBack()
    {
        return $this->dbh->rollBack();
    }
    
    public function pdoCommit()
    {
        return $this->dbh->commit();
    }
    
}
