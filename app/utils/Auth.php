<?php

namespace app\utils;


class Auth
{
    /** Método responsável por iniciar a sessão 
     * @param object $user
     * @return boolean
     */
    public static function initSession($user)
    {   
        
        if (session_start()) {
            
            $_SESSION['session_id'] = session_id();
            $_SESSION['name'] = $user->name;
            $_SESSION['email'] = $user->email;
            $_SESSION['type'] = $user->type;
            $_SESSION['logged'] = true ?? False;

            return true;

        } else {

            return false;
        }
    }


    /** Método responsável por iniciar a sessão 
     * @param 
     * @return object $result
    */
    public static function Validate()
    {      
        session_start(); 

        if (isset($_SESSION['session_id'])) {

            $result = json_encode(
                [
                    'session_id' => $_SESSION['session_id'],
                    'name' => $_SESSION['name'],
                    'email' => $_SESSION['email'],
                    'type' => $_SESSION['type'],
                    'status' => $_SESSION['logged'],
                ]
            );

            return json_decode($result);

        } else {

            $result['status'] = false;
            $result['session_id'] = false;
            $result = json_encode($result);
            return json_decode($result);
        }
    }

    /** Método responsável por iniciar a sessão 
     * 
     * @return string $result
     */
    public static function Logout()
    {
        session_start();
        session_unset();

        if (session_destroy()) {
            $result['status'] = false;
            $result['message'] = "Sua sessão foi finalizada com sucesso!";
            $result = json_encode($result);
            return json_decode($result);

        } else {
            $result['status'] = true;
            $result['message'] = "A sessão não foi finalizada!";
            $result = json_encode($result);
            return json_decode($result);
        }
    }
}
