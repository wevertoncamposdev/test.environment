<?php

namespace app\controller\pages;

use \app\dao\TestimonialsDAO;
use \app\utils\UploadFiles;
use \app\exceptions\FileException;
use \app\view\View;
use stdClass;

class TestimonialsController extends RenderPage
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
        $var->thead = View::render('content/table/' . $view . '/thead', '.html', $var); //OBTER THEAD DA TABLE
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
        $query = TestimonialsDAO::selectTable('', $view);

        if ($query->num_rows !== 0) {

            while ($row = $query->fetch_assoc()) {

                $var->id = $row['id_testimonials'];
                $var->author = $row['author_testimonials'];
                $var->message = $row['message_testimonials'];
                $var->image = $row['image_testimonials'];
                $var->date = $row['date_post_testimonials'];
                $var->update = $row['date_update_testimonials'];

                //VERIFICANDO STATUS DA PUBLICAÇÃO
                if ($row['status_testimonials'] == true) {
                    $var->status = 'SIM';
                } else {
                    $var->status = 'NÃO';
                }

                $itens .= View::render('content/table/' . $view . '/tbody', '.html', $var);
            }

            return $itens;
        } else {
            return false;
        }
    }

    /** Método responsável por receber valors POST e enviar para a inserção no banco de dados
     * @param Request $request
     * @param Object $auth
     * @return string $path
     */
    public static function insert($request, $auth, $path)
    {   

        try {

            //VERIFICAÇÕE E VALIDAÇÕES DO ARQUIVO
            FileException::image($_FILES);

            $post = json_encode($request->getPostVars());
            $post =  json_decode($post);

            //SE NÃO HOUVER ARQUIVO SALVE UM ARQUIVO PADRÃO
            empty($_FILES['file']['name'])?$upload = UploadFiles::uploadStandard($_FILES['file']['name'],$path):$upload = UploadFiles::upload($post, $path);
            
            $resultfile = json_decode($upload);

            if ($resultfile->status === true) {

                $resultDB = TestimonialsDAO::insert($post, $resultfile->path, $auth);
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

        } catch (FileException $e) {


            die;
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

        $result = TestimonialsDAO::select($search, $auth);
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

        try {

            //VERIFICAÇÕE E VALIDAÇÕES DOF ARQUIVO
            FileException::image($_FILES);

            $post = json_encode($request->getPostVars());
            $post = json_decode($post);
            
            if (empty($_FILES['file']['name'])) {
                $upload = UploadFiles::uploadStandard($post->FILE_UPDATE, $path);
            } else {
                UploadFiles::removeFile($post->FILE_UPDATE, $path);
                $upload = UploadFiles::upload($post, $path);
            }

            $resultfile = json_decode($upload);

            if ($resultfile->status === true) {

                $resultDB = TestimonialsDAO::put($post, $resultfile->path, $auth);
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
                
        }catch (FileException $e){

            die;

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
        $resultDB = TestimonialsDAO::delete($post->ID_UPDATE, $auth); //REMOVENDO BANCO DE DADOS
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
