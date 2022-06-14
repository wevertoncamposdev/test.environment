<?php
namespace app\http;

class Response {

    /** Código do Status HTTP
     * @var integer
    */
    private $statusCode = 200;

    /** Cabeçalho do Response
     * @var array
    */
    private $headers = [];

    /** Tipo de conteúdo que está sendo retornado
     * @var string
    */
    private $contentType = 'text/html';

    /** Conteúdo do Response
     * @var mixed
    */
    private $content;

    /** Método responsável por iniciar a classe e definir os valores
     * @param integer $statusCode
     * @param mixed $content
     * @param string $contentType
     */
    public function __construct($statusCode, $content, $contentType = 'text/html'){

        $this->statusCode = $statusCode;
        $this->content = $content;
        $this->setContentType($contentType);

    }

    /** Método responsável por alterar o contentType do Response 
     * @param string $contentType
    */
    public function setContentType($contentType){
        $this->contentType = $contentType;  
        $this->addHeader('Content-Type', $contentType);
       
    }

    /** Método responsável por alterar o contentType do Response 
     * @param string $key
     *  @param string $value
    */
    public function addHeader($key, $value){
        $this->headers[$key] = $value; 
    }

    /** Método responsável por enviar os headers para o navegador
     * 
     * 
    */
    private function sendHeaders(){
        //STATUS CODE
        http_response_code($this->statusCode);

        //ENVIAR HEADERS
        foreach($this->headers as $key => $value){
            header($key .': '. $value);
        }
       
    }

    /** Método responsável por enviar a resposta para o usuário 
     * 
     */
    public function sendResponse(){
        
        //ENVIA OS HEADERS
        $this->sendHeaders();

        //IMPRIME O CONTEÚDO
        switch($this->contentType){
            case 'text/html':
                echo $this->content;
                exit;
        }
    }

}
