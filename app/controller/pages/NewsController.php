<?php

namespace app\controller\pages;

use \app\dao\NewsDAO;
use \app\dao\ImagesDAO;
use \app\utils\UploadFiles;
use \app\view\View;
use \app\exceptions\FileException;
use stdClass;

class NewsController extends RenderPage
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
        $query = NewsDAO::selectTable('', $view);

        if ($query->num_rows !== 0) {

            while ($row = $query->fetch_assoc()) {

                $var->id = $row['id_news'];
                $var->title = $row['title_news'];
                $var->author = $row['author_news'];
                $var->body = $row['body_news'];
                $var->date = $row['date_post_news'];
                $var->update = $row['date_update_news'];

                //VERIFICANDO STATUS DA PUBLICAÇÃO
                $var->status = $row['status_news'] == true ? 'SIM' : 'NÃO';
                $var->public_status = $row['public_status_news'] == true ? 'SIM' : 'NÃO';

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
     * @param object $auth
     * @return string $view
     */
    public static function insert($request, $auth, $path)
    {
       
        try {

            //VERIFICAÇÕE E VALIDAÇÕES DO ARQUIVO
            FileException::image(); //more_files ok
            
            $post = json_encode($request->getPostVars());
            $post =  json_decode($post);    
           
            $upload = self::insertImage($post, $auth, $path); //TRATAR UMA IMAGEM PADRÃO CASO NÃO ENVIE NENHUMA IMAGEM
            $resultfile = json_decode($upload);
            
            //VERIFICAÇÕE E VALIDAÇÕES DO ARQUIVO
            FileException::missingInformationFile($resultfile);
 
            if ($resultfile->status === true) {

                $resultDB = NewsDAO::insert($post, $resultfile->id_image, $auth);
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
     * @param object $auth
     * @return string $path
     */
    public static function update($request, $auth, $path){

        $path = strtolower($path);
        $id = array_keys($_GET);
        $id = current($id);

        $var = new stdClass();
        $content = View::render('content/modal/'. $path .'/update', '.html', $var); //OBTER THEAD DA TABLE
        return $content;
    }

    /** Método responsável por retornar o conteúdo (view) solicitado pelo cliente
     * com dados consultados no db.
     * @param Request $request
     * @param string $auth
     * @return string $view
     */
    public static function select($search, $auth)
    {

        $result = NewsDAO::select($search, $auth);
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


        $resultDB = NewsDAO::put($post, $auth);
        $resultDB = json_decode($resultDB);

        if ($resultDB->status === true) {

            //SUCESSO
            echo ('<pre>');
            print_r($resultDB);
            echo ('<br>');
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

        echo('<pre>'); print_r($post); echo('</pre>'); 
        echo('<pre>'); print_r($_FILES); echo('</pre>');exit;

        $resultfile = UploadFiles::removeFile($post->FILE_UPDATE, $path); //REMOVENDO ARQUIVO
        $resultDB = NewsDAO::delete($post->ID_UPDATE, $auth); //REMOVENDO BANCO DE DADOS
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

    /** Método responsável por armazenar as imagens e retornar o caminho armazenados. 
     * @param object $post
     * @param string $path
     * @return string $upload
     */
    public static function insertImage($post, $auth, $path)
    {   
        

        if (isset($_FILES['file']) && !empty($_FILES['file']['name'][0])) {
            $upload =  UploadFiles::upload($post, $path);
            return $upload;
        }

        if (isset($_FILES['more_files']) && !empty($_FILES['more_files']['name'][0])) {
            $upload = UploadFiles::uploadMultipleImage($post, $auth, strtolower($path . '/'));
            return $upload;
        }

        $upload['status'] = true;
        $upload['id_image'][0] = false;
        $upload['message'] = 'Não enviou nenhuma imagem!';

        return json_encode($upload);


    }

    public static function getImagesNews($request, $auth, $path){


        $content = '';

        $images = ImagesDAO::getImages($_GET['query']);
        
        while ($row = $images->fetch_assoc()) {

            $var = new stdClass();
            $var->id = $row['id_images'];
            $var->reference = $row['reference_images'];
            $var->path = $row['path_images'];
            $var->author = $row['author_images'];
            $var->date_post = $row['date_post_images'];

            $content .= View::render('content/images/image-news', '.html', $var);
            
        }
        return $content;
    }
}
