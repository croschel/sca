<?php
  if(empty($_SESSION['usuario']))
    include 'verifica_sessao.php';
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<Script Type="text/javascript" Language="JavaScript">
function verifica(){
	if(document.usuario.idt.value==''){
		alert("ATENÇÃO! Os campos com * são de preenchimento obrigatório.");
		onclick="history.go(-1)";
	}
</script>

<?php
	echo "<table border='0' width='100%' align='left' valign='top'>";
	echo "<tr align='center' bgcolor='$cor_fundo'><td COLSPAN='9'><br><t1>Abastecimentos Autorizados<p></td></tr>";
	echo "<tr align='center' bgcolor='$cor1'>";
		echo "<td><coluna>EB</td>
				<td><coluna>Viatura</td>
				<td><coluna>Cota da OM</td>
				<td><coluna>Dt Autz</td>
				<td><coluna>Combustível</td>
				<td><coluna>Qtd</td>
				<td><coluna>Reservatório</td>
				<td><coluna>Motorista</td>
				<td><coluna>Opção</td>
				";
	echo "</tr>";

	/*Seleciona a relação de abastecimentos autorizados no posto da om*/

	$query=" SELECT o.sigla, a.viatura_eb, a.qtd_autorizada, c.nome, concat(v.marca,' - ', v.modelo,' do(a) ', om.sigla), c.codigo, a.motorista, a.reservatorio_codigo, a.od_atual,
						a.missao, a.dt_autorizacao, t.tipo_cota, a.tp_abastecimento 
					FROM om o, viatura v, combustivel c, abastecimento a, reservatorio r, tipo_cota t, om
					WHERE o.codom=a.cota_om
						and v.eb=a.viatura_eb
						and om.codom=v.om_codom
						and c.codigo=v.combustivel_codigo
						and r.codigo=a.reservatorio_codigo
						and c.codigo = r.combustivel_codigo
						and t.cod_tp_cota=a.cod_tp_cota
						and a.qtd_abastecida is null 
						and a.reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."')
						order by a.dt_autorizacao ";

	$result = mysql_query($query);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$dt_autz=substr($row[10],8,2)."/".substr($row[10],5,2)."/".substr($row[10],0,4);
		if($row[12]=='Carregamento de Cisterna') {
			$row[4]=$row[12];
			echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'><td><lin>$row[1]</td><td align='left'><lin>$row[4]</td><td align='left'><lin>$row[0]</td><td><lin>$dt_autz</td><td><lin>$row[3]</td><td><lin>$row[2] Lts</td><td><lin>Nº $row[7]</td>
			<td align='left'><lin>$row[6]</td><td><lin><a href='index.php?nomeArquivo=cad_abast.php&dt_abast=$row[10]&eb=$row[1]&tipo=cisterna' style='text-decoration:none'>Abastecer</a></td></tr>";
		} else {
			echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'><td><lin>$row[1]</td><td align='left'><lin>$row[4]</td><td align='left'><lin>$row[0]</td><td><lin>$dt_autz</td><td><lin>$row[3]</td><td><lin>$row[2] Lts</td><td><lin>Nº $row[7]</td>
			<td align='left'><lin>$row[6]</td><td><lin><a href='index.php?nomeArquivo=cad_abast.php&dt_abast=$row[10]&eb=$row[1]&tipo=vtr' style='text-decoration:none'>Abastecer</a></td></tr>";
		}
	}
	$num_rows = mysql_num_rows($result);
	mysql_free_result($result);

	echo "<tr><td>&nbsp;</td></tr>";
	echo "</table>";
	echo "<p>";
?>

