<?php
  if(!isset($_SESSION['usuario']))
    include "verifica_sessao.php";
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<?php 
	echo "<font size='5'><br><t1>Relação das Organizaçães Militares Cadastradas no Sistema</b></font> <p>";
	if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='ADMINISTRADOR') or (($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']=='SIM'))) {
		echo "<div align='left'><t2><a href='index.php?nomeArquivo=cad_om.php' style='text-decoration:none'>&nbsp;&nbsp;Cadastrar Nova OM<IMG SRC='./imagens/novo.png' width='25' height='25' border=0></a><br>";
	}

	echo "<table border='0' width='100%' align='center' valign='top'>";

  	/*/Seleciona a relacao de Unidades*/
	$qrs_oc="SELECT distinct om1.codom, om1.sigla
				FROM om om1, om om2
				where 
					om1.codom=om2.subordinacao 
					AND om1.subordinacao is not null
					ORDER BY abs(substring(om1.sigla,6)), om1.sigla";

	$result = mysql_query($qrs_oc);
  	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo "<tr bgcolor='$cor1'><td colspan='7'><coluna>&nbsp;&nbsp;Comando:&nbsp;<font color='$cor_submenu'>$row[1]</td></tr>";
		echo "<tr align='center' bgcolor='$cor2'>
					<td width='10%' colspan='2'><coluna>Codom</td>
					<td width='20%'><coluna>Sigla</td>
					<td width='40%'><coluna>Nome da OM</td>
					<td width='10%'><coluna>RM</td>";
		if (($_SESSION['perfil']=='ADMINISTRADOR *') OR ($_SESSION['perfil']=='ADMINISTRADOR') OR ($_SESSION['perfil']=='FISCAL DA OM') OR ($_SESSION['perfil']=='CMT DA OM')){
			echo "<td width='10%'><coluna>Alterar</td><td width='10%'><coluna>Remover</td></tr>";
		}

  		$result1 = mysql_query("select * from om where subordinacao='$row[0]' or codom='$row[0]' order by  sigla");
   		
  		while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
			echo "<tr bgcolor='$cor3' class='marcar_linha'><td width='2%' bgcolor='$cor_fundo'></td><td><lin>$row1[0]</td><td><lin>$row1[1]</td><td><lin>$row1[2]</td><td align='center'><lin>$row1[6]ª RM</td>";
			if ($_SESSION['perfil']=='ADMINISTRADOR *') {
				echo"<td align='center'><lin><a href='index.php?nomeArquivo=alterar_om.php&&codom=$row1[0]'>Alterar</td><td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&&valor=$row1[0]&&tab=om&&ch=codom&&registro=a OM $row1[1]&&arquivo=remover_om.php'>Remover</td></tr>";
			} elseif ($_SESSION['perfil']=='ADMINISTRADOR'){
				if(($_SESSION['codom']==$row1[0]) or ($_SESSION['codom']==$row1[5])) {
					echo"<td align='center'><lin><a href='index.php?nomeArquivo=alterar_om.php&&codom=$row1[0]'>Alterar</td><td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&&valor=$row1[0]&&tab=om&&ch=codom&&registro=a OM $row1[1]&&arquivo=remover_om.php'>Remover</td></tr>";
				} else {
					echo"<td align='center'><lin>-</td><td align='center'><lin>-</td></tr>";
				}
			}elseif (($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='CMT DA OM')){
				if($_SESSION['codom']==$row1[0]) {
					echo"<td align='center'><lin><a href='index.php?nomeArquivo=alterar_om.php&&codom=$row1[0]'>Alterar</td><td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&&valor=$row1[0]&&tab=om&&ch=codom&&registro=a OM $row1[1]&&arquivo=remover_om.php'>Remover</td></tr>";
				} else {
					echo"<td align='center'><lin>-</td><td align='center'><lin>-</td></tr>";
				}
			}
  		}
  		echo"<tr bgcolor='$cor_fundo'><td colspan=6>&nbsp;</td></tr>";
  	}
    	
	echo "<tr align='center' bgcolor='$cor1'><td colspan='6'><coluna>Organização Cadastradas e Não Integrantes do Sistema</td></tr>";
	echo "<tr align='center' bgcolor='$cor2'>
				<td width='10%' colspan='2'><coluna>Codom</td>
				<td width='20%'><coluna>Sigla</td>
				<td width='40%'><coluna>Nome da OM</td>";
	if (($_SESSION['perfil']=='ADMINISTRADOR *') OR ($_SESSION['perfil']=='ADMINISTRADOR') OR ($_SESSION['perfil']=='FISCAL DA OM')){
		echo "<td width='10%'><coluna>Alterar</td><td width='10%'><coluna>Remover</td></tr>";
	}
	$qry = "select * from om where tipo='Não Integrante' or oc is null or subordinacao is null order by sigla";
  	$result2 = mysql_query($qry);
  	while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
		echo "<tr bgcolor='$cor3' class='marcar_linha'>
					<td colspan='2' align='center'><lin>$row2[0]</td>
					<td><lin>$row2[1]</td>
					<td><lin>$row2[2]</td>";
		if (($_SESSION['perfil']=='ADMINISTRADOR *') OR ($_SESSION['perfil']=='ADMINISTRADOR') OR ($_SESSION['perfil']=='FISCAL DA OM')){
			echo "<td align='center'><lin><a href='index.php?nomeArquivo=alterar_om.php&&codom=$row2[0]'>Alterar</td><td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&&valor=$row2[0]&&tab=om&&ch=codom&&registro=a OM $row2[1]&&arquivo=remover_om.php'>Remover</td></tr>";
		}
 	}
	echo "</table>";
	
?>

