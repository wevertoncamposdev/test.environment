<?php

namespace app\controller\pages;

use \app\dao\OccupationDAO;
use \app\view\View;
use stdClass;

class OccupationController extends RenderPage
{
    /** Método responsável por retornar o conteúdo (view)
     * com dados consultados no db.
     * @param object $auth
     * @param string $view
     * @return string 
     */
    public static function get($auth, $view)
    {
        $view = strtolower($view); //resources\view\content\table\noticias\tbody.html

        $var = new stdClass();
        $var->thead = View::render('content/table/' . $view . '/thead', '.html', $var); //OBTER THEAD DA TABLE
        $var->tbody = self::getTable($view); //OBTER TBODY DA TABELA
        $content = View::render(strtolower($auth->type) . '/' . $view, '.html', $var);
        return parent::getPage(strtolower($auth->type), '/page', $view, $content, $auth);
    }

    /** Método responsável por gerar a tabela com dados consultados no db.
     * 
     * @param string $view
     * @return string 
     */
    public static function getTable($view)
    {
        $itens = '';
        $var = new stdClass();
        $query = OccupationDAO::selectTable('', $view);

        if ($query->num_rows !== 0) {

            while ($row = $query->fetch_assoc()) {

                $var->id = $row['id_occupation'];
                $var->name = $row['name_occupation'];
                $var->description = $row['description_occupation'];

                //VERIFICANDO STATUS DA PUBLICAÇÃO
                if ($row['status_occupation'] == true) {
                    $var->status = 'SIM';
                } else {
                    $var->status = 'NÃO';
                }

                if ($row['public_status_occupation'] == true) {
                    $var->public_status = 'SIM';
                } else {
                    $var->public_status = 'NÃO';
                }

                $itens .= View::render('content/table/' . $view . '/tbody', '.html', $var);
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

        $resultDB = OccupationDAO::insert($post, $auth);
        $resultDB = json_decode($resultDB);
        if ($resultDB->status === true) {

            //SUCESSO
            echo ('<pre>');
            print_r($resultDB);
            echo ('</pre>');
            die;
        } else {

            //ERRO ENVIO AO BANCO DE DADOS
            return $resultDB->message;
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

        $result = OccupationDAO::select($search, $auth);
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
        
        $resultDB = OccupationDAO::put($post, $auth);
        $resultDB = json_decode($resultDB);
        if ($resultDB->status === true) {

            //SUCESSO
            echo ('<pre>');
            print_r($resultDB);
            echo ('</pre>');
            die;
        } else {

            //ERRO ENVIO AO BANCO DE DADOS
            return $resultDB->message;
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

        $resultDB = OccupationDAO::delete($post->ID_UPDATE, $auth); //REMOVENDO BANCO DE DADOS
        $resultDB = json_decode($resultDB);

        if ($resultDB->status === true) {

            //SUCESSO
            echo ('<pre>');
            print_r($resultDB);
            echo ('</pre>');
            die;
        } else {
            //ERRO ENVIO AO BANCO DE DADOS
            return $resultDB->message;
        }
    }
}
