<?php

use Simpleframework\Applib\Controller;
use Simpleframework\Helpers\Util;
use Simpleframework\Helpers\Json;
use Simpleframework\Middleware\Csrf;
use Simpleframework\Middleware\Sanitize;
use Simpleframework\RABC\PrivilegedUser;
use Simpleframework\Middleware\UserToken;

Util::startSession();

class Authors extends Controller
{
    private const AUTHORNAME = 'authorname';
    private const AUTHORNAME_ERR = 'authorname_err';
    private const USERID = 'UserID';
    
    private const ERROR = 'errors/error';
    private const ADDAUTHOR = 'Authors/addauthor';
    private const UPDATEAUTHOR = 'Authors/editauthor';
    private const PRIVUSER = 'privuser';
    
    private const DTDRAW = 'draw';
    private const DTLENGTH =  'length';
    private const DTSTART =  'start';
    private const DTSEARCH = 'search';
    private const DTORDER = 'order';
    private const DTVALUE = 'value';
    
    private $userID;

    public function __construct()
    {
        $this->usertoken = new UserToken();
        if (!$this->usertoken->checkIfUserIsLoggedin())
        {
            Util::redirect('index.php');
        }
        $this->usertoken->checkToken();
        
        
        
        $this->userID = $_SESSION['UserID'] ?? 0;
        $this->authorModel = $this->model('Author');  
        $this->privuser = new PrivilegedUser(DBCONFIG);
        $this->privuser->getPriUserByID($this->userID);
    }
    
    public function index()
    {
        if ($this->privuser->hasPrivileage('ShowAuthor'))
        {
            Util::redirect('Authors/showauthors');
        }
        else 
        {
            Util::redirect(self::ERROR);
        }
    }

    public function addauthor()
    {
        if (!$this->privuser->hasPrivileage('AddAuthor'))
        {
            Util::redirect(self::ERROR);
        }
        
        if (Util::isPOST())
        {
            $_post = Sanitize::cleanInputArray(INPUT_POST);
            
            Csrf::exitOnCsrfTokenFailure();
            
            $data =[
                self::AUTHORNAME => trim($_post[self::AUTHORNAME]),
                self::AUTHORNAME_ERR =>'',
                self::USERID => $this->userID,
                self::PRIVUSER => $this->privuser
            ];
            
            if (empty($data[self::AUTHORNAME]))
            {
                $data[self::AUTHORNAME_ERR] = 'Skriv in ett namn';
                $this->view(self::ADDAUTHOR,$data);
                exit();
            }
            
            if (empty($data[self::AUTHORNAME_ERR]))
            {
                $this->authorModel->addAutor($data);
                Util::flash('addauthor',$data[self::AUTHORNAME] .' har lagts till');
            }
            
            
            
            $this->view(self::ADDAUTHOR,$data);
        }
        else 
        {
            $data =[
                self::AUTHORNAME => '',
                self::AUTHORNAME_ERR => '',
                self::PRIVUSER => $this->privuser
            ];
            
            $this->view(self::ADDAUTHOR,$data);
        }
    }
    
    public function showauthors()
    {
        $data =[
            self::PRIVUSER => $this->privuser
        ];
        
        $this->view('Authors/showauthors',$data);
    }
    
    public function delauthor($id)
    {
        if (!$this->privuser->hasPrivileage('AddAuthor'))
        {
            Util::redirect(self::ERROR);
        }
        
        if (Util::isPOST())
        {
            Csrf::exitOnCsrfTokenFailure();
              
            $data =[
                'ID' => Sanitize::cleanInt($id),
                'UserID' => $this->userID
            ];
            
            $this->authorModel->delAutor($data);
            
            //$this->view('Authors/showauthors');
            Util::redirect('Authors/showauthors');
        }
    }
    
    public function editauthor($id)
    {
        if (!$this->privuser->hasPrivileage('AddAuthor'))
        {
            Util::redirect(self::ERROR);
        }
        
        if (Util::isPOST() && isset($_POST[self::AUTHORNAME])) 
        {
            Csrf::exitOnCsrfTokenFailure();
            
            $_post = Sanitize::cleanInputArray(INPUT_POST);
              
            $data = [
                'ID' => Sanitize::cleanInt($_post['AID']),
                'UserID' => $this->userID,
                 self::AUTHORNAME => $_POST[self::AUTHORNAME],
   
            ];
           
            if (empty($data[self::AUTHORNAME]))
            {
                $data[self::AUTHORNAME_ERR] = 'Skriv in ett namn';
                $this->view(self::UPDATEAUTHOR,$data);
                exit();
            }
            
            if (empty($data[self::AUTHORNAME_ERR]))
            {
                if ($this->authorModel->editAutor($data))
                {
                    Util::redirect('Authors/showauthors');
                }
            }
           
        }
        elseif (Util::isPOST())
        {
            Csrf::exitOnCsrfTokenFailure();
            $data =[
                'ID' => Sanitize::cleanInt($id),
                'UserID' => $this->userID,
                self::PRIVUSER => $this->privuser
            ];
           
            $da = $this->authorModel->getOneAuthor($data);
            
            $data[self::AUTHORNAME] = Sanitize::cleanString($da->Authorname);
             
            $this->view(self::UPDATEAUTHOR,$data);
        }
    }
    
    
    
    public function ajaxGetAuthorData()
    {
        if (Util::isPOST())
        {
            $_post = Sanitize::cleanInputArray(INPUT_POST);
            Csrf::exitOnCsrfTokenFailure();
            
            $data['UserID'] = (int)$this->userID;
            
            $rtn = false;
            if (isset($_post[self::DTDRAW])) 
            {
                $data[self::DTDRAW] = Sanitize::cleanInt($_post[self::DTDRAW]);
            }
            if (isset($_post[self::DTLENGTH])) {
                $data[self::DTLENGTH] = Sanitize::cleanInt($_post[self::DTLENGTH]);
            }
            if (isset($_post[self::DTSTART])) {
                $data[self::DTSTART] = Sanitize::cleanInt($_post[self::DTSTART]);
            }
            
            $extra = array();
            if (isset($_post[self::DTSEARCH]) && trim($_post[self::DTSEARCH][self::DTVALUE]) != '') 
            {
                $extra[self::DTSEARCH] = $_post[self::DTSEARCH][self::DTVALUE];
            }
            
            if (isset($_post[self::DTORDER]) && isset($_post[self::DTORDER][0])) 
            { // TODO: with shift click we can do more orders but we just force one
                $extra[self::DTORDER] = array($_post[self::DTORDER][0]['column'], $_post[self::DTORDER][0]['dir']);
            }
            
            if ( isset($data[self::DTDRAW]) && is_numeric($data[self::DTDRAW]) ) 
            {
                $rtn =  $this->authorModel->getAllAutors($data,$extra); 
            }
            
            echo Json::toJSON($rtn);
            
        }
    }
}