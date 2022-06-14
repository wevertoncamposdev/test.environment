<?php

namespace app\utils;
use app\dao\ImagesDAO;

class UploadFiles
{

    /** Método responsável por fazer o upload de arquivos
     * @param array $files
     * @param object $post
     * @param string $path
     */
    public static function upload($post, $path)
    {

        //EXTENSÃO DO ARQIVO ENVIADO
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $filestype = self::filesType(); //VERIFICAR O TIPO DE ARQUIVO - arquivo || imagem

        //TRATANDO O CAMINHO
        $localpath = "uploads/"; //UPLOAD DO SUBDOMINIO - SIA

        $path = strtolower($path . '/'); //ROTA DE REQUISIÇÃO
        $date = date('m-Y') . '/';

        //TRATANDO NOME DO ARQUIVO E LOCAL DE ARMAZENAMENTO
        $temp = $_FILES['file']['tmp_name'];
        $name =  rtrim($filestype, '/') . '_' . uniqid() . "." . $ext;
        $local =  $localpath . $path . $filestype . $date;



        //VERIFICANDO SE A PASTA EXISTE
        if (file_exists($local)) {
            //$local =  $localpath . $filestype . $path . $date . $name;
            $local =  $localpath . $path . $filestype . '/' . $date . $name;
        } else {
            mkdir($local, 0777, true);
            //$local =  $localpath . $filestype . $path . $date . $name;
            $local =  $localpath . $path . $filestype . '/' . $date . $name;
        }

        //upload/depoimentos/imagem/data/arquivo...
        //upload/eventos/imagem/data/arquivo...
        //upload/noticias/imagem/data/arquivo...
        //upload/transparencia/arquivo/data/arquivo...

        //SALVANDO ARQUIVO LOCAL
        if (move_uploaded_file($temp, $local)) {

            if ($filestype == 'imagem') {
                $imgResize = new TreatingImage($local);
                $imgResize->resize(840);
                $imgResize->save($local, 100);
            }

            //CRIANDO UMA CÓPIA PUBLIC PARA O SITE
            $result['dominio'] = self::copyPublic($filestype, $local);
            $result['local'] = 'Arquivo salvo no local com sucesso!  (SIA)';
            $result['path'] = $local;
            $result['status'] = true;

            return json_encode($result);
        } else {

            $result['message'] =  'Erro ao enviar o arquivo!';
            $result['status'] = false;

            return json_encode($result);
        };
    }

    /** Método responsável por retornar o mesmo caminho do arquivo ou o caminho de um arquivo padrão
     * @param string $files
     * @return string $result
     */
    public static function uploadStandard($files, $path)
    {
        $path = strtolower($path);

        if (!empty($files)) {
            $result['path'] = $files;
            $result['local'] = 'Arquivo mantido com sucesso! (SIA)';
            $result['status'] = true;
        } else {
            $result['path'] = 'uploads/' . $path . '/' . $path . '.png';
            $result['local'] = 'Arquivo padrão salvo com sucesso! (SIA)';
            $result['status'] = true;
        }

        return json_encode($result);
    }

