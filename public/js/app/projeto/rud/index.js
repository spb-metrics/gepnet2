function recuperarPastas(){
    $.ajax({
        url: url_recuperar_pastas,
        dataType: 'json',
        type: 'GET',
        async: true,
        cache: true,
        processData: true,
        data:{'idprojeto' : $('#idprojeto').val()} ,
        success: function(data) {
            if(data){
                var options = $("#nompasta");
                options.empty();
                options.append($("<option />").val("").text("Selecione"));
                $.each(data, function() {
                    options.append($("<option />").val(this).text(this));
                });
//                options.find('option[value='+ $num +']').attr('selected', 'selected');
            }
        },
        error: function() {
            $.pnotify({
                text: 'Falha ao enviar a requisição',
                type: 'error',
                hide: true
            });
        }
    });
}

function fileTree(){
    $('#fileTree').fileTree({
            root: $('#idprojeto').val() + '/',
            script: base_url + '/projeto/rud/file-tree' },
        function(file) {
//            alert(encodeURIComponent(file));
//            window.open(base_url + '/projeto/rud/download/file/' + file);
            window.open(base_url + '/projeto/rud/download/file/' + file.replace(/\//gi ,":!"));
//            window.open(base_url + '/projeto/rud/download/file/' + encodeURIComponent(file));
        });

}

function refreshTree(){
    $( "#fileTree" ).fadeOut( "slow", function() {
        fileTree();
    });
    $('#fileTree').fadeIn("slow");
}

$(function() {

    $.pnotify.defaults.history = false;

    fileTree();
    var
        $form = $("form#form-rud");
        $formPasta = $("form#form-rud-pasta");
        url_recuperar_pastas = base_url + "/projeto/rud/pesquisarjson";
        url_cadastrar = base_url + "/projeto/rud/add/format/json";
        url_criar_pasta = base_url + "/projeto/rud/addpasta/format/json";
        url_remover = base_url + "/projeto/rud/delete/format/json";
        url_download = base_url + "/projeto/rud/download/";

    $form.validate({
        errorClass: 'error',
        validClass: 'success',
        submitHandler: function(form) {
            var options = {
                url: url_cadastrar,
                dataType: 'json',
                type: 'POST',
                success: function(data) {
//                    if(typeof data.msg.text != 'string'){
//                        $.formErrors(data.msg.text);
//                        fileTree();
//                        return;
//                    }
                    $.pnotify(data.msg);
//                    if(data.success){
                        fileTree();
                        $("#resetbutton").trigger('click');
//                    }
                }
            };
            $form.ajaxSubmit(options);
        }
    });

    $formPasta.validate({
        errorClass: 'error',
        validClass: 'success',
        submitHandler: function(form) {
            var options = {
                url: url_criar_pasta,
                dataType: 'json',
                type: 'POST',
                success: function(data) {
//                    console.log('formPasta Submit');
                    $.pnotify(data.msg);
                    refreshTree();
                    recuperarPastas();
                    $('#resetbuttonpasta').trigger('click');
                }

            };
            $formPasta.ajaxSubmit(options);
        }
    });

    $("#excluir").on('click', function(){
        var selected = new Array();
        $('input:checked').each(function() {
            selected.push($(this).attr('name'));
        });

        if(selected.length == 0){
            $.pnotify({
                text: 'Selecione um arquivo/diretório.',
                type: 'error',
                hide: true
            });
            return false;
        }

        $.ajax({
            url: url_remover,
            dataType: 'json',
            type: 'POST',
            async: true,
            cache: true,
            processData: true,
            data:{'arquivos' : selected, 'idprojeto': $("#idprojeto").val()} ,
            success: function(data) {
                refreshTree();
                recuperarPastas();
                $('#resetbuttonpasta').trigger('click');
                $.pnotify(data.msg);
            },
            error: function(data) {
                $.pnotify(data.msg);
            }
        });


    });

    recuperarPastas();

});


