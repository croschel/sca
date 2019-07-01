<?php
  if(empty($_SESSION['usuario']))
    include "verifica_sessao.php";
?>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<font size=5><br><b>Consulta de Abastecimentos Realizados</b></font> <p>

<table border="0" width="100%" align="center" valign="top">
<tr bgcolor="#4A708B">
<td><b>OM:</b> <i><font color='blue'><?echo $_GET['om']?> &nbsp;&nbsp;&nbsp;&nbsp;</font>
</i><b> EB:</b> <i><font color='blue'><?echo $_GET['ebvtr']?>&nbsp;&nbsp;&nbsp;&nbsp;</font>
</i><b>VIATURA: </b><i><font color='blue'><?echo $_GET['vtr']?>&nbsp;&nbsp;&nbsp;&nbsp;</font>
 </i><b>Combustível: </b><i><font color='blue'><?echo $_GET['comb']?></td>
</tr>
</table>

<table border="0" width="100%" align="center" valign="top">
	<tr align="center" bgcolor="#4A708B">
<td width="10%" ROWSPAN=2><b>Data</td>
<td width="6%" ROWSPAN=2><b>Hora</td>
<td width="14%" COLSPAN=2><b>Quantidade</td>
<td width="15%" ROWSPAN=2><b>Odômetro</td>
<td width="25%" ROWSPAN=2><b>Local</td>
<td width="30%" ROWSPAN=2><b>Motorista</td>
	</tr>
	<tr align="center" bgcolor="#4A708B">
<td width="7%"><b>Autz</td>
<td width="7%"><b>Abast</td>
	</tr>

	<tr bgcolor="#4A708B">


	<?php    
        //Conexão ao Banco de Dados
    	include "conexao.php";
    	//Seleciona a relação de alunos
	if ($_SESSION['perfil']=='FISCAL DA OM'){
		$result = mysql_query("SELECT a.dt_abastecimento, c.nome, a.qtd_autorizada, a.qtd_abastecida, a.motorista, a.reservatorio_codigo, a.od_atual
				FROM abastecimento a, om o, combustivel c, viatura v
				WHERE o.codom = v.om_codom
				AND c.codigo = v.combustivel_codigo
				AND v.eb = a.viatura_eb
				AND v.om_codom = '".$_SESSION['codom']."'
				AND a.qtd_abastecida is not null
				AND v.eb = ".$ebvtr."
				ORDER BY o.sigla, dt_abastecimento DESC ");
	} 
	else
	{
		$result = mysql_query("SELECT a.dt_abastecimento, c.nome, a.qtd_autorizada, a.qtd_abastecida, a.motorista, a.reservatorio_codigo, a.od_atual
				FROM abastecimento a, om o, combustivel c, viatura v
				WHERE o.codom = v.om_codom
				AND c.codigo = v.combustivel_codigo
				AND v.eb = a.viatura_eb
				AND a.qtd_abastecida is not null
				AND v.eb = ".$ebvtr."
				ORDER BY o.sigla, dt_abastecimento DESC 
				LIMIT 0 , 100");
	}

    	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$ano=substr($row[0],0,4);
		$mes=substr($row[0],5,2);
		$dia=substr($row[0],8,2);
		$hora=substr($row[0],11,5);
		$dt_nota = $dia."/".$mes."/".$ano;
	//Seeleciona a OM do reservatorio
	$busca="select o.sigla from om o, reservatorio r where o.codom=r.om_codom and r.codigo=".$row[5];
	$result = mysql_query($busca,$conexao);
	$local = mysql_fetch_row($result);
		echo"<tr bgcolor='#7EC0EE' align='center'><td>$dt_nota</td><td>$hora</td><td>$row[2] Lts</td><td>$row[3] Lts</td><td>$row[6]</td><td>$local[0] - Res Nº $row[5]</td><td align='left'>$row[4]</td></tr>";
    	}
    	$num_rows = mysql_num_rows($result);
    	mysql_free_result($result);
?>
	</tr>
</table>
<br>  
<a href="#" onclick="history.go(-1)"> VOLTAR</a>
