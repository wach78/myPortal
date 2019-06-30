<?php

use Simpleframework\Applib\Controller;
use Simpleframework\Helpers\Util;
use Simpleframework\Middleware\UserToken;
use Simpleframework\RABC\PrivilegedUser;
util::startSession();

class Dashboards extends Controller
{
    private const PRIVUSER = 'privuser';
    private const NUMBOOKS = 'numbooks';
    public function __construct()
    {
        $this->usertoken = new UserToken();
        if (!$this->usertoken->checkIfUserIsLoggedin())
        {
            Util::redirect('index.php');
        }
        
        $this->usertoken->checkToken();
        $this->userID = (int)$_SESSION['UserID'] ?? 0;
        $this->privuser = new PrivilegedUser(DBCONFIG);
        $this->privuser->getPriUserByID($this->userID);
        
        $this->dashboardModel = $this->model('Dashboard');
    }
    
    public function index()
    {
        
        if (!$this->privuser->hasPrivileage('Dashboard'))
        {
            Util::redirect(self::ERROR);
        }
        
        $numberOfBooks = $this->dashboardModel->getNumberOfBooksForUser($this->userID);
        
        $data = [
            self::NUMBOOKS => $numberOfBooks,
            self::PRIVUSER => $this->privuser
            
        ];
        $this->view('Dashboards/index',$data);
    }
}