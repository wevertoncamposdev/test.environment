<?php

namespace app\dao;

use \app\model\database\Database;

class OccupationDAO extends Database
{

    /** Método responsável obter dados do banco de dados 
     * @param string $query
     * @return string $result
     */
    public static function selectID($search, $auth)
    {
        $sql = "SELECT * FROM `crech964_sia`.`occupation` WHERE `id_occupation` = '$search'";

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
        $sql = "SELECT * FROM `crech964_sia`.`occupation` WHERE `name_occupation` LIKE '$search%' ORDER BY `name_occupation` ASC";

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
    public static function insert($post, $auth)
    {
        $mysqli = parent::DBConnect('sia');
        $count = self::count() + 1;

        empty($post->status) ? $post->status = false : $post->status = true;
        empty($post->public_status) ? $post->public_status = false : $post->public_status = true;

        $sql = "INSERT INTO `crech964_sia`.`occupation` 
        (`id_occupation`,`name_occupation`,`description_occupation`, `status_occupation`, `public_status_occupation`) 
        VALUES ('$count','$post->name', '$post->description', '$post->status', '$post->public_status');";

        if (mysqli_multi_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['message'] =  'Informações de Ocupação registrado com sucesso!';
            return json_encode($result);
        } else {
            $result['status'] = false;
            $result['message'] =  'Erro ao registrar as informações de Ocupação!';
            return json_encode($result);
        }
    }

    /** Método responsável atualizar os dados
     * @param object $post
     * @param string $file
     * @param string $auth
     */
    public static function put($post, $auth)
    {

        empty($post->status) ? $post->status = false : $post->status = true;
        empty($post->public_status) ? $post->public_status = false : $post->public_status = true;

        $sql = "UPDATE `crech964_sia`.`occupation` 
        SET 
        `name_occupation` = '$post->name',  
        `description_occupation` = '$post->description',
        `status_occupation` = '$post->status',
        `public_status_occupation` = '$post->public_status'
        
        WHERE (`id_occupation` = '$post->ID_UPDATE');";

        $mysqli = parent::DBConnect('sia');

        if (mysqli_multi_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['message'] =  'Informações do Ocupação atualizado com sucesso!';
            return json_encode($result);
        } else {
            $result['status'] = false;
            $result['message'] =  'Erro ao atualizar as informações do Ocupação';
            return json_encode($result);
        }
    }

    public static function delete($search, $auth)
    {
        $sql = "DELETE FROM `crech964_sia`.`occupation` WHERE (`id_occupation` = '$search');";

        $mysqli = self::DBConnect('sia');
        $mysqli->begin_transaction();
        if ($mysqli->query($sql)) {
            $result['status'] = true;
            $result['message'] =  'Informações de Ocupação deletado com sucesso!';
            $mysqli->commit();
            $mysqli->close();
            return json_encode($result);
        } else {

            $result['status'] = false;
            $result['message'] =  'Erro ao deletar as informações de Ocupação!';
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
        $sql = "SELECT * FROM crech964_sia.occupation ORDER BY `name_occupation` ASC;";
        //$sql = "SELECT * FROM `crech964_sia`.`team` WHERE `name_team` LIKE '$search%' ORDER BY `name_team` ASC";

        $mysqli = self::DBConnect('sia');
        $mysqli->begin_transaction();
        $query = $mysqli->query($sql);
        $mysqli->commit();
        $mysqli->close();

        return $query;
    }

    /** Método responsável obter dados do banco 
     * @return object $query
     */
    public static function selectOption()
    {
        $sql = "SELECT * FROM crech964_sia.occupation ORDER BY `name_occupation` ASC;";

        $mysqli = self::DBConnect('sia');
        $mysqli->begin_transaction();
        $query = $mysqli->query($sql);
        $mysqli->commit();
        $mysqli->close();

        return $query;
    }

    public static function count()
    {
        $sql = "SELECT COUNT(*) as count FROM `crech964_sia`.`occupation`;";

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
