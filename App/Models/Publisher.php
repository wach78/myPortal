<?php
use Simpleframework\Applib\Database;
use Simpleframework\RABC\PrivilegedUser;
use Simpleframework\Middleware\Sanitize;

class Publisher extends Database
{
    public  function __construct()
    {
        parent::__construct(DBCONFIG);
    }
    
    public function addPublisher($data)
    {
        $query = 'INSERT INTO `publisher` ( `UserID`, `Publishername`)  VALUES (:userID, :publishername)';
        $this->query($query);
        $this->bind(':userID',$data['UserID'],PDO::PARAM_INT);
        $this->bind(':publishername',$data['publishername']);
        return $this->execute();
    }
    
    public function editPublisher($data)
    {
        $query = 'UPDATE `publisher` SET Publishername = :publishername WHERE ID = :ID AND UserID = :userID';
        $this->query($query);
        $this->bind(':publishername',$data['publishername']);
        $this->bind(':ID',$data['ID'],PDO::PARAM_INT);
        $this->bind(':userID',$data['UserID'],PDO::PARAM_INT);
        return $this->execute();
    }
    
    public function delPublisher($data)
    {
        $query = 'DELETE FROM `publisher` WHERE  ID = :ID AND UserID = :userID';
        $this->query($query);
        $this->bind(':ID',$data['ID'],PDO::PARAM_INT);
        $this->bind(':userID',$data['UserID'],PDO::PARAM_INT);
        return $this->execute();
    }
    
    public function getOnePublisher($data)
    {
        $query = 'SELECT Publishername FROM publisher WHERE  ID = :ID AND UserID = :userID LIMIT 1';
        $this->query($query);
        $this->bind(':ID',$data['ID'],PDO::PARAM_INT);
        $this->bind(':userID',$data['UserID'],PDO::PARAM_INT);
        return $this->single();
    }
    
    public function getAllPublisherByUserID($userID)
    {
        $query = 'SELECT ID, Publishername FROM publisher WHERE UserID = :UserID';
        $this->query($query);
        $this->bind(':UserID',$userID);
        return $this->resultSet();
    }
    
    public function getAllPublisher($data,$extra)
    {
        $pagestart = $data['start'];
        $pagelength = $data['length'];
        $where = array();
        $da = array();
        
        $query = 'SELECT ID, Publishername FROM publisher WHERE UserID = :UserID';
        
        
        $querycount  = 'SELECT count(ID) AS cnt FROM publisher WHERE UserID = :UserID';
        $this->query($querycount);
        $this->bind(':UserID',$data['UserID'],PDO::PARAM_INT);
        $cntRow = $this->single();
        
        
        if ($pagestart != null && is_numeric($pagestart))
        {
            if ($pagestart < 0)
            {
                $pagestart = 0;
            }
            if ($pagelength < 0)
            {
                $pagelength = AUTOR_LIMIT;
            }
            
            if ($pagestart == 0)
            {
                $limit = ' LIMIT '. $pagelength;
            }
            else
            {
                $limit = ' LIMIT '. $pagestart .','. $pagelength;
            }
        }
        
        $rtn['recordsTotal'] = 0;
        $rtn['recordsFiltered'] = 0;
        
        
        $rtn['recordsTotal'] =  $cntRow ->cnt;
        $rtn['recordsFiltered'] =  $cntRow ->cnt;
        
        
        if (isset($extra) && $extra != null)
        {
            $j = 0;
            foreach ( $extra as $key => $value ) {
                switch($key)
                {
                    case 'search':
                        if ($value != '')
                        {
                            $value = '%'.$value.'%';
                            
                            $where[] = 'Publishername LIKE :search'.$j;
                            $da[] = $value;
                            $j++;
                        }
                        break;
                    case 'order':
                        
                        $value[0] = 1;
                        $columns = '/^SELECT ([\(\)\>\"\.\`, \w]*) FROM/';
                        $header = array();
                        if (preg_match($columns, $query, $matches ) ) {
                            $tmparr = explode(', ', $matches[1]);
                            
                            foreach ($tmparr as $col) {
                                
                                $tmp2arr = explode(' AS ',$col);
                                if (count($tmp2arr) == 1)
                                {
                                    $header[] = $tmp2arr[0];
                                }
                                else
                                {
                                    $header[] = $tmp2arr[1];
                                }
                                
                            }
                        }
                        if ($value[0] == $value[1])
                        {
                            $order = '';
                        }
                        elseif (count($header) > 0)
                        {
                            $order = ' ORDER BY '. $header[$value[0]].' '.$value[1].' ';
                        }
                        else
                        {
                            $order = '';
                        }
                        break;
                }
            }
        }//if
        $rtn['recordsFiltered'] = 0;
        if (count( $where ) > 0)
        {
            $querycount .= ' AND (' . implode( ' OR ', $where ) . ');';
        }
        
        $querycount  = 'SELECT count(ID) AS cnt FROM publisher WHERE UserID = :UserID';
        $this->query($querycount);
        $this->bind(':UserID',$data['UserID'],PDO::PARAM_INT);
        $cntRow = $this->single();
        
        $rtn['recordsTotal'] =  $cntRow ->cnt;
        $rtn['recordsFiltered'] =  $cntRow ->cnt;
        
        
        
        
        
        if (count( $where ) > 0)
        {
            $query .= ' AND (' . implode( ' OR ', $where ) . ')';
        }
        
        $query .= $order . $limit;
        
        
        $this->query($query);
        $this->bind(':UserID',$data['UserID'],PDO::PARAM_INT);
        
        
        $len = count($da);
        
        for ($i = 0; $i < $len; $i++)
        {
            $this->bind(':search'.$i,$da[$i]);
        }
        
        
        
        $datarows = $this->resultSet();
        
        $rowcols = [];
        $i = 0;
       
        $puser = new PrivilegedUser(DBCONFIG);
        $userID = (int)$_SESSION['UserID'] ?? 0;
        $puser->getPriUserByID($userID);
        
        foreach ($datarows  as $row ) {
            $rowcols[$i] = array();
            $rowcols[$i][0] = array();
            
            
            foreach ($row as $key => $col) {
                
                
                if ($key == 'ID')
                {
                    $rowcols[$i][0]['ID'] = (int)Sanitize::cleanOutput($col);
                    if ($puser->hasPrivileage('AddPublisher'))
                    {
                        $rowcols[$i][0]['Del'] = 1;
                    }
                    
                    $rowcols[$i][0]['Update'] = 0;
                    if ($puser->hasPrivileage('AddPublisher'))
                    {
                        $rowcols[$i][0]['Update'] = 1;
                    }
                }
                else
                {
                    $rowcols[$i][] = Sanitize::cleanOutput($col);
                }
                
                
            }
            $i++;
        }
        
        $rtn['data'] = $rowcols;
        
        return $rtn;
    }
    
}