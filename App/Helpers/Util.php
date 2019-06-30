<?php
namespace Simpleframework\Helpers;

class Util
{
    
    public static function redirect($page)
    {
        $str = URLROOT .$page;
        $url = filter_var($str,FILTER_SANITIZE_URL);
        header('location: '.$url);
        exit();
    }
    
    public static function isPOST()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }
    
    public static function isGET()
    {
       return  $_SERVER['REQUEST_METHOD'] == 'GET';
    }
    
    
    
    public static function flash($name = '', $message = '', $class = 'alert alert-success')
    {
        if (!empty($name))
        {
            if (!empty($message) && empty($_SESSION[$name]))
            {
                if (!empty($_SESSION[$name]))
                {
                    unset($_SESSION[$name]);
                }
                
                if (!empty($_SESSION[$name .'_class']))
                {
                    unset($_SESSION[$name.'_class']);
                }
                
                $_SESSION[$name] = $message;
                $_SESSION[$name.'_class'] = $class;
            }
            elseif(empty($message) && !empty($_SESSION[$name]))
            {
                $class = !empty($_SESSION[$name.'_class']) ? $_SESSION[$name.'_class'] : '';
                echo '<div class="'.$class.'" id="msg-flash">'. $_SESSION[$name].'</div>';
                
                unset($_SESSION[$name]);
                unset($_SESSION[$name.'_class']);
            }
        }
    }
    
    private static function sessionOptions()
    {
        return [
            /*'read_and_close' => true,*/
            'sid_length' =>32,
            'sid_bits_per_character' => 6,
            'cookie_httponly' => true,
            'use_only_cookies' => true,
            'use_trans_sid' => false,
            'cookie_lifetime' => 0,
            'use_strict_mode' => true
        ];
    }
    
    public static function startSession()
    {
        @session_start(self::sessionOptions());
    }
    
    
    
}