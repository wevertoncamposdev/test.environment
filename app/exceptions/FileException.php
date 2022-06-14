<?php

namespace app\exceptions;

class FileException extends \Exception
{
    public static function image()
    {   

        if (isset($_FILES['file']) && !empty($_FILES['file']['name'][0])) {
            self::files(); //VERIFICANDO ENVIOS DE IMAGEM UNICA
        }


        if (isset($_FILES['more_files']) && !empty($_FILES['more_files']['name'][0])) {
            self::moreFiles(); //VERIFICANDO ENVIOS DE MAIS IMAGENS
        }

        
    }

    public static function files(){

        //TIPOS DE ARQUIVO SUPORTADOS
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION); //EXTENSÃO DO ARQIVO ENVIADO
        $validate = array('png', 'PNG', 'jpg', 'JPG', 'jpeg', '');

        //VERIFICANDO O TIPO DE ARQUIVO
        if (!in_array($ext, $validate)) {

            throw new \Exception("Tipo de mídia não suportado! ", 415);

            /* 415 Tipo de mídia não suportado
                O código de status 415 (Tipo de mídia não suportado) indica que o
                servidor de origem está se recusando a atender a solicitação porque a carga útil
                está em um formato não suportado por este método no recurso de destino.
                O problema de formato pode ser devido aos pedidos indicados
                Content-Type ou Content-Encoding, ou como resultado da inspeção do
                dados diretamente.*/
        }

        //VERIFICANDO O TAMANHO DO ARQUIVO MÁX -> 12MB (1024*(1024*12))
        if ($_FILES['file']['size'] > 12582912) {

            throw new \Exception("Carga Muito Grande! ", 413);

            /* 413 Carga Muito Grande
                O código de status 413 (Payload Too Large) indica que o servidor está
                recusando-se a processar uma solicitação porque a carga útil da solicitação é maior
                do que o servidor está disposto ou é capaz de processar. O servidor PODE fechar
                a conexão para evitar que o cliente continue a solicitação.

                Se a condição for temporária, o servidor DEVE gerar um
                Campo de cabeçalho Retry-After para indicar que */
        }

        //VERIFICANDO O TIPO DE ERROR
        switch ($_FILES['file']['error']) {
            case 1:
                throw new \Exception("O arquivo enviado excede o limite definido na diretiva upload_max_filesize do php.ini", 413);
            case 2:
                throw new \Exception("O arquivo excede o limite definido em MAX_FILE_SIZE no formulário HTML", 413);
            case 3:
                throw new \Exception("O upload do arquivo foi feito parcialmente.", 400);
            case 5:
                throw new \Exception("não houve erro, o upload foi bem sucedido.", 200);
            case 6:
                throw new \Exception("Pasta temporária ausente.", 400);
            case 7:
                throw new \Exception("Falha ao escrever o arquivo no disco.", 400);
            case 8:
                throw new \Exception("Uma extensão do PHP interrompeu o upload do arquivo. O PHP não fornece uma maneira de determinar qual extensão causou a interrupção do upload. Examinar a lista das extensões carregadas com o phpinfo() pode ajudar.", 400);
        }

    }

    public static function moreFiles()
    {

        //echo('<pre>'); print_r($_FILES['more_files']); echo('</pre>');
        $files = $_FILES['more_files'];
        $validate = array('png', 'PNG', 'jpg', 'JPG', 'jpeg', '');

        //VERIFICANDO O TIPO DE ARQUIVO
        foreach ($files['name'] as $file) {

            //TIPOS DE ARQUIVO SUPORTADOS
            $ext = pathinfo($file, PATHINFO_EXTENSION); //EXTENSÃO DO ARQIVO ENVIADO

            //VERIFICANDO O TIPO DE ARQUIVO
            if (!in_array($ext, $validate)) {

                throw new \Exception("Tipo de mídia não suportado! ", 415);

                /* 415 Tipo de mídia não suportado
                    O código de status 415 (Tipo de mídia não suportado) indica que o
                    servidor de origem está se recusando a atender a solicitação porque a carga útil
                    está em um formato não suportado por este método no recurso de destino.
                    O problema de formato pode ser devido aos pedidos indicados
                    Content-Type ou Content-Encoding, ou como resultado da inspeção do
                    dados diretamente.
                    */
            }
        }

        //VERIFICANDO O TAMANHO DO ARQUIVO MÁX -> 12MB (1024*(1024*12))
        foreach ($files['size'] as $file) {

            //VERIFICANDO O TAMANHO DO ARQUIVO MÁX -> 12MB (1024*(1024*12))
            if ($file > 12582912) {

                throw new \Exception("Carga Muito Grande! ", 413);

                /* 413 Carga Muito Grande
                        O código de status 413 (Payload Too Large) indica que o servidor está
                        recusando-se a processar uma solicitação porque a carga útil da solicitação é maior
                        do que o servidor está disposto ou é capaz de processar. O servidor PODE fechar
                        a conexão para evitar que o cliente continue a solicitação.

                        Se a condição for temporária, o servidor DEVE gerar um
                        Campo de cabeçalho Retry-After para indicar que 
                    */
            }
        }

        //VERIFICANDO O TIPO DE ERROR
        foreach ($files['error'] as $file) {

            //VERIFICANDO O TIPO DE ERROR
            switch ($file) {
                case 1:
                    throw new \Exception("O arquivo enviado excede o limite definido na diretiva upload_max_filesize do php.ini", 413);
                case 2:
                    throw new \Exception("O arquivo excede o limite definido em MAX_FILE_SIZE no formulário HTML", 413);
                case 3:
                    throw new \Exception("O upload do arquivo foi feito parcialmente.", 400);
                case 5:
                    throw new \Exception("não houve erro, o upload foi bem sucedido.", 200);
                case 6:
                    throw new \Exception("Pasta temporária ausente.", 400);
                case 7:
                    throw new \Exception("Falha ao escrever o arquivo no disco.", 400);
                case 8:
                    throw new \Exception("Uma extensão do PHP interrompeu o upload do arquivo. O PHP não fornece uma maneira de determinar qual extensão causou a interrupção do upload. Examinar a lista das extensões carregadas com o phpinfo() pode ajudar.", 400);
            }
        }

    }

    /** Método responsável por verificar se o houve erro no armazenamento dos arquivos 
     * @param  $file
    */
    public static function missingInformationFile($file)
    {
        
        if ($file->status == false) {

            throw new \Exception('Erro interno do servidor!', 500);

            /* 500 Erro interno do servidor
                O código de status 500 (Internal Server Error) indica que o servidor
                encontrou uma condição inesperada que o impediu de cumprir
                o pedido. */
        }
    }

}
