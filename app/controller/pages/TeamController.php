<?php

namespace app\controller\pages;

use \app\dao\TeamDAO;
use \app\dao\OccupationDAO;
use \app\utils\UploadFiles;
use \app\view\View;
use stdClass;

class TeamController extends RenderPage
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
        $var->option = self::getOption();
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
        $query = TeamDAO::selectTable('', $view);

        if ($query->num_rows !== 0) {

            while ($row = $query->fetch_assoc()) {

                $var->id = $row['id_team'];
                $var->name = $row['name_team'];
                $var->occupation = $row['occupation_team'];
                $var->birth = $row['birth_team'];
                $var->image = $row['image_team'];
                $var->start_date = $row['start_date_team'];
                $var->end_date = $row['end_date_team'];
                $var->name_occupation = $row['name_occupation'];
                $var->description_occupation = $row['description_occupation'];
                $var->id_occupation = $row['id_occupation'];

                //VERIFICANDO STATUS DA PUBLICAÇÃO
                if ($row['status_team'] == true) {
                    $var->status = 'SIM';
                } else {
                    $var->status = 'NÃO';
                }

                if ($row['public_status_team'] == true) {
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

    /** Método responsável por gerar a tabela com dados consultados no db.
     * 
     * @param object $auth
     * @param string $view
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

            $resultDB = TeamDAO::insert($post, $resultfile->path, $auth);
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

        $result = TeamDAO::select($search, $auth);
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

            $resultDB = TeamDAO::put($post, $resultfile->path, $auth);
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
        $resultDB = TeamDAO::delete($post->ID_UPDATE, $auth); //REMOVENDO BANCO DE DADOS
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
