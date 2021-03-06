/**
 * Comment
 */
function selectRow(row) {
    //console.log(row);
    $('.input-selecionado')
        .find('input:hidden').val(row.idpessoa).trigger('blur')
        .end()
        .find('input:text').val(row.nompessoa).trigger('blur');
}

$(function() {

    $("#accordion").accordion();

    $.pnotify.defaults.history = false;

    $("#tabs").tabs();

    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        language: 'pt-BR'
    });

    $('.mask-tel').mask("(99) 9999-9999");

    //$('.externo').hide();

    $('.origem').click(function() {
        context = $(this).data('cont');
        $('.cont').hide().find('input,select').hide().prop('disabled', true);
        $('.' + context).show().find('select,input:text,input:hidden').fadeIn('slow').prop('disabled', false);
        $('.origem').removeClass('active');
        $('.origem-' + context).addClass('active');
    });

    $('.origem:first').trigger('click');

    $("#btn-adicionar-externo").click(function() {
        $("form#form-parte-externo").submit();
    });

    $("#btn-adicionar").click(function() {
//        console.log($("form#form-parte").serialize());
        $("form#form-parte").submit();
//        $.ajax({
//            url: base_url + "/projeto/tap/partesinteressadas/format/json",
//            dataType: 'json',
//            type: 'POST',
//            data: $("form#form-parte").serialize(),
//            success: function(data) {
//                $.pnotify(data.msg);
//                if (data.success) {
//                    $.adicionar(data.parte);
//                }
//
//            },
//            error: function() {
//                $.pnotify({
//                    text: 'Falha ao enviar a requisição',
//                    type: 'error',
//                    hide: false
//                });
//            }
//        });
    });

    $(document.body).on('click', '.classeDoSeuBotao', function(event) {
        event.preventDefault();
//        console.log('excluir');

    });

    $.adicionar = function(parte) {
        var $row = "<tr class='success'>" +
                "<td><a class='btn actionfrm excluir excluirbutton' title='Excluir Interessado' data-id='" + parte.idparteinteressada + "' >" +
                "<i class='icon-trash'></i>" +
                "</a></td>" +
                "<td>" + parte.nomparteinteressada + "</td>" +
                "<td>" + parte.nomfuncao + "</td>" +
                "<td>" + parte.destelefone + "</td>" +
                "<td>" + parte.desemail + "</td>" +
                "<td>" + parte.domnivelinfluencia + "</td>" +
                "</tr>";
        $("#listagemInteressados")
                .removeClass('hide')
                .find("table tbody").prepend($row);
        $("#nenhumregistro").hide();
    };


    $(document.body).on("click", ".excluirbutton", function() {
        var $this = $(this);
        $.ajax({
            url: base_url + "/projeto/tap/excluirparte/format/json/id/" + $this.data('id'),
            dataType: 'json',
            type: 'POST',
            data: {
                id: $this.data('id')
            },
            success: function(data) {
//                console.log('success!');
//                console.log(data);
                $.pnotify(data.msg);
                if (data.success) {
                    
//                    window.location.reload(true);
                    if($("#listagemInteressados").find("table tr:visible").length <= 2){
                        $this.parent().parent().hide();
                        $("#nenhumregistro").show();
                    } else {
                        $this.parent().parent().fadeOut();
                    }
                }

            },
            error: function() {
                $.pnotify({
                    text: 'Falha ao enviar a requisição',
                    type: 'error',
                    hide: false
                });
            }
        });
    });
    
    var $form = $("form#form-parte");

    $form.validate({
        errorClass: 'error',
        validClass: 'success',
        submitHandler: function(form) {
            enviar_ajax("/projeto/tap/partesinteressadas/format/json", "form#form-parte", function(data) {
//                if (data.success) {
                if (data.success) {
                    $.adicionar(data.parte);
                }
                    //window.location.href = base_url + "/projeto/tap/informacoesiniciais/idprojeto/" + data.dados.idprojeto;
                    //$("#resetbutton").trigger('click');
//                }
            });
            //console.log('enviando');
        }
    });
    
    var $formExterno = $("form#form-parte-externo");
    $formExterno.validate({
        errorClass: 'error',
        validClass: 'success',
        submitHandler: function(formExterno) {
            enviar_ajax("/projeto/tap/partesinteressadasexterno/format/json", "form#form-parte-externo", function(data) {
                if (data.success) {
                    $.adicionar(data.parte);
                }
            });
        }
    });
    
     $(".pessoa-button").on('click', function(event) {
        event.preventDefault();
        $(this).closest('.container-pessoa').find('.control-group').removeClass('input-selecionado');
        $(this).closest('.control-group').addClass('input-selecionado');
        if ($("table#list-grid-pessoa").length <= 0) {
            $.ajax({
                url: base_url + "/cadastro/pessoa/grid",
                type: "GET",
                dataType: "html",
                success: function(html) {
                    $(".grid-append").append(html).slideDown('fast');
                }
            });
            $('.pessoa-button')
                .off('click')
                .on('click',function() {
                    var $this = $(this);
                    $(".grid-append").slideDown('fast', function(){
                        $this.closest('.container-pessoa').find('.control-group').removeClass('input-selecionado');
                        $this.closest('.control-group').addClass('input-selecionado');
                    });
                });
        } 
    });
});