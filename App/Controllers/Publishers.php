<?php
use Simpleframework\Applib\Controller;
use Simpleframework\Helpers\Util;
use Simpleframework\Helpers\Json;
use Simpleframework\Middleware\Csrf;
use Simpleframework\Middleware\Sanitize;
use Simpleframework\RABC\PrivilegedUser;
use Simpleframework\Middleware\UserToken;

Util::startSession();

class Publishers extends Controller
{
    private const PUBLISHERNAME = 'publishername';
    private const PUBLISHERNAME_ERR = 'publishername_err';
    private const USERID = 'UserID';
    
    private const ERROR = 'errors/error';
    private const ADDPUBLISHER = 'publishers/addpublisher';
    private const UPDATEPUBLISHER = 'publishers/editpublisher';
    private const SHOWPUBLISHER = 'publishers/showpublisher';
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
        
        $this->userID = $_SESSION['UserID'] ?? 0;
        $this->publisherModel = $this->model('Publisher');
        $this->privuser = new PrivilegedUser(DBCONFIG);
        $this->privuser->getPriUserByID($this->userID);
    }
    
    public function index()
    {
        if ($this->privuser->hasPrivileage('ShowPublisher'))
        {
            Util::redirect(self::SHOWPUBLISHER);
        }
        else
        {
            Util::redirect(self::ERROR);
        }
    }
    
    public function addpublisher()
    {
        if (!$this->privuser->hasPrivileage('AddPublisher'))
        {
            Util::redirect(self::ERROR);
        }
        
        if (Util::isPOST())
        {
            $_post = Sanitize::cleanInputArray(INPUT_POST);
            
            Csrf::exitOnCsrfTokenFailure();
            
            $data =[
                self::PUBLISHERNAME => trim($_post[self::PUBLISHERNAME]),
                self::PUBLISHERNAME_ERR =>'',
                self::USERID => $this->userID,
                self::PRIVUSER => $this->privuser
            ];
            
            if (empty($data[self::PUBLISHERNAME]))
            {
                $data[self::PUBLISHERNAME_ERR] = 'Skriv in ett namn';
                $this->view(self::ADDPUBLISHER,$data);
                exit();
            }
            
            if (empty($data[self::PUBLISHERNAME_ERR]))
            {
                $this->publisherModel->addPublisher($data);
                Util::flash('addpublisher',$data[self::PUBLISHERNAME] .' har lagts till');
            }
            
            
            
            $this->view(self::ADDPUBLISHER,$data);
        }
        else
        {
            $data =[
                self::PUBLISHERNAME => '',
                self::PUBLISHERNAME_ERR => '',
                self::PRIVUSER => $this->privuser
            ];
            
            $this->view(self::ADDPUBLISHER,$data);
        }
    }
    
    public function showpublisher()
    {
        $data =[
            self::PRIVUSER => $this->privuser
        ];
        
        $this->view(SELF::SHOWPUBLISHER,$data);
    }
    
    public function delpublisher($id)
    {
        if (!$this->privuser->hasPrivileage('AddPublisher'))
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
            
            $this->publisherModel->delPublisher($data);
            
            Util::redirect(self::SHOWPUBLISHER);
        }
    }
    
    public function editpublisher($id)
    {
        if (!$this->privuser->hasPrivileage('AddPublisher'))
        {
            Util::redirect(self::ERROR);
        }
        
        if (Util::isPOST() && isset($_POST[self::PUBLISHERNAME]))
        {
            Csrf::exitOnCsrfTokenFailure();
            
            $_post = Sanitize::cleanInputArray(INPUT_POST);
            
            $data = [
                'ID' => Sanitize::cleanInt($_post['ID']),
                'UserID' => $this->userID,
                self::PUBLISHERNAME => $_POST[self::PUBLISHERNAME]
            ];
            
            if (empty($data[self::PUBLISHERNAME]))
            {
                $data[self::PUBLISHERNAME_ERR] = 'Skriv in ett namn';
                $this->view(self::UPDATEPUBLISHER,$data);
                exit();
            }
            
            if (empty($data[self::PUBLISHERNAME_ERR]))
            {
                if ($this->publisherModel->editPublisher($data))
                {
                    Util::redirect(self::SHOWPUBLISHER);
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
            
            $da = $this->publisherModel->getOnePublisher($data);
            
            $data[self::PUBLISHERNAME] = Sanitize::cleanString($da->Publishername);
            
            $this->view(self::UPDATEPUBLISHER,$data);
        }
    }
    
    public function ajaxGetPublisherData()
    {
        if (Util::isPOST())
        {
            $_post = Sanitize::cleanInputArray(INPUT_POST);
            Csrf::exitOnCsrfTokenFailure();
            
            $data['UserID'] = $this->userID;
            
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
                $rtn = $this->publisherModel->getAllPublisher($data,$extra);
            }
            
            echo Json::toJSON($rtn);
            
        }
    }
    
    
    
}