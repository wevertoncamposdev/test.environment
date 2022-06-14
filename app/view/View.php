<?php

namespace app\view;

class View
{
    private static $vars = [];

    /** Método que recebe as váriaveis da URL 
     * @param array $vars
     */
    public static function init($vars = [])
    {

        self::$vars = $vars;
    }

    public static function access($vars)
    {

        $acess = "Access";
        return $vars . $acess;
    }

    /** Método responsável por buscar o arquivo que contém 
     * o conteúdo da view.
     * @param string $view
     * @return string
     */
    private static function getContentView($view, $type)
    {
        $file = __DIR__ . '/../../resources/view/' . $view . $type;

        try{

            if(!file_exists($file)){
                throw new \Exception('File does not exist <a href="http://localhost:82">Return</a>');
            }

            return file_get_contents($file);

        }catch(\Exception $e){
            die($e->getMessage());
        }
        
    }

    /** Método responsável por rederizar o conteúdo 
     * exitente no arquivo encontrado por @getContentView().
     * @param string $view
     * @param string $type
     * @param object $var
     * @return string
     */
    public static function render($view, $type, $var)
    {

        //CONTEÚDO DA VIEW
        $view = strtolower($view);

        $contentView = self::getContentView($view, $type);
       
        //TRATANDO CHAVES PARA ENVIAR PARA VIEW
        $vars = get_object_vars($var);
        $vars = array_merge(self::$vars, $vars);
        $keys = array_keys($vars);
        $keys = array_map(function ($item) {
            return '{{' . $item . '}}';
        }, $keys);
        
        return str_replace($keys, array_values($vars), $contentView);
    }

    /** Método responsável por rederizar o conteúdo 
     * exitente no arquivo encontrado por @getContentView().
     * @param string $view
     * @param string $type
     * @param object $auth
     * @return string
     */
    public static function renderTable($view, $type, $auth)
    {

        //CONTEÚDO DA VIEW
        $view = strtolower($view);
        $contentView = self::getContentView($view, $type);


        //TRATANDO CHAVES PARA ENVIAR PARA VIEW
        
        
        
        $vars = array_merge(self::$vars, $auth[0]);
        
        $keys = array_keys($vars);
        $keys = array_map(function ($item) {
            return '{{' . $item . '}}';
        }, $keys);
        
        return str_replace($keys, array_values($vars), $contentView);
    }

    public static function renderArray($view, $type, $vars = [])
    {

        //CONTEÚDO DA VIEW  
        $contentView = self::getContentView($view, $type);

        $keys = array_keys($vars);

        $keys = array_map(function ($item) {
            return '{{' . $item . '}}';
        }, $keys);


        return str_replace($keys, array_values($vars), $contentView);
    }
}
