<?php

use \app\http\Response;
use \app\utils\Auth;
use \app\controller\pages\GetPage;



/* **************************************************************** AUTENTICAÇÃO ******************************************************* */


//ROTA INICIO
$router->get('/', [
    function ($request) {

        return new Response(200, GetPage::getStaticPage('start'));
    }
]);

$router->get('/{page}', [
    function ($request) {

        $path = $request->getUri();
        $path = explode('/', $path);

        return new Response(200, GetPage::getStaticPage($path[1]));
    }
]);
