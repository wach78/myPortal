<?php
/**
 *  prevent multiple logins of the user and automatically log out from other places when it gets logged in.
 */

namespace Simpleframework\Middleware;

use Simpleframework\Helpers\Util;
use PDO;
use Simpleframework\Applib\Database;

class UserToken extends Database
{
    public function __construct()
    {
        parent::__construct(DBCONFIG);
    }
    
    
    
    public  function getUsertoken($len=32)
    {
        return sha1(bin2hex(random_bytes($len)));
    }
    
    public function insertUsertoken($UserID,$token)
    {
        $query = 'INSERT INTO usertoken (UserID, Token) VALUES (:UserID, :Token)';
        $this->query($query);
        $this->bind(':UserID',$UserID);
        $this->bind(':Token',$token);
        return $this->execute();
    }
    
    public function updateUsertoken($UserID,$token)
    {
        $query = 'UPDATE usertoken SET Token = :Token WHERE UserID = :UserID LIMIT 1';
        $this->query($query);
        $this->bind(':Token',$token);
        $this->bind(':UserID',$UserID);
        return $this->execute();
    }
    
    public function checkIfUserTokenExits($UserID,$token)
    {
        $query = 'SELECT ID FROM usertoken WHERE UserID = :UserID LIMIT 1';
        $this->query($query);
        $this->bind(':UserID',$UserID);
        $value = $this->single(PDO::FETCH_ASSOC);
        
        return isset($value['ID']);
    }
    
    public function getToken($UserID)
    {
        $query = 'SELECT Token FROM usertoken WHERE UserID = :UserID LIMIT 1';
        $this->query($query);
        $this->bind(':UserID',$UserID,PDO::PARAM_INT);
        $value = $this->single();
        return $value->Token ?? false;
    }
    
    public function checkToken()
    {
        if (static::checkIfUserIsLoggedin())
        {
            $userID = (int)$_SESSION['UserID'];
            $token = $this->getToken($userID);
        
            //var_dump($token);
            //var_dump( $_SESSION['usertoken']);
            
            if (isset($_SESSION['usertoken']) &&  $_SESSION['usertoken'] == $token)
            {
                return true;
            }
            else 
            {
                unset($_SESSION['userlogin']);
                unset($_SESSION['usertoken']);
                $_SESSION = array();
                session_destroy();
                Util::redirect('users/login');
            }
            
        }
    }
    
    public static function checkIfUserIsLoggedin()
    {
        return isset($_SESSION['UserID']);
    }
    
    public function harddeleteusertoken($id)
    {
        $query = 'DELETE FROM usertoken WHERE UserID = :userID';
        $this->query($query);
        $this->bind(':userID',$id);
        $this->execute();
    }
    
}