    /** Método responsável por fazer o upload de arquivos
     * @param array $files
     * @param object $post
     * @param string $path
     */
    public static function uploadMultipleImage($post, $auth, $path)
    {

        $count = count($_FILES['more_files']['name']);
        $file = array();
        
        for ($i = 0; $i < $count; $i++) {

            //EXTENSÃO DO ARQIVO ENVIADO
            $ext = pathinfo($_FILES['more_files']['name'][$i], PATHINFO_EXTENSION);
            $filestype = self::filesTypeMultiple($i); //VERIFICAR O TIPO DE ARQUIVO - arquivo || imagem

            //TRATANDO O CAMINHO
            $localpath = "uploads/"; //UPLOAD DO SUBDOMINIO - SIA
            $date = date('m-Y') . '/';

            //TRATANDO NOME DO ARQUIVO E LOCAL DE ARMAZENAMENTO
            $temp = $_FILES['more_files']['tmp_name'][$i];
            $name =  $filestype . '_' . uniqid() . "." . $ext;
            $local =  $localpath . $path . $filestype . '/' . $date;
            $filestype = $filestype . '/';

            //VERIFICANDO SE A PASTA EXISTE
            if (file_exists($local)) {
                //$local =  $localpath . $filestype . $path . $date . $name;
                $local =  $localpath . $path . $filestype . $date . $name;
            } else {
                mkdir($local, 0777, true);
                //$local =  $localpath . $filestype . $path . $date . $name;
                $local =  $localpath . $path . $filestype . $date . $name;
            }

            //upload/depoimentos/imagem/data/arquivo...
            //upload/eventos/imagem/data/arquivo...
            //upload/noticias/imagem/data/arquivo...
            //upload/transparencia/arquivo/data/arquivo...

            //SALVANDO ARQUIVO LOCAL
            if (move_uploaded_file($temp, $local)) {

                if ($filestype == 'imagem/') {
                    $imgResize = new TreatingImage($local);
                    $imgResize->resize(840);
                    $imgResize->save($local, 100);
                }

                //CRIANDO UMA CÓPIA PUBLIC PARA O SITE
                $file['dominio'][$i] = self::copyPublic($filestype, $local);
                $file['local'][$i] = 'Arquivo salvo no local com sucesso!  (SIA)';
                $file['path'][$i] = $local;
                $file['status'][$i] = true;

            } else {

                $file['message'][$i] =  'Erro ao enviar o arquivo!';
                $file['status'][$i] = false;
                
            };
        }

        $result = ImagesDAO::insert($file, $auth, $path);
        return $result;

    }

    private static function path($path){
        return strtolower($path . '/');
    }

    /** Método responsável por copiar arquivos para o dominio publicação
     * @param string $filestype
     * @param string $local
     * @return string
     */
    public static function copyPublic($filestype, $local)
    {

        $localpath = explode('/', $local); //SEPARAR CADA CAMINHO
        $filename = end($localpath); //OBTER NAME
        $path = array_pop($localpath); //OBTER CAMINHO
        $folder = implode('/', $localpath) . '/'; //OBTER STRING DO CAMINHO
        $publicpath = "/home2/crech964/novo.crechealvorada.org/" . $folder; //UPLOAD DO DOMINIO PUBLICO - SITE

        //VERIFICANDO SE A PASTA EXISTE NO DOMINIO
        if (file_exists($publicpath)) {
            $publicfile = $publicpath . $filename;
        } else {
            mkdir($publicpath, 0777, true);
            $publicfile = $publicpath . $filename;
        }

        if (copy($local, $publicfile)) {
            return 'Arquivo salvo no dominio publico com sucesso! (SITE)';
        } else {
            return 'Erro ao salvar arquivo no dominio publico! (SITE)';
        }
    }

    /** Método responsável por verificar o tipo de arquivo que está sendo enviados
     * @param array $files
     * @return string 
     */
    private static function filesType()
    {
        $type = $_FILES['file']['type'];
        $type = explode('/', $type);

        switch ($type[0]) {

            case 'image':
                return 'imagem';
                break;
            case 'application':
                return 'arquivo';
                break;
        }
    }

     /** Método responsável por verificar o tipo de arquivo que está sendo enviados
     * @param array $files
     * @return string 
     */
    private static function filesTypeMultiple($i)
    {
        $type = $_FILES['more_files']['type'][$i];
        $type = explode('/', $type);

        switch ($type[0]) {

            case 'image':
                return 'imagem';
                break;
            case 'application':
                return 'arquivo';
                break;
        }
    }

    /** Método responsável por remover o arquivo
     * @param array $files
     * @return string $result
     */
    public static function removeFile($file, $path)
    {

        if ($file == 'upload/' . $path . '/' . $path . '.png') {
            return true;
        }

        if (file_exists($file)) {
            unlink($file);
        }

        self::removeFilePublic($file);
        $result['local'] =  'Arquivo local deletado com sucesso! (SIA)';
        $result['status'] = true;

        return json_encode($result);
    }

    /** Método responsável por remover o arquivo do dominio publico
     * @param array $files
     * @return array
     */
    private static function removeFilePublic($file)
    {

        $publicfile = "/home2/crech964/novo.crechealvorada.org/" . $file;

        if (file_exists($publicfile)) {
            unlink($publicfile);
            return $result['dominio'] =  'Arquivo publico deletado com sucesso! (SITE)';
        }
    }
}
