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

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <title>PORTAL || TRANSPARÊNCIA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="PESQUISA DADOS SOBRE CORONAVIRUS NO PORTAL DA TRANSPARÊNCIA DO GOVERNO" name="PESQUISA DADOS SOBRE CORONAVIRUS NO PORTAL DA TRANSPARÊNCIA DO GOVERNO" />
    <meta content="Weverton Campos" name="Weverton Campos" />

    <!-- third party css -->
    <link href="assets/css/vendor/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
    <!-- third party css end -->

    <!-- third party css -->
    <link href="assets/css/vendor/dataTables.bootstrap5.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/responsive.bootstrap5.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/buttons.bootstrap5.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/select.bootstrap5.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/fixedHeader.bootstrap5.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/vendor/fixedColumns.bootstrap5.css" rel="stylesheet" type="text/css" />
    <!-- third party css end -->

    <!-- third party css -->
    <link href="assets/css/vendor/britecharts.min.css" rel="stylesheet" type="text/css" />

    <!-- Datatables css -->
    <link href="assets/css/vendor/buttons.bootstrap5.css" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />


</head>

<body class="show" data-layout-color="light" data-layout-mode="fluid" data-rightbar-onstart="true" data-leftbar-theme="dark">

    <section class="container mt-5">
        <h3 class="text-center">PESQUISA DE DADOS SOBRE CORONAVIRUS NO PORTAL DA TRANSPARÊNCIA DO GOVERNO</h3>
        <hr>
        <form method="post" action='/' class="d-flex justify-content-center">
            <div class="row d-flex justify-content-center">
                <div class="col-6 mb-3">
                    <input class="form-control" type="text" name="info[]" placeholder="AAAAMM">
                    <label for="date" class="">Exemplo: 202101</label>
                </div>
                <div class="col-6 mb-3">
                    <input class="form-control" type="number" value="" name="info[]" placeholder="Qtd de Página">
                    <label for="date" class="">Exemplo: 1, 10 ou 100</label>
                </div>
                <input class="form-control btn btn-success w-25" type="submit">
            </div>

        </form>
        <hr>

        <?php

        if (empty($_POST['info'][0])) {
        } else {
            $date = $_POST['info'][0];
            $page = $_POST['info'][1];
            $result = array();
            $maxValue = array();

            for ($i = 1; $i <= $page; $i++) {

                $url = "https://api.portaldatransparencia.gov.br/api-de-dados/coronavirus/transferencias?mesAno=$date&pagina=$i";
                $client = curl_init($url);
                $headers = ['chave-api-dados: b2ac369138bd866ba9b0a92f31bced84'];

                curl_setopt($client, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($client, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($client);
                $response = json_decode($response);
                foreach ($response  as $key => $value) {
                    array_push($result, (array)$value);
                    array_push($maxValue, $value->valor);
                }
            }
            $cont = array(2, 4, 5);
            $json_result = json_encode($result);
        }
        ?>

        <div class="d-flex justify-content-center">
            <h3>RESULTADOS</h3>
        </div>

        <div class="tab-content mb-5 mt-3">

            <div class="tab-pane show active" id="basic-datatable-preview">
                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>

                            <?php

                            if (isset($result)) {
                                $row = array();
                                foreach ($result as $row) {
                                    $row = $row;
                                }

                                foreach ($row as $key => $val) {
                                    echo ("<th>$key</th>");
                                }
                                //curl_close($client);
                            }
                            ?>

                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        if (isset($result)) {
                            foreach ($result as $row) {
                                echo "<tr>";
                                foreach ($row as $key => $val) {
                                    echo ("<td>$val</td>");
                                }
                                echo "</tr>";
                            }
                            curl_close($client);
                        }

                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="container mb-5">
        <div class="row">
            <div class="col-xl-12 h-100">
                <div class="card">
                    <div class="card-body">
                        <div dir="ltr">
                            <div id="basic-bar" class="apex-charts" data-colors="#39afd1"></div>
                        </div>
                    </div>
                    <!-- end card body-->
                </div>
                <!-- end card -->
            </div>
        </div>
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

    <!-- third party js -->
    <script src="assets/js/vendor/jquery.dataTables.min.js"></script>
    <script src="assets/js/vendor/dataTables.bootstrap5.js"></script>
    <script src="assets/js/vendor/dataTables.responsive.min.js"></script>
    <script src="assets/js/vendor/responsive.bootstrap5.min.js"></script>
    <script src="assets/js/vendor/dataTables.buttons.min.js"></script>
    <script src="assets/js/vendor/buttons.bootstrap5.min.js"></script>
    <script src="assets/js/vendor/buttons.html5.min.js"></script>
    <script src="assets/js/vendor/buttons.flash.min.js"></script>
    <script src="assets/js/vendor/buttons.print.min.js"></script>
    <script src="assets/js/vendor/dataTables.keyTable.min.js"></script>
    <script src="assets/js/vendor/dataTables.select.min.js"></script>
    <script src="assets/js/vendor/fixedColumns.bootstrap5.min.js"></script>
    <script src="assets/js/vendor/fixedHeader.bootstrap5.min.js"></script>
    <!-- third party js ends -->

    <!-- demo app -->
    <script src="assets/js/pages/demo.datatable-init.js"></script>
    <!-- end demo js-->

    <!-- third party:js -->
    <script src="assets/js/vendor/apexcharts.min.js"></script>
    <!-- <script src="assets/js/pages/demo.apex-bar.js"></script> -->
    <!-- third party end -->

    <script>
        let result = <?= $json_result ?>;

        let valor = []
        let data = []
        result.forEach((value) => {
            data.push(value['favorecido'])
            valor.push(value['valor'])
        })


        let el = document.getElementById('basic-bar');
        let optinons = {
            chart: {
                type: 'bar',
            },
            plotOptions: {
                bar: {
                    horizontal: true
                }
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                name: 'Valor',
                data: valor
            }],
            xaxis: {
                categories: data,
                labels: {
                    show: true
                }
            },
            yaxis:{
                labels: {
                    show: false
                }
            },
            title: {
                text: 'Maiores Valores',
                align: 'center',
                
            },
            subtitle: {
                text: 'Análise de maiores valores aprovados',
                align: 'center'
            }

        }
        let chart = new ApexCharts(el, optinons);
        chart.render();
    </script>



</body>

</html>