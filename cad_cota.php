<?php
     include "verifica_sessao.php";
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<script type="text/javascript">
	function getCredito(nr, combustivel, cod_comb, qtd_disp, reservatorio, local, codom, tp_comb) {
  		document.location=('index.php?nomeArquivo=cad_cota.php&nr=' + nr + '&combustivel=' + combustivel + '&cod_comb=' + cod_comb + '&qtd_disp=' + qtd_disp + '&reservatorio=' + reservatorio + '&local=' + local + '&codom=' + codom + '&tp_comb=' + tp_comb );
		
	}

$(document).ready(function () {
//		alert('teste');
	$("input[name='qtd_comb']").css({width:'60px',})
	$("#posto").change(function () {
		$("input[name='qtd_comb']").attr("max",Math.floor($("option:selected",this).attr("id")));	

		if (Math.floor($("option:selected",this).attr("id"))>0) {
			$("input[name='qtd_max']").attr("value",Math.floor($("option:selected",this).attr("id"))+' Lts');
		}else {
			$("input[name='qtd_max']").attr("value",' Não Definida');
		}	
		
		if (parseInt(document.cota.qtd_max.value) < parseInt(document.cota.qtd_atual.value)) {
			document.cota.qtd_atd.max = parseInt(document.cota.qtd_max.value);
		} else {
			document.cota.qtd_atd.max = parseInt(document.cota.qtd_atual.value);
		}
	})

	$("input[name='qtd_max']").css({background:'transparent', border:'none', color:'red',})
	$("input[name='saldo']").css({background:'transparent', border:'none', color:'red',})

})

</script>

