<?php

use Simpleframework\Applib\Database;

class Dashboard extends Database
{
    public  function __construct()
    {
        parent::__construct(DBCONFIG);
    }
    
    
    public function getNumberOfBooksForUser($userID)
    {
        $query = 'SELECT COUNT(ID) as n FROM `books` WHERE `UserID` = :userID';
        $this->query($query);
        $this->bind('userID',$userID,PDO::PARAM_INT);
        $value = $this->single(PDO::FETCH_ASSOC);
        return $value['n'] ?? -1;
    }
}