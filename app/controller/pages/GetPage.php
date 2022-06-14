<?php

namespace app\controller\pages;

use \app\view\View;
use \app\model\database\Database;
use \app\controller\pages\RenderPage;
use \app\utils\Auth;
use \app\communication\Email;
use stdClass;

class GetPage extends RenderPage
{


    /** Método responsável por retornar o conteúdo (view) solicitado pelo cliente
     * @param string $view
     * @return string
     */
    public static function getStaticPage($path)
    {
        $var = new stdClass();
        $content = View::render($path, '.html', $var);
        return parent::getPage('layout/', 'page', $path, $content, $var);

    }


    /** Método responsável por cadastrar o usuário no banco de dados
     * @param Request
     * @param string $view
     * @return string
     */
    public static function insertUser($request, $view)
    {
        $postVars = json_encode($request->getPostVars());
        $result = Database::register($postVars);
        $result = json_decode($result);

        if ($result->status === false) {
            return self::getStaticPage($result, 'Erro');
        } else {
            return self::getStaticPage($result, 'Sucesso');
        }
    }

    /** Método responsável por atualizar os dados do usuário no banco de dados
     * @param Request
     * @param string $view
     * @return string
     */
    public static function updateUser($request, $view)
    {
        $postVars = json_encode($request->getPostVars());
        $result = Database::update($postVars);
        $result = json_decode($result);

        if ($result->status === false) {
            return self::getStaticPage($result, 'Erro');
        } else {
            return self::getStaticPage($result, 'Sucesso');
        }
    }

    /** Método responsável por iniciar a autenticação do usuário
     * @param Request
     * @return string
     */
    public static function initAuth($request)
    {

        $post = $request->getPostVars();

        if ($post['email'] == 'guest@guest' && $post['password'] == 'guest@guest') {

            $auth['email'] = 'guest@guest';
            $auth['name'] = 'guest';
            $auth['type'] = 'guest';
            $auth['session_id'] = '';
            $auth = json_encode($auth);
            $auth = json_decode($auth);

            $session = Auth::initSession($auth); //INIT SESSION

            if ($session === true) {

                $auth->session_id = $_SESSION['session_id'];
                unlink('./temp.txt');
                $auth = json_encode($auth);
                header('Location: ' . 'noticias');
                die();
            } else {

                $auth['status'] = false;
                $auth = json_encode($auth);
                return  self::getStaticPage(json_decode($auth), 'Entrar');
            }
        }

        $auth = Database::auth(json_encode($post));
        if ($auth->status === true) {

            $cod['session_id'] = rand(1000, 9999);
            $mail = new Email;
            $send = $mail->sendEmail($auth->email, 'Codigo de Acesso', 'Seu código de acesso é: ' . $cod['session_id']);

            if ($send) {

                $validate = json_encode(array_merge((array) $auth, $cod));

                /* GERAR ARQUIVO COM CÓDIGO DE ACESSO*/
                $file = fopen('./temp.txt', 'w+');

                if ($file === false) {

                    $auth = array();
                    $auth['status'] = false;
                    $auth['message'] = 'Erro ao gerar código de acesso';
                    $auth = json_encode($auth);

                    return self::getStaticPage(json_decode($auth), 'Erro'); //ERRO DE ACESSO

                } else {

                    fwrite($file, $validate);
                    fclose($file);

                    return self::getStaticPage(json_decode($validate), 'Autenticacao'); //ACESSO LIBERADO ENVIAR PARA AUTH 2F

                }
            } else {

                //$mail->getError();
                $auth = array();
                $auth['status'] = false;
                $auth['message'] = 'Erro ao enviar email';
                $auth = json_encode($auth);
                return self::getStaticPage(json_decode($auth), 'Erro'); //ERRO AO ENVIAR EMAIL
            }
        } else {
            return self::getStaticPage($auth, 'Erro'); //ERRO NA VALIDAÇÃO DO BANCO DE DADOS
        }
    }

    /** Método responsável por iniciar a autenticação de 2 fatores
     * @param Request
     * @return string
     */
    public static function Auth2F($request)
    {

        $postVars = $request->getPostVars();
        $file = file('./temp.txt');

        if ($file) {

            $auth = json_decode($file[0]);

            if ($postVars['access'] === strval($auth->session_id)) {

                $session = Auth::initSession($auth); //INIT SESSION

                if ($session === true) {

                    $auth->session_id = $_SESSION['session_id'];
                    unlink('./temp.txt');
                    $auth = json_encode($auth);
                    header('Location: ' . 'painel');
                    die();
                } else {

                    $auth['status'] = false;
                    $auth = json_encode($auth);
                    return  self::getStaticPage(json_decode($auth), 'Entrar');
                }
            } else {

                $auth = new stdClass();
                $auth->status = false;
                $auth->message = 'Código errado, tente novamente';
                $auth = json_encode($auth);
                unlink('./temp.txt');
                return  self::getStaticPage(json_decode($auth), 'Erro');
            }
        } else {

            $auth = new stdClass();
            $auth->status = false;
            $auth->message = 'Erro ao ler arquivos!';
            $auth = json_encode($auth);

            return self::getStaticPage(json_decode($auth), 'Erro');
        }
    }
}
