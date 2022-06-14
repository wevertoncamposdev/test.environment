<?php

namespace app\dao;

use \app\model\database\Database;

class TeamDAO extends Database
{

    /** Método responsável obter dados do banco de dados 
     * @param string $query
     * @return string $result
     */
    public static function selectID($search, $auth)
    {
        $sql = "SELECT * FROM `crech964_sia`.`team` WHERE `id_team` = '$search'";

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
        $sql = "SELECT * FROM `crech964_sia`.`team` WHERE `name_team` LIKE '$search%' ORDER BY `name_team` ASC";

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

        $id = 'team_' . md5(rand(0, 9999));

        $mysqli = parent::DBConnect('sia');

        empty($post->status)?$post->status = false : $post->status = true;
        empty($post->public_status)?$post->public_status = false : $post->public_status = true;
        
        $sql = "INSERT INTO `crech964_sia`.`team` 
        (`id_team`, `name_team`,`occupation_team`, `birth_team`,`image_team`, `start_date_team`, `end_date_team`, `status_team`, `public_status_team`) 
        VALUES ('$id', '$post->name', '$post->occupation', '$post->birth', '$file', '$post->start_date', '$post->end_date', '$post->status', '$post->public_status');";

        if (mysqli_multi_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['message'] =  'Informações de Equipe registrado com sucesso!';
            return json_encode($result);
        } else {
            $result['status'] = false;
            $result['message'] =  'Erro ao registrar as informações de Equipe!';
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

        if (empty($post->status)) {$post->status = false;} else {$post->status = true;}
        if (empty($post->public_status)) {$post->public_status = false;} else {$post->public_status = true;}
        
        
        
        $sql = "UPDATE `crech964_sia`.`team` 
        SET 
        `name_team` = '$post->name',  
        `occupation_team` = '$post->occupation',
        `birth_team` = '$post->birth',
        `image_team` = '$file',
        `start_date_team` = '$post->start_date',
        `end_date_team` = '$post->end_date',
        `status_team` = '$post->status',
        `public_status_team` = '$post->public_status'
        
        WHERE (`id_team` = '$post->ID_UPDATE');";

        $mysqli = parent::DBConnect('sia');

        if (mysqli_multi_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['message'] =  'Informações do Equipe atualizado com sucesso!';
            return json_encode($result);
        } else {
            $result['status'] = false;
            $result['message'] =  'Erro ao atualizar as informações do Equipe';
            return json_encode($result);
        }
    }

    public static function delete($search, $auth)
    {
        $sql = "DELETE FROM `crech964_sia`.`team` WHERE (`id_team` = '$search');";

        $mysqli = self::DBConnect('sia');
        $mysqli->begin_transaction();
        if ($mysqli->query($sql)) {
            $result['status'] = true;
            $result['message'] =  'Informações de Equipe deletado com sucesso!';
            $mysqli->commit();
            $mysqli->close();
            return json_encode($result);
        }else{

            $result['status'] = false;
            $result['message'] =  'Erro ao deletar as informações de Equipe!';
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
        $sql = "SELECT * FROM crech964_sia.team INNER JOIN crech964_sia.occupation ON id_occupation = occupation_team;";
        //$sql = "SELECT * FROM `crech964_sia`.`team` WHERE `name_team` LIKE '$search%' ORDER BY `name_team` ASC";

        $mysqli = self::DBConnect('sia');
        $mysqli->begin_transaction();
        $query = $mysqli->query($sql);
        $mysqli->commit();
        $mysqli->close();

        return $query;
    }
}
