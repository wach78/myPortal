<?php

use Simpleframework\Applib\Controller;
use Simpleframework\Helpers\Util;
use Simpleframework\Helpers\Json;
use Simpleframework\Middleware\Csrf;
use Simpleframework\Middleware\Sanitize;
use Simpleframework\RABC\PrivilegedUser;
use Simpleframework\Middleware\Validate;
use Simpleframework\Middleware\UserToken;

util::startSession();

class Books extends Controller
{
    private const BOOKNAME = 'bookname';
    private const BOOKNAME_ERR = 'book_err';
    private const SERIEID = 'serieID';
    private const SERIEID_ERR = 'serieID_err';
    private const CATEGORYID = 'categoryID'; 
    private const CATEGORYID_ERR = 'categoryID_err';
    private const PUBLISHERID = 'publiserID';
    private const PUBLISHERID_ERR = 'publiserID_err';
    private const PAGES = 'pages';  
    private const PAGES_ERR = 'pages_err'; 
    private const ISBN = 'ISBN';
    private const ISBN_ERR = 'ISBN_err';
    private const DESCRIPTION = 'description';
    private const DESCRIPTION_ERR = 'description_err';
    private const HAVEREAD = 'haveread';
    private const HAVEREAD_ERR = 'haveread_err';
    private const SELECTPICKERAUTHORS= "selectpickerauthors";
    
    private const SELECTPICKERSERIE = 'selectpickerserie';
    private const SELECTPICKERCATEGORY = 'selectpickercategorie';
    private const SELECTPICKERPUBLISHER = 'selectpickerpubliser';
    
    private const PRIVUSER = 'privuser';

    
    private const USERID = 'UserID';
    
    private const AUTHORSDATA = 'authorsdata';
    private const CATEGORIEDATA = 'categoriedata';
    private const SERIEDATA = 'seriedata';
    private const PUBLUSHERDATA = 'publiserdata';
    private const BOOKHAVEAUTORS = 'bookhaveautors';

    private const ERROR = 'errors/error';
    private const ADDBOOK = 'books/addbook';
    private const UPDATEBOOK = 'books/editbook';
    private const SHOWBOOK = 'books/showbooks';
    
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
        
       
        
