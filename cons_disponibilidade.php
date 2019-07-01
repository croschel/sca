<?php
  if(!isset($_SESSION['usuario']))
    include "verifica_sessao.php";
?>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<?php    
	echo "<table border='0' bgcolor='$cor_fundo' width='100%' align='left' valign='top'>";
	echo "<tr align='center'><td COLSPAN='8'>&nbsp;</td></tr>";
	echo "<tr align='center'><td COLSPAN='8'><b><font size='5'><I>Disponibilidade de Combustíveis</b></I></td></tr>";
   
   //############## RELACIONA OS COMANDOS ######################
   $query = "SELECT * FROM om 
   			 where 
					tipo in ('OC','Integrante') "; 
	if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')){
		$query .= "and nome like 'COMANDO%' ";
	}
	elseif (($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')){
		$query .= "and nome like 'COMANDO%'
				and codom='".$_SESSION['codom']."' ";
		$_GET['oc']=$_SESSION['codom'];
	}
	elseif ((($_SESSION['perfil']=='CMT DA OM') or ($_SESSION['perfil']=='FISCAL DA OM')) and ($_SESSION['gerente']!='SIM')){		
		$query .= "and nome like 'COMANDO%'
				and codom='".$_SESSION['oc']."' ";
				$_GET['oc']=$_SESSION['oc'];
				$_GET['codom']=$_SESSION['codom'];
	}
	elseif (((($_SESSION['perfil']=='CMT DA OM') or ($_SESSION['perfil']=='FISCAL DA OM')) and ($_SESSION['gerente']=='SIM')) or ($_SESSION['perfil']=='ABASTECEDOR')){		
		$query .= "	and nome like 'COMANDO%'
						and codom='".$_SESSION['oc']."'
					";
				$_GET['oc']=$_SESSION['oc'];
				if($_GET['codom']=='') {
					$_GET['codom']=$_SESSION['codom'];
				}
	}
	$query .= "ORDER BY abs(sigla), sigla ";
	//############################################################
	
	$resultado=mysql_query($query);
	while ($oc = mysql_fetch_array($resultado, MYSQL_NUM)) {
		if($oc[0]==$_GET['oc']){
			echo "<tr bgcolor='$cor1' align='left'><td COLSPAN='8'><coluna>&nbsp;<a href='index.php?nomeArquivo=cons_disponibilidade.php' style='text-decoration:none'><font color='$cor_submenu'>$oc[1]</font></a></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')){
				echo " <a href='index.php?nomeArquivo=cons_disponibilidade.php' style='text-decoration:none'>Fechar</a>";
			}			
			echo "</td></tr>";
			echo "<tr bgcolor='$cor1' align='left'><td bgcolor='$cor_fundo'>";
			echo "<table>";
			echo "<tr bgcolor='$cor1' align='center'><td bgcolor='$cor_fundo'>&nbsp;&nbsp;&nbsp;&nbsp;</td><td><coluna>OM</td><td><coluna>&nbsp;&nbsp;Gasolina&nbsp;&nbsp;</td><td><coluna>&nbsp;&nbsp;&nbsp;Diesel&nbsp;&nbsp;&nbsp;</td></tr>";


			//############# LISTA AS UNIDADES DOS COMANDOS ######################
			$qry_disp ="SELECT codom, sigla, oc
							FROM om
							WHERE ";
			if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')){			
				$qry_disp .="subordinacao='$oc[0]' or codom='$oc[0]' ";

			}
			elseif (($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')){			
				$qry_disp .="subordinacao='$oc[0]' or codom='$oc[0]' 
					union
					SELECT codom, sigla, oc
					FROM om 
					WHERE codom in(select e.om_codom from om, reservatorio r, estoque e where om.codom=r.om_codom and r.codigo=e.reservatorio_codigo and om.oc='$oc[0]')
					";
											
			}
			elseif ((($_SESSION['perfil']=='CMT DA OM') or ($_SESSION['perfil']=='FISCAL DA OM')) and ($_SESSION['gerente']!='SIM')){
				$qry_disp .="codom='".$_GET['codom']."' ";
			}
			elseif ((($_SESSION['perfil']=='CMT DA OM') or ($_SESSION['perfil']=='FISCAL DA OM')) and ($_SESSION['gerente']=='SIM')){		
				$qry_disp .="codom='".$_GET['codom']."' or codom in( select e.om_codom from reservatorio r, estoque e 
																					where r.codigo=e.reservatorio_codigo and r.om_codom='".$_SESSION['codom']."' and e.qtd_atual>0) ";
			}
			elseif ($_SESSION['perfil']=='ABASTECEDOR'){		
				$qry_disp .="codom in( select e.om_codom from reservatorio r, estoque e 
				                       where r.codigo=e.reservatorio_codigo and r.om_codom='".$_SESSION['codom']."') ";
			}
			$qry_disp .= "ORDER BY abs(sigla), sigla ";
			//################################################################

			
			$res_disp=mysql_query($qry_disp);
			while ($om = mysql_fetch_array($res_disp, MYSQL_NUM)) {

				//###### CALCULA A DISPONIBILIDADE DE GASOLINA DE OM ##########
				$qry_g ="SELECT ifnull(round(sum(e.qtd_atual)),0)
						FROM estoque e, reservatorio r
						WHERE r.codigo = e.reservatorio_codigo ";
						
				if($abast_sem_saldo!='s') {
					$qry_g .="and e.qtd_atual>0 ";
				}		
				
				//#### CALCULA A DISPONIBILIDADE DE DIESEL DE OM ###########
				$qry_d ="SELECT ifnull(round(sum(e.qtd_atual)),0)
						FROM estoque e, reservatorio r
						WHERE r.codigo = e.reservatorio_codigo ";
						
				if($abast_sem_saldo!='s') {
					$qry_d .="and e.qtd_atual>0 ";
				}		
						
				if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')){			
					$qry_g .="and e.om_codom='$om[0]' ";
					
					$qry_d .="and e.om_codom='$om[0]' ";
					
				} elseif (($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')){			
					$qry_g .= "and e.om_codom='$om[0]' 
								and (r.codigo in (select codigo from reservatorio where om_codom in (select codom from om where subordinacao='".$_GET['oc']."')) 
									or r.codigo in(select reservatorio_codigo from estoque where om_codom in (select codom from om where subordinacao='".$_GET['oc']."'))) ";
					
					$qry_d .= "and e.om_codom='$om[0]' 
								and (r.codigo in (select codigo from reservatorio where om_codom in (select codom from om where subordinacao='".$_GET['oc']."')) 
									or r.codigo in(select reservatorio_codigo from estoque where om_codom in (select codom from om where subordinacao='".$_GET['oc']."'))) ";
	
				} elseif (($_SESSION['perfil']=='CMT DA OM') or ($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='ABASTECEDOR')) {
					$qry_g .="and e.om_codom='$om[0]' ";
						if($_SESSION['codom']!=$om[0]) {
							$qry_g .= "and r.om_codom='".$_SESSION['codom']."' ";
						}				
						
					$qry_d .="and e.om_codom='$om[0]' ";
						if($_SESSION['codom']!=$om[0]) {
							$qry_d .= "and r.om_codom='".$_SESSION['codom']."' ";
						}
						
				}  
				$qry_g .= "and r.combustivel_codigo='1' ";
				$qry_d .= "and r.combustivel_codigo='2' ";

				$res_g=mysql_query($qry_g);
				$gas = mysql_fetch_row($res_g);
				$res_d=mysql_query($qry_d);
				$d = mysql_fetch_row($res_d);
				
				//Verifica o somatório dos abastecimentos autorizados e não realizados de gasolina
				$qrya = "select sum(qtd_autorizada) from abastecimento 
								where reservatorio_codigo in (select codigo from reservatorio where combustivel_codigo='1' ";
					if(($_SESSION['perfil']=='CMT DA OM') or ($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='ABASTECEDOR')) {
						$qrya .= "and reservatorio_codigo in(select codigo from reservatorio where om_codom='".$_SESSION['codom']."') ";
					}			
					$qrya .= ") 
									and cota_om='$om[0]' and qtd_abastecida is null ";
				
				$resa = mysql_query($qrya);
				$qtdg = mysql_fetch_row($resa);

				//Verifica o somatório dos abastecimentos autorizados e não realizados de gasolina
				$qrya = "select sum(qtd_autorizada) from abastecimento 
								where reservatorio_codigo in (select codigo from reservatorio where combustivel_codigo='2' ";
					if(($_SESSION['perfil']=='CMT DA OM') or ($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='ABASTECEDOR')) {
						$qrya .= "and reservatorio_codigo in(select codigo from reservatorio where om_codom='".$_SESSION['codom']."') ";
					}			
					$qrya .= ") 
									and cota_om='$om[0]' and qtd_abastecida is null";
				$resa = mysql_query($qrya);
				$qtdd = mysql_fetch_row($resa);


				////////////////////////////////////////////		

				//Se o sistema permite abastecimento com saldo negativo
				if($abast_sem_saldo=='s') {
					// Saldo abastecido e não abateu o saldo
				
					$qry_saldo_g = "select SUM(a.qtd_abastecida - ifnull(h.qtd_abastecida ,0))
							from abastecimento a LEFT JOIN 
									(SELECT dt_abastecimento, viatura_eb, sum(qtd_abastecida) qtd_abastecida 
										FROM `historico_abastecimento` 
										GROUP BY dt_abastecimento, viatura_eb
									) h on (a.dt_abastecimento=h.dt_abastecimento and a.viatura_eb=h.viatura_eb)
							where a.qtd_abastecida > ifnull(h.qtd_abastecida ,0)
								and a.cota_om='$om[0]'
                                and a.reservatorio_codigo in (select codigo from reservatorio where combustivel_codigo='1')
							order by a.dt_abastecimento ";

					$qry_saldo_d = "select SUM(a.qtd_abastecida - ifnull(h.qtd_abastecida ,0))
							from abastecimento a LEFT JOIN 
									(SELECT dt_abastecimento, viatura_eb, sum(qtd_abastecida) qtd_abastecida 
										FROM `historico_abastecimento` 
										GROUP BY dt_abastecimento, viatura_eb
									) h on (a.dt_abastecimento=h.dt_abastecimento and a.viatura_eb=h.viatura_eb)
							where a.qtd_abastecida > ifnull(h.qtd_abastecida ,0)
								and a.cota_om='$om[0]'
                                and a.reservatorio_codigo in (select codigo from reservatorio where combustivel_codigo='2')
							order by a.dt_abastecimento ";

					$saldo=mysql_query($qry_saldo_g);
					$saldo_gas = mysql_fetch_row($saldo);
					$saldo=mysql_query($qry_saldo_d);
					$saldo_d = mysql_fetch_row($saldo);
				}
				///////////////////////////////////////////


				if($_GET['codom']==$om[0]) {
					
					//Verifica o somatório dos abastecimentos autorizados e não realizados de gasolina
					$qrya = "select sum(qtd_autorizada) from abastecimento 
								where reservatorio_codigo in (select codigo from reservatorio where combustivel_codigo='1' ";
					if(($_SESSION['perfil']=='CMT DA OM') or ($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='ABASTECEDOR')) {
						$qrya .= "and reservatorio_codigo in(select codigo from reservatorio where om_codom='".$_SESSION['codom']."') ";
					}			
					$qrya .= ") 
									and cota_om='$om[0]' and qtd_abastecida is null";
					$resa = mysql_query($qrya);
					$qtdg = mysql_fetch_row($resa);

					//Verifica o somatório dos abastecimentos autorizados e não realizados de gasolina
					$qrya = "select sum(qtd_autorizada) from abastecimento 
								where reservatorio_codigo in (select codigo from reservatorio where combustivel_codigo='2' ";
					if(($_SESSION['perfil']=='CMT DA OM') or ($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='ABASTECEDOR')) {
						$qrya .= "and reservatorio_codigo in(select codigo from reservatorio where om_codom='".$_SESSION['codom']."') ";
					}			
					$qrya .= ") 
									and cota_om='$om[0]' and qtd_abastecida is null";
					$resa = mysql_query($qrya);
					$qtdd = mysql_fetch_row($resa);

					echo "<tr bgcolor='$cor2'>
								<td bgcolor='$cor_fundo'></td>
								<td><coluna>&nbsp;&nbsp;$om[1]&nbsp;&nbsp;</td>
								<td align='right'><coluna>&nbsp;".number_format(($gas[0]-$saldo_gas[0]-$qtdg[0]),0,'','.')." Lts&nbsp;</td>
								<td align='right'><coluna>&nbsp;".number_format(($d[0]-$saldo_d[0]-$qtdd[0]),0,'','.')." Lts&nbsp;</td>
							</tr>";	
					echo "<tr><td bgcolor='$cor_fundo'></td><td bgcolor='$cor3' align='center'> ";
						if(($_SESSION['perfil']!='FISCAL DA OM') and ($_SESSION['perfil']!='ABASTECEDOR')) {
							echo "<i><a href='index.php?nomeArquivo=cons_disponibilidade.php&oc=$oc[0]' style='text-decoration:none'>Fechar</a>";
						}
						echo "</td><td valign='top' bgcolor='$cor3'>";
					echo "<table bgcolor='$cor_fundo' align='center' width='100%'>";
					echo "<tr bgcolor='$cor2' align='center'><td><coluna>&nbsp;Local&nbsp;</td><td><coluna>&nbsp;Combustível&nbsp;</td><td><coluna>&nbsp;Qtd&nbsp;</td></tr>";	
					
					/*LOCALIZA O DEPOSITO E A QUANTIDADE DE GASOLINA*/
					$qry_g ="SELECT concat(om.sigla,' (',a.sigla,' )'), IFNULL( ROUND( SUM( e.qtd_atual ) ) , 0 ), t.tipo_cota, t.cod_tp_cota, r.codigo 
						FROM om, estoque e, reservatorio r, tipo_cota t, om a
						WHERE om.codom = r.om_codom and t.cod_tp_cota=e.cod_tp_cota
						and r.codigo = e.reservatorio_codigo
						and a.codom = om.subordinacao
						and e.om_codom='$om[0]' 
						and r.combustivel_codigo='1' ";
						
					if($abast_sem_saldo!='s') {
						$qry_g .= "and ROUND(e.qtd_atual) > 0	 ";
					}
						
						/*LOCALIZA O DEPOSITO E A QUANTIDADE DE DIESEL*/
					$qry_d ="SELECT concat(om.sigla,' (',a.sigla,' )'), IFNULL( ROUND( SUM( e.qtd_atual ) ) , 0 ), t.tipo_cota, t.cod_tp_cota, r.codigo 
						FROM om, estoque e, reservatorio r, tipo_cota t, om a
						WHERE om.codom = r.om_codom and t.cod_tp_cota=e.cod_tp_cota
						and r.codigo = e.reservatorio_codigo
						and a.codom = om.subordinacao
						and e.om_codom='$om[0]'
						and r.combustivel_codigo='2' ";
						
					if($abast_sem_saldo!='s') {
						$qry_d .= "and ROUND(e.qtd_atual) > 0	 ";
					}
						
					if (($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')){	
						/*LOCALIZA O DEPOSITO E A QUANTIDADE DE GASOLINA*/
						$qry_g .="and (r.codigo in (select codigo from reservatorio where om_codom in (select codom from om where subordinacao='".$_GET['oc']."')) 
									or r.codigo in(select reservatorio_codigo from estoque where om_codom in (select codom from om where subordinacao='".$_GET['oc']."'))) ";
						
						/*LOCALIZA O DEPOSITO E A QUANTIDADE DE DIESEL*/
						$qry_d .="and (r.codigo in (select codigo from reservatorio where om_codom in (select codom from om where subordinacao='".$_GET['oc']."')) 
									or r.codigo in(select reservatorio_codigo from estoque where om_codom in (select codom from om where subordinacao='".$_GET['oc']."'))) ";
						
					} elseif (($_SESSION['perfil']=='CMT DA OM') or ($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='ABASTECEDOR')) {	
						/*LOCALIZA O DEPOSITO E A QUANTIDADE DE GASOLINA*/
						if($_SESSION['codom']!=$om[0]) {
							$qry_g .= $qry_g."and r.om_codom='".$_SESSION['codom']."' ";
						}
						$qry_g.="and r.combustivel_codigo='1' ";
						
						/*LOCALIZA O DEPOSITO E A QUANTIDADE DE DIESEL*/
						if($_SESSION['codom']!=$om[0]) {
							$qry_d .="and r.om_codom='".$_SESSION['codom']."' ";
						}
						$qry_d .="and r.combustivel_codigo='2' ";
					} 
	
					$qry_g .="group by om.codom, t.cod_tp_cota, r.codigo
						order by om.subordinacao, abs(om.sigla), om.sigla, t.tipo_cota";

					$qry_d .="group by om.codom, t.cod_tp_cota, r.codigo
						order by om.subordinacao, abs(om.sigla), om.sigla, t.tipo_cota";

					$post_g=mysql_query($qry_g);
					while ($posto = mysql_fetch_array($post_g, MYSQL_NUM)) {
						//Verifica o somatório dos abastecimentos autorizados e não realizados de gasolina
						$qrya = "select ifnull(sum(qtd_autorizada),0) from abastecimento 
									where cod_tp_cota = '$posto[3]' 
										and reservatorio_codigo = '$posto[4]'
										and cota_om='$om[0]' and qtd_abastecida is null";
						$resa = mysql_query($qrya);
						$qtda = mysql_fetch_row($resa);
						
						//Se o sistema permite abastecimento com saldo negativo
						if($abast_sem_saldo=='s') {
							//Verifica o saldo negativo da OM					
							$qry_saldo_g = "select SUM(a.qtd_abastecida - ifnull(h.qtd_abastecida ,0))
							from abastecimento a LEFT JOIN 
									(SELECT dt_abastecimento, viatura_eb, sum(qtd_abastecida) qtd_abastecida 
										FROM `historico_abastecimento` 
										GROUP BY dt_abastecimento, viatura_eb
									) h on (a.dt_abastecimento=h.dt_abastecimento and a.viatura_eb=h.viatura_eb)
							where a.qtd_abastecida > ifnull(h.qtd_abastecida ,0)
								and a.cota_om='$om[0]'
								and a.cod_tp_cota = '$posto[3]' 
                        and a.reservatorio_codigo in (select codigo from reservatorio where combustivel_codigo='1')
							order by a.dt_abastecimento ";
							
							$saldo=mysql_query($qry_saldo_g);
							$saldo_gas = mysql_fetch_row($saldo);		
						}	
						
						echo "<tr bgcolor='$cor3' class='marcar_linha'>
									<td><lin>&nbsp;$posto[0]&nbsp;&nbsp;</td>
									<td align='center'><lin>&nbsp;$posto[2]&nbsp;</td>
									<td align='right'><lin>&nbsp;&nbsp;".number_format(($posto[1]-$qtdg[0]-$saldo_gas[0]),0,'','.')." Lts&nbsp;</td>
								</tr>";	
					}	
					echo "</table>";
					echo "</td><td  valign='top' bgcolor='$cor3'>";	
					echo "<table bgcolor='$cor_fundo' align='center' width='100%'>";		
					echo "<tr bgcolor='$cor2' align='center'><td><coluna>&nbsp;Local&nbsp;</td><td><coluna>&nbsp;Combustível&nbsp;</td><td><coluna>&nbsp;Qtd&nbsp;</td></tr>";	
					
					$post_d=mysql_query($qry_d);
					while ($posto = mysql_fetch_array($post_d, MYSQL_NUM)) {
						//Verifica o somatãrio dos abastecimentos autorizados e não realizados de diesel
						$qrya = "select sum(qtd_autorizada) from abastecimento 
									where cod_tp_cota = '$posto[3]' 
										and reservatorio_codigo = '$posto[4]' 
										and cota_om='$om[0]' and qtd_abastecida is null";
						$resa = mysql_query($qrya);
						$qtda = mysql_fetch_row($resa);
						
						//Se o sistema permite abastecimento com saldo negativo
						if($abast_sem_saldo=='s') {
							//Verifica o saldo negativo da OM					
							$qry_saldo_d = "select SUM(a.qtd_abastecida - ifnull(h.qtd_abastecida ,0))
							from abastecimento a LEFT JOIN 
									(SELECT dt_abastecimento, viatura_eb, sum(qtd_abastecida) qtd_abastecida 
										FROM `historico_abastecimento` 
										GROUP BY dt_abastecimento, viatura_eb
									) h on (a.dt_abastecimento=h.dt_abastecimento and a.viatura_eb=h.viatura_eb)
							where a.qtd_abastecida > ifnull(h.qtd_abastecida ,0)
								and a.cota_om='$om[0]'
								and a.cod_tp_cota = '$posto[3]' 
                        and a.reservatorio_codigo in (select codigo from reservatorio where combustivel_codigo='1')
							order by a.dt_abastecimento ";
							
							$saldo=mysql_query($qry_saldo_d);
							$saldo_d = mysql_fetch_row($saldo);		
						}

						echo "<tr bgcolor='$cor3' class='marcar_linha'>
									<td><lin>&nbsp;$posto[0]&nbsp;</td>
									<td align='center'><lin>&nbsp;$posto[2]&nbsp;</td>
									<td align='right'><lin>&nbsp;&nbsp;".number_format(($posto[1]-$qtda[0]-$saldo_d[0]),0,'','.')." Lts&nbsp;</td>
								</tr>";	
					}	
					echo "</table></td></tr>";
					echo "<tr><td></td><td colspan='3' bgcolor='$cor2'>&nbsp;</td></tr>";
				} else {
					echo "<tr bgcolor='$cor3' class='marcar_linha'><td bgcolor='$cor_fundo'></td><td><lin>&nbsp;<a href='index.php?nomeArquivo=cons_disponibilidade.php&oc=$oc[0]&codom=$om[0]' style='text-decoration:none'>$om[1]</a>&nbsp;</td><td align='right'><lin>&nbsp;".number_format(($gas[0] - $qtdg[0] - $saldo_gas[0]),0,'','.')." Lts&nbsp</td><td align='right'><lin>&nbsp;".number_format(($d[0] - $qtdd[0] - $saldo_d[0]),0,'','.')." Lts&nbsp;</td></tr>";	
				}
				$s_gas=$s_gas+$gas[0];
				$s_d=$s_d+$d[0];
			}
			echo "<tr bgcolor='$cor1'><td bgcolor='$cor_fundo'></td><td><coluna>&nbsp;Total&nbsp;</td><td align='right'><coluna>&nbsp;".number_format($s_gas,0,'','.')." Lts&nbsp;</td><td align='right'><coluna>&nbsp;".number_format($s_d,0,'','.')." Lts&nbsp;</td></tr>";	
			echo "<tr><td bgcolor='$cor_fundo'></td><td colspan='3'><font color='$cor1'><b><i>Atenção:</b> Os números aqui apresentados já estão descontadas as quantidades autorizadas e não abastecidas.</i></td></tr>";
			if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')){
				echo "<tr bgcolor='$cor_fundo' align='center'><td bgcolor='$cor_fundo'></td><td colspan='5'><i><a href='index.php?nomeArquivo=cons_disponibilidade.php' style='text-decoration:none'>Fechar</a></td></tr>";
			}	
			echo "</table>";
			echo "</td></tr>";

		} else {
			echo "<tr bgcolor='$cor1' align='left'><td COLSPAN='8'><b>&nbsp;<i><a href='index.php?nomeArquivo=cons_disponibilidade.php&oc=$oc[0]' style='text-decoration:none'><font color='$cor_submenu'>$oc[1]</font>&nbsp;&nbsp;&nbsp;</b><linha>Listar</a>&nbsp;&nbsp;</td></tr>";
		}
	}
	echo "</table>";


?>
