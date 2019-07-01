<?php
     include "verifica_sessao.php";
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<?php 

	echo "<form name='om' method='POST' action='index.php?nomeArquivo=gravar_cota.php' >";
	echo "<center><t1><br>Repasse de Cota de Combustível <p></t1> </center>";
	echo "<table>";
	echo "<input type='hidden' name='nr_nota' value='".$_GET['nr']."'>";
	echo "<input type='hidden' name='cod_comb' value='".$_GET['cod_comb']."'>";
	echo "<input type='hidden' name='combustivel' value='".$_GET['combustivel']."'>";
	echo "<input type='hidden' name='reservatorio' value='".$_GET['reservatorio']."'>";
	echo "<tr><td align='right'>Combustível: </td><td><b><I><font color='$cor_menu'>".$_GET['combustivel']."</font></I></b></td><td></td></tr>";
	echo "<tr><td align='right'>Reservatório: </td><td><b><I><font color='$cor_menu'>Nº ".$_GET['reservatorio'].' - '.$_GET['local']."</font></I></b></td><td></td></tr>";
	echo "<tr><td align='right' width='40%'> Nota Fiscal Número: </td><td width='30%'><b><I><font color='$cor_menu'>".$_GET['nr']."</font></I></b></td><td width='30%'></td></tr>";
	
		
	/*/Seeleciona a quantidade distribuida ao OC*/
	$query="SELECT sum(qtd_destinada), sum(qtd_atual) FROM estoque 
		WHERE combustivel_codigo='".$_GET['cod_comb']."' and nr_nota_fiscal='".$_GET['nr']."' and om_codom='".$_SESSION['codom']."'";
	$result = mysql_query($query);
	$qtd_disp = mysql_fetch_row($result);

	//Verifica o somatãrio dos abastecimentos autorizados e não realizados
	$qrya = "select sum(qtd_autorizada) 
				from abastecimento 
				where reservatorio_codigo='".$_GET['reservatorio']."' and cota_om='".$_SESSION['codom']."' and qtd_abastecida is null";
	$resa = mysql_query($qrya);
	$qtda = mysql_fetch_row($resa);
	$qtd_disp[1] = str_replace(".", ",",($qtd_disp[1]-$qtda[0]));

	echo "<input type='hidden' name='qtd_destinada' value='$qtd_disp[0]'>";
	echo "<input type='hidden' name='qtd_atual' value='$qtd_disp[1]'>";
	echo "<input type='hidden' name='nr_repasse' value='$qtd_disp[2]'>";
	echo "<tr><td align='right'>Quantidade Disponível: </td><td><b><I><font color='red'>".round($qtd_disp[1],0)." Lts</font></I></b></td><td></td></tr>";
	echo "<tr><td align='right'>OM a ser Atendida: </td><td><select name='codom'>";
            			echo "<option value='' selected disabled>:-: ESCOLHA A OM :-:</option>";
						
						/*Seleciona a relação de unidades */
						$qry = "select om.codom, om.sigla, cmdo.sigla, om.oc, cmdo.sigla 
								from om cmdo, om 
								where om.subordinacao=cmdo.codom 
									and om.tipo in ('Integrante')
									and om.oc='".$_SESSION['codom']."'
									and om.codom<>'".$_SESSION['codom']."'
								order by abs(cmdo.sigla), cmdo.sigla, abs(om.sigla)
								";
						$result = mysql_query($qry);
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							if($cmdo!=$row[4]) {
 								echo "<optgroup label='Comando: $row[2]'>";
  								$cmdo=$row[4];
  							}
							echo "<option value='$row[0]' >$row[1]</option>";
						}
				
							/*Seleciona a relação de unidades*/
						$qry = "select om.codom, om.sigla, c.sigla, om.oc, om.subordinacao 
								from om, (select codom, sigla from om) c 
								where c.codom=om.oc 
									and om.tipo in('OC','Integrante')
									and om.oc!='".$_SESSION['codom']."'
								order by 3
								";
						$result = mysql_query($qry);
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							if($cmdo!=$row[3]) {
  								echo "<optgroup label='Comando: $row[2]'>";
  								$cmdo=$row[3];
  							}
							echo "<option value='$row[0]' >$row[1]</option>";
						}
				echo "</select></td><td></td></tr>";
	echo "<tr><td align='right'>Quantidade a Atender: </td><td><input type='number' min='1' size='10' max='".round($qtd_disp[1],0)."' name='qtd_atd'></td><td></td></tr>";
	echo "<tr><td align='right'>Número da Remessa: </td><td><input type='text' size='20' maxlength='20' name='nr_remessa'></td><td></td></tr>";
	echo "<tr><td align='right'>Tipo de Combustível: </td><td><select name='tp_cota'>";
						/*Seleciona a relação de tipos de cotas */
						$qry = "select * 
								from tipo_cota 
								where cod_tp_cota <> 0
								order by 1
								";
						$result = mysql_query($qry);
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$row[0]' >$row[1]</option>";
						}
				echo "</select></td></tr>";
	echo "<tr><td align='right'>Observação: </td><td><input type='text' size='40' maxlength='100' name='obs'></td><td></td></tr>";
	echo "<input type='hidden' name='operacao' value='1'>";
	echo "<tr><td align='right'><input type='submit' value= 'Cadastrar'></td><td COLSPAN='2'><input type='reset' value= '  Limpar  '>&nbsp;<input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";
	echo "</table>";
echo "</form>";
echo "</div>";

?>

