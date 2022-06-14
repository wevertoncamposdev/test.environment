<?php

namespace app\http;

use \Closure;
use \Exception;
use \ReflectionFunction;

use \app\http\Response;
use \app\controller\pages\GetPage;
use \app\controller\pages\PageDinamic;

class Router
{

    /** Método responsável por iniciar a classe
     * @var string $url
     */
    public function __construct($url)
    {
        $this->request = new Request($this);
        $this->url = $url;
        $this->setPrefix();
    }

    /** Método responsável por executar a rota atual
     * @return Response
     * @param array $params
     */
    public function run()
    {
        try {

            //OBTEM A ROTA ATUAL
            $route = $this->getRoute();

            //VERIFICA O CONTROLADOR
            if (!isset($route['controller'])) {
                throw new Exception('A URL não pode ser processada', 500);
            }

            //ARGUMENTOS
            $args = [];

            //REFLECTION
            $reflection = new ReflectionFunction($route['controller']);

            //VALIDANDO OS DADOS DA PÁGINA DINÂMICA
            foreach ($reflection->getParameters() as $parameter) {
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? '';
            }

            //EXECUÇÃO DA FUNÇÃO
            return call_user_func_array($route['controller'], $args);
        } catch (Exception $e) {
            return new Response($e->getCode(), $e->getMessage());
        }
    }

    /** URL completa do projeto (raiz)
     * @var string
     */
    private $url = '';

    /** Prefixo de todas as rotas
     * @var string
     */
    private $prefix = '';

    /** Indice de rotas
     * @var array
     */
    private $routes = [];

    /** Instância de resquest
     * @var request
     */
    private $request = '';


    /** Método responsável por definir o prefixo das rotas
     * @var string $url
     */
    private function setPrefix()
    {

        //INFORMAÇÕES DA URL
        $parseUrl = parse_url($this->url);

        //DEFINE PREFIXO
        $this->prefix = $parseUrl['path'] ?? '';
    }

    /** Método responsável por adicionar uma rota na classe
     * @param string $method
     * @param string $route
     * @param array $params
     */
    private function addRoute($method, $route, $params = [])
    {

        //VALIDAÇÃO DOS PARÂMENTROS
        foreach ($params as $key => $value) {
            if ($value instanceof Closure) {
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }
        //VARIÁVEIS DA ROTA
        $params['variables'] = [];

        //PRADRÃO DE VALIDAÇÃO DAS VARIÁVEIS DAS ROTAS
        $patternVariable = '/{(.*?)}/';
        if (preg_match_all($patternVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, '(.*?)', $route);
            $params['variables'] = $matches[1];
        }

        //PADRÃO DE VALIDAÇÃO DA URL
        $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';

        //ADICIONA A ROTA DENTRO DA CLASSE
        $this->routes[$patternRoute][$method] = $params;
    }


    /** Método responsável por retornar URI desconsiderando o prefixo
     * @param string $route
     * @param array $params
     */
    private function getUri()
    {
        //URI DA RESQUEST
        $uri = $this->request->getUri();

        //EXPLODE O PREFIXO
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

        //RETORNA A URI SEM O PREFIXO
        return end($xUri);
    }

    /** Método responsável por retornar os dados da rota atual
     * @return array
     */
    private function getRoute()
    {

        //URI
        $uri = $this->getUri();

        //METHOD
        $HTTPMethod = $this->request->getHTTPMethod();

        //VALIDAR AS ROTAS
        foreach ($this->routes as $patternRoute => $methods) {

            //VERIFICA SE A URI ESTÁ DE ACORDO COM O PADRÃO
            if (preg_match($patternRoute, $uri, $matches)) {

                //VEIFICAR O MÉTODO
                if (isset($methods[$HTTPMethod])) {


                    //REMOVE A PRIMEIRA POSIÇÃO
                    unset($matches[0]);

                    //CHAVES
                    $keys = $methods[$HTTPMethod]['variables'];


                    //VARIÁVEIS PROCESSADAS
                    $methods[$HTTPMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$HTTPMethod]['variables']['request'] = $this->request;

                    //RETORNO DOS PARÂMETROS DA ROTA
                    return $methods[$HTTPMethod];
                }

                //MÉTODO NÃO PERMITIDO
                header('Location: /'); die;
                throw new Exception("O Método não é permitido", 405);
            }
        }

        //URL NÃO PERMITIDO
        header('Location: /'); die;
        throw new Exception("URL:  " . $uri . "  não foi encontrada", 404);

    }

    /** Método responsável por definir uma rota de GET
     * @param string $route
     * @param array $params
     */
    public function get($route, $params = [])
    {
        return $this->addRoute('GET', $route, $params);
    }

    /** Método responsável por definir uma rota de POST
     * @param string $route
     * @param array $params
     */
    public function post($route, $params = [])
    {
        return $this->addRoute('POST', $route, $params);
    }

    /** Método responsável por definir uma rota de PUT
     * @param string $route
     * @param array $params
     */
    public function put($route, $params = [])
    {
        return $this->addRoute('PUT', $route, $params);
    }

    /** Método responsável por definir uma rota de DELETE
     * @param string $route
     * @param array $params
     */
    public function delete($route, $params = [])
    {
        return $this->addRoute('DELETE', $route, $params);
    }

    /** Método responsável por retornar a url atual
     * @return string 
     */
    public function getCurrentUrl()
    {
        return $this->url . $this->getUri();
    }
}
