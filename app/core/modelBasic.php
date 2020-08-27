<?php
namespace vgace\Basic;
use vgace\core\Loader;

class ModelBasic
{
    private $successCode = '00000';
    protected $db;
    private $options = '';
    protected $table = '';
    private $pool;
    
    public function __construct(){
        
    }

    private function _connent(){
        if(isset($this->pool['MYSQL'])){
            $this->db = $this->pool['MYSQL'];
        }else{
            $G = \vgace\core\Registry::get('G');
            $dbinfo = $G['configInit']['db']['MYSQL'];
            $this->db = Loader::get($dbinfo['type'], [
                'DB_DSN'  => "mysql:host={$dbinfo['host']};port={$dbinfo['port']};dbname={$dbinfo['db']};charset=utf8mb4;",
                'DB_USER' => $dbinfo['user'],
                'DB_PWD'  => $dbinfo['pwd'],
                'DB_TIME' => $dbinfo['timeout'],
                ]);
            $this->pool['MYSQL'] = $this->db;
        }
    }

    final protected function _table($table)
    {
        if (! $table) {
            return $this;
        }
        
        $this->table = $table;
        unset($table, $this->options);
        
        return $this;
    }
    
    final protected function _field($field)
    {
        if (! $field) {
            return $this;
        }
        
        $str = '';
        if (is_array($field)) {
            foreach ($field as $val) {
                $as = explode('as', $val);
                if (count($as) == 1) {
                    $str .= '`' . trim($val, ' ') . '`,';
                } else {
                    $str .= '`' . trim($as[0], ' ') . '` as ' . trim($as[1], ' ') . ',';
                }
                
            }
            
            $this->options['field'] = substr($str, 0, strlen($str) - 1);
        } else {
            $this->options['field'] = $field;
        }
        
        unset($str, $field);
        
        return $this;
    }
    
    final protected function _where($where)
    {
        if (! $where) {
            return $this;
        }
        
        $str = '';
        $param = array();
        if (is_array($where)) {
            $total = count($where);
            $i = 1;
            foreach ($where as $key => $val) {
                $kp = explode(" ", $key);
                if (count($kp) == 1) {
                    $str .= '`' . $key . '` = :' . $key;
                    $param = array_merge($param, array(
                        ':' . $key => $val
                    ));
                } else {
                    $str .= '`' . $kp[0] . '` ' . $kp[1] . ' :' . $kp[0];
                    $param = array_merge($param, array(
                        ':' . $kp[0] => $val
                    ));
                }
                if ($i != $total) {
                    $str .= ' AND ';
                }
                $i ++;
            }
            
            $this->options['where'] = $str;
            $this->options['prepare'] = $param;
        } else {
            $this->options['where'] = $where;
        }
        
        unset($str, $i, $total, $kp, $where, $param);
        
        return $this;
    }
    
    final protected function _order($order)
    {
        $str = '';
        if (! $order) {
            return $this;
        }
        
        if (is_array($order)) {
            $total = sizeof($order);
            $i = 1;
            foreach ($order as $key => $val) {
                $str .= '`' . $key . '` ' . $val;
                if ($i != $total) {
                    $str .= ' , ';
                }
                $i ++;
            }
        } else {
            $str = $order;
        }
        
        $this->options['order'] = $str;
        unset($str, $i, $total, $order);
        
        return $this;
    }
    
    final protected function _limit($limit)
    {
        if (! $limit) {
            return $this;
        }
        
        $this->options['limit'] = $limit;
        unset($limit);
        
        return $this;
    }
    
    final protected function _select()
    {
        list ($sql, $param) = $this->_generateSql();
        $this->_connent();
        
        $query = $this->db->pdoExec($sql, $param);
        
        if ($query->errorCode() != $this->successCode) {
            return false;
        }
        
        $result = $query->fetchAll();
        
        return $result;
    }
    
    final protected function _selectOne()
    {
        $this->options['limit'] = 1;
        
        list ($sql, $param) = $this->_generateSql();
        $this->_connent();
        
        $query = $this->db->pdoExec($sql, $param);
        if ($query->errorCode() != $this->successCode) {
            return false;
        }
        
        $result = $query->fetch();
        
        return $result;
    }
    
