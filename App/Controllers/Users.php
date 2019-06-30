<?php
use Simpleframework\Applib\Controller;
use Simpleframework\Helpers\Util;
use Simpleframework\Middleware\Csrf;
use Simpleframework\Middleware\Sanitize;
use Simpleframework\Middleware\Validate;
use Simpleframework\Email\Sendemail;
use Simpleframework\Middleware\UserToken;

util::startSession();

class Users extends Controller
{
    private const EMAIL = 'email';
    private const EMAIL_ERR = 'email_err';
    private const PASSWORD = 'password';
    private const PASSWORD_ERR = 'password_err';
    private const USERLOGIN = 'users/login';
    private const USERS = 'users';
    private const RESTORE = self::USERS .DS. 'restore';
    private const FORGOT = self::USERS .DS.' forgot';
    
    private const CONFIRMPASS = 'confirmpassword';
    private const CONFIRMPASS_ERR = 'confirmpassword_err';
    private const OLDPASSWORD = 'oldpassword';
    private const OLDPASSWORD_ERR = 'oldpassword_err';
    private const USERCHANGEPASS = 'users/changepass';
    
    private const PASS = 'pass';
    private const PASS_ERR = 'pass_err';
    
    public function __construct()
    {
        $this->userModel = $this->model('User');
        $this->email = new Sendemail(PORTALMAIL);
    }
    
    public function index()
    {
        if (isset($_SESSION['userlogin']) && $_SESSION['userlogin'])
        {
            $data = [
                
            ];
            $this->view('users/index',$data);
        }
        else 
        {
            if (Util::isPOST())
            {
                $_post = Sanitize::cleanInputArray(INPUT_POST);
                
                
                $data =[
                    self::EMAIL => trim($_post[self::EMAIL]),
                    self::PASSWORD => trim($_post[self::PASSWORD]),
                    self::EMAIL_ERR => '',
                    self::PASSWORD_ERR => '',
                ];
                
                if (empty($data[self::EMAIL]))
                {
                    $data[self::EMAIL_ERR] = 'Please enter email';
                }
                
                if (empty($data[self::PASSWORD]))
                {
                    $data[self::PASSWORD_ERR] = 'Please enter password';
                }
                
                if (empty($data['email_err']) && empty($data['password_err']))
                {
                    $loggedInuser = $this->userModel->login($data['email'],$data['password']);
                    
                    if ($loggedInuser)
                    {
                        $_SESSION['userlogin'] = true;
                        util::redirect('Dashboards/index');
                        
                    }
                    else
                    {
                        if (isset($_SESSION['userBlocked']) && $_SESSION['userBlocked'])
                        {
                            $error = 'Konto är blockat';
                        }
                        else
                        {
                            $error = 'Fel användare eller lösenord';
                        }
                        $data[self::PASSWORD_ERR] = $error;
                        $this->view(self::USERLOGIN,$data);
                    }
                }
                
                $this->view(self::USERLOGIN,$data);
            }
            else
            {
                $data =[
                    self::EMAIL => '',
                    self::PASSWORD => '',
                    self::EMAIL_ERR => '',
                    self::PASSWORD_ERR => '',
                ];
                
                $this->view(self::USERLOGIN,$data);
                
            }
            
        }
    }
    
    public function login()
    {
        
        if (Util::isPOST())
        {
            $_post = Sanitize::cleanInputArray(INPUT_POST);
            
            
            $data =[
                self::EMAIL => trim($_post[self::EMAIL]),
                self::PASSWORD => trim($_post[self::PASSWORD]),
                self::EMAIL_ERR => '',
                self::PASSWORD_ERR => '',
            ];
            
            if (empty($data[self::EMAIL]))
            {
                $data[self::EMAIL_ERR] = 'Please enter email';
            }
            
            if (empty($data[self::PASSWORD]))
            {
                $data[self::PASSWORD_ERR] = 'Please enter password';
            }
            
            if (empty($data['email_err']) && empty($data['password_err']))
            {
                $loggedInuser = $this->userModel->login($data['email'],$data['password']);
                
                if ($loggedInuser)
                {
                    $_SESSION['userlogin'] = true;
                    util::redirect('Dashboards/index');
      
                }
                else 
                {
                    if (isset($_SESSION['userBlocked']) && $_SESSION['userBlocked'])
                    {
                        $error = 'Konto är blockat';
                    }
                    else 
                    {
                        $error = 'Fel användare eller lösenord';
                    }
                    $data[self::PASSWORD_ERR] = $error;
                    $this->view(self::USERLOGIN,$data);
                }
            }
            
            $this->view(self::USERLOGIN,$data);
        }
        else
        {
            $data =[
                self::EMAIL => '',
                self::PASSWORD => '',
                self::EMAIL_ERR => '',
                self::PASSWORD_ERR => '',
            ];
            
            $this->view(self::USERLOGIN,$data);
            
        }
    }
    
