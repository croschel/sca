<?php
	include "verifica_sessao.php";

	/*Seeleciona os dados do recebimento*/
	$query="select r.nr_nota_fiscal, r.combustivel_codigo, c.nome, r.dt_rec, r.qtd_rec, r.combustivel_codigo, r.nr_pedido, r.nr_remessa, r.reservatorio_codigo, r.oc, r.valor, r.contrato 
		FROM combustivel c, recebimento r 
		WHERE c.codigo = r.combustivel_codigo 
		and nr_nota_fiscal='".$_GET['nr_nota_fiscal']."'
		and combustivel_codigo='".$_GET['combustivel']."'";
	$result = mysql_query($query,$conexao);
	$row = mysql_fetch_row($result);
	$ano=substr($row[3],0,4);
	$mes=substr($row[3],5,2);
	$dia=substr($row[3],8,2);
	$dt_nota = $dia."/".$mes."/".$ano;
	$row[10]= str_replace('.',',',$row[10]);
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body onLoad="document.cota_rec.qtd_atend.select();">

<script language="JavaScript" type="text/javascript">
function mascara(dt_rec){
              var mdata = '';
              mdata = mdata + dt_rec;
              if (mdata.length == 2){
                  mdata = mdata + '/';
                  document.forms[0].dt_rec.value = mdata;
              }
              if (mdata.length == 5){
                  mdata = mdata + '/';
                  document.forms[0].dt_rec.value = mdata;
              }
              if (mdata.length == 10){
                  return true;
              }
          }

	function verificar_qtd(){
		if (parseInt(document.recebimento.qtd_rec.value) < parseInt(document.recebimento.qtd_dispo.value)){
			alert("ATENÇÃO! A quantidade recebida é menor que a quantidade já utilizada ou distribuída !");
			history.go(0);
		}
	}
	
</script>

<?php
	echo "<br><center><t1>Alteração do Recebimento de Combustível</t1> <p>";
	echo "<form method='POST' action='index.php?nomeArquivo=gravar_recebimento.php' name='recebimento'>";
	echo "<table>";
	echo "<tr><td align='right' width='50%'>Nº da Nota Fiscal: </td><td width='50%'><input type='text' size='15' maxlength='15' name='nr_nota_fiscal' readonly='true' value='$row[0]'></td></tr>";
	echo "<input type='hidden' name='combustivel' value='$row[1]'>";
	echo "<tr><td align='right'>Tipo de Combustível: </td><td><input type='text' size='15' maxlength='15' name='desc_comb' readonly='true' value='$row[2]'></td></tr>";
	echo "<tr><td align='right'>Local de Armazenamento: </td><td><input type='text' size='25' maxlength='20' name='reservatorio' readonly='true' value='Reservatório nº $row[8]'></td></tr>";
	 
		/*Seeleciona a quantidade distribuida ao OC*/
		$query="SELECT qtd_atual, qtd_destinada FROM estoque 
			WHERE combustivel_codigo='".$_GET['combustivel']."' and nr_nota_fiscal='".$_GET['nr_nota_fiscal']."' and om_codom='$row[9]' and cod_tp_cota=0";
		$result = mysql_query($query);
		$qtd_disp = mysql_fetch_row($result);
		$usado=($row[4]-$qtd_disp[0]);

		if (($qtd_dist[0]==1) and ($usado==0)) {
			echo "<tr><td align='right'>órgão Controlador: </td><td><select name='oc'>";
 						/*Seleciona a relacao de oc*/
						$result1 = mysql_query("SELECT codom, sigla
									FROM om
									WHERE tipo = 'OC'
									ORDER BY sigla desc");
						while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
							echo "<option value=$row1[0] "; if ($row1[0]==$row[9]) {echo "selected"; $oc=$row1[0];} echo ">$row1[1] </option>";
						}
						echo "</select></td></tr>";
		} else {
			echo "<tr><td align='right'>órgão Controlador: </td><td><input type='text' size='25' maxlength='30' name='sigla_oc' readonly='true' value='".$_GET['oc']."'>";
			echo "<input type='hidden' name='oc' value='$row[9]'>";
		}
	
	echo "<tr><td align='right'>Data do Recebimento: </td><td><input type='text' size='10' maxlength='10' name='dt_rec' id=dt_ini value='$dt_nota'> <I>dd/mm/aaaa</I></td></tr>";
	echo "<tr><td align='right'>Quantidade Distribuída ou Utilizada: </td><td><input type='text' size='6' name='qtd_dispo' value='".round(($row[4]-$qtd_disp[0]),3)."' readonly dir='rtl'> <i>Litros</td><td></td></tr>";
	echo "<tr><td align='right'>Quantidade Recebida: </td><td><input type='text' size='6' maxlength='10' name='qtd_rec' value='$row[4]' "; if ($qtd_dist[0]!=1) {echo "onblur='verificar_qtd()'";} echo " dir='rtl'> <I>Litros</I></td></tr>";
	echo "<input type='hidden' name='qtd_disp' value='$qtd_disp[0]'>";
	echo "<input type='hidden' name='qtd_destinada' value='$qtd_disp[1]'>";
	echo "<input type='hidden' name='qtd_anterior' value='$row[4]'>";
	echo "<input type='hidden' name='cod_comb' value='".$_GET['combustivel']."'>";
	echo "<input type='hidden' name='nr_nota' value='".$_GET['nr_nota_fiscal']."'>";
	echo "<input type='hidden' name='reservatorio' value='$row[8]'>";
	echo "<input type='hidden' name='oc_ant' value='$row[9]'>";
	echo "<tr><td align='right'>Valor Unitário: </td><td width='15%' align='left'><input type='text' size='6' maxlength='10' name='valor' value='$row[10]' dir='rtl'></td></tr>";
	echo "<tr><td align='right'>Nº do Contrato: </td><td width='15%' align='left'><input type='text' size='10' maxlength='10' name='contrato' value='$row[11]'></td></tr>";
	echo "<tr><td align='right'>Nº da Remessa: </td><td><input type='text' size='20' maxlength='20' name='nr_remessa' value='$row[7]'> </td></tr>";
	echo "<tr><td align='right'>Nº do Pedido: </td><td><input type='text' size='20' maxlength='20' name='nr_pedido' value='$row[6]'> </td></tr>";
	echo " <input type='hidden' name='operacao' value='2'>";
	echo "<!--tr><td align='right'>Quantidade Distribuída ou Utilizada: </td><td>&nbsp;<b><I><font color='red'>".($row[4]-$qtd_disp[0])." Lts</font></I></b></td><td></td></tr-->";
	echo "<tr><td></td><td></td></tr>";
	echo "<tr><td align='right'><input type='submit' value= 'Gravar'></td><td><input type='reset' value= '  Cancelar  '>&nbsp;<input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";
   echo "</table>";


echo "</form>";

?>


