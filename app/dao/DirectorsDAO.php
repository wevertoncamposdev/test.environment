<?php

namespace app\dao;

use \app\model\database\Database;

class DirectorsDAO extends Database
{

    /** Método responsável obter dados do banco de dados 
     * @param string $query
     * @return string $result
     */
    public static function selectID($search, $auth)
    {
        $sql = "SELECT * FROM `crech964_sia`.`directors` WHERE `id_directors` = '$search'";

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
        
        $sql = "SELECT * FROM `crech964_sia`.`directors` WHERE `name_directors` LIKE '$search%' ORDER BY `start_date_directors` DESC";

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

        $id = 'directors_' . md5(rand(0, 9999));

        $mysqli = parent::DBConnect('sia');

        empty($post->status) ? $post->status = false : $post->status = true;
        empty($post->public_status) ? $post->public_status = false : $post->public_status = true;
       
        $sql = "INSERT INTO `crech964_sia`.`directors` 
        (`id_directors`, `name_directors`,`occupation_directors`, `birth_directors`, `image_directors`, `start_date_directors`, `end_date_directors`, `status_directors`, `public_status_directors`) 
        VALUES ('$id', '$post->name', '$post->occupation', '$post->birth', '$file', '$post->start_date', '$post->end_date', '$post->status', '$post->public_status');";

        if (mysqli_multi_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['message'] =  'Informações de Diretor(a) registrado com sucesso!';
            return json_encode($result);
        } else {
            $result['status'] = false;
            $result['message'] =  'Erro ao registrar as informações de Diretor(a)!';
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

        empty($post->status) ? $post->status = false : $post->status = true;
        empty($post->public_status) ? $post->public_status = false : $post->public_status = true;
       
        $sql = "UPDATE `crech964_sia`.`directors` 
        SET 
        `name_directors` = '$post->name',  
        `occupation_directors` = '$post->occupation',
        `birth_directors` = '$post->birth',
        `image_directors` = '$file',
        `start_date_directors` = '$post->start_date',
        `end_date_directors` = '$post->end_date',
        `status_directors` = '$post->status',
        `public_status_directors` = '$post->public_status'
        
        WHERE (`id_directors` = '$post->ID_UPDATE');";

        $mysqli = parent::DBConnect('sia');

        if (mysqli_multi_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['message'] =  'Informações do diretor(a) atualizado com sucesso!';
            return json_encode($result);
        } else {
            $result['status'] = false;
            $result['message'] =  'Erro ao atualizar as informações do diretor(a)';
            return json_encode($result);
        }
    }

    public static function delete($search, $auth)
    {
        $sql = "DELETE FROM `crech964_sia`.`directors` WHERE (`id_directors` = '$search');";

        $mysqli = self::DBConnect('sia');
        $mysqli->begin_transaction();
        if ($mysqli->query($sql)) {
            $result['status'] = true;
            $result['message'] =  'Informações de Diretor(a) deletado com sucesso!';
            $mysqli->commit();
            $mysqli->close();
            return json_encode($result);
        }else{

            $result['status'] = false;
            $result['message'] =  'Erro ao deletar as informações de Diretor(a)!';
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
        $sql = "SELECT * FROM crech964_sia.directors INNER JOIN crech964_sia.occupation ON id_occupation = occupation_directors;";
        
        $mysqli = self::DBConnect('sia');
        $mysqli->begin_transaction();
        $query = $mysqli->query($sql);
        $mysqli->commit();
        $mysqli->close();

        return $query;
    }
}
