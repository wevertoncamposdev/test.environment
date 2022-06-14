<?php

namespace app\common;

class Environment
{

    /** Método responsável por carregar as variáveis de ambiente do projeto 
     * @param string $dir Caminho absoluto do arquivo .env
     */
    public static function load()
    {
        //VERIFICA SE O ARQUIVO .ENV EXISTE
        if (!file_exists(__DIR__ . '/.env')) {
            return false;
        }

        //DEFINE AS VARIÁVEIS DE AMBIENTE
        $lines = file(__DIR__ . '/.env');
        foreach ($lines as $line) {
            putenv(trim($line));
        }
    }
}
