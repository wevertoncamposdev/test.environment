<?php

namespace app\dao;

use \app\model\database\Database;
use \app\dao\OccupationDAO;
class ProvidersDAO extends Database
{

    /** Método responsável obter dados do banco de dados 
     * @param string $query
     * @return string $result
     */
    public static function selectID($search, $auth)
    {
        $sql = "SELECT * FROM `crech964_sia`.`providers` WHERE `id_providers` = '$search'";

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

        $sql = "SELECT * FROM `crech964_sia`.`providers` WHERE `name_providers` LIKE '$search%' ORDER BY `start_date_providers` DESC";

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

    /** Método responsável consultar as ocupações do prestador
     * @param string $id
     * @return array $result
     */
    public static function selectProvidersHasOccupation($id)
    {

        $sql = "SELECT GROUP_CONCAT(ocupacao.id_occupation),  
        GROUP_CONCAT(ocupacao.name_occupation), 
        GROUP_CONCAT(ocupacao.status_occupation)
        FROM crech964_sia.providers AS prestador
        INNER JOIN crech964_sia.providers_has_occupation AS phc ON prestador.id_providers = phc.cod_providers
        INNER JOIN crech964_sia.occupation AS ocupacao ON ocupacao.id_occupation = phc.cod_occupation
        WHERE prestador.id_providers = '$id';";

        $mysqli = self::DBConnect('sia');
        $mysqli->begin_transaction();
        $query = $mysqli->query($sql);
        $mysqli->commit();
        $mysqli->close();

        $result = array();
        while ($row = $query->fetch_row()) {
            $result = $row;
        }

        return $result;
    }

    /** Método responsável inserir dados do banco de dados 
     * @param object $post
     * @param string $file
     * @param string $auth
     */
    public static function insert($post, $file, $auth)
    {
        $mysqli = parent::DBConnect('sia');

        /* Gerando ID a partir dos dados de contagem */
        $id = self::count('providers') + 1;

        /* Passando para Boolean */
        empty($post->status) ? $post->status = false : $post->status = true;
        empty($post->public_status) ? $post->public_status = false : $post->public_status = true;

        $resultProviders = self::insertProviders($id, $post, $file, $mysqli);
        $resultProvidersHasOccupation = self::insertProvidersHasOccupation($id, $post, $mysqli);
        $result = array_merge($resultProviders, $resultProvidersHasOccupation);

        return json_encode($result);
    }

    /** Método responsável por inserir dados de do prestador na tabela de prestador
     * @param int $id
     * @param object $post
     * @param string $file
     * @param object $mysqli
     */
    public static function insertProviders($id, $post, $file, $mysqli)
    {
        /* Data de Postagem */
        date_default_timezone_set('America/Sao_Paulo');
        $date_post = date("Y-m-d");

        /*
        Providers Columns:
        id_providers int(2) PK 
        name_providers varchar(100) 
        contact_providers varchar(45) 
        address_providers varchar(100) 
        birth_providers date 
        formation_providers varchar(100) 
        occupation_providers int(2) 
        image_providers varchar(100) 
        start_date_providers date 
        end_date_providers date 
        status_providers tinyint(4) 
        public_status_providers tinyint(4) 
        date_post_providers date 
        date_update_providers date 
        */

        $sql = "INSERT INTO `crech964_sia`.`providers` 
        (`id_providers`, 
        `name_providers`,
        `contact_providers`, 
        `address_providers`, 
        `birth_providers`, 
        `formation_providers`,  
        `image_providers`,
        `start_date_providers`, 
        `end_date_providers`, 
        `status_providers`, 
        `public_status_providers`,
        `date_post_providers`)
        VALUES (
            '$id', 
            '$post->name', 
            '$post->contact', 
            '$post->address', 
            '$post->birth',
            '$post->formation',
            '$file',   
            '$post->start_date', 
            '$post->end_date', 
            '$post->status', 
            '$post->public_status',
            '$date_post');";

        if (mysqli_query($mysqli, $sql)) {
            $result['status'] = true;
            $result['status_providers'] = true;
            $result['message_providers'] =  'As Informações de Prestador foram registrado com sucesso!';
            return $result;
        } else {
            $result['status'] = false;
            $result['status_providers'] = false;
            $result['message_providers'] =  'Erro ao registrar as informações de Prestador!';
            return $result;
        }
    }

          /** Método responsável por inserir dados de ocupação o prestador na tabela N:N
     * @param int $id
     * @param object $post
     * @param object $mysqli
     */
    public static function insertProvidersHasOccupation($id, $post, $mysqli)
    {
        /* Data de Postagem */
        date_default_timezone_set('America/Sao_Paulo');
        $date_post = date("Y-m-d");
        
        
        /* 
        providers_has_occupation Columns:
        id_providers_has_occupation int(2) PK 
        date_post date 
        date_update date 
        cod_providers int(2) 
        cod_occupation int(2) 
        start_date date 
        end_date date 
        status tinyint(4)
        */

        $result = array();
        foreach ($post->occupation as $occupation) {

            $id_providers_has_occupation = self::count('providers_has_occupation') + 1;

            $sql = "INSERT INTO `crech964_sia`.`providers_has_occupation` (
                `id_providers_has_occupation`,
                `date_post`,
                `cod_providers`, 
                `cod_occupation`, 
                `start_date`, 
                `status`) 
            VALUES ('$id_providers_has_occupation', '$date_post', '$id', '$occupation', '$date_post', '1');";

            if (mysqli_query($mysqli, $sql)) {
                $result['status'] = true;
                $result['status_occupation'] = true;
                $result['message_occupation'] =  'Informações de Ocupação do Prestador foi registrado com sucesso!';
            } else {
                $result['status'] = false;
                $result['status_occupation'] = false;
                $result['message_occupation'] =  'Erro ao registrar as informações de Ocupação do Prestador!';
            }
        };

        return  $result;
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

        $mysqli = parent::DBConnect('sia');
        $result = self::putProviders($post, $file, $mysqli);
        return json_encode($result);
    }

