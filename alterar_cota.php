<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body onLoad="document.cota_rec.qtd_atend.select();">
<script>
	function verificar_qtd(){
		if (parseInt(document.cota_rec.qtd_atend.value) > parseInt(document.cota_rec.qtd_max.value)){
			alert("ATENÇÃO! Quantidade atendida maior que a quantidade máxima que poderá ser distribuída !");
			history.go(0);
		}
	}
</script>
<?php
	include "verifica_sessao.php";

	/*/Seeleciona todos os alunos*/
	$query="select * from cotas
		where recebimento_combustivel_codigo='".$_GET['cod_comb']."' 
		and recebimento_nr_nota_fiscal='".$_GET['nr_nota']."' 
		and om_codom='".$_GET['codom']."'";
	$result = mysql_query($query,$conexao);
	$row = mysql_fetch_row($result);

	echo "<br><br><center><b>ALTERAÇÃO NA DISTRIBUIÇÃO DE COMBUSTÍVEL</b> </center><p>";
	echo "<form method='POST' action='index.php?nomeArquivo=gravar_cota.php' name='cota_rec'>";
	echo "<table>";
	echo "<tr><td align='right' >Organização Militar: </td><td ><b><I><font color='blue'>".$_GET['om']."</font></I></b></td></tr>";
	echo "<tr><td align='right' >Combustível: </td><td ><b><I><font color='blue'>".$_GET['combustivel']."</font></I></b></td></td></tr>";
	echo "<tr><td align='right' >Reservatório: </td><td ><b><I><font color='blue'>Nº ".$_GET['reservatorio']." - ".$_GET['local']."</font></I></b></td></tr>";
	echo "<tr><td align='right' >Nota Fiscal Número: </td><td ><b><I><font color='blue'>".$_GET['nr_nota']."</font></I></b></td></tr>";
	
	/*Seeleciona a quantidade distribuida ao OC*/
	$query="SELECT qtd_destinada, qtd_atual FROM cotas 
		WHERE recebimento_combustivel_codigo='".$_GET['cod_comb']."' and recebimento_nr_nota_fiscal='".$_GET['nr_nota']."' and om_codom='".$_SESSION['codom']."'";
	$result = mysql_query($query);
	$qtd_disp = mysql_fetch_row($result);

	echo "<input type='hidden' name='qtd_rec' value='$qtd_disp[0]'>";
	echo "<input type='hidden' name='qtd_atual' value='$qtd_disp[1]'>";
	echo "<tr><td align='right'>Quantidade Máxima a ser Distribuída: </td><td><I><input type='text' size='10' name='qtd_max' value='".round($qtd_disp[1]+$row[3])."' readonly> Lts</font></I></b></td><td></td></tr>";

	echo "<tr><td align='right'>Quantidade Atendida: </td><td><input type='text' size='10' maxlength='10' name='qtd_atend' value='$row[3]' onblur='verificar_qtd()'><i> Lts</td></tr>";

	echo "<tr><td align='right'>Quantidade Atual da OM: </td><td><b><I><font color='red'>".round($row[8])." Lts</td></tr>";
	echo "<tr><td align='right'>Número da Remessa: </td><td><input type='text' size='20' maxlength='20' name='nr_remessa' value='$row[4]'></td></tr>";
	echo "<tr><td align='right'>Tipo de Combustível: </td><td><input type='checkbox' "; if ($row[5]=='a') echo "checked"; echo "name='administrativo'><i> Administrativo</td><td></td></tr>";
	echo "<tr><td align='right'></td><td><input type='checkbox' "; if ($row[6]=='e') echo "checked"; echo "name='ensino'><i> Ensino</td><td></td></tr>";
	echo "<tr><td align='right'></td><td><input type='checkbox' "; if ($row[7]=='o') echo "checked"; echo "name='operacional'><i> Operacional</td><td></td></tr>";
	echo "<input type='hidden' name='qtd_anterior' value='$row[3]'>";
	echo "<input type='hidden' name='qtd_atual_om' value='$row[8]'>";
	echo "<input type='hidden' name='cod_comb' value='".$_GET['cod_comb']."'>";
	echo "<input type='hidden' name='nr_nota' value='".$_GET['nr_nota']."'>";
	echo "<input type='hidden' name='codom' value='".$_GET['codom']."'>";
	echo "<input type='hidden' name='combustivel' value='".$_GET['combustivel']."'>";
	echo "<input type='hidden' name='om' value='".$_GET['om']."'>";
	echo "<input type='hidden' name='reservatorio' value='".$_GET['reservatorio']."'>";
	echo "<input type='hidden' name='operacao' value='2'>";
	echo "<tr><td></td><td></td></tr>";
	echo "<tr><td align='right'><input type='submit' value= 'Gravar'></td><td><input type='reset' value= '  Cancelar  '>&nbsp;<input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";
	echo "</table>";

	echo "</form>";

?>