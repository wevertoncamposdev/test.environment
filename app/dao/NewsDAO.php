<?php

namespace app\dao;

use \app\model\database\Database;

class NewsDAO extends Database
{

    /** Método responsável obter dados do banco de dados 
     * @param string $query
     * @return string $result
     */
    public static function selectID($search, $auth)
    {
        $sql = "SELECT * FROM `crech964_sia`.`news` WHERE `id_news` = '$search'";

        $mysqli = self::DBConnect();
        $mysqli->begin_transaction();
        $query = $mysqli->query($sql);
        $mysqli->commit();
        $mysqli->close();

        if ($query->num_rows === 0) {
            return 'Não há registro com o valor: ' . $search;
        } else {

            $result = [];
            while ($row = $query->fetch_assoc()) {
                array_push($result, $row);
            }

            $result = (object)$result;
            $result = json_encode(
                $result,
                JSON_PRETTY_PRINT | //ESCAPE FORMATTING
                    JSON_UNESCAPED_UNICODE | //ESCAPE utf8
                    JSON_UNESCAPED_SLASHES | //ESCAPE /
                    JSON_HEX_TAG | //ESCAPE ''
                    JSON_HEX_QUOT | //ESCAPE ""
                    JSON_HEX_AMP | //ESCAPE &
                    JSON_FORCE_OBJECT | //FORCE OBJECT
                    JSON_NUMERIC_CHECK | //ESCAPE NUMBER
                    JSON_PRESERVE_ZERO_FRACTION //ESCAPE FLOAT
            );

            return $result;
        }
    }

    /** Método responsável obter dados do banco de dados 
     * @param string $query
     * @return string $result
     */
    public static function select($search, $auth)
    {
        $sql = "SELECT * FROM `crech964_sia`.`news` WHERE `name_news` LIKE '$search%' ORDER BY `date_post_news` DESC";

        $mysqli = self::DBConnect();
        $mysqli->begin_transaction();
        $query = $mysqli->query($sql);
        $mysqli->commit();
        $mysqli->close();

        if ($query->num_rows === 0) {
            return 'Não há registro com o valor: ' . $search;
        } else {

            $result = [];
            while ($row = $query->fetch_assoc()) {
                array_push($result, $row);
            }

            $result = (object)$result;
            $result = json_encode(
                $result,
                JSON_PRETTY_PRINT | //ESCAPE FORMATTING
                    JSON_UNESCAPED_UNICODE | //ESCAPE utf8
                    JSON_UNESCAPED_SLASHES | //ESCAPE /
                    JSON_HEX_TAG | //ESCAPE ''
                    JSON_HEX_QUOT | //ESCAPE ""
                    JSON_HEX_AMP | //ESCAPE &
                    JSON_FORCE_OBJECT | //FORCE OBJECT
                    JSON_NUMERIC_CHECK | //ESCAPE NUMBER
                    JSON_PRESERVE_ZERO_FRACTION //ESCAPE FLOAT
            );

            return $result;
        }
    }

    /** Método responsável inserir dados do banco de dados 
     * @param object $post
     * @param array $file
     * @param object $auth
     */
    public static function insert($post, $file, $auth)
    {
        
        //GERANDO ID
        $id = self::count() + 1;

        //OBTENDO DATA
        date_default_timezone_set('America/Sao_Paulo');
        $date = date("Y-m-d H:i:s");

        $summary = explode(".", $post->body);
        $summary = strip_tags($summary[1]);

        //VALIDANDO STATUS
        $post->status = empty($post->status) ? false : true;
        $post->public_status = empty($post->public_status) ? false : true;
        $sql = "INSERT INTO `crech964_sia`.`news` 
        (`id_news`, `title_news`,`summary_news`,`body_news`, `author_news`, `date_post_news`, `date_update_news`, `status_news`,`public_status_news`) 
        VALUES ('$id', '$post->title', '$summary', '$post->body', '$post->author', '$date', '$date', '$post->status','$post->public_status');";

       
        if (!empty($file[0])) {
            foreach ($file as $f) {
                $sql .= "INSERT INTO `crech964_sia`.`news_has_images` 
                        (`cod_news`,`cod_images`, `date`) 
                        VALUES ('$id', '$f' , '$date');";
            }
        }

        $mysqli = parent::DBConnect();

        if (mysqli_multi_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['message'] =  'Notícia registrado com sucesso!';
            return json_encode($result);
        } else {
            $result['status'] = false;
            $result['message'] =  'Erro ao registrar o notícia!';
            return json_encode($result);
        }
    }

    public static function insertNewsHasImages($cod_news, $file)
    {
    }

    /** Método responsável atualizar os dados
     * @param object $post
     * @param string $file
     * @param string $auth
     */
    public static function put($post, $auth)
    {
        //DATA DE ATUALIZAÇÃO
        date_default_timezone_set('America/Sao_Paulo');
        $date = date("Y-m-d H:i:s");

        //VALIDANDO STATUS
        $post->status = empty($post->status) ? false : true;
        $post->public_status = empty($post->public_status) ? false : true;

        $sql = "UPDATE `crech964_sia`.`news` 
        SET 
        `title_news` = '$post->title',  
        `body_news` = '$post->body',
        `author_news` = '$post->author',
        `date_update_news` = '$date',
        `status_news` = '$post->status',
        `public_status_news` = '$post->public_status'
        
        WHERE (`id_news` = '$post->ID_UPDATE');";

        $mysqli = parent::DBConnect();

        if (mysqli_multi_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['message'] =  'Notícia atualizado com sucesso!';
            return json_encode($result);
        } else {
            $result['status'] = false;
            $result['message'] =  'Erro ao atualizar o notícia!';
            return json_encode($result);
        }
    }

    public static function delete($search, $auth)
    {
        $sql = "DELETE FROM `crech964_sia`.`news` WHERE (`id_news` = '$search');";

        $mysqli = self::DBConnect();
        $mysqli->begin_transaction();
        if ($mysqli->query($sql)) {
            $result['status'] = true;
            $result['message'] =  'Notícia deletado com sucesso!';
            $mysqli->commit();
            $mysqli->close();
            return json_encode($result);
        } else {

            $result['status'] = false;
            $result['message'] =  'Erro ao deletar notícia!';
            $mysqli->commit();
            $mysqli->close();
            return json_encode($result);
        }
    }

    /** Método responsável obter dados do banco 
     * @param string $query
     * @return object $query
     */
    public static function selectTable($search, $auth)
    {
        $sql = "SELECT * FROM `crech964_sia`.`news` WHERE `title_news` LIKE '$search%' ORDER BY `date_post_news` DESC";

        $mysqli = self::DBConnect();
        $mysqli->begin_transaction();
        $query = $mysqli->query($sql);
        $mysqli->commit();
        $mysqli->close();

        return $query;
    }

    /** Método responsável fazer a contagem da quantidade dos dados
     * @return integer 
     */
    public static function count()
    {
        $sql = "SELECT COUNT(*) as count FROM `crech964_sia`.`news`;";

        $mysqli = self::DBConnect();
        $mysqli->begin_transaction();
        $query = $mysqli->query($sql);
        $mysqli->commit();
        $mysqli->close();

        $result = [];
        while ($row = $query->fetch_assoc()) {
            return $row['count'];
        }
    }
}
