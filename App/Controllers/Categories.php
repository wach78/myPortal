<?php
use Simpleframework\Applib\Controller;
use Simpleframework\Helpers\Util;
use Simpleframework\Helpers\Json;
use Simpleframework\Middleware\Csrf;
use Simpleframework\Middleware\Sanitize;
use Simpleframework\RABC\PrivilegedUser;
use Simpleframework\Middleware\UserToken;

Util::startSession();


class Categories extends Controller
{
    private const CATEGORIESNAME = 'categoriesname';
    private const CATEGORIESNAME_ERR = 'categories_err';
    private const USERID = 'UserID';
    
    private const ERROR = 'errors/error';
    private const ADDCATEGORIES = 'categories/addcategory';
    private const UPDATECATEGORIES = 'categories/editcategory';
    private const SHOWCATEGORIES = 'categories/showcategory';
    private const PRIVUSER = 'privuser';
    
    //datatables
    private const DTDRAW = 'draw';
    private const DTLENGTH = 'length';
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
        $this->categoryModel = $this->model('Categorie');
        $this->privuser = new PrivilegedUser(DBCONFIG);
        $this->privuser->getPriUserByID($this->userID);
    }
    
    public function index()
    {
        if ($this->privuser->hasPrivileage('ShowCategories'))
        {
            Util::redirect(self::SHOWCATEGORIES);
        }
        else
        {
            Util::redirect(self::ERROR);
        }
    }
    
    public function addcategory()
    {
        
        if (!$this->privuser->hasPrivileage('AddCategories'))
        {
            Util::redirect(self::ERROR);
        }
        
        
        if (Util::isPOST())
        {
            $_post = Sanitize::cleanInputArray(INPUT_POST);
            
            Csrf::exitOnCsrfTokenFailure();
            
            $data =[
                self::CATEGORIESNAME => trim($_post[self::CATEGORIESNAME]),
                self::CATEGORIESNAME_ERR =>'',
                self::USERID => $this->userID,
                self::PRIVUSER => $this->privuser
            ];
            
            if (empty($data[self::CATEGORIESNAME]))
            {
                $data[self::CATEGORIESNAME_ERR] = 'Skriv in ett namn';
                $this->view(self::ADDCATEGORIES,$data);
                exit();
            }
            
            if (empty($data[self::CATEGORIESNAME_ERR]))
            {
                $this->categoryModel->addCategory($data);
                Util::flash('addcategory',$data[self::CATEGORIESNAME] .' har lagts till');
            }
            
            $this->view(self::ADDCATEGORIES,$data);
        }
        else
        {
            $data =[
                self::CATEGORIESNAME => '',
                self::CATEGORIESNAME_ERR => '',
                self::PRIVUSER => $this->privuser
            ];
            
            $this->view(self::ADDCATEGORIES,$data);
        }
    }
    
    
    public function showcategory()
    {
        $data =[
            self::PRIVUSER => $this->privuser
        ];
        
        $this->view(SELF::SHOWCATEGORIES,$data);
    }
    
    public function delcategory($id)
    {
        if (!$this->privuser->hasPrivileage('AddCategories'))
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
            
            $this->categoryModel->delcategory($data);
            
            Util::redirect(self::SHOWCATEGORIES);
        }
    }
    
    public function editcategory($id)
    {
        if (!$this->privuser->hasPrivileage('AddCategories'))
        {
            Util::redirect(self::ERROR);
        }
        
        if (Util::isPOST() && isset($_POST[self::CATEGORIESNAME]))
        {
            Csrf::exitOnCsrfTokenFailure();
            
            $_post = Sanitize::cleanInputArray(INPUT_POST);
            
            $data = [
                'ID' => Sanitize::cleanInt($_post['ID']),
                'UserID' => $this->userID,
                self::CATEGORIESNAME => $_POST[self::CATEGORIESNAME]
            ];
            
            if (empty($data[self::CATEGORIESNAME]))
            {
                $data[self::CATEGORIESNAME_ERR] = 'Skriv in ett namn';
                $this->view(self::UPDATECATEGORIES,$data);
                exit();
            }
            
            if (empty($data[self::CATEGORIESNAME_ERR]))
            {
                if ($this->categoryModel->editCategory($data))
                {
                    Util::redirect(self::SHOWCATEGORIES);
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
            
            $da = $this->categoryModel->getOneCategory($data);
            
            $data[self::CATEGORIESNAME] = Sanitize::cleanString($da->Categoriesname);
            
            $this->view(self::UPDATECATEGORIES,$data);
        }
    }
    
    public function ajaxGetCategoryData()
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
                $rtn = $this->categoryModel->getAllCategory($data,$extra);
            }
            
            echo Json::toJSON($rtn);
            
        }
    }
}