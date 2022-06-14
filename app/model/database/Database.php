<?php

namespace app\model\database;

use \app\common\Environment;
use Mysqli;
use Mysqli\Exception;

/** Comando interessantes
 * mysqli_insert_id — Retorna o id gerado automaticamente na última consulta
 * mysqli::change_user — Modifica o usuário para a conexão com o banco de dados especificada
 * mysqli_field_count — Retorna o número de colunas para a consulta mais recente
 * mysqli::get_client_info — Retorna a versão do cliente MySQL como uma string
 * mysqli::get_client_version — Obtém informação sobre o cliente MySQL
 * mysqli::get_connection_stats — Returns statistics about the client connection
 * mysqli_get_host_info — Retorna uma string representando o tipo da conexão usada
 * mysqli->protocol_version — Retorna a versão do protocolo MySQL usada
 * mysqli->server_info — Retorna a versão do servidor MySQL
 * mysqli->server_version — Retorna a versão do servidor MySQL como um integer
 * mysqli::kill — Solicita ao servidor o encerramento de um thread do MySQL
 * mysqli::more_results — Verifica se há mais algum resultado de uma multi query
 * mysqli::multi_query — Executa uma consulta no banco de dados
 * mysqli::next_result — Prepara o próximo resultado de multi_query
 * mysqli::options — Configura opções
 * mysqli::ping — Faz ping em uma conexão de servidor ou tenta reconectar se a conexão caiu
 * mysqli::prepare — Prepara uma instrução SQL para execução
 * mysqli::release_savepoint — Remove o savepoint nomeado do conjunto de savepoints da transação atual
 * mysqli::rollback — Reverte a transação atual
 * mysqli::savepoint — Defina um ponto de salvamento de transação nomeado
 * mysqli::select_db — Seleciona o banco de dados padrão para consultas de banco de dados
 * 
 * 
 * mysqli_result::fetch_all — Busca todas as linhas de resultado como um array associativo, um array numérico ou ambos
 * mysqli_fetch_array — Obtem uma linha do resultado como uma matriz associativa, numérica, ou ambas
 * mysqli_result::fetch_assoc — Obtem uma linha do conjunto de resultados como uma matriz associativa
 * mysqli_result::fetch_column — Busca uma única coluna da próxima linha de um conjunto de resultados
 * mysqli_fetch_object — Retorna a linha atual do conjunto de resultados como um objeto
 * mysqli_result::fetch_row — Obtém uma linha do resultado como uma matriz numerada
 * mysqli_result::$field_count — Obtém o número de campos no conjunto de resultados
 * mysqli_result::$num_rows — Obtém o número de linhas no conjunto de resultados
 */

class Database
{
    /** Método responsável por conectar com o banco de dados 
     * 
     * @param string $db
     * @return object $mysqli
     */
    public static function DBConnect()
    {
        //BUSCANDO VARIÁVEIS DE AMBIENTE
        Environment::load();

        $db = 'LOCAL';
        //$db = 'SIA';

        $host = getenv('DB_HOST_' . $db);
        $user = getenv('DB_USER_' . $db);
        $password = getenv('DB_PASSWORD_' . $db);
        $database = getenv('DB_DATABASE_' . $db);
        $port = getenv('DB_PORT_' . $db);


        $mysqli = new mysqli($host, $user, $password, $database, $port);
        mysqli_set_charset($mysqli, "utf8");

        return $mysqli;
    }


    /** Método responsável por registrar no banco de dados 
     * 
     * @param string $vars
     * @return string $result
     */
    public static function register($auth)
    {
        // DEFINE DATA - FAZER DIRETO NO BANCO DE DADOS POSTERIORMENTE
        date_default_timezone_set('America/Sao_Paulo');
        $date =  date("Y-m-d H:i:s");

        //DECODE VARIÁVEIS DA REQUEST POST
        $auth = json_decode($auth);

        //VERIFICAR SE O USUÁRIO JÁ EXISTE NO BANCO DE DADOS
        $verify = self::verify($auth->name, $auth->email);

        if ($verify === true) {

            $result['status'] = false;
            $result['message'] =  'Este usuário já exite!';
            return json_encode($result);
        } else {

            $password = md5($auth->password);

            $sql = "INSERT INTO crech964_sia.user (`name_user`, `email_user`, `contact_user`, `password_user`, `type_user`, `date_user`) 
            VALUES ('$auth->name', '$auth->email', '$auth->phone', '$password', '$auth->type', '$date');";

            $mysqli = self::DBConnect('sia');

            if (mysqli_multi_query($mysqli, $sql)) {
                $mysqli->close();
                $result['status'] = true;
                $result['message'] =  'Usuário cadastrado com sucesso!';
                return json_encode($result);
            } else {
                $result['status'] = false;
                $result['message'] =  'Erro ao registrar!';
                return json_encode($result);
            }
        }
    }