    public static function putProviders($post, $file, $mysqli)
    {

        /* Data de atualização */
        date_default_timezone_set('America/Sao_Paulo');
        $date_update = date("Y-m-d");

        /*
        Providers Columns:
        id_providers int(2) PK 
        name_providers varchar(100) 
        contact_providers varchar(45) 
        address_providers varchar(100) 
        birth_providers date 
        formation_providers varchar(100) 
        image_providers varchar(100) 
        start_date_providers date 
        end_date_providers date 
        status_providers tinyint(4) 
        public_status_providers tinyint(4) 
        date_post_providers date 
        date_update_providers date 
        */

        $sql = "UPDATE `crech964_sia`.`providers` 
        SET 
        `name_providers` = '$post->name',
        `contact_providers` = '$post->contact',
        `address_providers` = '$post->address',
        `birth_providers` = '$post->birth',
        `formation_providers` = '$post->formation',
        `image_providers` = '$file',
        `start_date_providers` = '$post->start_date',
        `end_date_providers` = '$post->end_date',
        `status_providers` = '$post->status',
        `public_status_providers` = '$post->public_status',
        `date_update_providers` = '$date_update'
        WHERE (`id_providers` = '$post->ID_UPDATE');";


        if (mysqli_query($mysqli, $sql)) {
            $mysqli->close();
            $result['status'] = true;
            $result['status_providers'] = true;
            $result['message_providers'] =  'As informações do Prestador foram atualizado com sucesso!';
            return $result;
        } else {
            $result['status'] = false;
            $result['status_providers'] = true;
            $result['message_providers'] =  'Erro ao atualizar as informações do Prestador';
            return $result;
        }
    }

    public static function putProviderHasOccupation(){
        
    }

    public static function delete($search, $auth)
    {
        $sql = "DELETE FROM `crech964_sia`.`providers` WHERE (`id_providers` = '$search');";

        $mysqli = self::DBConnect('sia');
        $mysqli->begin_transaction();

        if ($mysqli->query($sql)) {
            $result['status'] = true;
            $result['message'] =  'Informações de Prestador deletado com sucesso!';
            $mysqli->commit();
            $mysqli->close();
            return json_encode($result);
        } else {

            $result['status'] = false;
            $result['message'] =  'Erro ao deletar as informações de Prestador!';
            $mysqli->commit();
            $mysqli->close();
            return json_encode($result);
        }


    }

    public static function deleteProvidersHasOccupation($id, $post, $mysqli){

        /* Data de Postagem */
        date_default_timezone_set('America/Sao_Paulo');
        $date_post = date("Y-m-d");
        
        
        /* 
        providers_has_occupation Columns:
        id_providers_has_occupation int(2) PK 
        date_post date 
        date_update date 
        cod_providers int(2) 
        cod_occupation int(2) 
        start_date date 
        end_date date 
        status tinyint(4)
        */

        $result = array();
        foreach ($post->occupation as $occupation) {

            $id_providers_has_occupation = self::count('providers_has_occupation') + 1;

            $sql = "INSERT INTO `crech964_sia`.`providers_has_occupation` (
                `id_providers_has_occupation`,
                `date_post`,
                `cod_providers`, 
                `cod_occupation`, 
                `start_date`, 
                `status`) 
            VALUES ('$id_providers_has_occupation', '$date_post', '$id', '$occupation', '$date_post', '1');";

            if (mysqli_query($mysqli, $sql)) {
                $result['status'] = true;
                $result['status_occupation'] = true;
                $result['message_occupation'] =  'Informações de Ocupação do Prestador foi registrado com sucesso!';
            } else {
                $result['status'] = false;
                $result['status_occupation'] = false;
                $result['message_occupation'] =  'Erro ao registrar as informações de Ocupação do Prestador!';
            }
        };

        return  $result;
    }

    /** Método responsável obter dados do banco 
     * @param string $query
     * @return object $query
     */
    public static function selectTable($search, $auth)
    {
        $sql = "SELECT * FROM crech964_sia.providers";

        $mysqli = self::DBConnect('sia');
        $mysqli->begin_transaction();
        $query = $mysqli->query($sql);
        $mysqli->commit();
        $mysqli->close();

        return $query;
    }

    /** Método responsável fazer a contagem da quantidade dos dados
     * @param string $table
     * @return integer 
     */
    public static function count($table)
    {
        $sql = "SELECT COUNT(*) as count FROM `crech964_sia`.`$table`;";

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
