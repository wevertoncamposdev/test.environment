<?php

namespace app\dao;

use \app\model\database\Database;

class SponsorsDAO extends Database
{

    /** Método responsável obter dados do banco de dados 
     * @param string $query
     * @return string $result
     */
    public static function selectID($search, $auth)
    {
        $sql = "SELECT * FROM `crech964_sia`.`sponsors` WHERE `id_sponsors` = '$search'";

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

    /** Método responsável obter dados do banco de dados 
     * @param string $query
     * @return string $result
     */
    public static function select($search, $auth)
    {   
        
        $sql = "SELECT * FROM `crech964_sia`.`sponsors` WHERE `name_sponsors` LIKE '$search%' ORDER BY `start_date_sponsors` DESC";

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
    public static function insert($post, $file, $auth)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $date_post = date("Y-m-d");

        $mysqli = parent::DBConnect('sia');
        $count = self::count() + 1;

        empty($post->status) ? $post->status = false : $post->status = true;
        empty($post->public_status) ? $post->public_status = false : $post->public_status = true;
       
        $sql = "INSERT INTO `crech964_sia`.`sponsors` 
        (`id_sponsors`, `name_sponsors`,`description_sponsors`, `image_sponsors`, `start_date_sponsors`, `end_date_sponsors`, `status_sponsors`, `public_status_sponsors`, `date_post_sponsors`) 
        VALUES ('$count', '$post->name', '$post->description', '$file', '$post->start_date', '$post->end_date', '$post->status', '$post->public_status', '$date_post');";

        if (mysqli_multi_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['message'] =  'Informações de Patrocinador registrado com sucesso!';
            return json_encode($result);
        } else {
            $result['status'] = false;
            $result['message'] =  'Erro ao registrar as informações de Patrocinador!';
            return json_encode($result);
        }
    }

    /** Método responsável atualizar os dados
     * @param object $post
     * @param string $file
     * @param string $auth
     */
    public static function put($post, $file, $auth)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $date_update = date("Y-m-d");

        empty($post->status) ? $post->status = false : $post->status = true;
        empty($post->public_status) ? $post->public_status = false : $post->public_status = true;
       
        $sql = "UPDATE `crech964_sia`.`sponsors` 
        SET 
        `name_sponsors` = '$post->name',  
        `description_sponsors` = '$post->description',
        `image_sponsors` = '$file',
        `start_date_sponsors` = '$post->start_date',
        `end_date_sponsors` = '$post->end_date',
        `status_sponsors` = '$post->status',
        `public_status_sponsors` = '$post->public_status',
        `date_update_sponsors` = '$date_update'
        
        WHERE (`id_sponsors` = '$post->ID_UPDATE');";

        $mysqli = parent::DBConnect('sia');

        if (mysqli_multi_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['message'] =  'Informações do Patrocinador atualizado com sucesso!';
            return json_encode($result);
        } else {
            $result['status'] = false;
            $result['message'] =  'Erro ao atualizar as informações do Patrocinador';
            return json_encode($result);
        }
    }

    public static function delete($search, $auth)
    {
        $sql = "DELETE FROM `crech964_sia`.`sponsors` WHERE (`id_sponsors` = '$search');";

        $mysqli = self::DBConnect('sia');
        $mysqli->begin_transaction();
        if ($mysqli->query($sql)) {
            $result['status'] = true;
            $result['message'] =  'Informações de Patrocinador deletado com sucesso!';
            $mysqli->commit();
            $mysqli->close();
            return json_encode($result);
        }else{

            $result['status'] = false;
            $result['message'] =  'Erro ao deletar as informações de Patrocinador!';
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
        $sql = "SELECT * FROM crech964_sia.sponsors";
        
        $mysqli = self::DBConnect('sia');
        $mysqli->begin_transaction();
        $query = $mysqli->query($sql);
        $mysqli->commit();
        $mysqli->close();

        return $query;
    }

    public static function count()
    {
        $sql = "SELECT COUNT(*) as count FROM `crech964_sia`.`sponsors`;";

        $mysqli = self::DBConnect('sia');
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

