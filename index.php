<?php
/** Padrão PSR-4 
 * @param string $
 * 
 * Para evitar erros de caminhos de diretórios entre SO diferente, Linux e Windows.
 * Difina diretório em minúsculo. Ex. app/controller/pages. 
 * Os arquivos de classes precisão sem de acordo com a classe Ex. GetPage.
 * Quando for usar a classe Ex. use app/controller/pages/GetPage 
*/

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/includes/app.php';

use \app\http\Router;

//INICIA O ROUTER
$router = new Router(URL);

//INCLUI AS ROTAS DE PÁGINAS
include(__DIR__ . '/routes/Pages.php');

//IMPRIME RESPONSE DA ROTA
$router->run()->sendResponse();