    /** Método responsável por atualizar o usuário no banco de dados 
     * 
     * @param string $vars
     * @return string $result
    */
    public static function update($auth)
    {
        // DEFINE DATA - FAZER DIRETO NO BANCO DE DADOS POSTERIORMENTE
        date_default_timezone_set('America/Sao_Paulo');
        $date =  date("Y-m-d H:i:s");

        //DECODE VARIÁVEIS DA REQUEST POST
        $auth = json_decode($auth);

        //VERIFICAR SE O USUÁRIO JÁ EXISTE NO BANCO DE DADOS
        $verify = self::verify($auth->name, $auth->email);

        if ($verify === true) {

            $password = md5($auth->password);
            $sql = "UPDATE `crech964_sia`.`user` SET 
            `name_user` = '$auth->name', 
            `email_user` = '$auth->email', 
            `contact_user` = '$auth->phone', 
            `password_user` = '$password' WHERE (`email_user` = '$auth->email');";

            $mysqli = self::DBConnect('sia');

            if (mysqli_multi_query($mysqli, $sql)) {
                $mysqli->close();
                $result['status'] = true;
                $result['message'] =  'Usuário atualizado com sucesso!';
                return json_encode($result);
            } else {
                $result['status'] = false;
                $result['message'] =  'Erro ao atualizar!';
                return json_encode($result);
            }
        } else {

            $result['status'] = false;
            $result['message'] =  'Este usuário não exite!';
            return json_encode($result);
        }
    }

    /** Método responsável por autenticar usuário 
     * @param string $vars
     * @return object $result
     */
    public static function auth($auth)
    {
        $auth = json_decode($auth);
        $sql = "SELECT * FROM `crech964_sia`.`user` WHERE email_user = '$auth->email';";
        $mysqli = self::DBConnect('sia');
        $query = mysqli_query($mysqli, $sql);

        if ($query->num_rows == 0) {
            $mysqli->close();
            $result['status'] = false;
            $result['message'] = 'Este email não está cadastrado!';
            $result = json_encode($result);
            return json_decode($result);
        }

        while ($row = $query->fetch_assoc()) {

            switch (true) {

                case $row['password_user'] !== md5($auth->password):
                    $mysqli->close();
                    $result['status'] = false;
                    $result['message'] = 'Senha invalida!';
                    $result = json_encode($result);
                    return json_decode($result);

                case $row['email_user'] == $auth->email && $row['password_user'] == md5($auth->password):
                    $mysqli->close();
                    $result['status'] = true;
                    $result['name'] = $row['name_user'];
                    $result['email'] = $row['email_user'];
                    $result['type'] = $row['type_user'];
                    $result = json_encode($result);
                    return json_decode($result);
            }
        }
    }

    /** Método responsável por verificar se o usuário já existe no banco de dados
     * @param string $email
     */
    public static function verify($name, $email)
    {

        $sql = "SELECT name_user, email_user FROM crech964_sia.user WHERE name_user = '$name' OR email_user = '$email'";
        $mysqli = self::DBConnect();
        $query = mysqli_query($mysqli, $sql);
        $rows = $query->num_rows;

        switch ($rows) {
            case 0:
                return false;
                break;
            case ($rows > 0):

                while ($row = $query->fetch_assoc()) {
                    if ($row['name_user'] ===  $name) {
                        $mysqli->close();
                        return true;
                    }
                    if ($row['email_user'] ===  $email) {
                        $mysqli->close();
                        return true;
                    } else {
                        $mysqli->close();
                        return false;
                    }
                }
                break;
        }
    }
}
