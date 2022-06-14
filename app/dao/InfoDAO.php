<?php

namespace app\dao;

use \app\model\database\Database;

class InfoDAO extends Database
{

    /** Método responsável obter dados do banco de dados 
     * @param string $query
     * @return string $result
     */
    public static function selectID($search, $auth)
    {
        $sql = "SELECT * FROM `crech964_site`.`info` WHERE `id_info` = '$search'";

        $mysqli = self::DBConnect('site');
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
        $sql = "SELECT * FROM `crech964_site`.`info` WHERE `title_info` LIKE '$search%' ORDER BY `date_post_info` DESC";

        $mysqli = self::DBConnect('site');
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
     * @param string $file
     * @param string $auth
     */
    public static function insert($post, $auth)
    {
        
        $id = 'info_' . md5(rand(0, 9999));
        date_default_timezone_set('America/Sao_Paulo');
        $date = date("Y-m-d H:i:s");
        $mysqli = parent::DBConnect('site');

        if (empty($post->status)) {
            $post->status = false;
        } else {
            $post->status = true;
        }
        
        $sql = "INSERT INTO `crech964_site`.`info` 
        (`id_info`, `type_info`,`title_info`, `message_info`, `author_info`, `date_post_info`, `status_info`) 
        VALUES ('$id', '$post->type', '$post->title', '$post->message', '$post->author', '$date', '$post->status');";

        if (mysqli_multi_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['message'] =  'Informação registrada com sucesso!';
            return json_encode($result);
        } else {
            $result['status'] = false;
            $result['message'] =  'Erro ao registrar a informação!';
            return json_encode($result);
        }
    }

    /** Método responsável atualizar os dados
     * @param object $post
     * @param string $auth
     */
    public static function put($post, $auth)
    {
        if (empty($post->status)) {
            $post->status = false;
        } else {
            $post->status = true;
        }

        date_default_timezone_set('America/Sao_Paulo');
        $date = date("Y-m-d H:i:s");
        
        $sql = "UPDATE `crech964_site`.`info` 
        SET 
        `type_info` = '$post->type',
        `title_info` = '$post->title',  
        `message_info` = '$post->message',
        `author_info` = '$post->author',
        `date_update_info` = '$date',
        `status_info` = '$post->status'
        
        WHERE (`id_info` = '$post->ID_UPDATE');";

        $mysqli = parent::DBConnect('site');

        if (mysqli_multi_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['message'] =  'Informação atualizada com sucesso!';
            return json_encode($result);
        } else {
            $result['status'] = false;
            $result['message'] =  'Erro ao atualizar esta informação!';
            return json_encode($result);
        }
    }

    public static function delete($search, $auth)
    {
        $sql = "DELETE FROM `crech964_site`.`info` WHERE (`id_info` = '$search');";

        $mysqli = self::DBConnect('site');
        $mysqli->begin_transaction();
        if ($mysqli->query($sql)) {
            $result['status'] = true;
            $result['message'] =  'Informação excluida com sucesso!';
            $mysqli->commit();
            $mysqli->close();
            return json_encode($result);
        }else{

            $result['status'] = false;
            $result['message'] =  'Erro ao excluir esta informação!';
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
        $sql = "SELECT * FROM `crech964_site`.`info` WHERE `title_info` LIKE '$search%' ORDER BY `date_post_info` DESC";

        $mysqli = self::DBConnect('site');
        $mysqli->begin_transaction();
        $query = $mysqli->query($sql);
        $mysqli->commit();
        $mysqli->close();

        return $query;
    }
}
