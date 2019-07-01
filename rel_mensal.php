<?php
  if(empty($_SESSION['usuario']))
    include "verifica_sessao.php";
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<font size=5><br><b>Mapa Mensal de Combustível</b></font> <p>Mês: 

<table border="0" width="100%" align="center" valign="top">
	<tr align="center" bgcolor="#4A708B">
		<td width="33%" COLSPAN=2>MINISTÉRIO DA DEFESA<br>EXÉRCITO BRASILEIRO<br>2º BATALHÃO LOGÍSTICO LEVE<br>(5º G Can 90 AAé/1954)<br>BATALHÃO CIDADE DE CAMPINAS</td>
		<td width="33%"><b>MAPA MENSAL DE COMBUSTíVEL</td>
		<td width="33%"><b>Movimento do mês de:<br><br></b> Setembro - 2008</td>
	</tr>
</table><p>
<table border="0" width="50%" align="left" valign="top">
	<tr bgcolor='#4A708B' align='center'><td COLSPAN=2><b> DISPONIBILIDADE ATUAL DAS UNIDADES</td></tr>
	<tr align='center' bgcolor='#4A708B'><td width='80%'><i>Local de Armazenamento</td><td width='20%'><i>Quantidade</td></td></tr>
	<?php    
        //Conexão ao Banco de Dados
    	include "conexao.php";
    	//Seleciona a relação de alunos
	$resultado = mysql_query("SELECT codom, sigla
				FROM om 
				ORDER BY sigla");
	while ($om = mysql_fetch_array($resultado, MYSQL_NUM)) {
		echo "<tr bgcolor='#4A708B' align='left'><td COLSPAN=2><b><font color='#000000'>$om[1]</td></tr>
		<tr bgcolor='#7EC0EE' align='left'><td COLSPAN=2><font color='blue'><i>&nbsp;&nbsp;&nbsp;Gasolina</td></tr>
		";

		$total_gas=0;

		$resultado1 = mysql_query("SELECT o.sigla, d.qtd_atual, d.reservatorio_codigo
					FROM om o, disponibilidade d,  reservatorio r
					WHERE o.codom = r.om_codom
					and r.codigo = d.reservatorio_codigo
					and d.om_codom=".$om[0]."
					and r.combustivel_codigo='1'
					ORDER BY d.reservatorio_codigo");
		while ($gasolina = mysql_fetch_array($resultado1, MYSQL_NUM)) {
			echo "<tr bgcolor='#7EC0EE' align='center'><td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Reservatório do(a) $gasolina[0]</td><td><font color='blue'>$gasolina[1] Lts</td></tr>";
			$total_gas=$total_gas + $gasolina[1];
		}
		echo "<tr align='center' bgcolor='#7EC0EE'><td><b><i>Total</td><td><b><font color='red'>$total_gas Lts</td></tr>";
//		echo "<tr align='center' bgcolor='#6CA6CD'><td COLSPAN=2>&nbsp;</td></tr>";

		echo"<tr bgcolor='#7EC0EE' align='left'><td COLSPAN=2><font color='blue'><i>&nbsp;&nbsp;&nbsp; Diesel</td></tr>";
		$total_diesel=0;
		$resultado1 = mysql_query("SELECT o.sigla, d.qtd_atual, d.reservatorio_codigo
					FROM om o, disponibilidade d,  reservatorio r
					WHERE o.codom = r.om_codom
					and r.codigo = d.reservatorio_codigo
					and d.om_codom=".$om[0]."
					and r.combustivel_codigo='2'
					ORDER BY d.reservatorio_codigo");
		while ($diesel = mysql_fetch_array($resultado1, MYSQL_NUM)) {
			echo "<tr bgcolor='#7EC0EE' align='center'><td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Reservatório do(a) $diesel[0]</td><td><font color='blue'>$diesel[1] Lts</td></tr>";
			$total_diesel=$total_diesel + $diesel[1];
		}
		echo "<tr align='center' bgcolor='#7EC0EE'><td><b><i>Total</td><td><b><font color='red'>$total_diesel Lts</td></tr>";

//		echo "<tr align='center' bgcolor='#6CA6CD'><td COLSPAN=2>&nbsp;</td></tr>";
//		echo "<tr align='center' bgcolor='#6CA6CD'><td COLSPAN=2><hr></td></tr>";
//		echo "<tr align='center' bgcolor='#6CA6CD'><td COLSPAN=2>&nbsp;</td></tr>";
    	}
	?>

</table>
<table border="0" width="50%" align="left" valign="top">
	<tr bgcolor='#4A708B' align='center'><td bgcolor='#6CA6CD'></td><td COLSPAN=2 width="80%"> QUANTIDADE RECEBIDA NO MÊS</td></tr>
	<?php    
        //Conexão ao Banco de Dados
    	include "conexao.php";
    	//Seleciona a relação de alunos
	$result = mysql_query("SELECT c.nome, sum(r.qtd_rec)
			FROM combustivel c, recebimento r
			WHERE c.codigo = r.combustivel_codigo
	 		group by c.nome");
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
 		echo"<tr><td bgcolor='#6CA6CD'></td><td bgcolor='#7EC0EE'>$row[0]</td><td bgcolor='#7EC0EE' align='right'>$row[1] Lts &nbsp; </td></tr>";
    	}
    	$num_rows = mysql_num_rows($result);
    	mysql_free_result($result);
	?>
</table><p>
