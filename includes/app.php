<?php 

use \app\view\View;

define('URL', 'http://localhost:82');

//ENVIRONMENT - É O GETENV

//DEFINE O VALOR PADRÃO DAS VARIÁVEIS
View::init([
    'URL' => URL
]);