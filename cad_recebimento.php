<?php
     include "verifica_sessao.php";
?>
<Script Language="JavaScript">
function getStates(what) {
   if (what.selectedIndex != '') {
      var cod = what.value;
      document.location=('index.php?nomeArquivo=cad_recebimento.php&cod=' + cod);
   }
}

$(document).ready(function () {
	$("input[name='qtd_rec']").css({width:'65px',})
	$("#posto").change(function () {
//		alert($("#posto :selected").attr('value'));
		var tanque = $("#posto :selected").attr('value').split("|");
		$("input[name='capacidade']").attr("value",Math.floor(tanque[1]));
		$("input[name='qtd']").attr("value",Math.floor(tanque[2]));
		$("input[name='livre']").attr("value",Math.floor(tanque[3]));
		$("input[name='qtd_rec']").attr("max",Math.floor(tanque[4]));
				
		
/*		$("input[name='capacidade']").attr("value",Math.floor($("option:selected",this).attr("id")));
		$("input[name='qtd']").attr("value",Math.floor($("option:selected",this).attr("id")));
		$("input[name='livre']").attr("value",Math.floor($("option:selected",this).attr("id")));
		$("input[name='qtd_rec']").attr("max",Math.floor($("option:selected",this).attr("id")));*/
	})
	$("input[name='capacidade']").css({background:'transparent', border:'none', color:'red', fontSize:'14', fontWeight:'bold',})
	$("input[name='qtd']").css({background:'transparent', border:'none', color:'red', fontSize:'14', fontWeight:'bold',})
	$("input[name='livre']").css({background:'transparent', border:'none', color:'red', fontSize:'14', fontWeight:'bold',})

})

</Script>

<script language="JavaScript" type="text/javascript">
function mascara(dt_nota){
              var mdata = '';
              mdata = mdata + dt_nota;
              if (mdata.length == 2){
                  mdata = mdata + '/';
                  document.forms[0].dt_nota.value = mdata;
              }
              if (mdata.length == 5){
                  mdata = mdata + '/';
                  document.forms[0].dt_nota.value = mdata;
              }
              if (mdata.length == 10){
                  return true;
              }
          }
</script>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<?php

	echo "<form name='viatura' method='POST' action='index.php?nomeArquivo=gravar_recebimento.php' >";
	echo "<center><t1><br>Recebimento de Combustível<p></t1> </center>";
	echo "<table>";
	echo "<tr><td width='12%' align='right'>Combustível: </td></td><td><select name='combustivel' onChange='getStates(this);'>";
							echo "<option value='' selected></option>";
							/*Seleciona a relacao de combustiveis*/
							$result = mysql_query('select * from combustivel order by 1');
							while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
								$nome = $row[1];
								$codigo = $row[0];
								echo "<option value='$codigo' "; if ($_GET['cod']==$codigo){ echo 'SELECTED';} echo "> $nome </option>";
							}
							echo "</select></td></tr>";
	echo "<tr><td align='right'>Reservatório: </td><td><select name='reservatorio' id='posto'>";
						/*Seleciona a relação de reservatorios*/
						$result1 = mysql_query("SELECT r.codigo, o.sigla, r.capacidade, truncate(IFNULL( SUM( e.qtd_atual ) , 0 ),0) , truncate(r.capacidade - IFNULL( SUM( e.qtd_atual ) , 0 ),0) 
									FROM om o, reservatorio r
									LEFT JOIN estoque e ON ( r.codigo = e.reservatorio_codigo )
									WHERE o.codom = r.om_codom
										and o.codom = '".$_SESSION['codom']."'
										and r.combustivel_codigo='".$_GET['cod']."'
										and r.situacao = 'Ativo'
									GROUP BY r.codigo
									ORDER BY 1");
						$cont=1;
						while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
							echo "<option value='$row1[0]|$row1[2]|$row1[3]|$row1[4]' > Nº $row1[0] - $row1[1]";
							if($cont=='1') {
								$valor=$row1[2];
								$qtd = $row1[3];
								$livre = $row1[4];
							}
							$cont++;
						}
						echo "</select></td></tr>";
	echo "<tr><td align='right'>Capacidade: </td><td><input type='text' name='capacidade' size='5' dir='rtl' value='$valor' readonly><font color='red'> Lts</font></td></tr>";
	echo "<tr><td align='right'>Qtd Depositada: </td><td><input type='text' name='qtd' size='5' dir='rtl' value='$qtd' readonly><font color='red'> Lts</font></td></tr>";
	echo "<tr><td align='right'>Tancagem Livre: </td><td><input type='text' name='livre' size='5' dir='rtl' value='$livre' readonly><font color='red'> Lts</font></td></tr>";
	echo "<tr><td align='right'>Depósito na Cota da: </td><td><select name='oc'>";
 						
						/*Seleciona a relação de reservatorios*/
						$result2 = mysql_query("SELECT codom, sigla
									FROM om
									WHERE tipo = 'OC'
									ORDER BY sigla desc");
						while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
							echo "<option value='$row2[0]' "; if($row2[0]==$_SESSION['oc']) { echo "selected";} echo "> $row2[1] </option>";
						}
						echo "</select></td></tr>";
	echo "<tr><td align='right'>Nº Nota Fiscal: </td><td width='10%' align='left'><input type='text' size='10' maxlength='15' name='nota_fiscal'></td></tr>";
	echo "<tr><td align='right'>Data do Recebimento: </td><td width='20%' align='left'><input type='text' id='dt_ini' size='10' maxlength='10' name='dt_nota'></td></tr>";
	echo "<tr><td align='right'>Quantidade Recebida: </td><td width='15%' align='left'><input type='number' max='$livre' name='qtd_rec'><I> Litros</I></td></tr>";
	echo "<tr><td align='right'>Valor Unitário: </td><td width='15%' align='left'><input type='text' size='6' maxlength='10' name='valor'></td></tr>";
	echo "<tr><td align='right'>Nº do Contrato: </td><td width='15%' align='left'><input type='text' size='10' maxlength='10' name='contrato'></td></tr>";
	echo "<tr><td align='right'>Nº do Protocolo: </td><td width='15%' align='left'><input type='text' size='20' maxlength='20' name='nr_pedido'></td></tr>";
	echo "<tr><td align='right'>Nº da Remessa: </td><td width='15%' align='left'><input type='text' size='20' maxlength='20' name='nr_remessa'></td></tr>";
	echo "<input type='hidden' name='operacao' value='1'>";
	echo "<tr><td></td><td></td></tr>";
	echo "<tr><td align='right'><input type='submit' value= 'Cadastrar'></td><td align='left'><input type='reset' value= '  Limpar  '>&nbsp;<input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";	
   echo "</table>";
echo "</form>";
echo "</div>";

?>