<?php 

	echo "<form name='cota' method='POST' action='index.php?nomeArquivo=gravar_cota.php' >";
	echo "<center><t1><br>Repasse de Cota de Combustível <p></t1> </center>";
	echo "<table border='0'>";
	echo "<input type='hidden' name='nr_nota' value='".$_GET['nr']."'>";
	echo "<input type='hidden' name='cod_comb' value='".$_GET['cod_comb']."'>";
	echo "<input type='hidden' name='combustivel' value='".$_GET['combustivel']."'>";
	echo "<input type='hidden' name='reservatorio' value='".$_GET['reservatorio']."'>";
	echo "<tr><td align='right'>Combustível: </td><td><b><I><font color='$cor_menu'>".$_GET['combustivel']."</font></I></b></td></tr>";
	echo "<tr><td align='right'>Reservatório: </td><td><b><I><font color='$cor_menu'>Nú ".$_GET['reservatorio'].' - '.$_GET['local']."</font></I></b></td></tr>";
	echo "<tr><td align='right'> Nota Fiscal Número: </td><td><b><I><font color='$cor_menu'>".$_GET['nr']."</font></I></b></td></tr>";
	
		
	/*/Seeleciona a quantidade distribuida ao OC*/
	$query="SELECT sum(qtd_destinada), sum(qtd_atual) FROM estoque 
		WHERE combustivel_codigo='".$_GET['cod_comb']."' and nr_nota_fiscal='".$_GET['nr']."' and om_codom='".$_SESSION['codom']."'";
	$result = mysql_query($query);
	$qtd_disp = mysql_fetch_row($result);

	//Verifica o somatório dos abastecimentos autorizados e não realizados
	$qrya = "select sum(qtd_autorizada) 
				from abastecimento 
				where reservatorio_codigo='".$_GET['reservatorio']."' and cota_om='".$_SESSION['codom']."' and qtd_abastecida is null";
	$resa = mysql_query($qrya);
	$qtda = mysql_fetch_row($resa);
	$qtd_disp[1] = $qtd_disp[1]-$qtda[0];

	echo "<input type='hidden' name='qtd_destinada' value='$qtd_disp[0]'>";
	echo "<input type='hidden' name='qtd_atual' value='$qtd_disp[1]'>";
	echo "<input type='hidden' name='nr_repasse' value='$qtd_disp[2]'>";
	echo "<tr><td align='right'>Quantidade Disponível na NF: </td><td><b><I><font color='red'>".round($qtd_disp[1],0)." Lts</font></I></b></td></tr>";

						
	//Seleciona a relatóo de unidades
	$qry = "select distinct om.codom, om.sigla, cmdo.sigla, om.oc, cmdo.sigla 
				from om cmdo, om ";
	
	//Se o sistema trabalha com crédito
	if($controle_credito=='s') {	
		$qry .= "left join credito c on (om.codom =c.om_codom) ";
	} 
	$qry .= "where om.subordinacao=cmdo.codom
					and om.tipo in ('Integrante')
					and om.oc='".$_SESSION['codom']."'
					and om.codom<>'".$_SESSION['codom']."'
				order by abs(cmdo.sigla), cmdo.sigla, abs(om.sigla)
			";


	echo "<tr><td align='right'>OM a ser Atendida: </td>";
		echo "<td><select name='codom' required onChange='getCredito(\"".$_GET['nr']."\",\"".$_GET['combustivel']."\",\"".$_GET['cod_comb']."\",\"".$_GET['qtd_disp']."\",\"".$_GET['reservatorio']."\",\"".$_GET['local']."\",this.value,\"".$_GET['tp_comb']."\")'>";
   		echo "<option value='' selected disabled>:-: ESCOLHA A OM :-:</option>";

		$result = mysql_query($qry);
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
			if($cmdo!=$row[4]) {
 				echo "<optgroup label='Comando: $row[2]'>";
  				$cmdo=$row[4];
  			}
			echo "<option value='$row[0]' "; if($_GET['codom']==$row[0]) { echo "selected"; } echo ">$row[1] </option>";
		}
		echo "</select></td></tr>";
				
	//Seleciona a relação de tipos de cotas 
	$qry = "select * 
				from tipo_cota ";
	if($_SESSION['perfil']!='ADMINISTRADOR *') {
		$qry .= "where cod_tp_cota>0 ";
	}		
	$qry .= "order by 1
			";
	$result = mysql_query($qry);
	echo "<tr><td align='right'>Tipo de Combustírvel: </td><td><select name='tp_cota' required onChange='getCredito(\"".$_GET['nr']."\",\"".$_GET['combustivel']."\",\"".$_GET['cod_comb']."\",\"".$_GET['qtd_disp']."\",\"".$_GET['reservatorio']."\",\"".$_GET['local']."\",\"".$_GET['codom']."\",this.value)'>";
						echo "<option value='' selected disabled>:-: ESCOLHA A COTA :-:</option>";
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$row[0]' "; if($_GET['tp_comb']==$row[0]) { echo "selected"; } echo ">$row[1]</option>";
						}
				echo "</select>";
			if($abast_c_saldo_neg=='s') {	
				$qry = "SELECT TRUNCATE(ifnull(sum(qtd_atual),0),0) FROM `estoque`
							where om_codom='".$_GET['codom']."'
								and combustivel_codigo='".$_GET['cod_comb']."' 
								and cod_tp_cota='".$_GET['tp_comb']."' 
								and reservatorio_codigo='".$_GET['reservatorio']."' 
						";
				$result = mysql_query($qry);
				$saldo = mysql_fetch_row($result);
				
				$qry = "select a.qtd_abastecida - ifnull(h.qtd_abastecida ,0)
							from abastecimento a LEFT JOIN 
									(SELECT dt_abastecimento, viatura_eb, sum(qtd_abastecida) qtd_abastecida 
										FROM `historico_abastecimento` 
										GROUP BY dt_abastecimento, viatura_eb
									) h on (a.dt_abastecimento=h.dt_abastecimento and a.viatura_eb=h.viatura_eb)
							where a.qtd_abastecida > ifnull(h.qtd_abastecida ,0)
								and a.cota_om='".$_GET['codom']."'
								and a.cod_tp_cota='".$_GET['tp_comb']."' 
								and a.reservatorio_codigo='".$_GET['reservatorio']."' ";
				$result = mysql_query($qry);
				while ($qtd = mysql_fetch_array($result, MYSQL_NUM)) {
					$saldo_neg = $saldo_neg+$qtd[0];
				}

				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Saldo:&nbsp;<input type='text' name='saldo' size='10' value='".($saldo[0] - $saldo_neg)." Lts' readonly>";
			}
			echo "</td></tr>";

	if($controle_credito=='s') {
		//Seleciona a relacao de tipos de cotas 
		$qry = "select * 
				from credito
				where om_codom='".$_GET['codom']."'
					and combustivel_codigo='".$_GET['cod_comb']."' 
					and cod_tp_cota='".$_GET['tp_comb']."' 
				order by 1
								";
		echo "<tr><td align='right'>Crédito Disponível para a OM: </td><td><select name='credito' id='posto' required>";
						echo "<option value='' selected disabled>:-: ESCOLHA O CRÉDITO :-:</option>";
						echo "<option value='' >SEM CRÉDITO DEFINIDO</option>";
						$result = mysql_query($qry);
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option id='$row[6]' value='$row[0]' "; if($_GET['tp_comb']==$row[0]) { echo "selected"; } echo ">$row[4]</option>";
						}
				echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Disponibilidade:&nbsp;<input type='text' name='qtd_max' size='10' value='$valor Lts' readonly> </td></tr>";
	}

	echo "<tr><td align='right'>Quantidade a Atender: </td><td><input type='number' min='1' size='10' max='".round($qtd_disp[1],0)."' name='qtd_atd' required></td></tr>";
	echo "<tr><td align='right'>Número da Remessa: </td><td><input type='text' size='20' maxlength='20' name='nr_remessa'></td></tr>";
	echo "<tr><td align='right'>Observação: </td><td><input type='text' size='40' maxlength='100' name='obs'></td></tr>";
	echo "<input type='hidden' name='operacao' value='1'>";
	echo "<tr><td align='right'><input type='submit' value= 'Cadastrar'></td><td COLSPAN='2'><input type='reset' value= '  Limpar  '>&nbsp;<input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";
	echo "</table>";
echo "</form>";
echo "</div>";

?>

