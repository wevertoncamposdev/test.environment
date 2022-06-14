<?php

namespace app\dao;

use \app\model\database\Database;

class EventsDAO extends Database
{

    /** Método responsável obter dados do banco de dados 
     * @param string $query
     * @return string $result
     */
    public static function selectID($search, $auth)
    {
        $sql = "SELECT * FROM `crech964_site`.`events` WHERE `id_events` = '$search'";

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
        $sql = "SELECT * FROM `crech964_site`.`events` WHERE `name_events` LIKE '$search%' ORDER BY `date_post_events` DESC";

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
    public static function insert($post, $file, $auth)
    {

        $id = 'events_' . md5(rand(0, 9999));
        date_default_timezone_set('America/Sao_Paulo');
        $date = date("Y-m-d H:i:s");
        $mysqli = parent::DBConnect('site');

        if (empty($post->status)) {
            $post->status = false;
        } else {
            $post->status = true;
        }
        
        $sql = "INSERT INTO `crech964_site`.`events` 
        (`id_events`, `name_events`, `date_post_events`, `message_events`, `image_events`, `status_events`) 
        VALUES ('$id', '$post->name', '$date', '$post->message', '$file', '$post->status');";

        if (mysqli_multi_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['message'] =  'Evento registrado com sucesso!';
            return json_encode($result);
        } else {
            $result['status'] = false;
            $result['message'] =  'Erro ao registrar o events!';
            return json_encode($result);
        }
    }

    /** Método responsável atualizar dados de events
     * @param object $post
     * @param string $file
     * @param string $auth
     */
    public static function put($post, $file, $auth)
    {

        if (empty($post->status)) {
            $post->status = false;
        } else {
            $post->status = true;
        }

        date_default_timezone_set('America/Sao_Paulo');
        $data = date("Y-m-d H:i:s");

        $sql = "UPDATE `crech964_site`.`events` 
        SET 
        `name_events` = '$post->name',  
        `message_events` = '$post->message',
        `image_events` = '$file',
        `status_events` = '$post->status', 
        `date_update_events` = '$data' 
        
        WHERE (`id_events` = '$post->ID_UPDATE');";

        
        $mysqli = parent::DBConnect('site');

        if (mysqli_multi_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['message'] =  'Evento atualizado com sucesso!';
            return json_encode($result);
        } else {
            $result['status'] = false;
            $result['message'] =  'Erro ao atualizar o evento!';
            return json_encode($result);
        }
    }

    public static function delete($search, $auth)
    {
        $sql = "DELETE FROM `crech964_site`.`events` WHERE (`id_events` = '$search');";

        $mysqli = self::DBConnect('site');
        $mysqli->begin_transaction();
        if ($mysqli->query($sql)) {
            $result['status'] = true;
            $result['message'] =  'Evento deletado com sucesso!';
            $mysqli->commit();
            $mysqli->close();
            return json_encode($result);
        }else{

            $result['status'] = false;
            $result['message'] =  'Erro ao deletar evento!';
            $mysqli->commit();
            $mysqli->close();
            return json_encode($result);

        }
    }

    /** Método responsável obter dados do banco de dados 
     * @param string $query
     * @return object $query
     */
    public static function selectTable($search, $auth)
    {
        $sql = "SELECT * FROM `crech964_site`.`events` WHERE `name_events` LIKE '$search%' ORDER BY `date_post_events` DESC";

        $mysqli = self::DBConnect('site');
        $mysqli->begin_transaction();
        $query = $mysqli->query($sql);
        $mysqli->commit();
        $mysqli->close();

        return $query;
    }
}
