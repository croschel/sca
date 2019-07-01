<?php
  if(!isset($_SESSION['usuario']))
    include "verifica_sessao.php";
?>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<?php    
	echo "<font size=5><br><t1>Relação de Viaturas Cadastradas no Sistema</b></font> <p>";

	echo "<table border='0' width='100%' align='center' valign='top'>";
	echo "<tr><td colspan='6' align='center'>";
	echo "<hr><form method='GET' action='index.php?nomeArquivo=pesq_viatura.php' name='viatura'>";
	echo "<input type='hidden' name='nomeArquivo' value='pesq_viatura.php'>";
	echo "<t2><i><font color=blue>Localizar uma Viatura: <input type='text' size='40' maxlength='50' name='eb' value='".$_GET['eb']."' title='Para localizar uma viatura digite o EB, Chassi ou o modelo da viatura e pressione <<Executar>>.'>";
	echo "<input type='hidden' name='pesq' value='1'>";
	echo "<input type='submit' value= ' Executar '><hr>";
	echo "</form>";
	
	if(($_GET['pesq']) and ($_GET['eb']!='')){
		$qry="select o.sigla, v.eb, v.modelo, v.marca, c.nome, v.disponivel, v.consumo, o.codom
				FROM viatura v, om o, combustivel c 
				WHERE o.codom = v.om_codom
				AND c.codigo = v.combustivel_codigo
				AND v.situacao='Em Carga'
				and (v.eb like '%".$_GET['eb']."%' or v.modelo like '%".$_GET['eb']."%' or v.marca like '%".$_GET['eb']."%' )
				and v.situacao='Em Carga'
				ORDER BY o.sigla";
		$result1 = mysql_query($qry);
		echo "<table border='0' width='50%' align='center' valign='top'>";
		echo "<tr align='center'>
				<td>&nbsp;</td>
				<td width='50' bgcolor='$cor1'><coluna>OM</td>
				<td width='200' bgcolor='$cor1'><coluna>EB</td>
				<td width='250' bgcolor='$cor1'><coluna>Viatura</td>
				<td>&nbsp;</td>
				</tr>";
		if(mysql_num_rows($result1)<1){
			echo "<tr align='center'>";
				echo "<td><lin>&nbsp;</td>";
				echo "<td bgcolor='$cor3' colspan='3'><lin><marcar>Não foi localizada nenhuma viatura com esta sequência de caracteres</lin></td>";
			echo "</tr>";
		}	
		 while ($vtr = mysql_fetch_array($result1, MYSQL_NUM)){
		 	if($vtr[0] != $ant){
		 		echo "<tr><td></td><td colspan='3' bgcolor='$cor2'><coluna1>&nbsp;&nbsp;&nbsp;$vtr[0]</td></tr>";
		 		$ant = $vtr[0];
		 	}
			echo "<tr align='center'>";
				echo "<td><lin>&nbsp;</td>";
				echo "<td bgcolor='$cor3' colspan='2'><lin><a href='index.php?nomeArquivo=pesq_viatura.php&listar=$vtr[7]&eb=$vtr[1]' style='text-decoration:none'>$vtr[1]</a></lin></td>";
				echo "<td bgcolor='$cor3' ><lin>$vtr[3] - $vtr[2]</lin></td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	echo "</td</tr>";

  /*Conexão ao Banco de Dados*/ 
	include "conexao.php";
  	$query=("select * from om where tipo<>'Não Integrante' and tipo='oc' order by sigla desc");

   $result = mysql_query($query);
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo "<tr bgcolor='#4A708B'><td colspan=3><coluna>Orgão Controlador: <font color=blue>$row[1]</td><td bgcolor='#6CA6CD'>&nbsp;</td></tr>";

		$result1 = mysql_query($query1);
		echo "<tr align='center' bgcolor='#4A708B'><td width='2%' bgcolor='#6CA6CD'></td><td width='40%'><coluna>Organização Militar</td><td width='8%'><coluna>Qtd de Vtr</td><td width='50%' bgcolor='#6CA6CD'>&nbsp;</td></tr>";
		while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
			echo"<tr bgcolor='#7EC0EE' ><td width='2%' bgcolor='#6CA6CD'></td><td><a name='$row1[1]'></a><lin>&nbsp;$row1[1] &nbsp;&nbsp;&nbsp;<a href='index.php?nomeArquivo=cons_viatura.php"; if ($_GET['listar']==$row1[0]){ echo "' style='text-decoration:none'>Fechar";} else { echo "&listar=$row1[0]#$row1[1]' style='text-decoration:none'>Listar";} echo "</td><td align='center'><lin>$row1[2]</td><td bgcolor='#6CA6CD'>&nbsp;</td></tr>";

			/*/Seleciona a relação de viaturas em carga*/ 
			$result2 = mysql_query("SELECT v.eb, v.modelo, v.marca, o.sigla, c.nome, v.disponivel, v.consumo
				FROM viatura v, om o, combustivel c 
				WHERE o.codom = v.om_codom
				AND c.codigo = v.combustivel_codigo
				AND o.codom = '$row1[0]'
				AND v.situacao='Em Carga'
				ORDER BY o.sigla");

			if ($_GET['listar']==$row1[0]){
				echo "<tr><td width='2%' bgcolor='#6CA6CD'></td><td colspan=3>";
				echo "<table border=0 width='100%'>";					
				echo "<tr align='center' bgcolor='#4A708B'><td width='2%' bgcolor='#6CA6CD'>&nbsp;</td><td colspan='7'><coluna>Relação de Viaturas em Carga do(a) $row1[1]</td></tr>";
				if (($_SESSION['perfil']=='CONSULTOR') or ($_SESSION['perfil']=='CONSULTOR *')){
					echo "<tr align='center' bgcolor='#4A708B'><td width='2%' bgcolor='#6CA6CD'>&nbsp;</td><td width='12%'><coluna>EB</td><td width='23%'><coluna>Viatura</td><td width='10%'><coluna>Disponível</td><td width='10%'><coluna>Combustível</td><td width='6%'><coluna>Consumo</td></tr>";
					while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
						echo"<tr bgcolor='#7EC0EE' ><td bgcolor='#6CA6CD'></td><td align='center'><lin>$row2[0]</td><td><lin>$row2[2] - $row2[1]</td><td align='center'><lin>$row2[5]</td><td align='center'><lin>$row2[4]</td><td align='center'><lin>$row2[6]</td></tr>";
					}
				} else {
					echo "<tr align='center' bgcolor='#7EC0EE'><td width='2%' bgcolor='#6CA6CD'>&nbsp;</td><td width='12%'><coluna>EB</td><td width='23%'><coluna>Viatura</td><td width='10%'><coluna>Disponível</td><td width='10%'><coluna>Combustível</td><td width='6%'><coluna>Consumo</td><td width='8%'><coluna>Alterar</td><td width='8%'><coluna>Remover</td></tr>";
					while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
						echo"<tr bgcolor='#7EC0EE' ><td bgcolor='#6CA6CD'></td><td align='center'><lin>$row2[0]</td><td><lin>$row2[2] - $row2[1]</td><td align='center'><lin>$row2[5]</td><td align='center'><lin>$row2[4]</td><td align='center'><lin>$row2[6]</td><td align='center'><lin><a href='index.php?nomeArquivo=alterar_viatura.php&&eb=$row2[0]'>Alterar</td><td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&&valor=$row2[0]&&tab=viatura&&ch=eb&&registro=a Viatura $row[1] - EB $row[0]&&arquivo=remover_viatura.php'>Remover</td></tr>";
    				}
				}
    			echo "</table></td></tr>";					
			}

			/*Seleciona a relação de viaturas descarregadas*/ 
			$result2 = mysql_query("SELECT v.eb, v.modelo, v.marca, o.sigla, c.nome, v.disponivel, v.consumo
				FROM viatura v, om o, combustivel c 
				WHERE o.codom = v.om_codom
				AND c.codigo = v.combustivel_codigo
				AND o.codom = '$row1[0]'
				AND v.situacao='Descarregada'
				ORDER BY o.sigla");

			if ($_GET['listar']==$row1[0]){
				echo "<tr><td width='2%' bgcolor='#6CA6CD'></td><td colspan=3>";
				echo "<table border=0 width='100%'>";					
				echo "<tr align='center' bgcolor='#4A708B'><td width='2%' bgcolor='#6CA6CD'>&nbsp;</td><td colspan='7'><coluna>Relação de Viaturas Descarregadas</td></tr>";
				if (($_SESSION['perfil']=='CONSULTOR') or ($_SESSION['perfil']=='CONSULTOR *')){
					echo "<tr align='center' bgcolor='#4A708B'><td width='2%' bgcolor='#6CA6CD'>&nbsp;</td><td width='12%'><coluna>EB</td><td width='23%'><coluna>Viatura</td><td width='10%'><coluna>Disponível</td><td width='10%'><coluna>Combustível</td><td width='6%'><coluna>Consumo</td></tr>";
					while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
						echo"<tr bgcolor='#7EC0EE' ><td bgcolor='#6CA6CD'></td><td align='center'><lin>$row2[0]</td><td><lin>$row2[2] - $row2[1]</td><td align='center'><lin>$row2[5]</td><td align='center'><lin>$row2[4]</td><td align='center'><lin>$row2[6]</td></tr>";
					}
				} else {
					echo "<tr align='center' bgcolor='#7EC0EE'><td width='2%' bgcolor='#6CA6CD'>&nbsp;</td><td width='12%'><coluna>EB</td><td width='23%'><coluna>Viatura</td><td width='10%'><coluna>Disponível</td><td width='10%'><coluna>Combustível</td><td width='6%'><coluna>Consumo</td><td width='8%'><coluna>Alterar</td><td width='8%'><coluna>Remover</td></tr>";
					while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
						echo"<tr bgcolor='#7EC0EE' ><td bgcolor='#6CA6CD'></td><td align='center'><lin>$row2[0]</td><td><lin>$row2[2] - $row2[1]</td><td align='center'><lin>$row2[5]</td><td align='center'><lin>$row2[4]</td><td align='center'><lin>$row2[6]</td><td align='center'><lin><a href='index.php?nomeArquivo=alterar_viatura.php&&eb=$row2[0]'>Alterar</td><td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&&valor=$row2[0]&&tab=viatura&&ch=eb&&registro=a Viatura $row[1] - EB $row[0]&&arquivo=remover_viatura.php'>Remover</td></tr>";
    				}
				}
	    		echo "</table></td></tr>";					
			}

		}
		echo "<tr><td>&nbsp;</td></tr>";
	}
   
   
   
	echo "<tr bgcolor='#4A708B'><td colspan=3><coluna>Organizações Militares Não Integrantes do Sistema </td><td bgcolor='#6CA6CD'>&nbsp;</td></tr>";
	$query1=("select o.codom, o.sigla, count(v.eb) from om o, viatura v where o.codom=v.om_codom and o.tipo='Não Integrante' group by v.om_codom order by o.sigla desc");

	$result1 = mysql_query($query1);
	echo "<tr align='center' bgcolor='#4A708B'><td width='2%' bgcolor='#6CA6CD'></td><td width='40%'><coluna>Organização Militar</td><td width='8%'><coluna>Qtd de Vtr</td><td width='50%' bgcolor='#6CA6CD'>&nbsp;</td></tr>";
	while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
		echo"<tr bgcolor='#7EC0EE' ><td width='2%' bgcolor='#6CA6CD'></td><td><a name='$row1[1]'></a><lin>&nbsp;$row1[1] &nbsp;&nbsp;&nbsp;<a href='index.php?nomeArquivo=cons_viatura.php"; if ($_GET['listar']==$row1[0]){ echo "' style='text-decoration:none'>Fechar";} else { echo "&&listar=$row1[0]#$row1[1]' style='text-decoration:none'>Listar";} echo "</td><td align='center'><lin>$row1[2]</td><td bgcolor='#6CA6CD'>&nbsp;</td></tr>";

		/*/Seleciona a relação de viaturas*/
		$result2 = mysql_query("SELECT v.eb, v.modelo, v.marca, o.sigla, c.nome, v.disponivel, v.consumo
				FROM viatura v, om o, combustivel c 
				WHERE o.codom = v.om_codom
				AND c.codigo = v.combustivel_codigo
				AND o.codom = '$row1[0]'
				ORDER BY o.sigla");

		if ($_GET['listar']==$row1[0]){
			echo "<tr><td width='2%' bgcolor='#6CA6CD'></td><td colspan=3><table>";					
			if (($_SESSION['perfil']!='ABASTECEDOR') and ($_SESSION['perfil']!='ADMINISTRADOR *') and ($_SESSION['perfil']!='ADMINISTRADOR')){
				echo "<tr align='center' bgcolor='#4A708B'><td width='2%' bgcolor='#6CA6CD'>&nbsp;</td><td width='12%'><coluna>EB</td><td width='23%'><coluna>Viatura</td><td width='10%'><coluna>Disponível</td><td width='10%'><coluna>Combustível</td><td width='6%'><coluna>Consumo</td></tr>";
				while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
					echo"<tr bgcolor='#7EC0EE' ><td bgcolor='#6CA6CD'></td><td align='center'><lin>$row2[0]</td><td><lin>$row2[2] - $row2[1]</td><td align='center'><lin>$row2[5]</td><td align='center'><lin>$row2[4]</td><td align='center'><lin>$row2[6]</td></tr>";
				}
			} else {
				echo "<tr align='center' bgcolor='#7EC0EE'><td width='2%' bgcolor='#6CA6CD'>&nbsp;</td><td width='12%'><coluna>EB</td><td width='23%'><coluna>Viatura</td><td width='10%'><coluna>Disponível</td><td width='10%'><coluna>Combustível</td><td width='6%'><coluna>Consumo</td><td width='8%'><coluna>Alterar</td><td width='8%'><coluna>Remover</td></tr>";
				while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
					echo"<tr bgcolor='#7EC0EE' ><td bgcolor='#6CA6CD'></td><td align='center'><lin>$row2[0]</td><td><lin>$row2[2] - $row2[1]</td><td align='center'><lin>$row2[5]</td><td align='center'><lin>$row2[4]</td><td align='center'><lin>$row2[6]</td><td align='center'><lin><a href='index.php?nomeArquivo=alterar_viatura.php&&eb=$row2[0]'>Alterar</td><td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&&valor=$row2[0]&&tab=viatura&&ch=eb&&registro=a Viatura $row[1] - EB $row[0]&&arquivo=remover_viatura.php'>Remover</td></tr>";
    			}
			}
    		echo "</table></td></tr>";					
		}
	}
	echo "<tr><td>&nbsp;</td></tr>";

	echo "</table>";

?>


