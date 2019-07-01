(function($) {
   verifica_form = function() {
      $('div[name="meh_sem_gelo"] input[type="submit"]').attr('disabled','disabled');
      $('div[name="meh_sem_gelo"] input[type="text"]').numeric();

      $('div[name="meh_sem_gelo"] input[type="text"]').keyup(function(){
         var valor_solicitado = $(this).val();
         var identificador = $(this).attr("id");
         var parte = identificador.split("_");
         var indice = parte[2];
         var valor_estoque = parseFloat($('#div_val_id_'+indice).text());
         var valor_disponivel = parseFloat( $('#div_disp_id_'+indice).text() );
         
         //alert(valor_estoque + " ; " + valor_disponivel); // DEPURACAO
         
         var valor_maior = valor_disponivel;
         var complemento = "quantidade disponivel!";
         if(valor_disponivel < valor_estoque)
         {
            valor_maior = valor_estoque;
            complemento = "quantidade em estoque!";
         }
         
         if(valor_solicitado > valor_maior)
         {
            alert("Quantidade solicitada maior que a " + complemento);
            $(this).val(valor_maior);
         }
         
         if($('#qt_solicit_'+indice).val() != "")
         {
            $('#gravar_'+indice).removeAttr('disabled');
            
         } else
         {
            $('#gravar_'+indice).attr('disabled','disabled');
         }
      });};
})(jQuery);

(function($) {
   filtra_numerico = function() {
      $('#form_alt_qt input[type="text"]').numeric();
      $('#form_alt_qt input[type="text"]').keyup(function(){
         var identificador = $(this).attr("id");
         var parte = identificador.split("_");
         var indice = parte[2];
         
         if($('#qt_alt_'+indice).val() == "")
         {
            $('#gravar_'+indice).attr('disabled','disabled');
            
         } else
         {
            $('#gravar_'+indice).removeAttr('disabled');
         }
         
      });
   };
})(jQuery);