    public function changepass()
    {
        $this->usertoken = new UserToken();
        if (!$this->usertoken->checkIfUserIsLoggedin())
        {
            Util::redirect('index.php');
        }
        
        if (Util::isPOST())
        {
            $_post = Sanitize::cleanInputArray(INPUT_POST);
            
            $data =[
                self::PASSWORD => trim($_post[self::PASSWORD]),
                self::CONFIRMPASS => trim($_post[self::CONFIRMPASS]),
                self::OLDPASSWORD => trim($_post[self::OLDPASSWORD]),
                self::PASSWORD_ERR => '',
                self::CONFIRMPASS_ERR => '',
                self::OLDPASSWORD_ERR => '',
            ];
            
            $error = false;
            if (empty($data[self::PASSWORD]))
            {
                $data[self::PASSWORD_ERR] = 'Kan inte vara tom';
                $error = true;
            }
            
            if (empty($data[self::CONFIRMPASS]))
            {
                $data[self::CONFIRMPASS_ERR] = 'Kan inte vara tom';
                $error = true;
            }
            
            if (empty($data[self::OLDPASSWORD]))
            {
                $data[self::OLDPASSWORD_ERR] = 'Kan inte vara tom';
                $error = true;
            }
            
            if ($error)
            {
                $this->view(self::USERCHANGEPASS,$data);
            }
            
            if ($data[self::PASSWORD] != $data[self::CONFIRMPASS])
            {
                $data[self::PASSWORD_ERR] = 'Lösenord och Bekräfta lösenord behöver vara lika';
                $data[self::CONFIRMPASS_ERR] = 'Lösenord och Bekräfta lösenord behöver vara lika';
                $this->view(self::USERCHANGEPASS,$data);
            }
            
            $userID = $_SESSION['UserID'] ?? 0;
            $username = $this->userModel->getUsernamrByID($userID);
            
            $verifiedPass = $this->userModel->verifiedPass($data[self::OLDPASSWORD], $username);
            
            if ($verifiedPass)
            {
                $this->userModel->changePassword($userID,$data[self::PASSWORD]);
                Util::flash('updatepass','Lösenordet är ändrat');
                
                $data =[
                    self::PASSWORD => '',
                    self::CONFIRMPASS => '',
                    self::OLDPASSWORD => '',
                    self::PASSWORD_ERR => '',
                    self::CONFIRMPASS_ERR => '',
                    self::OLDPASSWORD_ERR => '',
                ];
                
                $this->view(self::USERCHANGEPASS,$data);
            }
            
            $this->view(self::USERCHANGEPASS,$data);
         
        }
        else 
        {
            $data =[
                self::PASSWORD => '',
                self::CONFIRMPASS => '',
                self::OLDPASSWORD => '',
                self::PASSWORD_ERR => '',
                self::CONFIRMPASS_ERR => '',
                self::OLDPASSWORD_ERR => '',
            ];
            
            $this->view(self::USERCHANGEPASS,$data);
        }
    }
    
