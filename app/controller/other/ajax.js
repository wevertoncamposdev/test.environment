/**
 * 
 * @WevertonCampos
 *  
 * Verifica o @buttom que está clicando 
 * Se esse id está correto ele faz a consulta no banco de dados e retorna o resultado. 
 * Após retornar o resultado ele altera o @attributo para não fazer mais a consulta ,
 * e manter as informações na página. evitando assim necessidade de varias @query
 * 
 * Clicando novamente no @buttom ele identifica que o @attributo foi alterado,
 * e não faz a consulta novamente
 * 
 * Esse @snippet pode servir futuramente para segurança, evintar consultado no banco com id alterada.
 *
 * 
*/

/* EVENTOS QUE OCORREM EM TODAS AS PÁGINAS */
/*$(document).ready(function () {

 
    $('#reload-insert').click(function () {
        window.location.reload();
    });

    $('#reload-upload').click(function () {
        window.location.reload();
    });

    $('#btn-search').click(function (event) {

        event.preventDefault();
        let value = $("#input-search").val(); 
        let url = window.location.origin + '/' + value.toLowerCase();
        window.location.href = url;

    });

    
    $('#btn-send').click(function (event) {

        event.preventDefault();
        let url = window.location.origin + '/send';
        var data = new FormData($('#form-send')[0]);

        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: url,
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 800000,
            beforeSend: function () {
                $('#send-result').html(

                    `
                    <div class="row d-flex justify-content-center text-center">
                        <div class="spinner-border text-primary mb-2" role="status"></div>
                        <div class="text-muted">Enviando dados...</div> 
                    </div>  
                    `
                )
            },
            success: function (result) {
                $("#send-result").html(`
                <div class="col-12 mb-2">
                    <div class="text-center">
                        <h2 class="mt-0">
                            <i class="mdi mdi-check-all"></i>
                        </h2>
                        <h3 class="mt-0">Parabéns!</h3>

                        <p class="w-75 mb-2 mx-auto">
                            Os dados foram enviados com sucesso!
                        </p>
                    </div>
                </div>
                <div class="modal-footer"></div>
                ${result}
                `);
            },
            error: function (e) {
                $("#send-result").html(e.responseText);
                console.log("ERROR : ", e);
            }
        });

    });

    $('#btn-put').click(function (event) {

        event.preventDefault();
        let url = window.location.origin + '/put';
        var data = new FormData($('#form-update')[0]);

        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: url,
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 800000,
            beforeSend: function () {
                $('#put-result').html(

                    `
                    <div class="row d-flex justify-content-center text-center">
                        <div class="spinner-border text-primary mb-2" role="status"></div>
                        <div class="text-muted">Atualizando dados...</div> 
                    </div>  
                    `
                )
            },
            success: function (result) {
                $("#put-result").html(`
                <div class="col-12 mb-2">
                    <div class="text-center">
                        <h2 class="mt-0">
                            <i class="mdi mdi-check-all"></i>
                        </h2>
                        <h3 class="mt-0">Parabéns!</h3>

                        <p class="w-75 mb-2 mx-auto">
                            Os dados foram atualizados com sucesso!
                        </p>
                    </div>
                </div>
                <div class="modal-footer"></div>
                ${result}
                `);
            },
            error: function (e) {
                $("#put-result").html(e.responseText);
                console.log("ERROR : ", e);
            }
        });


    });


    $('#btn-delete').click(function (event) {

        event.preventDefault();
        let url = window.location.origin + '/delete';
        var data = new FormData($('#form-update')[0]);

        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: url,
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 800000,
            beforeSend: function () {
                $('#delete-result').html(

                    `
                    <div class="row d-flex justify-content-center text-center">
                        <div class="spinner-border text-primary mb-2" role="status"></div>
                        <div class="text-muted">Excluindo dados...</div> 
                    </div>  
                    `
                )
            },
            success: function (result) {
                $("#delete-result").html(`
                <div class="col-12 mb-2">
                    <div class="text-center">
                        <h2 class="mt-0">
                            <i class="mdi mdi-check-all"></i>
                        </h2>
                        <h3 class="mt-0">Parabéns!</h3>

                        <p class="w-75 mb-2 mx-auto">
                            Os dados foram excluido com sucesso!
                        </p>
                    </div>
                </div>
                <div class="modal-footer"></div>
                ${result}
                `);
            },
            error: function (e) {
                $("#delete-result").html(e.responseText);
                console.log("ERROR : ", e);
            }
        });
    });

    $('#body').on('click', 'a', function (e) {

        let id = $(this).attr('id');
        let view = id.split('_');
        console.log(view);

        switch (view[0]) {

            case 'news':

                
                break;

            case 'events':


                break;

            case 'testimonials':


            case 'info':

                document.getElementById("id-update").value = id;
                document.getElementById("date-post").innerHTML = document.getElementById("date_post_" + id).innerHTML;
                document.getElementById("date-update").innerHTML = document.getElementById("date_update_" + id).innerHTML;

                let type = document.getElementById("type_" + id).innerHTML;
                $("#type_update option:contains(" + type + ")").attr('selected', 'selected');
                document.getElementById("title-update").value = document.getElementById("title_" + id).innerHTML;
                document.getElementById("author-update").value = document.getElementById("author_" + id).innerHTML;
                document.getElementById("message-update").innerHTML = document.getElementById("message_" + id).innerHTML;

                if (document.getElementById("status_" + id).innerHTML == 'SIM') {
                    document.getElementById("status-update").checked = true;
                } else {
                    document.getElementById("status-update").checked = false;
                }
                break;

            case 'directors':

                document.getElementById("id-update").value = id;

                document.getElementById("preview-update").src = document.getElementById("image_" + id).innerHTML;
                document.getElementById("image-update").value = document.getElementById("image_" + id).innerHTML;
                document.getElementById("name-update").value = document.getElementById("name_" + id).innerHTML;

                let occupation_directors = document.getElementById("id_occupation").innerHTML;
                $("#occupation_update option[value='" + occupation_directors + "']").attr('selected', 'selected');

                document.getElementById("birth_update").value = document.getElementById("birth_" + id).innerHTML;
                document.getElementById("start_date_update").value = document.getElementById("start_date_" + id).innerHTML;
                document.getElementById("end_date_update").value = document.getElementById("end_date_" + id).innerHTML;


                if (document.getElementById("status_" + id).innerHTML == 'SIM') {
                    document.getElementById("status_update").checked = true;
                } else {
                    document.getElementById("status_update").checked = false;
                }

                if (document.getElementById("public_status_" + id).innerHTML == 'SIM') {
                    document.getElementById("public_status_update").checked = true;
                } else {
                    document.getElementById("public_status_update").checked = false;
                }
                break;

            case 'team':

                document.getElementById("id_update").value = id;

                document.getElementById("preview_update").src = document.getElementById("image_" + id).innerHTML;
                document.getElementById("image_update").value = document.getElementById("image_" + id).innerHTML;

                document.getElementById("name_update").value = document.getElementById("name_" + id).innerHTML;

                let occupation = document.getElementById("id_occupation_" + id).innerHTML;
                $("#occupation_update option[value='" + occupation + "']").attr('selected', 'selected');

                document.getElementById("birth_update").value = document.getElementById("birth_" + id).innerHTML;
                document.getElementById("start_date_update").value = document.getElementById("start_date_" + id).innerHTML;
                document.getElementById("end_date_update").value = document.getElementById("end_date_" + id).innerHTML;

                if (document.getElementById("status_" + id).innerHTML == 'SIM') {
                    document.getElementById("status_update").checked = true;
                } else {
                    document.getElementById("status_update").checked = false;
                }

                if (document.getElementById("public_status_" + id).innerHTML == 'SIM') {
                    document.getElementById("public_status_update").checked = true;
                } else {
                    document.getElementById("public_status_update").checked = false;
                }

                break;

            case 'occupation':


            case 'sponsors':


            case 'providers':
                
               
                break;
        }


    })

});
 */

//MOSTRAR A IMAGEM AO INSERIR DADOS
/* document.getElementById("file-insert").addEventListener("change", function () {

    if (this.files && this.files[0]) {
        var file = new FileReader();
        file.onload = function (e) {
            document.getElementById("preview-insert").src = e.target.result;
        };
        file.readAsDataURL(this.files[0]);
    }

}, false); */

//MOSTRAR A IMAGEM AO ATUALIZAR DADOS
/* document.getElementById("file-update").addEventListener("change", function () {

    if (this.files && this.files[0]) {
        var file = new FileReader();
        file.onload = function (e) {
            document.getElementById("preview-update").src = e.target.result;
        };
        file.readAsDataURL(this.files[0]);
    }

}, false); */