        $this->userID = (int)$_SESSION['UserID'] ?? 0;
        $this->bookModel = $this->model('Book');
        $this->categoryModel = $this->model('Categorie');
        $this->publisherModel = $this->model('Publisher');
        $this->authorModel = $this->model('Author');
        $this->serieModel = $this->model('Serie');
        
        
        $this->privuser = new PrivilegedUser(DBCONFIG);
        $this->privuser->getPriUserByID($this->userID);
    }
    
    public function index()
    {
        
        if ($this->privuser->hasPrivileage('ShowBooks'))
        {
            Util::redirect(self::SHOWBOOK);
        }
        else
        {
            Util::redirect(self::ERROR);
        }
        
    }
    
    public function addbook()
    {
        
        if (!$this->privuser->hasPrivileage('AddBooks'))
        {
            Util::redirect(self::ERROR);
        }
        
        
        if (Util::isPOST())
        {
            $_post = Sanitize::cleanInputArray(INPUT_POST);
            
            Csrf::exitOnCsrfTokenFailure();
            
            //var_dump($_post);
            
            $data =[
                self::BOOKNAME => trim($_post[self::BOOKNAME]),
                self::SERIEID => (int)$_post[self::SELECTPICKERSERIE],
                self::SELECTPICKERAUTHORS => $_post[self::SELECTPICKERAUTHORS],
                self::CATEGORYID => (int)$_post[self::SELECTPICKERCATEGORY],
                self::PUBLISHERID => (int)$_post[self::SELECTPICKERPUBLISHER],
                self::PAGES => trim($_post[self::PAGES]),
                self::ISBN => trim($_post[self::ISBN]),
                self::DESCRIPTION => trim($_post[self::DESCRIPTION]),
                self::HAVEREAD => trim($_post[self::HAVEREAD]),
                self::BOOKNAME_ERR => '',
                self::SERIEID_ERR => '',
                self::CATEGORYID_ERR => '',
                self::PUBLISHERID_ERR => '',
                self::PAGES_ERR => '',
                self::ISBN_ERR => '',
                self::DESCRIPTION_ERR,
                self::HAVEREAD_ERR,
                self::USERID => (int)$this->userID, 
                self::AUTHORSDATA => $this->authorModel->getALLAutorsByUserID($this->userID),
                self::CATEGORIEDATA => $this->categoryModel->gettALLCategoryByUserID($this->userID),
                self::SERIEDATA => $this->serieModel-> getAllSerieByUserID($this->userID),
                self::PUBLUSHERDATA => $this->publisherModel->getAllPublisherByUserID($this->userID),
                self::PRIVUSER => $this->privuser
            ];
            
            
            
            if (empty($data[self::BOOKNAME]))
            {
                $data[self::BOOKNAME_ERR] = 'Skriv in ett namn';
                $this->view(self::ADDBOOK,$data);
                exit();
            }
            
           if (!Validate::validateInt($data[self::PAGES]))
           {
               $data[self::PAGES_ERR] = 'Behöver vara ett heltal';
               $this->view(self::ADDBOOK,$data);
               exit();
           }
            
            
           if (empty($data[self::BOOKNAME_ERR]) && empty($data[self::PAGES_ERR]))
           {
                $data[self::PAGES] = (int)$data[self::PAGES];
                
                
                $bookID  = $this->bookModel->addBook($data);
              
                if ($bookID != 0)
                {
                    $len = count($data[self::SELECTPICKERAUTHORS]);
                    $authors = $data[self::SELECTPICKERAUTHORS];
                    
                    for ($i = 0; $i < $len; $i++)
                    {
                        $d = [
                            self::USERID =>  $data[self::USERID],
                            'bookID' => $bookID,
                            'authorID' => $authors[$i]
                        ];
                        
                        $this->bookModel->addbooksautors($d);
                    }

                
                    Util::flash('addbook',$data[self::BOOKNAME] .' har lagts till');
                }
            }
            
            $this->view(self::ADDBOOK,$data);
        }
        else
        {
            $data =[
                self::BOOKNAME => '',
                self::SERIEID => '',
                self::CATEGORYID => '',
                self::PUBLISHERID => '',
                self::PAGES => '',
                self::ISBN => '',
                self::DESCRIPTION => '',
                self::HAVEREAD => 0,
                self::BOOKNAME_ERR => '',
                self::SERIEID_ERR => '',
                self::CATEGORYID_ERR => '',
                self::PUBLISHERID_ERR => '',
                self::PAGES_ERR => '',
                self::ISBN_ERR => '',
                self::DESCRIPTION_ERR,
                self::HAVEREAD_ERR,
                self::AUTHORSDATA => $this->authorModel->getALLAutorsByUserID($this->userID),
                self::CATEGORIEDATA => $this->categoryModel->gettALLCategoryByUserID($this->userID ),
                self::SERIEDATA => $this->serieModel-> getAllSerieByUserID($this->userID ),
                self::PUBLUSHERDATA => $this->publisherModel->getAllPublisherByUserID($this->userID ),
                self::PRIVUSER => $this->privuser
                
            ];

       
            
            $this->view(self::ADDBOOK,$data);
        }
    }
    
    public function editbook($id)
    {
        if (!$this->privuser->hasPrivileage('AddBooks'))
        {
            Util::redirect(self::ERROR);
        }
        
        if (Util::isPOST() && isset($_POST[self::BOOKNAME]))
        {
            Csrf::exitOnCsrfTokenFailure();
            $_post = Sanitize::cleanInputArray(INPUT_POST);
            $id = (int)Sanitize::cleanInt($id);
            $d = [
                'ID' => $id,
                self::USERID => $this->userID
            ];

            $data =[
                self::BOOKNAME => trim($_post[self::BOOKNAME]),
                self::SERIEID => (int)$_post[self::SELECTPICKERSERIE],
                self::SELECTPICKERAUTHORS => $_post[self::SELECTPICKERAUTHORS],
                self::CATEGORYID => (int)$_post[self::SELECTPICKERCATEGORY],
                self::PUBLISHERID => (int)$_post[self::SELECTPICKERPUBLISHER],
                self::PAGES => trim($_post[self::PAGES]),
                self::ISBN => trim($_post[self::ISBN]),
                self::DESCRIPTION => trim($_post[self::DESCRIPTION]),
                self::HAVEREAD => trim($_post[self::HAVEREAD]),
                self::BOOKNAME_ERR => '',
                self::SERIEID_ERR => '',
                self::CATEGORYID_ERR => '',
                self::PUBLISHERID_ERR => '',
                self::PAGES_ERR => '',
                self::ISBN_ERR => '',
                self::DESCRIPTION_ERR,
                self::HAVEREAD_ERR,
                self::USERID => (int)$this->userID,
                self::BOOKHAVEAUTORS => $this->bookModel->getBookAutors($d),
                self::AUTHORSDATA => $this->authorModel->getALLAutorsByUserID($this->userID),
                self::CATEGORIEDATA => $this->categoryModel->gettALLCategoryByUserID($this->userID),
                self::SERIEDATA => $this->serieModel-> getAllSerieByUserID($this->userID),
                self::PUBLUSHERDATA => $this->publisherModel->getAllPublisherByUserID($this->userID),
                self::PRIVUSER => $this->privuser,
                'ID' => $id
            ];
            
            if (empty($data[self::BOOKNAME]))
            {
                $data[self::BOOKNAME_ERR] = 'Skriv in ett namn';
                $this->view(self::UPDATEBOOK,$data);
                exit();
            }
            
            if (!Validate::validateInt($data[self::PAGES]))
            {
                $data[self::PAGES_ERR] = 'Behöver vara ett heltal';
                $this->view(self::UPDATEBOOK,$data);
                exit();
            }
            
            
            $this->view(self::UPDATEBOOK,$data);
            
        }
        elseif (Util::isPOST())
        {
            Csrf::exitOnCsrfTokenFailure();
            $_post = Sanitize::cleanInputArray(INPUT_POST);
            
         
            $id = (int)Sanitize::cleanInt($id);
            $d = [
                'ID' => $id,
                self::USERID => $this->userID
            ];
            $bokdata = $this->bookModel->getOneBook($d);
   

            $data =[
                self::BOOKNAME => $bokdata->Bookname,
                self::SERIEID => $bokdata->SerieID,
                self::CATEGORYID => $bokdata->CategoryID,
                self::PUBLISHERID => $bokdata->PublisherID,
                self::PAGES => $bokdata->Pages,
                self::ISBN => $bokdata->ISBN,
                self::DESCRIPTION => $bokdata->Description,
                self::HAVEREAD => $bokdata->Haveread,
                self::BOOKNAME_ERR => '',
                self::SERIEID_ERR => '',
                self::CATEGORYID_ERR => '',
                self::PUBLISHERID_ERR => '',
                self::PAGES_ERR => '',
                self::ISBN_ERR => '',
                self::DESCRIPTION_ERR,
                self::HAVEREAD_ERR,
                self::BOOKHAVEAUTORS => $this->bookModel->getBookAutors($d),
                self::AUTHORSDATA => $this->authorModel->getALLAutorsByUserID($this->userID),
                self::CATEGORIEDATA => $this->categoryModel->gettALLCategoryByUserID($this->userID ),
                self::SERIEDATA => $this->serieModel-> getAllSerieByUserID($this->userID ),
                self::PUBLUSHERDATA => $this->publisherModel->getAllPublisherByUserID($this->userID ),
                self::PRIVUSER => $this->privuser,
                'ID' => $id
              ];
            
            $this->view(self::UPDATEBOOK,$data);
            
        }
        
        
        
    }
    
    
    public function showbooks()
    {
        $data =[
            self::PRIVUSER => $this->privuser
        ];
        
        $this->view(SELF::SHOWBOOK,$data);
    }
    
    public function delbook($id)
    {
        if (!$this->privuser->hasPrivileage('AddBooks'))
        {
            Util::redirect(self::ERROR);
        }
       
        if (Util::isPOST())
        {
            Csrf::exitOnCsrfTokenFailure();
            
            $data =[
                'ID' => Sanitize::cleanInt($id),
                'UserID' => (int)$this->userID
            ];
            
            $this->bookModel->delbooksautors($data);
            $this->bookModel->delbook($data);
            
            Util::redirect(self::SHOWBOOK);
        }
    }
    
    public function ajaxGetBooksData()
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
            {
                $extra[self::DTORDER] = array($_post[self::DTORDER][0]['column'], $_post[self::DTORDER][0]['dir']);
            
            }
            
            if ( isset($data[self::DTDRAW]) && is_numeric($data[self::DTDRAW]) )
            {
                $rtn = $this->bookModel->getAllBooks($data,$extra);
            }
            
            echo Json::toJSON($rtn);
            
        }
    }

}