    public function forgot()
    {
        
        if (Util::isPOST())
        {
            $_post = Sanitize::cleanInputArray(INPUT_POST);
            
            $data = [
                self::EMAIL => $_post[self::EMAIL],
                self::EMAIL_ERR => ''
            ];
            
            if (empty($data[self::EMAIL]))
            {
                $data[self::EMAIL_ERR] = 'Kan inte vara tom';
                $this->view('users/forgot',$data);
                exit();
            }
            
            if (!Validate::validateEmail($data[self::EMAIL]))
            {
                $data[self::EMAIL_ERR] = 'Fel på ePost adress';
                $this->view('users/forgot',$data);
                exit();
            }
            
            if (empty($data['email_err']))
            {
                $this->email->setSubject('Lösenords länk');
                $this->email->addsendToAddress($data[self::EMAIL],'test');
                
              
                $this->userModel->insertRecovery($data[self::EMAIL]);
                $d = $this->userModel->getToken($data[self::EMAIL]);
                
                $token = $d->Token ?? 0;
                
                
                $link = URLROOT . self::RESTORE.DS.$token;
                
                $a = '<a href="'.$link . '"> Restore pass</a>';
                $this->email->messages($a);
                $this->email->send();
                Util::flash('forgotpass','Ett meddelande har skickats till det angivna e-postadressen');  
            }
            
        }
        else 
        {
            
            $data = [
                    self::EMAIL => '',
                    self::EMAIL_ERR => ''
       
            ];
            
            $this->view('users/forgot',$data);
        }
    }
    
    public function restore($token)
    {
        $recoverydata = $this->userModel->checkTokenForRecoveryPass($token);
        
        $recoveryid = $recoverydata->ID ?? 0;
        $userID = $recoverydata->UserID ?? 0;
        
        if (Util::isPOST() && $recoveryid != 0)
        {
            Csrf::exitOnCsrfTokenFailure();
            
            $_post = Sanitize::cleanInputArray(INPUT_POST);
            
            $data =[
                self::PASS => trim($_post[self::PASS]),
                self::CONFIRMPASS => trim($_post[self::CONFIRMPASS]),
                self::PASS_ERR => '',
                self::CONFIRMPASS_ERR => '',
                "token" => $token,
                'errmsg' => ''
            ];
            $error = false;
            if (empty($data[self::PASS]))
            {
                $data[self::PASS_ERR] = 'Kan inte vara tom';
                $error = true;
            }
            
            if (empty($data[self::CONFIRMPASS]))
            {
                $data[self::CONFIRMPASS_ERR] = 'Kan inte vara tom';
                $error = true;
            }
            
            if ($data[self::PASS] != $data[self::CONFIRMPASS])
            {
                $data[self::PASS_ERR] =  $data[self::CONFIRMPASS_ERR] = 'Lösenord och bekräfta lösenord ska vara lika';
                $error = true;
            }
            
            if ($error)
            {
                $this->view(self::RESTORE,$data);
            }
            
            
            $this->userModel->changePassword($userID,$data[self::PASS]);
            $this->userModel->deleteRecovery($token,$recoveryid);
            Util::flash('restorepass','Lösenordet har ändrats');
            
            
            
            $data =[
                self::PASS => '',
                self::CONFIRMPASS => '',
                self::PASS_ERR => '',
                self::CONFIRMPASS_ERR => '',
                "token" => $token,
                'errmsg' => ''
            ];
            
            $this->view(self::RESTORE,$data);
            
            
        }
        elseif ($recoveryid != 0)
        {
            
            if (Validate::regex('/^[a-z0-9]+$/', $token) === 0)
            {
                Util::redirect("Errors".DS."index");
            }
            
            $data =[
                self::PASS => '',
                self::CONFIRMPASS => '',
                self::PASS_ERR => '',
                self::CONFIRMPASS_ERR => '',
                "token" => $token,
                'errmsg' => ''
            ];
            
            $this->view(self::RESTORE,$data);
        }
        
        else
        {
            $data =[
                
                'errmsg' => 'Token är ogiltig eller har upphört',
            ];
            $this->view(self::RESTORE,$data);
        }
    }
    
    
    
    
    public function logout()
    {
        unset($_SESSION['userlogin']);
        $_SESSION = array();
        session_destroy();
        Util::redirect(self::USERLOGIN);
    }
    
   
    
}