    final protected function _generateSql()
    {
        if (isset($this->options['field'])) {
            $field = $this->options['field'];
        } else {
            $field = '*';
        }
        
        $sql = 'SELECT ' . $field . ' FROM `' . $this->table . '`';
        $param = array();
        
        if (isset($this->options['where'])) {
            $sql .= ' WHERE ' . $this->options['where'];
            if (isset($this->options['prepare'])) {
                $param = $this->options['prepare'];
            }
        }
        
        if (isset($this->options['order'])) {
            $sql .= ' ORDER BY ' . $this->options['order'];
        }
        
        if (isset($this->options['limit'])) {
            $sql .= ' LIMIT ' . $this->options['limit'];
        }
        
        return array(
            $sql,
            $param
        );
    }
    
    final protected function _update($map, $self = false)
    {
        if (! $map || ! is_array($map)) {
            return false;
        } else {
            $sql = 'UPDATE ' . $this->table . ' SET ';
            $sets = $param = array();
            if ($self) {
                foreach ($map as $key => $value) {
                    $value = trim($value);
                    if (substr($value, 0, 1) == '+') {
                        $sets[] = "`$key` = `$key` + '" . substr($value, 1) . "'";
                    } elseif (substr($value, 0, 1) == '-') {
                        $sets[] = "`$key` = `$key` - '" . substr($value, 1) . "'";
                    } else {
                        $sets[] = '`' . $key . '` = :' . $key;
                        $param = array_merge($param, array(
                            ':' . $key => $value
                        ));
                    }
                }
            } else {
                foreach ($map as $key => $value) {
                    $sets[] = '`' . $key . '` = :' . $key;
                    $param = array_merge($param, array(
                        ':' . $key => $value
                    ));
                }
            }
            
            $sql .= implode(',', $sets) . ' ';
            
            if (isset($this->options['where'])) {
                $sql .= ' WHERE ' . $this->options['where'];
                if (isset($this->options['prepare'])) {
                    $param = array_merge($param, $this->options['prepare']);
                }
            }
            
            if (isset($this->options['limit'])) {
                $sql .= ' LIMIT ' . $this->options['limit'];
            }
            
            $this->_connent();
            
            $query = $this->db->pdoExec($sql, $param);
            $result = $query->errorCode() == $this->successCode ? true : false;
            
            return $result;
        }
    }
    
    final protected function _delete()
    {
        if (! isset($this->options['where'])) {
            return false;
        }
        
        $sql = 'DELETE FROM `' . $this->table . '` WHERE ' . $this->options['where'];
        
        $param = array();
        if (isset($this->options['prepare'])) {
            $param = $this->options['prepare'];
        }
        
        if (isset($this->options['limit'])) {
            $sql .= ' LIMIT ' . $this->options['limit'];
        }
        
        $this->_connent();
        
        $query = $this->db->pdoExec($sql, $param);
        $result = $query->errorCode() == $this->successCode ? true : false;
        
        return $result;
    }
    
    final protected function _insert($map = array())
    {
        if (! $map || ! is_array($map)) {
            return false;
        } else {
            $fields = $values = $param = array();
            
            foreach ($map as $key => $value) {
                $fields[] = '`' . $key . '`';
                $values[] = ':' . $key;
                $param = array_merge($param, array(
                    ':' . $key => $value
                ));
            }
            
            $fieldString = implode(',', $fields);
            $valueString = implode(',', $values);
            
            $sql = 'INSERT INTO ' . $this->table . " ($fieldString) VALUES ($valueString)";
            
            $this->_connent();
            
            $query = $this->db->pdoExec($sql, $param);
            $result = $query->errorCode() == $this->successCode ? true : false;
            
            return $result ? $this->db->lastInsertId() : false;
        }
    }
    
    final protected function _query($sql, $param = array())
    {
        if (! $sql) {
            return false;
        }
        
        $sql = trim($sql);
        if (! is_array($param)) {
            $param = array();
        }
        
        $this->_connent();
        
        $query = $this->db->pdoExec($sql, $param);
        $result = $query->errorCode() == $this->successCode ? true : false;
        if (! $result) {
            return false;
        }
        
        if (stripos($sql, 'select') === 0) {
            $result = $query->fetchAll();
        }
        
        if (stripos($sql, 'insert') === 0) {
            return $result ? $this->db->lastInsertId() : false;
        }
        
        return $result;
    }
    
    final protected function _pdoBegin()
    {
        $this->_connent();
        $this->db->pdoBeginTransaction();
    }
    
    final protected function _pdoCommit()
    {
        $this->db->pdoCommit();
    }
    
    final protected function _pdoRollBack()
    {
        $this->db->pdoRollBack();
    }
}
