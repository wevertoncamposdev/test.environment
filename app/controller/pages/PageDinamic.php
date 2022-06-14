<?php
namespace app\controller\pages;

use \app\utils\View;
use \app\model\database\Database;
use \app\model\database\Pagination;

class PageDinamic extends RenderPage {

     /** Método responsável por retornar o conteúdo (view) solicitado pelo cliente
     * com dados consultados no db.
     * @param Request $request
     * @param string $view
     * @return string
     */
    public static function getPageWithData($request, $view)
    {   

        $content = View::render('pages/' . $view, '.html', [
            'itens' => self::getItens($request, $view, $pagination),
            'pagination' => parent::getPagination($request, $view, $pagination),
            'titulo' => $view,
        ]);
        
        return parent::getPage('page', $view, $content);
    }

     /** Método responsável por retornar os itens que contém no banco de dados 
     * @param Request $request
     * @param string $view
     * @param Pagination $paginação //&$paginação - referencia de memória
     * @return string
     */
    private static function getItens($request, $view, &$pagination) //getTestimonies //OK
    {
        $table = strtolower(rtrim($view, 's'));
        
        //OBTER QUANTIDADE TOTAL DE REGISTROS
        $count = Database::count($table);
        
        //OBTER PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        
        $currentPage = $queryParams['page'] ?? 1;
        
        //INSTANCIAR A PAGINAÇÃO
        if($view == 'Depoimentos'){
            $pagination = new Pagination($count, $currentPage, 4);
        }else{
            $pagination = new Pagination($count, $currentPage, 1);
        }
        
        $result = Database::consult($view, $pagination->getLimit()); //$pagination->getLimit()
        
        $itens = '';
        
        echo('<pre>'); print_r($result); echo('</pre>'); exit;
        while ($row = $result->fetch_assoc()) {
            
            //TRANTANDO NOME PARA APRESENTAR SOMENTE NOME E ULTIMO SOBRENOME
            if($view == 'Depoimentos'){
                $nome = $row['nome_' . $table];
                $nome = explode(" ", $nome);
                $nome = $nome[0].' '. $nome[count($nome)-1];
            }else{
                $nome = $row['nome_' . $table];
            }

            $itens .= View::render('pages/content/content_' . $view, '.html', [
                'id'   => $row['id_' . $table],
                'nome' => $nome,
                'data' => date('d/m/Y H:i:s', strtotime($row['data_' . $table])),
                'descricao' => $row['descricao_' . $table],
                'imagem' => $row['caminho_imagem'],
                'id_imagem' => $row['id_imagem'],
            ]);
            
        }
        
        return $itens;
    }
}