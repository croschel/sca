<?php
  if(empty($_SESSION['usuario']))
    include "verifica_sessao.php";
$ano_ant = date("Y")-1;
?>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<?php
	echo "<table border='0' bgcolor='$cor_fundo' width='100%' align='left' valign='top'>";
	echo "<tr align='center'><td COLSPAN='9'>&nbsp;</td></tr>";
	echo "<tr align='center'><td COLSPAN='9'><b><font size='5'><I>Consumo Médio de Viaturas</b><br>(Período de ".date('d')."/".date('m')."/$ano_ant  ".date('d')."/".date('m')."/".date('Y').")</I></td></tr>";
	echo "<tr align='center'><td COLSPAN='9'>&nbsp;</td></tr>";
    
   /*Conexão ao Banco de Dados*/
	include "conexao.php";

	$query = "SELECT distinct om1.codom, om1.sigla
				FROM om om1, om om2
				where om1.codom=om2.subordinacao 
					AND om1.subordinacao is not null	";
	if (($_SESSION['perfil']=='ADMINISTRADOR') OR ($_SESSION['perfil']=='CONSULTOR')){		
		$query .= "and om1.codom='".$_SESSION['codom']."' ";
	}
	elseif ($_SESSION['perfil']=='FISCAL DA OM'){		
		$query .= "AND om1.codom='".$_SESSION['om_enq']."' ";
		$_GET['oc']=$_SESSION['om_enq'];
		$_GET['codom']=$_SESSION['codom'];
	}
	$query .= "ORDER BY abs(substring(om1.sigla,6)), om1.sigla ";
	
	$res=mysql_query($query);
	while ($oc = mysql_fetch_array($res, MYSQL_NUM)) {
		if($oc[0]==$_GET['oc']){
			echo "<tr align='center'><td COLSPAN='9'>&nbsp;</td></tr>";
			echo "<tr bgcolor='$cor1' align='left'><td COLSPAN='9'><b>&nbsp;GU:&nbsp;&nbsp;<i><a href='index.php?nomeArquivo=cons_consumo.php&oc=$oc[0]' style='text-decoration:none'><font color='$cor_submenu'>$oc[1]</font></a></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')){
				echo " <a href='index.php?nomeArquivo=cons_consumo.php' style='text-decoration:none'>Fechar</a>";
			}
			echo "</td></tr>";

			$query1 = "SELECT *
					FROM om
					WHERE subordinacao = '".$oc[0]."' ";
					
			if ($_SESSION['perfil']=='FISCAL DA OM'){
				$query1 .= "and codom='".$_SESSION['codom']."' ";
			}

			$query1 .= "ORDER BY abs(substring(sigla,6)), sigla ";

			$res1 = mysql_query($query1);		
				$cab=0;
			while ($om = mysql_fetch_array($res1, MYSQL_NUM)) {
				if($om[0]==$_GET['codom']){
					echo "<tr align='center' bgcolor='$cor_fundo'><td COLSPAN='9'>&nbsp;</td></tr>";
					echo "<tr bgcolor='$cor2' align='left'><td bgcolor='$cor_fundo'></td><td COLSPAN='8'><b>OM:&nbsp;&nbsp;<i><a href='index.php?nomeArquivo=cons_consumo.php&oc=$oc[0]&codom=$om[0]' style='text-decoration:none'><font color='$cor_submenu'>$om[1]</font></a></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *') or ($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')){					
						echo "<a href='index.php?nomeArquivo=cons_consumo.php&oc=$oc[0]' style='text-decoration:none'>Fechar</a>";
					}
					echo "</td></tr>";

					$total_gas=0;

					$query2 = "SELECT v.eb, v.marca, v.modelo, c.nome, v.consumo
						FROM viatura v,  combustivel c
						WHERE c.codigo = v.combustivel_codigo
						and v.om_codom='".$om[0]."'
						and v.situacao='Em Carga' 
						ORDER BY 2,3,1";
					$res2 = mysql_query($query2);
					while ($viatura = mysql_fetch_array($res2, MYSQL_NUM)) {
						$resultado2 = mysql_query("SELECT qtd_abastecida , od_atual, dt_abastecimento, horimetro
						FROM abastecimento 
						WHERE viatura_eb = '".$viatura[0]."'
						and dt_abastecimento between '".$ano_ant."-".date("m")."-".date("d")."' and '".date("y")."-".date("m")."-".date("d")." 23:59'
						and qtd_abastecida is not null
						order by dt_abastecimento"); 
						/*Busca o consumo médio das viaturas no período de um ano atras*/
						$i=0;
						if($cab==0) {
							echo "<tr align='center' bgcolor='$cor1'><td bgcolor='$cor_fundo'>&nbsp;&nbsp;</td><td><b>EB</td><td><b>Viatura</td><td><b>Combustível</td><td><b>Média Nominal</td>
								<td><b>Km Rodados</td><td><b>Qtd Abastecida</td><td><b>Média Km/L</td><td><b>Média L/H</td></tr>";
							$cab=1;
						}
						while ($consumo = mysql_fetch_array($resultado2, MYSQL_NUM)) {
						$qtd_abast[$i] = $consumo[0];/*captura o ultimo abastecimento*/
						$odometro[$i] = $consumo[1];/*verifica os odometros dos abastecimentos*/
						$horimetro[$i] = $consumo[3];
						$i++;
						}
						for ($x=0; $x<=$i-1; $x++){
							$qtd = $qtd + $qtd_abast[$x-1];/*somatorio das quantidades abastecidas para o calculo da media efetiva*/
						}
						$qtd_abastecida=0;
						for ($x=0; $x<=$i; $x++){
							$qtd_abastecida = $qtd_abastecida + $qtd_abast[$x-1];/*somatorio das quantidades abastecidas*/
						}
						$km=$odometro[$i-1]-$odometro[0];/*calcula a qtd de km percorridos*/
						$hs=$horimetro[$i-1]-$horimetro[0];/*calcula a qtd de horas decorridas*/
						if ($qtd > 0){/*evita a divisão por zero*/
							$media=$km/$qtd;/*calcula a média*/
							if($hs>0) {
								$media_hs = $qtd/$hs;/*calcula a média de litros por hora*/
							}
						}
						$decimal=substr($media,strpos($media,'.'),3);
						$inteiro=substr($media,0,strpos($media,'.'));
						echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'><td bgcolor='$cor_fundo'>&nbsp;</td>
									<td><a href='index.php?nomeArquivo=cons_consumo_vtr.php&viatura_eb=$viatura[0]&medio=$inteiro$decimal&percorrido=$km&oc=".$_GET['oc']."&codom=".$_GET['codom']."' style='text-decoration:none'>$viatura[0]</a></td>
									<td>$viatura[1] - $viatura[2] </td><td>$viatura[3] </td><td>$viatura[4] Km/L</td><td>$km Km</td><td>$qtd_abastecida Lts</td>
									<td>$inteiro$decimal Km/L</td><td>".number_format($media_hs, 1, ',', '.')." L/H</td></tr>";
						$media = $qtd = $ult_abast = $media_hs = 0;/*limpa a média e a qtd abastecida*/
						$odometro = NULL;
					} 
					if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *') or ($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')){					
						echo "<tr align='center' bgcolor='$cor1'><td bgcolor='$cor_fundo'>&nbsp;&nbsp;</td><td COLSPAN='8'><a href='index.php?nomeArquivo=cons_consumo.php&oc=$oc[0]' style='text-decoration:none'>Fechar</a></td></tr>";
					}
					echo "<tr align='center'><td COLSPAN='9'>&nbsp;</td></tr>";
				} else {
					if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *') or ($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')){
						echo "<tr bgcolor='$cor2' align='left'><td bgcolor='$cor_fundo'></td><td COLSPAN='8'><b>OM: <i><a href='index.php?nomeArquivo=cons_consumo.php&oc=$oc[0]&codom=$om[0]' style='text-decoration:none'><font color='$cor_submenu'>$om[1]</font></a></td></tr>";
					}
				}
			}

			if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')) {
				echo "<tr align='center' bgcolor='$cor_fundo'><td COLSPAN='9'><a href='index.php?nomeArquivo=cons_consumo.php' style='text-decoration:none'>Fechar</a></td></tr>";
			}
			echo "<tr align='center' bgcolor='$cor_fundo'><td COLSPAN='9'>&nbsp;</td></tr>";
		} else {
			echo "<tr bgcolor='$cor1' align='left'><td COLSPAN='9'><b>&nbsp;GU: <i><a href='index.php?nomeArquivo=cons_consumo.php&oc=$oc[0]' style='text-decoration:none'><font color='$cor_submenu'>$oc[1]</font></a></td></tr>";
		}
	}

	echo "</table>";
?>