(function($) {
   $(document).ready(function() {
      
      var exibicao = function(){
         $('.nova_os').css('display', 'none');
         var valor = $(this).val();
         $("#"+valor).css('display', 'block');
      }
      
      $('#viatura_eb_sel').keyup(exibicao);
      $('#viatura_eb_sel').change(exibicao);
      
      $('.nova_os').css('display', 'none');
      var valor = $('#viatura_eb_sel').val();
      $("#"+valor).css('display', 'block');
      
      // cadastro da pane
      
      $('.nova_pane').css('display', 'none');
      var teste = $("#eb_pane_sel :selected").text();
      $("#div_pane_" + teste).css('display', 'block');  // pane
      
      var exibicao_pane = function(){
         $('.nova_pane').css('display', 'none');
         //$('.nova_os_cad_pane').css('display', 'none');
         var valor = $(this).val();
         //$("#div_"+valor).css('display', 'block');       // viatura
         $("#div_pane_"+valor).css('display', 'block');  // pane
         //$("#div_os_"+valor).css('display', 'block');    // os
      }
      
      $('#eb_pane_sel').keyup(exibicao_pane);
      $('#eb_pane_sel').change(exibicao_pane);
      
      var clicado = false;
      $('#ocorrencia_pane').click(function(){
         if(!clicado)
         {
            $(this).attr("value", "");
            clicado = true;
         }
      });
      
      var clicado_kmw = false;
      $('#justificativa_kmw').click(function(){
         if(!clicado_kmw)
         {
            $(this).attr("value", "");
            clicado_kmw = true;
         }
      });
      
      var disp_marcado = $('#aval_kmw_disp').attr("checked");
      $('#msg_padrao').attr("disabled", !disp_marcado);
      
      $('#aval_kmw_disp').click(function(){
         $('#msg_padrao').attr("disabled", false);
         if(!clicado_kmw)
         {
            $('#justificativa_kmw').attr("value", "");
         }
      });
      
      $('#aval_kmw_indisp').click(function(){
//         $('#justificativa_kmw').attr("disabled", true);
         $('#msg_padrao').attr("disabled", true);
      });
      
      $('#tbl_manual').css('display', 'none');
      
      $('#manual_item_txt').click(function(){
         $('#tbl_bt').css('display', 'none');
         $('#tbl_manual').css('display', 'block');
      });
      
      $('#manual_item_botao').click(function(){
         $('#tbl_bt').css('display', 'block');
         $('#tbl_manual').css('display', 'none');
      });
      
      var mais_exibicao = function(){
         $('div[id ^= "oficina_"]').css('display', 'none');
         var valor = $(this).val();
         $("#oficina_"+valor).css('display', 'block');
      }
      
      $('#cod_oficina').keyup(mais_exibicao);
      $('#cod_oficina').change(mais_exibicao);
      
      // analise da pane
      
      var indice_disp = $("#val_disp").attr("selectedIndex");
      if(indice_disp == 0)
      {
         $("#motivo_disp").attr('disabled','disabled');
      }
      
      var exibicao_disp = function(){
         indice_disp = $("#val_disp").attr("selectedIndex");
         
         $("#motivo_disp").attr('disabled','disabled');
         
         if(indice_disp > 0)
         {
            $("#motivo_disp").removeAttr('disabled');
         }
      }
      
      $('#val_disp').keyup(exibicao_disp);
      $('#val_disp').change(exibicao_disp);
      
      var sel_analise = $('input:radio[name=modo_insercao]:checked').val();
      
      if(sel_analise == "Catalogo")
      {
         $('#tbl_analise_txt').css('display', 'none');
         $('#tbl_analise_catalogo').css('display', 'block');
      
      } else if(sel_analise == "Manualmente")
      {
         $('#tbl_analise_txt').css('display', 'block');
         $('#tbl_analise_catalogo').css('display', 'none');
      }
      
      $('#analise_catalogo').click(function(){
         $('#tbl_analise_txt').css('display', 'none');
         $('#tbl_analise_catalogo').css('display', 'block');
      });
      
      $('#analise_txt').click(function(){
         $('#tbl_analise_txt').css('display', 'block');
         $('#tbl_analise_catalogo').css('display', 'none');
      });
      
      $('#gfornec_sli input[type="checkbox"]').click(
         function()
         {
            var conteudo = $(this).val();
            var parte = conteudo.split("#");
            
            var identidade = 'gfornec_lin_' + parte.join('');
            
            $('#' + identidade).removeClass('marca_cbox');
            $('#' + identidade).addClass('linha_normal');
            
            if( $(this).attr('checked') )
            {
               $('#' + identidade).addClass('marca_cbox');
               $('#' + identidade).removeClass('linha_normal');
            }
         }
      );
      
      $('#sel_tudo').click(
         function()
         {
            var marcado = $('#sel_tudo').attr('checked');
            //$('input[type="checkbox"]').eq(3).attr('checked', marcado);
            $('input[id ^= "reg_estoq_"]').attr('checked', marcado);
         }
      );
      
   });
})(jQuery);

(function($) {
   $(document).ready(
      function()
      {
         $('tr[id ^= "lin_sup_os_"]').hide();
         $('a[id ^= "lnk_sup_os_"]').click(
            function(event)
            {
               event.preventDefault();
               
               var link = $(this).attr('href');
               var parte_geral = link.split('=');
               
               var tam_geral = parte_geral.length;
               
               var cod_componente = parte_geral[tam_geral - 1];
               
               var parte_om_codom = parte_geral[tam_geral - 2].split('&');
               
               var om_codom = parte_om_codom[0];
               
               var parte_ano_os = parte_geral[tam_geral - 3].split('&');
               
               var ano_os = parte_ano_os[0];
               
               var parte_nr_os = parte_geral[tam_geral - 4].split('&');
               
               var nr_os = parte_nr_os[0];
               
               $('tr[id ^= "lin_sup_os_"]').hide();
               $('#lin_sup_os_' + nr_os + ano_os + om_codom + cod_componente).show();
            }
         );
         
      }
   );
})(jQuery);
