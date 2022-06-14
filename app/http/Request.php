<?php

namespace app\http;

class Request
{

    private $_DELETE = array();
    private $_PUT = array();


    /** Contrutor da classe 
     *  @param string $router
     */
    public function __construct($router)
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'PUT':
                parse_str(file_get_contents('php://input'), $_PUT);

                break;
            case 'DELETE':
                parse_str(file_get_contents('php://input'), $_DELETE);
                
                break;
        }

        $this->router = $router;
        $this->queryParams = $_GET ?? [];
        $this->postVars = $_POST ?? [];
        $this->putVars = $_PUT ?? [];
        $this->deleteVars = $_DELETE ?? [];
        $this->headers = getallheaders();
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->serUri();
    }

    /** Instancia do Router
     * @var Router
     */
    private $router;

    /** Método HTTP da requisição 
     * 
     * @var string
     */
    private $httpMethod;

    /** URI da página
     * 
     * @var string
     */
    private $uri;

    /** Parâmetros da URL [$_GET]
     * 
     * @var array
     */
    private $queryParams = [];

    /** Variáveis recebidas no POST da página [$_POST]
     * 
     * @var array
     */
    private $postVars = [];

    /** Variáveis recebidas no PUT da página
     * 
     * @var array
     */
    private $putVars = [];

    /** Variáveis recebidas no DELETE da página
     * 
     * @var array
     */
    private $deleteVars = [];

    /** Cabeçalho da requisição
     * 
     * @var array
     */
    private $headers = [];


    /** Método responsável por retornar a instancia de Router 
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /** Método responsável por definir a URI
     * 
     */
    private function serUri()
    {

        //URI COMPLETA (GETS)
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';

        //REMOVE GETS DA URI
        $xURI = explode("?", $this->uri);
        $this->uri = $xURI[0];
    }

    /** Método responsável por retornar o método HTTP da requisição 
     * @return string
     */
    public function getHTTPMethod()
    {
        return $this->httpMethod;
    }

    /** Método responsável por retornar o método URI da requisição 
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /** Método responsável por retornar os headers da requisição 
     * @return string
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /** Método responsável por retornar os parâmetros da URL da requisição 
     * @return string
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /** Método responsável por retornar as variáveis POST da requisição 
     * @return string
     */
    public function getPostVars()
    {
        return $this->postVars;
    }

    /** Método responsável por retornar as variáveis PUT da requisição 
     * @return string
     */
    public function getPutVars()
    {
        return $this->putVars;
    }

    /** Método responsável por retornar as variáveis DELETE da requisição 
     * @return string
     */
    public function getDeleteVars()
    {
        return $this->deleteVars;
    }
}
