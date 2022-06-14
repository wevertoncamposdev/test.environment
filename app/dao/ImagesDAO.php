<?php

namespace app\dao;

use \app\model\database\Database;

class ImagesDAO extends Database
{

    /** Método responsável inserir dados do banco de dados 
     * @param object $file
     * @param object $auth
     * @param string $auth
     */
    public static function insert($file, $auth, $path)
    {

        //TRATANDO A REFERENCIA
        $path = explode('/', $path);
        $path = current($path);

        //DATA DE ATUALIZAÇÃO
        date_default_timezone_set('America/Sao_Paulo');
        $date = date("Y-m-d H:i:s");

        //QUANTIDAD DE ARQUIVOS
        $count = count($file['path']);

        //LISTANDO OS CAMINHOS DOS ARQUIVOS
        $file = $file['path'];

        $result = array();
        $mysqli = parent::DBConnect();

        try{

            for ($i = 0; $i < $count; $i++) {

                //GERANDO ID
                $id = self::count() + 1;
    
                $sql = "INSERT INTO `crech964_sia`.`images` 
                    (`id_images`, `reference_images`,`path_images`, `author_images`, `date_post_images`)
                    VALUES ('$id', '$path', '$file[$i]', '$auth->name', '$date');";
    
                if (mysqli_query($mysqli, $sql)) {
                    $result['id_image'][$i] = $id;
                    $result['message'][$i] =  'Imagem registrada com sucesso!';
                }
            }
    
            $mysqli->close();
            $result['status'] = true;
            return json_encode($result);

        }catch(\Exception $e){

            $result['status'] = false;
            $result['message'] = 'Erro ao salvar os arquivos no banco de dados!';
            return json_encode($result);
        }

    }

    /** Método responsável fazer a contagem da quantidade dos dados
     * @return integer 
     */
    public static function count()
    {
        $sql = "SELECT COUNT(*) as count FROM `crech964_sia`.`images`;";

        $mysqli = self::DBConnect();
        $mysqli->begin_transaction();
        $query = $mysqli->query($sql);
        $mysqli->commit();
        $mysqli->close();

        while ($row = $query->fetch_assoc()) {
            return $row['count'];
        }
    }

    /** Método responsável por retornar as imagens do banco de dados 
     * @param string $id
     * @return object $query
    */
    public static function getImages($id){

        
        $sql = "SELECT news.id_news, news.title_news, image.id_images, image.reference_images, image.path_images, image.author_images, image.date_post_images
        FROM crech964_sia.news_has_images AS images 
        INNER JOIN crech964_sia.images AS image ON image.id_images = images.cod_images
        INNER JOIN crech964_sia.news AS news ON news.id_news = images.cod_news
        WHERE news.id_news = $id;";

        $mysqli = self::DBConnect();
        $mysqli->begin_transaction();
        $query = $mysqli->query($sql);
        $mysqli->commit();
        $mysqli->close();
        
        try{

            if($query->num_rows == 0){
               throw new \Exception('Essa notícia não tem imagens', 500);
            }

            return $query;

        }catch(\Exception $e){
            die($e->getMessage());
        }


    }
}
