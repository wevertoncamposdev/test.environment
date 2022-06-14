<?php

namespace app\controller\pages;

use \app\dao\EventsDAO;
use \app\utils\UploadFiles;
use \app\view\View;
use stdClass;

class EventsController extends RenderPage
{

    /** Método responsável por retornar o conteúdo (view)
     * com dados consultados no db.
     * @param object $auth
     * @param string $view
     * @return string 
     */
    public static function get($auth, $view)
    {
        $view = strtolower($view);
        $var = new stdClass();
        $var->thead = View::render('content/table/'. $view . '/thead', '.html', $var); //OBTER THEAD DA TABLE
        $var->tbody = self::getTable($auth, $view); //OBTER TBODY DA TABELA
        $content = View::render(strtolower($auth->type) . '/' . $view, '.html', $var);
        return parent::getPage(strtolower($auth->type), '/page', $view, $content, $auth);
    }

    /** Método responsável por gerar a tabela com dados consultados no db.
     * 
     * @param object $auth
     * @param string $view
     * @return string 
     */
    public static function getTable($auth, $view)
    {
        $itens = '';
        $var = new stdClass();
        $query = EventsDAO::selectTable('', $view);

        if ($query->num_rows !== 0) {

            while ($row = $query->fetch_assoc()) {

                $var->id = $row['id_events'];
                $var->name = $row['name_events'];
                $var->message = $row['message_events'];
                $var->image = $row['image_events'];
                $var->date = $row['date_post_events'];
                $var->update = $row['date_update_events'];

                //VERIFICANDO STATUS DA PUBLICAÇÃO
                if ($row['status_events'] == true) {
                    $var->status = 'SIM';
                } else {
                    $var->status = 'NÃO';
                }

                $itens .= View::render('content/table/'. $view . '/tbody', '.html', $var);
            }

            return $itens;
        } else {
            return false;
        }
    }

    /** Método responsável por retornar o conteúdo (view) solicitado pelo cliente
     * com dados consultados no db.
     * @param Request $request
     * @param string $auth
     * @return string $view
     */
    public static function insert($request, $auth, $path)
    {
        $post = json_encode($request->getPostVars());
        $post =  json_decode($post);

        if (empty($_FILES['file']['name'])) {
            $upload = UploadFiles::uploadStandard($_FILES['file']['name'], $path);
        } else {
            $upload = UploadFiles::upload($_FILES, $post, $path);
        }

        $resultfile = json_decode($upload);
       
        if ($resultfile->status === true) {

            $resultDB = EventsDAO::insert($post, $resultfile->path, $auth);
            $resultDB = json_decode($resultDB);
            if ($resultDB->status === true) {

                //SUCESSO
                echo ('<pre>');
                print_r($resultfile);
                echo ('<br>');
                print_r($resultDB);
                echo ('<br>');
                echo ('</pre>');
                die;
            } else {

                //ERRO ENVIO AO BANCO DE DADOS
                return $resultDB->message;
            }
        } else {

            //ERRO ENVIO DE ARQUIVO
            return $resultfile->message;
        }
    }

    /** Método responsável por retornar o conteúdo (view) solicitado pelo cliente
     * com dados consultados no db.
     * @param Request $request
     * @param string $auth
     * @return string $view
     */
    public static function select($search, $auth)
    {

        $result = EventsDAO::select($search, $auth);
        return $result;
    }

    /** Método responsável por retornar o conteúdo (view) solicitado pelo cliente com informações sobre atualização de dados
     *
     * @param Request $request
     * @param string $auth
     * @return string $view
     */
    public static function put($request, $auth, $path)
    {
        $post = json_encode($request->getPostVars());
        $post = json_decode($post);

        if (empty($_FILES['file']['name'])) {
            $upload = UploadFiles::uploadStandard($post->FILE_UPDATE, $path); 
        } else {
            UploadFiles::removeFile($post->FILE_UPDATE, $path);
            $upload = UploadFiles::upload($_FILES, $post, $path);
        }

        $resultfile = json_decode($upload);

        if ($resultfile->status === true) {

            $resultDB = EventsDAO::put($post, $resultfile->path, $auth);
            $resultDB = json_decode($resultDB);
            if ($resultDB->status === true) {

                //SUCESSO
                echo ('<pre>');
                print_r($resultfile);
                echo ('<br>');
                print_r($resultDB);
                echo ('<br>');
                echo ('</pre>');
                die;
            } else {

                //ERRO ENVIO AO BANCO DE DADOS
                return $resultDB->message;
            }
        } else {

            //ERRO ENVIO DE ARQUIVO
            return $resultfile->message;
        }
    }

    /** Método responsável por retornar o conteúdo (view) solicitado pelo cliente com informações sobre exlusão de dados
     * com dados consultados no db.
     * @param Request $request
     * @param string $auth
     * @return string $view
     */
    public static function delete($request, $auth, $path)
    {
        $post = json_encode($request->getPostVars());
        $post = json_decode($post);

        $resultfile = UploadFiles::removeFile($post->FILE_UPDATE, $path); //REMOVENDO ARQUIVO
        $resultDB = EventsDAO::delete($post->ID_UPDATE, $auth); //REMOVENDO BANCO DE DADOS
        $resultDB = json_decode($resultDB);

        if ($resultDB->status === true) {
            
            //SUCESSO
            echo ('<pre>');
            print_r(json_decode($resultfile));
            echo ('<br>');
            print_r($resultDB);
            echo ('<br>');
            echo ('</pre>');
            die;

        } else {
            //ERRO ENVIO AO BANCO DE DADOS
            return $resultDB->message;
        }
    }
}
