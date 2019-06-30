<?php

use Simpleframework\Applib\Controller;
use Simpleframework\Helpers\Util;
use Simpleframework\Helpers\Json;
use Simpleframework\Middleware\Csrf;
use Simpleframework\Middleware\Sanitize;
use Simpleframework\RABC\PrivilegedUser;
use Simpleframework\Middleware\UserToken;

util::startSession();

class Series extends Controller
{
    private const SERIENAME = 'seriename';
    private const SERIENAME_ERR = 'seriename_err';
    private const USERID = 'UserID';
    
    private const ERROR = 'errors/error';
    private const ADDSERIE = 'series/addserie';
    private const UPDATESERIE = 'series/editserie';
    private const SHOWSERIE = 'series/showserie';
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
        $this->serieModel = $this->model('Serie');
        $this->privuser = new PrivilegedUser(DBCONFIG);
        $this->privuser->getPriUserByID($this->userID);
    }
    
    public function index()
    {
        if ($this->privuser->hasPrivileage('ShowSerie'))
        {
            Util::redirect(self::SHOWSERIE);
        }
        else
        {
            Util::redirect(self::ERROR);
        }
    }
    
    public function addserie()
    {
 
        if (!$this->privuser->hasPrivileage('AddSerie'))
        {
            Util::redirect(self::ERROR);
        }
        
      
        if (Util::isPOST())
        {
            $_post = Sanitize::cleanInputArray(INPUT_POST);
            
            Csrf::exitOnCsrfTokenFailure();
            
            $data =[
                self::SERIENAME => trim($_post[self::SERIENAME]),
                self::SERIENAME_ERR =>'',
                self::USERID => $this->userID,
                self::PRIVUSER => $this->privuser
            ];
            
            if (empty($data[self::SERIENAME]))
            {
                $data[self::SERIENAME_ERR] = 'Skriv in ett namn';
                $this->view(self::ADDSERIE,$data);
                exit();
            }
            
            if (empty($data[self::SERIENAME_ERR]))
            {
                $this->serieModel->addSerie($data);
                Util::flash('addserie',$data[self::SERIENAME] .' har lagts till');
            }
            
            $this->view(self::ADDSERIE,$data);
        }
        else
        {
            $data =[
                self::SERIENAME => '',
                self::SERIENAME_ERR => '',
                self::PRIVUSER => $this->privuser
            ];
            
            $this->view(self::ADDSERIE,$data);
        }
    }
    
    public function showserie()
    {
        $data =[
            self::PRIVUSER => $this->privuser
        ];
        
        $this->view(SELF::SHOWSERIE,$data);
    }
    
    public function delserie($id)
    {
        if (!$this->privuser->hasPrivileage('AddSerie'))
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
            
            $this->serieModel->delserie($data);
            
            Util::redirect(self::SHOWSERIE);
        }
    }
    
    public function editserie($id)
    {
        if (!$this->privuser->hasPrivileage('AddSerie'))
        {
            Util::redirect(self::ERROR);
        }
        
        if (Util::isPOST() && isset($_POST[self::SERIENAME]))
        {
            Csrf::exitOnCsrfTokenFailure();
            
            $_post = Sanitize::cleanInputArray(INPUT_POST);
            
            $data = [
                'ID' => Sanitize::cleanInt($_post['ID']),
                'UserID' => $this->userID,
                self::SERIENAME => $_POST[self::SERIENAME]
            ];
            
            if (empty($data[self::SERIENAME]))
            {
                $data[self::SERIENAME_ERR] = 'Skriv in ett namn';
                $this->view(self::UPDATESERIE,$data);
                exit();
            }
            
            if (empty($data[self::SERIENAME_ERR]))
            {
                if ($this->serieModel->editSerie($data))
                {
                    Util::redirect(self::SHOWSERIE);
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
            
            $da = $this->serieModel->getOneSerie($data);
            
            $data[self::SERIENAME] = Sanitize::cleanString($da->seriename);
            
            $this->view(self::UPDATESERIE,$data);
        }
    }
    
    public function ajaxGetSerieData()
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
                $rtn = $this->serieModel->getAllSerie($data,$extra);
            }
            
            echo Json::toJSON($rtn);
            
        }
    }
}