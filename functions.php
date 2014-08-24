<?php
class Session
{
    const SESSION_STARTED = TRUE;
    const SESSION_NOT_STARTED = FALSE;

    private $sessionState = self::SESSION_NOT_STARTED;

    private static $instance;

    private function __construct() {}

    public static function getInstance()
    {
        if ( !isset(self::$instance))
        {
            self::$instance = new self;
        }

        self::$instance->startSession();

        return self::$instance;
    }

    public function getUrl($type){
        $url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
        if(!$type) $url .= $_SERVER["REQUEST_URI"];
        return $url;
    }

    public function startSession()
    {
        if ( $this->sessionState == self::SESSION_NOT_STARTED )
        {
            $this->sessionState = session_start();
        }

        return $this->sessionState;
    }

    public function cart( $name , $value){

        if(!is_array($_SESSION[$name])) $_SESSION[$name] = array('cartData' => array(), 'cartValue' => 0);

        if($value['type']=='add'){
            $_SESSION[$name]['cartData'][] = $value;
        }
        elseif($value['type']=='remove' and is_numeric($value['itemNum'])){
            unset($_SESSION[$name]['cartData'][$value['itemNum']]);
        }

        $_SESSION[$name]['cartValue'] = 0;

        foreach($_SESSION[$name]['cartData'] as $basket){
            $_SESSION[$name]['cartValue'] += number_format($basket['value'], 2, '.', '');
        }
    }

    public function __set( $name , $value )
    {
        $_SESSION[$name] = $value;
    }

    public function __get( $name )
    {
        if ( isset($_SESSION[$name]))
        {
            return $_SESSION[$name];
        }
    }

    public function __isset( $name )
    {
        return isset($_SESSION[$name]);
    }

    public function __unset( $name )
    {
        unset( $_SESSION[$name] );
    }

    public function destroy()
    {
        if ( $this->sessionState == self::SESSION_STARTED )
        {
            $this->sessionState = !session_destroy();
            unset( $_SESSION );

            return !$this->sessionState;
        }

        return FALSE;
    }
}
