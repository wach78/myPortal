<?php
use Simpleframework\Applib\Database;
use Simpleframework\Middleware\Sanitize;
use Simpleframework\RABC\PrivilegedUser;

class Book extends Database
{
    public  function __construct()
    {
        parent::__construct(DBCONFIG);
    }
    
    public function addBook($data)
    {
        $query = 'INSERT INTO `books` (`UserID`, `Bookname`, `SerieID`, `CategoryID`, `PublisherID`, `Pages`, `ISBN`, `Description`, `Haveread`) 
                  VALUES ( :userID, :bookname, :serieID, :categoryID, :publisherID, :pages, :ISBN, :description ,:haveread )';
        
        $this->query($query);
        $this->bind(':userID',$data['UserID'],PDO::PARAM_INT);
        $this->bind(':bookname',$data['bookname'],PDO::PARAM_STR);
        $this->bind(':serieID',$data['serieID'],PDO::PARAM_INT);
        $this->bind(':categoryID',$data['categoryID'],PDO::PARAM_INT);
        $this->bind(':publisherID',$data['publiserID'],PDO::PARAM_INT);
        $this->bind(':pages',$data['pages'],PDO::PARAM_INT);
        $this->bind(':ISBN',$data['ISBN'],PDO::PARAM_STR);
        $this->bind(':description',$data['description']);
        $this->bind(':haveread',$data['haveread'],PDO::PARAM_INT);
        $this->execute();
        
        return $this->getLastID();
    }
    
    public function editBook($data)
    {
        $query = 'UPDATE `books` SET 
                 `Bookname`= :bookname,
                 `SerieID`= :serieID,
                 `CategoryID`= :categoryID,
                 `PublisherID`= :publisherID,
                 `Pages`= :pages,
                 `ISBN`= :ISBN,
                 `Description`= :description,
                 `Haveread`= :haveread
                  WHERE ID = :ID AND UserID = :userID';
        
        $this->query($query);
        
        $this->bind(':bookname',$data['bookname']);
        $this->bind(':serieID',$data['serieID'],PDO::PARAM_INT);
        $this->bind(':categoryID',$data['categoryID'],PDO::PARAM_INT);
        $this->bind(':publisherID',$data['publiserID'],PDO::PARAM_INT);
        $this->bind(':pages',$data['pages'],PDO::PARAM_INT);
        $this->bind(':ISBN',$data['ISBN']);
        $this->bind(':description',$data['description']);
        $this->bind(':haveread',$data['haveread'],PDO::PARAM_INT);
        $this->bind(':ID',$data['ID'],PDO::PARAM_INT);
        $this->bind(':userID',$data['UserID'],PDO::PARAM_INT);
        return $this->execute();
       
    }
    
    public function delBook($data)
    {
        $query = 'DELETE FROM `books` WHERE  ID = :ID AND UserID = :userID';
        $this->query($query);
        $this->bind(':ID',$data['ID'],PDO::PARAM_INT);
        $this->bind(':userID',$data['UserID'],PDO::PARAM_INT);
        return $this->execute();
    }
    
    public function getOneBook($data)
    {
        $query = 'SELECT `Bookname`, `SerieID`, `CategoryID`, `PublisherID`, `Pages`, `ISBN`, `Description`, `Haveread` FROM books WHERE  ID = :ID AND UserID = :userID LIMIT 1';
        $this->query($query);
        $this->bind(':ID',$data['ID'],PDO::PARAM_INT);
        $this->bind(':userID',$data['UserID'],PDO::PARAM_INT);
        return $this->single();
    }
    
    public function addbooksautors($data)
    {
        $query = 'INSERT INTO `bookshaveautors` (`UserID`, `BookID`, AuthorID ) VALUES ( :userID, :bookID, :authorID )';
        $this->query($query);
        $this->bind(':userID',$data['UserID'],PDO::PARAM_INT);
        $this->bind(':bookID',$data['bookID'],PDO::PARAM_INT);
        $this->bind(':authorID',$data['authorID'],PDO::PARAM_INT);
        return $this->execute();
    }
    
    public function delbooksautors($data)
    {
        $query = 'DELETE FROM `bookshaveautors` WHERE BookID = :bookID AND UserID = :userID';
        $this->query($query);
        $this->bind(':bookID',$data['ID'],PDO::PARAM_INT);
        $this->bind(':userID',$data['UserID'],PDO::PARAM_INT);
        return $this->execute();
    }
    
    public function getAutorsIDForOneBook($bookID,$userID)
    {
        $query = 'SELECT AuthorID FROM bookshaveautors WHERE BookID = :BID AND UserID = :UID';
        $this->query($query);
        $this->bind(':BID',$bookID,PDO::PARAM_INT);
        $this->bind(':UID',$userID,PDO::PARAM_INT);
        return  $this->resultSet();
    }
    
    
    public function getAutorsNameForOneBook($bookID)
    {
        $query = 'SELECT Authorname FROM author WHERE ID IN (SELECT AuthorID FROM bookshaveautors WHERE bookshaveautors.BookID = :BID )';
        $this->query($query);
        $this->bind(':BID', $bookID,PDO::PARAM_INT);
        $values = $this->resultSet();
        $autors = [];
        foreach ($values as $v) {
            
            $s =  $v->Authorname ?? '';
            $autors[] = Sanitize::cleanOutput($s);
            
        }    
        
         return $autors;
    }
    
    public function getBookAutors($data)
    {
        $query = 'SELECT author.ID, author.Authorname 
                  FROM author
                  JOIN bookshaveautors on bookshaveautors.AuthorID = author.ID
                  WHERE bookshaveautors.UserID = :userID AND bookshaveautors.BookID = :bookID
                  ';
        $this->query($query);
        $this->bind(':bookID',$data['ID'],PDO::PARAM_INT);
        $this->bind(':userID',$data['UserID'],PDO::PARAM_INT);
        return $this->resultSet(); 
    }
    
