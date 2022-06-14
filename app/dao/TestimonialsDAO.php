<?php

namespace app\dao;

use \app\model\database\Database;

class TestimonialsDAO extends Database
{

    /** Método responsável obter dados do banco de dados 
     * @param string $query
     * @return string $result
     */
    public static function selectID($search, $auth)
    {
        $sql = "SELECT * FROM `crech964_sia`.`testimonials` WHERE `id_testimonials` = '$search'";

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
        $sql = "SELECT * FROM `crech964_sia`.`testimonials` WHERE `author_testimonials` LIKE '$search%' ORDER BY `date_post_testimonials` DESC";

        $mysqli = self::DBConnect('sia');
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
    public static function insert($post, $files, $auth)
    {   
        //CRIANDO ID
        $id = 'testimonials_' . md5(rand(0, 9999));

        //VALIDANDO DATE
        date_default_timezone_set('America/Sao_Paulo');
        $data = date("Y-m-d H:i:s");
    
        //VALIDANDO STATUS
        empty($post->status)?$post->status = false : $post->status = true;

        $sql = "INSERT INTO `crech964_sia`.`testimonials` 
        (`id_testimonials`, `author_testimonials`, `date_post_testimonials`, `message_testimonials`, `image_testimonials`, `status_testimonials`) 
        VALUES ('$id', '$post->name', '$data', '$post->message', '$files', '$post->status');";
        
        $mysqli = parent::DBConnect('sia');

        if (mysqli_multi_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['message'] =  'Depoimento registrado com sucesso!';
            return json_encode($result);
        } else {
            $result['status'] = false;
            $result['message'] =  'Erro ao registrar o depoimento!';
            return json_encode($result);
        }
    }

    /** Método responsável atualizar dados de depoimento
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
        $date = date("Y-m-d H:i:s");

        $sql = "UPDATE `crech964_sia`.`testimonials` 
        SET 
        `author_testimonials` = '$post->name',  
        `message_testimonials` = '$post->message',
        `image_testimonials` = '$file',
        `status_testimonials` = '$post->status', 
        `date_update_testimonials` = '$date' 
        
        WHERE (`id_testimonials` = '$post->ID_UPDATE');";

        $mysqli = parent::DBConnect('sia');

        if (mysqli_multi_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['message'] =  'Depoimento atualizado com sucesso!';
            return json_encode($result);
        } else {
            $result['status'] = false;
            $result['message'] =  'Erro ao atualizar o depoimento!';
            return json_encode($result);
        }
    }

    public static function delete($search, $auth)
    {
        $sql = "DELETE FROM `crech964_sia`.`testimonials` WHERE (`id_testimonials` = '$search');";

        $mysqli = self::DBConnect('sia');
        $mysqli->begin_transaction();
        if ($mysqli->query($sql)) {
            $result['status'] = true;
            $result['message'] =  'Depoimento deletado com sucesso!';
            $mysqli->commit();
            $mysqli->close();
            return json_encode($result);
        }else{

            $result['status'] = false;
            $result['message'] =  'Erro ao deletar depoimento!';
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
        $sql = "SELECT * FROM `crech964_sia`.`testimonials` WHERE `author_testimonials` LIKE '$search%' ORDER BY `date_post_testimonials` DESC";

        $mysqli = self::DBConnect();
        $mysqli->begin_transaction();
        $query = $mysqli->query($sql);
        $mysqli->commit();
        $mysqli->close();

        return $query;
    }
}
