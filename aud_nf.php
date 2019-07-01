<?php
  if(!isset($_SESSION['usuario']))
    include "verifica_sessao.php";
?>

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" >
</head>

<?php    
	echo "<table border='0' bgcolor='$cor_fundo' width='100%' align='left' valign='top'>";
	echo "<tr align='center'><td>&nbsp;</td></tr>";
	echo "<tr align='center'><td><b><font size='5'><I>Auditoria de Notas Fiscais</b></I></td></tr>";
   
   //LISTA OS Comandos 
  /* SELECT distinct om1.codom, om1.sigla
				FROM om om1, om om2
				where 
					om1.codom=om2.subordinacao 
					AND om1.subordinacao is not null
					ORDER BY abs(substring(om1.sigla,6)), om1.sigla*/
					
	$query = "SELECT distinct om1.codom, om1.sigla
				FROM om om1, om om2
				where 
					om1.codom=om2.subordinacao 
					AND om1.subordinacao is not null
					and om1.tipo in ('OC','Integrante') ";
					
	if (($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')){
		$query .= "and om1.codom='".$_SESSION['codom']."' ";
		$_GET['oc']=$_SESSION['codom'];
	}
	elseif (($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='ABASTECEDOR')){		
		$query .= "and om2.codom='".$_SESSION['om_enq']."' ";
				$_GET['oc']=$_SESSION['om_enq'];
				if($_GET['codom']=='') {
					$_GET['codom']=$_SESSION['codom'];
				}
	}
	$query .= "ORDER BY abs(om1.sigla), om1.sigla ";

	$resultado=mysql_query($query);
	while ($oc = mysql_fetch_array($resultado, MYSQL_NUM)) {
		if($oc[0]==$_GET['oc']){
			echo "<tr bgcolor='$cor1' align='left'><td COLSPAN='3'><coluna>&nbsp;&nbsp;<a href='index.php?nomeArquivo=aud_nf.php' style='text-decoration:none'><font color='$cor_submenu'>$oc[1]</font></a></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')){
				echo " <a href='index.php?nomeArquivo=aud_nf.php' style='text-decoration:none'>Fechar</a>";
			}			
			echo "</td></tr>";
			echo "<tr bgcolor='$cor1' align='left'><td bgcolor='$cor_fundo'>";
			echo "<table border='0' width='100%'>";
			echo "<tr bgcolor='$cor1' align='center'>
						<td bgcolor='$cor_fundo'>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td><coluna>&nbsp;OM&nbsp;</td>
						<td><coluna>&nbsp;Reservatório&nbsp;</td>
						<td><coluna>&nbsp;Combustível&nbsp;</td>
						<td><coluna>&nbsp;Recebimentos&nbsp;</td>
						<td bgcolor='$cor_fundo'>&nbsp;&nbsp;&nbsp;&nbsp;</td>
					</tr>";

			/*CALCULA A DISPONIBILIDADE DE GASOLINA DE OM*/					

			$qry_tanc ="SELECT om.codom, om.sigla, r.codigo, c.nome
						FROM om, reservatorio r, combustivel c
						WHERE om.codom=r.om_codom
							and c.codigo = r.combustivel_codigo ";
			if((($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']=='SIM')) or ($_SESSION['perfil']=='ABASTECEDOR')) {	
				$qry_tanc = $qry_tanc."and om.codom='".$_SESSION['codom']."' ";
			} else {			
				$qry_tanc = $qry_tanc."and (om.subordinacao='$oc[0]' or om.codom='$oc[0]') ";
			}
			$qry_tanc = $qry_tanc."order by om.sigla, c.nome";
			$res_tanc=mysql_query($qry_tanc);
			while ($om = mysql_fetch_array($res_tanc, MYSQL_NUM)) {
				if($_GET['res']==$om[2]) {
					echo "<tr><td>&nbsp;</td></tr>";
					echo "<tr bgcolor='$cor2' align='center'><td bgcolor='$cor_fundo'></td>
								<td align='left'><coluna>&nbsp;$om[1]&nbsp;</td>
								<td><coluna>Nº&nbsp;$om[2]</td>
								<td><coluna>$om[3]</td>
								<td><coluna><a href='index.php?nomeArquivo=aud_nf.php&oc=".$_GET['oc']."' style='text-decoration:none'>Fechar</a></td>
								<td bgcolor='$cor_fundo'></td>";

					echo "</tr>";

					echo "<tr><td></td><td colspan='4'>";
	
					$qry_det = "SELECT r.nr_nota_fiscal, r.dt_rec, r.qtd_rec, r.combustivel_codigo, sum(e.qtd_atual), sum(qtd_destinada)
									FROM recebimento r, estoque e
									where r.nr_nota_fiscal=e.nr_nota_fiscal
										and r.combustivel_codigo=e.combustivel_codigo
										and r.reservatorio_codigo='".$_GET['res']."'
									GROUP BY r.nr_nota_fiscal, r.dt_rec, r.qtd_rec, r.combustivel_codigo ";
					if($_GET['sit']!='todas') {
						$qry_det .= "HAVING sum(e.qtd_atual)>0 ";
					}
					$qry_det .= "ORDER BY 2 desc ";
					$res_det=mysql_query($qry_det);
					echo "<table border='0' width='100%' >";
					echo "<tr bgcolor='$cor2' align='center'>
								<td><coluna>Nota Fiscal</td>
								<td><coluna>Data</td>
								<td><coluna>Combustível</td>
								<td><coluna>Qtd Recebida</td>
								<td><coluna>Saldo Atual</td>
								<td><coluna>Repasses</td>
							</tr>";
					while ($det = mysql_fetch_array($res_det, MYSQL_NUM)) {
						if($det[2] != $det[5]) {
							$atencao = '**';
							$titulo = "title='Divergência entre a quantidade recebida e a quantidade distribuída!'";
						} else {
							$atencao = $titulo = '';
						}
						echo "<tr bgcolor='$cor3' align='center'  class='marcar_linha'>
									<td><a name='$det[0]'></a><lin>$det[0]</td>
									<td><lin>".substr($det[1],8,2)."/".substr($det[1],5,2)."/".substr($det[1],0,4)."</td>
									<td><lin>$om[3]</td>
									<td align='right'><lin>".number_format($det[2],0,',','.')." Lt</td>
									<td align='right'><lin>".number_format($det[4],3,',','.')." Lt</td>
									<td>&nbsp;<a href='index.php?nomeArquivo=aud_nf.php&oc=".$_GET['oc']."&res=$om[2]&nf=$det[0]&comb=$det[3]&sit=".$_GET['sit']."#$det[0]' $titulo style='text-decoration:none'>Exibir $atencao</a>&nbsp;</td>
								</tr>";
						if(($_GET['nf']===$det[0]) and ($_GET['comb']==$det[3])) {
							echo "<tr><td>&nbsp;</td><td colspan='4'>";
							echo "<table border='1' width='100%'>";
							echo "<tr bgcolor='$cor2' align='center'>
								<td colspan='6'><coluna>Repasses da Nota Fiscal Nº $det[0]</td>
								</tr>";
							echo "<tr bgcolor='$cor2' align='center'>
								<td><coluna>OM</td>
								<td><coluna>Nº Repasse</td>
								<td><coluna>Data</td>
								<td><coluna>Qtd Repassada</td>
								<td><coluna>Saldo Atual</td>
							</tr>";
							$qry_rep = "SELECT om.sigla, e.nr_repasse, e.dt_repasse, e.qtd_destinada, e.qtd_atual, t.tipo_cota, e.om_codom
									FROM om, tipo_cota t, estoque e
									where om.codom=e.om_codom
										and t.cod_tp_cota=e.cod_tp_cota
										and e.nr_nota_fiscal='".$_GET['nf']."'
										and e.combustivel_codigo='".$_GET['comb']."'
									ORDER BY 3";
							$res_rep=mysql_query($qry_rep);
							$tot_rec = $tot_disp = 0;
							while ($rep = mysql_fetch_array($res_rep, MYSQL_NUM)) {
								echo "<tr bgcolor='$cor3' align='center'  class='marcar_linha'>
											<td><lin>$rep[0] - $rep[5]</td>
											<td><lin>
												<a href=\"#\" title='Listar destino do combustível deste repasse.' onClick=\"window.open('destino_comb.php?om=$rep[0]&dt=$rep[2]&codom=$rep[6]&oc=".$_GET['oc']."&rep=$rep[1]&nota_fiscal=$det[0]','janela','location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0,width=800,height=600');\">$rep[1]</a>
											</td>
											<td><lin>".substr($rep[2],8,2)."/".substr($rep[2],5,2)."/".substr($rep[2],0,4)."</td>
											<td align='right'><lin>".number_format($rep[3],3,',','.')." Lt</td>
											<td align='right'><lin>".number_format($rep[4],3,',','.')." Lt</td>
										</tr>";
										$tot_rec += $rep[3];
										$tot_disp += $rep[4];
							}
							echo "<tr bgcolor='$cor3' align='center'>
											<td colspan='3'><coluna>Total</td>
											<td align='right'><coluna>".number_format($tot_rec,3,',','.')." Lt</td>
											<td align='right'><coluna>".number_format($tot_disp,3,',','.')." Lt</td>
										</tr>";
							echo "<tr bgcolor='$cor2' align='center'><td colspan='6'><coluna><a href='index.php?nomeArquivo=aud_nf.php&oc=".$_GET['oc']."&res=".$_GET['res']."' style='text-decoration:none'>Fechar</a></td></tr>";
							echo "<tr bgcolor='$cor_fundo' align='center'><td colspan='6'>&nbsp;</td></tr>";

							echo "</table><td>&nbsp;</td></td></tr>";
						}
					}
					echo "<tr bgcolor='$cor3'><td colspan='6' align='center'><a href='index.php?nomeArquivo=aud_nf.php&oc=".$_GET['oc']."' style='text-decoration:none'>Fechar</a></td></tr>";
					if($_GET['sit']!='todas') {
						echo "<tr bgcolor='$cor_fundo'><td colspan='5' ><coluna><a href='index.php?nomeArquivo=aud_nf.php&oc=".$_GET['oc']."&res=$om[2]&nf=$det[0]&comb=$det[3]&sit=todas' style='text-decoration:none'>&nbsp;&nbsp;+ Exibir todas as Notas Fiscais</a></td></tr>";
					} else {
						echo "<tr bgcolor='$cor_fundo'><td colspan='5' ><coluna><a href='index.php?nomeArquivo=aud_nf.php&oc=".$_GET['oc']."&res=$om[2]&nf=$det[0]&comb=$det[3]' style='text-decoration:none'>&nbsp;&nbsp;- Exibir somente as Notas Fiscais com saldo maior que (0)zero</a></td></tr>";
					}
					echo "<tr><td colspan='4' align='center'>&nbsp;</td></tr>";
					echo "</table>";
					echo "</td><td bgcolor='$cor_fundo'></td></tr>";
				} else {
				echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'><td bgcolor='$cor_fundo'></td><td align='left'><lin>&nbsp;$om[1]&nbsp;</td>
							<td><lin>Nº&nbsp;$om[2]</td>
							<td><lin>$om[3]</td>
							<td>&nbsp;<a href='index.php?nomeArquivo=aud_nf.php&oc=".$_GET['oc']."&res=$om[2]&sit=".$_GET['sit']."' style='text-decoration:none'>Detalhar</a>&nbsp;</td>";
				echo "<td bgcolor='$cor_fundo'>&nbsp;</td></tr>";
					
				}
			}
			echo "</table>";
			echo "</td></tr>";

		} else {
			echo "<tr bgcolor='$cor1' align='left'>
						<td><b>&nbsp; <i><a href='index.php?nomeArquivo=aud_nf.php&oc=$oc[0]' style='text-decoration:none'><font color='$cor_submenu'>$oc[1]</font>&nbsp;&nbsp;&nbsp;</b><linha>Listar</a>&nbsp;&nbsp;$oc[2]</td>
					</tr>";
		}
	}
	echo "</table>";


?>
