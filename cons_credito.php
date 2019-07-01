<?php
  if(!isset($_SESSION['usuario']))
    include "verifica_sessao.php";
?>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
</head>

<?php 
	echo "<font size='5'><br><t1>Crédito das Organizações Militares</b></font> <p>";
	if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='ADMINISTRADOR')) {
		echo "<div align='left'><t2><a href='index.php?nomeArquivo=cad_credito.php' style='text-decoration:none'>&nbsp;&nbsp;Cadastrar Novo Crédito<IMG SRC='./imagens/novo.png' width='25' height='25' border='0'></a><br>";
	}

	echo "<table border='0' width='100%' align='center' valign='top'>";

  	/*/Seleciona a relacao de Unidades*/
	$qry_oc="SELECT distinct om1.codom, om1.sigla
				FROM om om1, om om2
				where 
					om1.codom=om2.subordinacao 
					AND om1.subordinacao is not null ";
	if(($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='CMT DA OM')) {		
		$qry_oc .= "AND om1.codom='".$_SESSION['om_enq']."' ";
	}
	$qry_oc .= "ORDER BY abs(substring(om1.sigla,6)), om1.sigla";

	$result = mysql_query($qry_oc);
  	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo "<tr bgcolor='$cor1'><td colspan='10'><coluna>&nbsp;&nbsp;Comando:&nbsp;<font color='$cor_submenu'>$row[1]</td></tr>";
		echo "<tr align='center' bgcolor='$cor2'>
					<td width='3%' bgcolor='$cor_fundo'></td>
					<td width='15%' ><coluna>OM</td>
					<td width='10%'><coluna>Combustível</td>
					<td width='10%'><coluna>Cota</td>
					<td width='20%'><coluna>Crédito</td>
					<td width='10%'><coluna>Quantidade</td>
					<td width='10%'><coluna>Saldo</td>";
			if (($_SESSION['perfil']=='ADMINISTRADOR *') OR ($_SESSION['perfil']=='ADMINISTRADOR')){
				echo "<td width='10%'><coluna>Alterar</td>
					<td width='5%'><coluna>Remover</td>";
			}
		echo "<td width='2%' bgcolor='$cor_fundo'>&nbsp;</td></tr>";

		$qry_om = "select om.codom, om.sigla, ifnull(c.cod_credito,'-'),  ifnull(t.tipo_cota,'-'), ifnull(c.desc_credito,'-'), ifnull(c.qtd_destinada,0), ifnull(c.qtd_atual,0), cb.nome
						from om LEFT JOIN credito c on (om.codom=c.om_codom) LEFT JOIN tipo_cota t on (t.cod_tp_cota=c.cod_tp_cota) left join combustivel cb on (cb.codigo = c.combustivel_codigo)
						where subordinacao = '$row[0]' ";
		if(($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='CMT DA OM')) {		
			$qry_om .= "AND om.codom='".$_SESSION['codom']."' ";
		}
		$qry_om .= "order by abs(om.sigla), om.sigla, cb.nome, c.cod_tp_cota, c.cod_credito ";
  		$result1 = mysql_query($qry_om);
   		
  		while ($row1 = mysql_fetch_array($result1, MYSQL_NUM)) {
				echo "<tr bgcolor='$cor3' class='marcar_linha'>";
					echo "<td width='2%' bgcolor='$cor_fundo'><a name='$row1[1]'></a></td>";
					if($om_ant!=$row1[1]) {
						echo "<td><lin>$row1[1]</td>";
					} else {
						echo "<td bgcolor='$cor_fundo'><lin></td>";
					}
					echo "<td align='center'><lin>$row1[7]</td>";
					echo "<td align='center'><lin>$row1[3]</td>";
					echo "<td><lin>$row1[2] - $row1[4]</td>";
					echo "<td align='right'><lin>".number_format($row1[5],0,'','.')." Lts</td>";
					echo "<td align='right'><lin>".number_format($row1[6],0,'','.')." Lts</td>";
				if ($_SESSION['perfil']=='ADMINISTRADOR *') {
					echo"<td align='center'><lin><a href='index.php?nomeArquivo=alterar_credito.php&cod_credito=$row1[2]&codom=$row1[0]'>Alterar</td>
							<td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&tab=credito&ch=cod_credito&valor=$row1[2]&ch1=om_codom&valor1=$row1[0]&registro=o Crédito $row1[4] ($row1[3]) da OM $row1[1]&arquivo=remover_credito.php&ancora=$row1[1]'>Remover</td>
							</tr>";
				} elseif ($_SESSION['perfil']=='ADMINISTRADOR'){
					if(($_SESSION['codom']==$row1[0]) or ($_SESSION['codom']==$row1[5])) {
						echo"<td align='center'><lin><a href='index.php?nomeArquivo=alterar_om.php&&codom=$row1[0]'>Alterar</td><td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&&valor=$row1[0]&&tab=om&&ch=codom&&registro=a OM $row1[1]&&arquivo=remover_om.php'>Remover</td></tr>";
					} else {
						echo"<td align='center'><lin>-</td><td align='center'><lin>-</td></tr>";
					}
				}
				$om_ant=$row1[1];
  		}
  		echo"<tr bgcolor='$cor_fundo'><td colspan=6>&nbsp;</td></tr>";
  	}
    	
	echo "</table>";
	
?>

