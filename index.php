<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8" />
    <title>PORTAL || TRANSPARÊNCIA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="PESQUISA DADOS SOBRE CORONAVIRUS NO PORTAL DA TRANSPARÊNCIA DO GOVERNO" name="PESQUISA DADOS SOBRE CORONAVIRUS NO PORTAL DA TRANSPARÊNCIA DO GOVERNO" />
    <meta content="Weverton Campos" name="Weverton Campos" />

    <!-- third party css -->
    <link href="assets/css/vendor/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
    <!-- third party css end -->

    <!-- App css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

</head>

<body class="show" data-layout-color="light" data-layout-mode="fluid" data-rightbar-onstart="true" data-leftbar-theme="dark">

    <section class="container mt-5">
        <h3>PESQUISA DADOS SOBRE CORONAVIRUS NO PORTAL DA TRANSPARÊNCIA DO GOVERNO</h3>
        <hr>
        <form method="post" action='/' class="d-flex justify-content-center">
            <div class="row d-flex justify-content-center">
                <div class="col-6">
                    <input class="form-control" type="text" name="date" placeholder="AAAAMM">
                    <label for="date" class="">Exemplo: 202101</label>
                </div>
                <div class="col-6">
                    <input class="form-control" type="number" value="" name="page" placeholder="Qtd de Página">
                    <label for="date" class="">Exemplo: 1, 10 ou 100 páginas</label>
                </div>
                <input class="form-control btn btn-success w-25" type="submit">
            </div>

        </form>

        <?php

        /** PORTAL DA TRANSPARÊNCIA
         * https://portaldatransparencia.gov.br/api-de-dados/cadastrar-email
         * https://transparencia.gov.br/
         * 
         * 
         * https://portaldatransparencia.gov.br/api-de-dados
         * 
         * 
         * https://api.portaldatransparencia.gov.br/swagger-ui.html
         * 
         * Acesso a API: {"key":"chave-api-dados","value":"b2ac369138bd866ba9b0a92f31bced84"}
         * 
         */
        if (empty($_POST['date'])) {
            die();
        } else {
            $date = $_POST['date'];
            $page = $_POST['page'];

            for ($i = 1; $i <= $page; $i++) {

                $url = "https://api.portaldatransparencia.gov.br/api-de-dados/coronavirus/transferencias?mesAno=$date&pagina=$i";
                $client = curl_init($url);
                $headers = ['chave-api-dados: b2ac369138bd866ba9b0a92f31bced84'];

                curl_setopt($client, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($client, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($client);
                $result = json_decode($response);
        ?>
                <hr>
                <h3>RESULTADOS</h3>

        <?php
                foreach ($result as $row) {

                    echo ('<pre>');
                    print_r($row);
                    echo ('</pre>');
                }

                //curl_close($client);
            }
        }
        ?>
    </section>
    <!-- bundle -->
    <script src="assets/js/vendor.min.js"></script>
    <script src="assets/js/app.min.js"></script>

    <!-- Dropzone js -->
    <script src="assets/js/vendor/dropzone.min.js"></script>

    <!-- File upload js -->
    <script src="assets/js/ui/component.fileupload.js"></script>

    <!-- Dropzone js -->
    <script src="assets/js/vendor/dropzone.js"></script>



</body>

</html>