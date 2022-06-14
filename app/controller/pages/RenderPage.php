<?php

namespace app\controller\pages;

use \app\view\View;
use \app\model\database\Database;

class RenderPage
{

    /** Método responsável por renderizar o topo da página 
     * @param string $page
     * @return string
     */
    private static function getHeader($page)
    {
        $title['title'] = $page;
        $title = json_encode($title);
        return View::render('layout/header', '.html', json_decode($title));
    }

    /** Método responsável por renderizar o topo da página
     *  
     * @return string
     */
    private static function getFooter()
    {
        $footer['footer'] = 'footer';
        $footer = json_encode($footer);
        return View::render('layout/footer', '.html', json_decode($footer));
    }

    /** Método responsável por retornar o conteúdo (view) da página genérica
     * @param string $path
     * @param string $page
     * @param string $view
     * @param string $content
     * @param object $auth
     */
    public static function getPage($path, $page, $view, $content, $auth)
    {
        $view = ucfirst($view);

        $result = [
            'title' => ucfirst($view),
            'header' => self::getHeader($view),
            'footer' => self::getFooter($view),
            'content' => $content,
        ];


        $result = json_encode($result);
        return View::render($path . $page, '.html', json_decode($result));
    }
}
