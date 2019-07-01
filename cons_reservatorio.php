<?php
  if(!isset($_SESSION['usuario']))
    include "verifica_sessao.php";
?>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<br><t1>Relação de Reservatórios Cadastrados no Sistema</b></t1> <p>
<?php
	if (($_SESSION['perfil']!='CONSULTOR') and ($_SESSION['perfil']!='CONSULTOR *')){
		echo "<div align='left'><t2><a href='index.php?nomeArquivo=cad_reservatorio.php' style='text-decoration:none'>&nbsp;&nbsp;Cadastrar Reservatório<IMG SRC='./imagens/novo.png' width='25' height='25' border=0></a><br>";
	}

	echo "<table border='0' width='100%' align='center' valign='top'>";
	echo "<tr align='center' bgcolor='$cor1'>";
	if (($_SESSION['perfil']=='CONSULTOR') OR ($_SESSION['perfil']=='CONSULTOR *')){
		echo "<td><coluna>Nº Reservatório</td><td><coluna>Localização</td><td><coluna>Combustível</td><td><coluna>Capacidade</td><td><coluna>Taxa Evaporação</td><td><coluna>Situação</td>";
	}
	else {
		echo "<td><coluna>Nº Reservatório</td><td><coluna>Localização</td><td><coluna>Combustível</td><td><coluna>Capacidade</td><td><coluna>Taxa Evaporação</td><td><coluna>Situação</td><td><coluna>Alterar</td><td><coluna>Remover</td>";
	}
		
	echo "</tr>";
   
   /*Seleciona a relação de alunos*/
  	if (($_SESSION['perfil']=='ADMINISTRADOR *') OR ($_SESSION['perfil']=='CONSULTOR *')){
  		$result = mysql_query("SELECT r.codigo, o.sigla, c.nome, r.capacidade, r.situacao, r.evaporacao 
				FROM combustivel c,reservatorio r, om o
				WHERE c.codigo=r.combustivel_codigo
				and o.codom=r.om_codom
				order by CONCAT(REPEAT('0', 20 - LENGTH(r.codigo)), r.codigo)");
	} else {
   	$result = mysql_query("SELECT r.codigo, o.sigla, c.nome, r.capacidade, r.situacao, r.evaporacao  
				FROM combustivel c,reservatorio r, om o
				WHERE c.codigo=r.combustivel_codigo
				and o.codom=r.om_codom
				and (o.oc='".$_SESSION['codom']."' or o.codom='".$_SESSION['codom']."')
				order by CONCAT(REPEAT('0', 20 - LENGTH(r.codigo)), r.codigo)");
	}
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if (($_SESSION['perfil']=='CONSULTOR') OR ($_SESSION['perfil']=='CONSULTOR *')){
			echo"<tr bgcolor='$cor3' align='center' class='marcar_linha'><td><lin>$row[0]</td><td align='left'><lin>$row[1]</td><td><lin>$row[2]</td><td align='right'><lin>$row[3] Litros &nbsp;</td><td><lin>".number_format($row[5], 1, ',', '.')." % ao mês</td><td><lin>$row[4]</td></tr>";
		} else{
			echo"<tr bgcolor='$cor3' align='center' class='marcar_linha'><td><lin>$row[0]</td><td align='left'><lin>$row[1]</td><td><lin>$row[2]</td><td align='right'><lin>$row[3] Litros &nbsp;</td><td><lin>".number_format($row[5], 1, ',', '.')." % ao mês</td><td><lin>$row[4]</td><td><lin><a href='index.php?nomeArquivo=alterar_reservatorio.php&codigo=$row[0]'>Alterar</a></td><td><lin><a href='index.php?nomeArquivo=remover_registro.php&valor=$row[0]&tab=reservatorio&ch=codigo&registro=o Reservatório de $row[1] - Código: $row[0]&arquivo=remover_reservatorio.php'>Remover</a></td></tr>";
		}
	}
  	$num_rows = mysql_num_rows($result);
  	mysql_free_result($result);
?>
	</tr>
</table>