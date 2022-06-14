<?php

namespace app\controller\pages;

use \app\dao\DirectorsDAO;
use \app\dao\OccupationDAO;
use \app\utils\UploadFiles;
use \app\view\View;
use stdClass;

class DirectorsController extends RenderPage
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
        $var->thead = View::render('content/table/'. $view . '/thead', '.html', $var); //OBTER THEAD DA TABLE
        $var->tbody = self::getTable($auth, $view); //OBTER TBODY DA TABELA
        $var->option = self::getOption(); //OBTER TBODY DA TABELA
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
        $query = DirectorsDAO::selectTable('', $view);

        if ($query->num_rows !== 0) {

            while ($row = $query->fetch_assoc()) {

                $var->id = $row['id_directors'];
                $var->name = $row['name_directors'];
                $var->occupation = $row['occupation_directors'];
                $var->birth = $row['birth_directors'];
                $var->image = $row['image_directors'];
                $var->start_date = $row['start_date_directors'];
                $var->end_date = $row['end_date_directors'];
                $var->id_occupation = $row['id_occupation'];
                $var->name_occupation = $row['name_occupation'];


                //VERIFICANDO STATUS DA PUBLICAÇÃO
                $row['status_directors'] == true ? $var->status = 'SIM':$var->status = 'NÃO';
                $row['public_status_directors'] == true ? $var->public_status = 'SIM':$var->public_status = 'NÃO';

                $itens .= View::render('content/table/'.$view .'/tbody', '.html', $var);
            }

            return $itens;
        } else {
            return false;
        }
    }

    /** Método responsável por gerar a tabela com dados consultados no db.
     * 
     * @return string 
     */
    public static function getOption()
    {
        $itens = '';
        $var = new stdClass();
        $query = OccupationDAO::selectOption();

        if ($query->num_rows !== 0) {

            while ($row = $query->fetch_assoc()) {

                $var->id = $row['id_occupation'];
                $var->name = $row['name_occupation'];

                $itens .= View::render('content/option/option', '.html', $var);
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

            $resultDB = DirectorsDAO::insert($post, $resultfile->path, $auth);
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

        $result = DirectorsDAO::select($search, $auth);
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

            $resultDB = DirectorsDAO::put($post, $resultfile->path, $auth);
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
        $resultDB = DirectorsDAO::delete($post->ID_UPDATE, $auth); //REMOVENDO BANCO DE DADOS
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