    public function getAllBooks($data,$extra)
    {
        $pagestart = $data['start'];
        $pagelength = $data['length'];
        $where = array();
        $da = array();
        
        //'SELECT Authorname FROM author WHERE ID IN (SELECT AuthorID FROM bookshaveautors WHERE bookshaveautors.BookID  = books.ID )'
        
        $query = 'SELECT books.ID, books.Bookname, serie.Seriename, ""  as authors ,categories.Categoriesname, books.Pages, books.ISBN, publisher.Publishername, books.Description, books.Haveread FROM books ' .
                   'JOIN serie on serie.ID = books.SerieID
                   JOIN categories on categories.ID = books.CategoryID
                   JOIN publisher on publisher.ID = books.PublisherID
                   WHERE books.UserID = :UserID
                ';

        
        $limit = $this->calcLimit($pagestart,$pagelength);
        
        $querycount  = 'SELECT count(ID) AS cnt FROM books WHERE books.UserID = :UserID';
        $this->query($querycount);
        $this->bind(':UserID',$data['UserID'],PDO::PARAM_INT);
        $cntRow = $this->single();
        
        $rtn['recordsTotal'] =  $cntRow->cnt ?? 0;
        $rtn['recordsFiltered'] =  $cntRow->cnt ?? 0;
        
     
        if (isset($extra) && $extra != null)
        {
            $querycount2  = 'SELECT count(books.ID) AS cnt 
                             FROM books 
                             JOIN serie on serie.ID = books.SerieID
                             JOIN categories on categories.ID = books.CategoryID
                             JOIN publisher on publisher.ID = books.PublisherID
                             WHERE books.UserID = :UserID';

            foreach ( $extra as $key => $value )
            {
                switch($key)
                {
                    case 'search':
                        if ($value != '')
                        {
                            $value = '%'.$value.'%';
                            
                            $where[] = 'books.Bookname LIKE :search0';
                            $where[] = 'books.Pages LIKE :search1';
                            $where[] = 'books.ISBN LIKE :search2';
                            $where[] = 'books.Pages LIKE :search3';
                            $where[] = 'serie.Seriename LIKE :search4';
                            $where[] = 'categories.Categoriesname LIKE :search5';
                            $where[] = 'publisher.Publishername LIKE :search6';

                            $da[] = $value;
                            $da[] = $value;
                            $da[] = $value;
                            $da[] = $value;
                            $da[] = $value;
                            $da[] = $value;
                            $da[] = $value;
                        }
                        break;
                    case 'order':
                        
                        $value[0] = 1;
                        $columns = '/^SELECT ([\(\)\>\"\.\`, \w]*) FROM/';
                        $header = array();
                        //var_dump($columns, $query);
                        if (preg_match($columns, $query, $matches ) )
                        {
                           
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
                       // var_dump($header);
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
            $querycount2 .= ' AND (' . implode( ' OR ', $where ) . ');';
  
        }
        
        $this->query($querycount2);
        $len = count($da);
        for ($i = 0; $i < $len; $i++)
        {
            $this->bind(':search'.$i,$da[$i]);
        }
        
        $this->bind(':UserID',$data['UserID'],PDO::PARAM_INT);
        $cntRow = $this->single();
        
        $rtn['recordsTotal'] =  $cntRow->cnt ?? 0;
        $rtn['recordsFiltered'] =  $cntRow->cnt ?? 0;
        

        if (count( $where ) > 0)
        {
            $query .= ' AND (' . implode( ' OR ', $where ) . ')';  
        }
        
        $query .= $order . $limit;
      
        
        $this->query($query);
        $this->bind(':UserID',$data['UserID'],PDO::PARAM_INT);
        
        if (count( $where ) > 0)
        {
            $len = count($da);
            for ($i = 0; $i < $len; $i++)
            {
                $this->bind(':search'.$i,$da[$i]);
            }
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
             //   var_dump($key);
                if ($key == 'ID')
                {
                    $rowcols[$i][0]['ID'] = (int)Sanitize::cleanInt($col);
                    if ($puser->hasPrivileage('AddBooks'))
                    {
                        $rowcols[$i][0]['Del'] = 1;
                    }
                    
                    $rowcols[$i][0]['Update'] = 0;
                    if ($puser->hasPrivileage('AddBooks'))
                    {
                        $rowcols[$i][0]['Update'] = 1;
                    }
                    
                }
                elseif ($key == 'authors')
                {
                   // var_dump($rowcols[$i][0]);
                    $query = 'SELECT Authorname FROM author WHERE ID IN (SELECT AuthorID FROM bookshaveautors WHERE bookshaveautors.BookID = :BID )';
                    $this->query($query);
                    $this->bind(':BID', $rowcols[$i][0]['ID'] ,PDO::PARAM_INT);
                    $values = $this->resultSet();
                    $str = [];
                    foreach ($values as $v) {
                                                
                        $s =  $v->Authorname ?? '';
                        $str[] = Sanitize::cleanOutput($s);

                    }    
                    
                    $rowcols[$i][3] =  implode($str, '<br >');
                    
                }
                else
                {
                    $rowcols[$i][] = Sanitize::cleanOutput($col);
                }
                
                
            }
            $i++;
        }
       //array sort toka om autors index 3 
        $rtn['data'] = $rowcols;
        
        
        return $rtn;
 
    }
    
    private function calcLimit($pagestart,$pagelength)
    {
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
            
            return $limit;
        }
        
        return  ' LIMIT '. $pagelength;
    }
    
    

}