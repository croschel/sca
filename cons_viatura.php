<?php
  if(!isset($_SESSION['usuario']))
    include "verifica_sessao.php";
?>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
</head>

<?php    
	echo "<br><t1>Relação de Viaturas Cadastradas no Sistema</t1></font> <p>";

	echo "<table border='0' width='100%' align='center' valign='top'>";

	echo "<tr><td colspan='6' align='center'>";
	echo "<hr><form method='GET' action='index.php?nomeArquivo=cons_viatura.php' name='viatura'>";
	echo "<input type='hidden' name='nomeArquivo' value='cons_viatura.php'>";
	echo "<t2><i><font color=blue>Localizar uma Viatura: <input type='text' size='40' maxlength='50' name='eb' value='".$_GET['eb']."' title='Para localizar uma viatura digite o EB, a placa ou o modelo da viatura e pressione <<Executar>>.'>";
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
				echo "<td bgcolor='$cor3' colspan='2'><lin><a href='index.php?nomeArquivo=cons_viatura.php&listar=$vtr[7]&eb=$vtr[1]#aqui' style='text-decoration:none'>$vtr[1]</a></lin></td>";
				echo "<td bgcolor='$cor3' ><lin>$vtr[3] - $vtr[2]</lin></td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	echo "</td</tr>";

	if (($_SESSION['perfil']!='CONSULTOR') and ($_SESSION['perfil']!='CONSULTOR *') and ($_SESSION['perfil']!='ABASTECEDOR')){
		echo "<div align='left'><t2><a href='index.php?nomeArquivo=cad_viatura.php' style='text-decoration:none'>&nbsp;&nbsp;Cadastrar Viatura<IMG SRC='./imagens/novo.png' width='25' height='25' border=0></a>";
	}

	/*Busca os órgãos controladores*/
  	$query="SELECT distinct om1.codom, om1.sigla
				FROM om om1, om om2
				where 
					om1.codom=om2.subordinacao 
					AND om1.subordinacao is not null
					ORDER BY abs(om1.sigla), om1.sigla";
   	

   $result = mysql_query($query);
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo "<tr bgcolor='$cor1'><td colspan='3'><coluna>&nbsp;&nbsp;Comando: <font color='$cor_submenu'>$row[1]</td><td bgcolor='$cor_fundo'>&nbsp;</td></tr>";
		$query1= "select o.codom, o.sigla, count(v.eb) 
					from om o, viatura v 
					where o.codom=v.om_codom and v.situacao='Em Carga' and (o.subordinacao='$row[0]' or o.codom='$row[0]') 
					group by v.om_codom 
					order by abs(o.sigla)";

		$result1 = mysql_query($query1);
		echo "<tr align='center' bgcolor='$cor2'><td width='2%' bgcolor='$cor_fundo'></td><td width='40%'><coluna>Organização Militar</td><td width='8%'><coluna>Qtd de Vtr</td><td width='50%' bgcolor='$cor_fundo'>&nbsp;</td></tr>";
		while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
			echo"<tr bgcolor='$cor3' class='marcar_linha'><td width='2%' bgcolor='$cor_fundo'></td><td><a name='$row1[1]'></a><lin>&nbsp;$row1[1] &nbsp;&nbsp;&nbsp;<a href='index.php?nomeArquivo=cons_viatura.php"; if ($_GET['listar']==$row1[0]){ echo "' style='text-decoration:none'>Fechar";} else { echo "&listar=$row1[0]#$row1[1]' style='text-decoration:none'>Listar";} echo "</td><td align='center'><lin>$row1[2]</td><td bgcolor='$cor_fundo'>&nbsp;</td></tr>";

			/*/Seleciona a relao de viaturas em carga*/ 
			$qry_vtr = "SELECT v.eb, v.modelo, v.marca, o.sigla, c.nome, v.disponivel, v.consumo, o.oc, o.codom
				FROM viatura v, om o, combustivel c 
				WHERE o.codom = v.om_codom
				AND c.codigo = v.combustivel_codigo
				AND o.codom = '$row1[0]'
				AND v.situacao='Em Carga'
				ORDER BY abs(o.sigla)";
			$result2 = mysql_query($qry_vtr);

			if ($_GET['listar']==$row1[0]){
				echo "<tr><td width='2%' bgcolor='$cor_fundo'></td><td colspan=3>";
				echo "<table border='0' width='100%'>";					
				echo "<tr align='center' bgcolor='$cor2'><td width='2%' bgcolor='$cor_fundo'>&nbsp;</td><td colspan='7'><coluna>Relação de Viaturas em Carga do(a) $row1[1]</td></tr>";
				echo "<tr align='center' bgcolor='$cor3'><td width='2%' bgcolor='$cor_fundo'>&nbsp;</td><td width='12%'><coluna>EB</td><td width='23%'><coluna>Viatura</td><td width='10%'><coluna>Disponível</td><td width='10%'><coluna>Combustível</td><td width='6%'><coluna>Consumo</td><td width='8%'><coluna>Alterar</td><td width='8%'><coluna>Remover</td></tr>";
				while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
					if($_GET['eb']==$row2[0]) {
						echo"<tr bgcolor='$destaque'><td bgcolor='$cor_fundo'></td><td align='center'><a name='aqui'><coluna>$row2[0]</td><td><coluna>$row2[2] - $row2[1]</td><td align='center'><coluna>$row2[5]</td><td align='center'><coluna>$row2[4]</td><td align='center'><coluna>$row2[6] km/l</td>";
					} else {
						echo"<tr bgcolor='$cor3' class='marcar_linha'><td bgcolor='$cor_fundo'></td><td align='center'><lin>$row2[0]</td><td><lin>$row2[2] - $row2[1]</td><td align='center'><lin>$row2[5]</td><td align='center'><lin>$row2[4]</td><td align='center'><lin>$row2[6] km/l</td>";
					}
					if ($_SESSION['perfil']=='ADMINISTRADOR *') {
						echo"<td align='center'><lin><a href='index.php?nomeArquivo=alterar_viatura.php&eb=$row2[0]'>Alterar</a></td><td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&valor=$row2[0]&tab=viatura&ch=eb&registro=a Viatura $row2[1] $row2[2] - EB ou Placa $row2[0]&arquivo=remover_viatura.php&valor1=".$_GET['listar']."&ancora=$row1[1]'>Remover</a></td>";
    				} elseif (($_SESSION['perfil']=='ADMINISTRADOR') and ($_SESSION['codom']==$row2[7])) { 
						echo"<td align='center'><lin><a href='index.php?nomeArquivo=alterar_viatura.php&eb=$row2[0]'>Alterar</a></td><td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&valor=$row2[0]&tab=viatura&ch=eb&registro=a Viatura $row2[1] $row2[2] - EB ou Placa $row2[0]&arquivo=remover_viatura.php&valor1=".$_GET['listar']."&ancora=$row1[1]'>Remover</a></td>";
    				} elseif ((($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='CMT DA OM')) and ($_SESSION['codom']==$row2[8])) { 
						echo"<td align='center'><lin><a href='index.php?nomeArquivo=alterar_viatura.php&eb=$row2[0]'>Alterar</a></td><td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&valor=$row2[0]&tab=viatura&ch=eb&registro=a Viatura $row2[1] $row2[2] - EB ou Placa $row2[0]&arquivo=remover_viatura.php&valor1=".$_GET['listar']."&ancora=$row1[1]'>Remover</a></td>";
    				} else { 
						echo"<td align='center'><lin>-</td><td align='center'><lin>-</td>";
    				}
    				echo "</tr>";
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
				echo "<tr><td width='2%' bgcolor='$cor_fundo'></td><td colspan='3'>";
				echo "<table border='0' width='100%'>";					
				echo "<tr align='center' bgcolor='$cor2'><td width='2%' bgcolor='$cor_fundo'>&nbsp;</td><td colspan='7'><coluna>Relação de Viaturas Descarregadas</td></tr>";
				if (($_SESSION['perfil']=='CONSULTOR') or ($_SESSION['perfil']=='CONSULTOR *')){
					echo "<tr align='center' bgcolor='$cor1'><td width='2%' bgcolor='$cor_fundo'>&nbsp;</td><td width='12%'><coluna>EB</td><td width='23%'><coluna>Viatura</td><td width='10%'><coluna>Disponível</td><td width='10%'><coluna>Combustível</td><td width='6%'><coluna>Consumo</td></tr>";
					while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
						echo"<tr bgcolor='$cor3' ><td bgcolor='$cor_fundo'></td><td align='center'><lin>$row2[0]</td><td><lin>$row2[2] - $row2[1]</td><td align='center'><lin>$row2[5]</td><td align='center'><lin>$row2[4]</td><td align='center'><lin>$row2[6]</td></tr>";
					}
				} else {
					echo "<tr align='center' bgcolor='$cor1'><td width='2%' bgcolor='$cor_fundo'>&nbsp;</td><td width='12%'><coluna>EB</td><td width='23%'><coluna>Viatura</td><td width='10%'><coluna>Disponível</td><td width='10%'><coluna>Combustível</td><td width='6%'><coluna>Consumo</td><td width='8%'><coluna>Alterar</td><td width='8%'><coluna>Remover</td></tr>";
					while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
						echo"<tr bgcolor='$cor3' ><td bgcolor='$cor_fundo'></td><td align='center'><lin>$row2[0]</td><td><lin>$row2[2] - $row2[1]</td><td align='center'><lin>$row2[5]</td><td align='center'><lin>$row2[4]</td><td align='center'><lin>$row2[6]</td><td align='center'><lin><a href='index.php?nomeArquivo=alterar_viatura.php&eb=$row2[0]'>Alterar</a></td><td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&valor=$row2[0]&tab=viatura&ch=eb&registro=a Viatura $row2[1] - EB ou Placa $row2[0]&arquivo=remover_viatura.php'>Remover</a></td></tr>";
    				}
				}
	    		echo "</table></td></tr>";					
			}

		}
		echo "<tr><td>&nbsp;</td></tr>";
	}
   
   
   
	echo "<tr bgcolor='$cor1'><td colspan=3><coluna>Organizações Militares Não Integrantes do Sistema </td><td bgcolor='$cor_fundo'>&nbsp;</td></tr>";
	$query1=("select o.codom, o.sigla, count(v.eb) from om o, viatura v where o.codom=v.om_codom and o.tipo='Não Integrante' group by v.om_codom order by o.sigla desc");

	$result1 = mysql_query($query1);
	echo "<tr align='center' bgcolor='$cor2'><td width='2%' bgcolor='$cor_fundo'></td><td width='40%'><coluna>Organização Militar</td><td width='8%'><coluna>Qtd de Vtr</td><td width='50%' bgcolor='$cor_fundo'>&nbsp;</td></tr>";
	while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
		echo"<tr bgcolor='$cor3'><td width='2%' bgcolor='$cor_fundo'></td><td><a name='$row1[1]'></a><lin>&nbsp;$row1[1] &nbsp;&nbsp;&nbsp;<a href='index.php?nomeArquivo=cons_viatura.php"; if ($_GET['listar']==$row1[0]){ echo "' style='text-decoration:none'>Fechar";} else { echo "&&listar=$row1[0]#$row1[1]' style='text-decoration:none'>Listar";} echo "</a></td><td align='center'><lin>$row1[2]</td><td bgcolor='$cor_fundo'>&nbsp;</td></tr>";

		/*/Seleciona a relação de viaturas*/
		$result2 = mysql_query("SELECT v.eb, v.modelo, v.marca, o.sigla, c.nome, v.disponivel, v.consumo
				FROM viatura v, om o, combustivel c 
				WHERE o.codom = v.om_codom
				AND c.codigo = v.combustivel_codigo
				AND o.codom = '$row1[0]'
				ORDER BY o.sigla");

		if ($_GET['listar']==$row1[0]){
			echo "<tr><td width='2%' bgcolor='$cor_fundo'></td><td colspan='3'>";					
			echo "<table border='0' width='100%'>";
			echo "<tr align='center' bgcolor='$cor1'><td width='2%' bgcolor='$cor_fundo'>&nbsp;</td><td width='12%'><coluna>EB</td><td width='23%'><coluna>Viatura</td><td width='10%'><coluna>Disponível</td><td width='10%'><coluna>Combustível</td><td width='6%'><coluna>Consumo</td><td width='8%'><coluna>Alterar</td><td width='8%'><coluna>Remover</td></tr>";
			while ($row2 = mysql_fetch_array($result2, MYSQL_NUM)) {
				if($_GET['eb']==$row2[0]) {
					echo"<tr bgcolor='$destaque'><td bgcolor='$cor_fundo'></td><td align='center'><a name='aqui'><coluna>$row2[0]</td><td><coluna>$row2[2] - $row2[1]</td><td align='center'><coluna>$row2[5]</td><td align='center'><coluna>$row2[4]</td><td align='center'><coluna>$row2[6] km/l</td>";
				} else {
					echo"<tr bgcolor='$cor3' class='marcar_linha'><td bgcolor='$cor_fundo'></td><td align='center'><lin>$row2[0]</td><td><lin>$row2[2] - $row2[1]</td><td align='center'><lin>$row2[5]</td><td align='center'><lin>$row2[4]</td><td align='center'><lin>$row2[6] km/l</td>";
					}
//				echo"<tr bgcolor='$cor3'><td bgcolor='$cor_fundo'></td><td align='center'><lin>$row2[0]</td><td><lin>$row2[2] - $row2[1]</td><td align='center'><lin>$row2[5]</td><td align='center'><lin>$row2[4]</td><td align='center'><lin>$row2[6]</td>";
				if (($_SESSION['perfil']=='FISCAL DA OM') OR ($_SESSION['perfil']=='ADMINISTRADOR *') OR ($_SESSION['perfil']=='ADMINISTRADOR')){
					echo"<td align='center'><lin><a href='index.php?nomeArquivo=alterar_viatura.php&eb=$row2[0]'>Alterar</a></td><td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&valor=$row2[0]&tab=viatura&ch=eb&registro=a Viatura $row2[1] - EB ou Placa $row2[0]&arquivo=remover_viatura.php'>Remover</a></td></tr>";
    			} else {
					echo"<td align='center'><lin>-</td><td align='center'><lin>-</td></tr>";
    			}
		}
    		echo "</table></td></tr>";					
		}
	}
	echo "<tr><td>&nbsp;</td></tr>";

	echo "</table>";

?>


