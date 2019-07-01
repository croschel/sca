<?php
  	if(empty($_SESSION['usuario']))
    include "verifica_sessao.php";
   
	if(!$_GET['ordem']) {
		$_GET['ordem']="2";
	}
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<font size=5><br><b>Relatório de Abastecimentos Realizados</b></font> <br>
<?php 
if ($_GET['dt_ini'] == '') {
  $data_ini = date('Y-m-d', mktime(0, 0, 0, date(m), date(d)-30, date(Y)));
  $data_fim = date('Y-m-d');
}
else {
  $data_ini = substr($_GET['dt_ini'],6,4)."-".substr($_GET['dt_ini'],3,2)."-".substr($_GET['dt_ini'],0,2);
  $data_fim = substr($_GET['dt_fim'],6,4)."-".substr($_GET['dt_fim'],3,2)."-".substr($_GET['dt_fim'],0,2);
}

$data_fim = $data_fim;
$dt_inicio = substr($data_ini,8,2)."/".substr($data_ini,5,2)."/".substr($data_ini,0,4);
$dt_final = (substr($data_fim,8,2))."/".substr($data_fim,5,2)."/".substr($data_fim,0,4);
if($_GET['s']=='') {
	$_GET['s']='false';
}
if($_GET['local']=='') {
	$_GET['local']='todos';
}

?> 

<script language="JavaScript" type="text/javascript">

/*function getOM(what) {
      var cod = what.value;
      var ini = document.getElementById("dt_ini").value;
      var fim = document.getElementById("dt_fim").value;
      var cota = document.getElementById("tp_cota").value;
      document.location=('index.php?nomeArquivo=mapa.php&codom=' + cod + '&dt_ini=' + ini + '&dt_fim=' + fim + '&tp_cota=' + cota );
}

function getCota(what) {
      var cota = what.value;
      var cod = document.getElementById("codom").value;
      var ini = document.getElementById("dt_ini").value;
      var fim = document.getElementById("dt_fim").value;
      document.location=('index.php?nomeArquivo=mapa.php&codom=' + cod + '&dt_ini=' + ini + '&dt_fim=' + fim + '&tp_cota=' + cota );
}*/

function getRel(cota,tp_cota,ordem,local,dt_ini,dt_fim,origem,s) {
//      var s = what.checked;
      document.location=('index.php?nomeArquivo=rel_abast.php&cota=' + cota + '&tp_cota=' + tp_cota + '&ordem=' + ordem + '&local=' + local + '&dt_ini=' + dt_ini + '&dt_fim=' + dt_fim + '&origem=' + origem + '&s=' + s );
}

</script>

<?php 
	echo "<table border='0' width='100%' align='center' valign='top'>";


	///////////  ATUALIZA A QUANTIDADE ATUAL NO ESTOQUE DA OM ///////////////////

	if($_GET['alterar']=='1') {
		if($_GET['tipo_cota']=='0') {
			$_GET['tipo_cota']='1';
		}
		
		$qry="select nr_repasse, om_codom, qtd_atual, dt_repasse 
					from estoque 
					where reservatorio_codigo='".$_GET['reservatorio']."'
					and om_codom='".$_GET['om_codom']."'
					and cod_tp_cota ='".$_GET['tipo_cota']."' 
					and qtd_atual>0
					order by dt_repasse";
		$result1 = mysql_query($qry);
		$qtd_disp = 0;
		//// Verifica se há saldo suficiente para retirar do estoque ///////////
		while ($qtd = mysql_fetch_array($result1, MYSQL_NUM)) {
			$qtd_disp = $qtd_disp + $qtd[2];
		}
		$num_lin=0;
		$result1 = mysql_query($qry);
		if($qtd_disp>=$_GET['qtd_abast']) { /* Se existir saldo*/
			while(($_GET['qtd_abast']>0) and (mysql_num_rows($result1)>=$num_lin)) { /*Enquanto não for debitado das cotas todo o valor abastecido e o numermo de cotas for maior ou igual ao contador*/
				$cota = mysql_fetch_array($result1, MYSQL_NUM);

				if ($cota[2]>=$_GET['qtd_abast']){
						
					/*Atualiza o estoque da OM*/
					$query2 = "update estoque set qtd_atual='". ($cota[2] - $_GET['qtd_abast']) ."' where nr_repasse='".$cota[0]."' and om_codom='".$_GET['om_codom']."'";

					/*Registra historico abastecimento */ 
					$query3 = "insert into historico_abastecimento (nr_repasse, om_codom, dt_abastecimento, viatura_eb, qtd_abastecida)
										values('".$cota[0]."', '".$_GET['om_codom']."', '".$_GET['dt_abast']."', '".$_GET['eb']."', '".$_GET['qtd_abast']."')";

					$_GET['qtd_abast']=0;	
								
				}else{
						
					/*Atualiza o estoque da OM*/
					$query2 = "update estoque set qtd_atual='0' where nr_repasse='".$cota[0]."' and om_codom='".$_GET['om_codom']."' ";				

					/*Registra historico abastecimento*/
					$query3 = "insert into historico_abastecimento (nr_repasse, om_codom, dt_abastecimento, viatura_eb, qtd_abastecida)
										values('".$cota[0]."', '".$_GET['om_codom']."', '".$_GET['dt_abast']."', '".$_GET['eb']."', '$cota[2]')";
						
					$_GET['qtd_abast'] = $_GET['qtd_abast'] - $cota[2];				
				}
					
					$resultado = mysql_query($query2);
					$resultado = mysql_query($query3);

				$num_lin++;
			}
				
		} else {
			
			echo "<font color='red'><p>ATENÇÃO!<br> Saldo Insuficiente da OM para o Abastecimento.<br> Saldo Atual: ".number_format($qtd_disp,3,',','.')." Lts.<br> Necessidade: ".number_format(($_GET['qtd_abast'] - $qtd_disp),3,',','.')." Lts.</font><p>";
			
		}
		$_GET['alterar']='0';
	} elseif($_GET['alterar']=='2') {
echo		$qry="select nr_repasse, om_codom, qtd_atual, dt_repasse 
					from estoque 
					where reservatorio_codigo='".$_GET['reservatorio']."'
					and om_codom='".$_GET['om_codom']."'
					and cod_tp_cota ='".$_GET['tipo_cota']."' 
					and qtd_atual<=0
					order by dt_repasse desc";
		$result1 = mysql_query($qry);
		
		$_GET['alterar']='0';

	}

	///////////  FIM DO PROCEDIMENTO QUE ATUALIZA A QUANTIDADE ATUAL NO ESTOQUE DA OM ///////////////////


	echo "<form name='frm_autz' method='GET' action='index.php?nomeArquivo=rel_abast.php&ordem=".$_GET['ordem']."' >";
	echo "<input type='hidden' name='nomeArquivo' value='rel_abast.php'>";
	echo "<input type='hidden' name='ordem' value='".$_GET['ordem']."'>";
	/*/Relação de Combustivel*/

	echo "<tr align='center' >	<td colspan='13' ><br>";	
	
	echo "<table border='0' width='100%' align='center' valign='top'>";
		
	/*/Relação de OM detentora combustível*/
	$qrycota = "select distinct om.codom, om.sigla 
					from om, abastecimento a
					where om.codom=a.cota_om
						AND a.dt_abastecimento between '".$data_ini." 00:00' and '".$data_fim." 23:59'
						AND a.qtd_abastecida is not null ";
						
	if(($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')) {
		
					$qrycota .=	" ";
					
	} elseif(($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')) {
		
		$qrycota .= "and a.cota_om in (select cota_om from abastecimento a, viatura v, om where om.codom=v.om_codom and v.eb=a.viatura_eb and om.subordinacao='".$_SESSION['codom']."') ";
		
	} elseif(($_SESSION['perfil']=='CMT DA OM') or ($_SESSION['perfil']=='FISCAL DA OM')) {
		$qrycota = "select distinct om.codom, om.sigla 
					from om, abastecimento a
					where om.codom=a.cota_om
						and (a.cota_om ='".$_SESSION['codom']."' or a.reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."'))
						AND a.dt_abastecimento between '".$data_ini." 00:00' and '".$data_fim." 23:59'
						AND a.qtd_abastecida is not null ";

	} elseif($_SESSION['perfil']=='ABASTECEDOR') {
		$qrycota = "select distinct om.codom, om.sigla 
					from om, abastecimento a
					where om.codom=a.cota_om
						and a.reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."')
						AND a.dt_abastecimento between '".$data_ini." 00:00' and '".$data_fim." 23:59'
						AND a.qtd_abastecida is not null ";
	} 
	$qrycota .=	"order by abs(om.sigla), om.sigla";

	$rescota = mysql_query($qrycota);
	echo "<tr>";
	echo "<td>";
			echo "<b><I>Cota do(a): </b></i>";
			echo "<select name='cota' onChange='getRel(this.value,\"".$_GET['tp_cota']."\",\"".$_GET['ordem']."\",\"".$_GET['local']."\",\"".$_GET['dt_ini']."\",\"".$_GET['dt_fim']."\",\"".$_GET['origem']."\",\"".$_GET['s']."\")'>";
			echo "<option value='Todas' >TODAS</option>";
			while ($cota = mysql_fetch_array($rescota, MYSQL_NUM)) {
				echo "<option value='$cota[0]' "; if($_GET['cota']==$cota[0]) {echo "selected";} echo "> $cota[1] </option>";
			}
			echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "</td>";		
	/*/Relação de Tipos de Cota */
	$qrycota = "SELECT *
					FROM tipo_cota
					where cod_tp_cota<>'0'
					order by 2";

	$rescota = mysql_query($qrycota);
	echo "<td>";
			echo "<b><I>Tipo de Combustível: </b></i>";
			echo "<select name='tp_cota' id='tp_cota' onChange='getRel(\"".$_GET['cota']."\",this.value,\"".$_GET['ordem']."\",\"".$_GET['local']."\",\"".$_GET['dt_ini']."\",\"".$_GET['dt_fim']."\",\"".$_GET['origem']."\",\"".$_GET['s']."\")'>";
			echo "<option value='Todas' >TODAS</option>";
			while ($cota = mysql_fetch_array($rescota, MYSQL_NUM)) {
				echo "<option value='$cota[0]' "; if($_GET['tp_cota']==$cota[0]) {echo "selected";} echo "> $cota[1] </option>";
			}
			echo "</select>";
	echo "</td>";
	
	//Relação dos Locais 
	$qrylocal = "SELECT distinct r.om_codom, om.sigla
					FROM om, reservatorio r
					where om.codom=r.om_codom ";
					
	if(($_SESSION['perfil']=='CMT DA OM') OR ($_SESSION['perfil']=='FISCAL DA OM')) {
		$qrylocal = "SELECT distinct r.om_codom, om.sigla
					FROM om, reservatorio r, abastecimento a
					where om.codom=r.om_codom 
						and r.codigo=a.reservatorio_codigo ";
	}
	if($_SESSION['perfil']=='ABASTECEDOR') {
		$qrylocal = "SELECT distinct r.om_codom, om.sigla
					FROM om, reservatorio r
					where om.codom=r.om_codomand om.codom='".$_SESSION['codom']."' ";
	}
	$qrylocal .= "order by abs(om.sigla), om.sigla";

	$reslocal = mysql_query($qrylocal);
	echo "<td>";
			echo "<b><I>Local: </b></i>";
			echo "<select name='local' onChange='getRel(\"".$_GET['cota']."\",\"".$_GET['tp_cota']."\",\"".$_GET['ordem']."\",this.value,\"".$_GET['dt_ini']."\",\"".$_GET['dt_fim']."\",\"".$_GET['origem']."\",\"".$_GET['s']."\")'>";
			echo "<option value='todos' >TODOS</option>";
			while ($local = mysql_fetch_array($reslocal, MYSQL_NUM)) {
				echo "<option value='$local[0]' "; if($_GET['local']==$local[0]) {echo "selected";} echo "> $local[1] </option>";
			}
			echo "</select>";
	echo "</td>";
	
	echo "<td>";
	echo "<b><I>Período de: </b></i>
					<input type='text' OnKeyUp='mascara(this.value);' size='10' maxlength='10' name='dt_ini' id='dt_ini' value='".$dt_inicio."'><b><I>  </b></I> 
					<input type='text' OnKeyUp='mascara1(this.value);' size='10' maxlength='10' name='dt_fim' id='dt_fim' value='".$dt_final."'>";
	echo "</td>";
	echo "<td>";
	echo "<input type='submit' value= 'Pesquisar'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<td>";
//	if($_SESSION['usuario']=='0309755346') {
		if($_GET['origem']=='s') {
			echo "&nbsp;<a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem=".$_GET['ordem']."&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&origem=n' style='text-decoration:none' title='Ocultar o repasse do combustível abastecido'>Ocultar Origem Combustível</a>";
			echo "<br><input type='checkbox' name='sit' onChange='getRel(\"".$_GET['cota']."\",\"".$_GET['tp_cota']."\",\"".$_GET['ordem']."\",\"".$_GET['local']."\",\"".$_GET['dt_ini']."\",\"".$_GET['dt_fim']."\",\"".$_GET['origem']."\",this.checked)'"; if($_GET['s']=='true') { echo "checked ";} echo ">Mostrar somente abastecimentos sem Origem";
		} else {
			echo "<a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem=".$_GET['ordem']."&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&origem=s' style='text-decoration:none' title='Mostrar o repasse do combustível abastecido'>Mostrar Origem Combustível</a>";
		}		
//	}			
	echo "</td>";
	echo "<input type='hidden' name='origem' value='".$_GET['origem']."'>";
	echo "<input type='hidden' name='s' value='".$_GET['s']."'>";
	echo "</form>";
	echo "</table>";
	echo "</td>	</tr>";
	echo "<tr align='center' bgcolor='$cor1'>";
		echo "<td rowspan='2'><b><a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='2'){echo "2 desc";}else{echo "2";} echo "&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&local=".$_GET['local']."&origem=".$_GET['origem']."&s=".$_GET['s']."' style='text-decoration:none'><font color='$cor_submenu'>Viatura</font></a></td>";
		echo "<td colspan='2'><b><a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='3'){echo "3 desc";}else{echo "3";} echo "&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&local=".$_GET['local']."&origem=".$_GET['origem']."&s=".$_GET['s']."' style='text-decoration:none'><font color='$cor_submenu'>Data</font></a></td>";
		echo "<td rowspan='2'><b><a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='4'){echo "4 desc";}else{echo "4";} echo "&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&local=".$_GET['local']."&origem=".$_GET['origem']."&s=".$_GET['s']."' style='text-decoration:none'><font color='$cor_submenu'>Comb</font></a></td>";
		echo "<td rowspan='2'><b><a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='10'){echo "10 desc";}else{echo "10";} echo "&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&local=".$_GET['local']."&origem=".$_GET['origem']."&s=".$_GET['s']."' style='text-decoration:none'><font color='$cor_submenu'>Tp Comb</font></a></td>";
		echo "<td rowspan='2'><b><a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='5'){echo "5 desc";}else{echo "5";} echo "&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&local=".$_GET['local']."&origem=".$_GET['origem']."&s=".$_GET['s']."' style='text-decoration:none'><font color='$cor_submenu'>Local</font></a></td>";
		echo "<td rowspan='2'><b><a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='6'){echo "6 desc";}else{echo "6";} echo "&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&local=".$_GET['local']."&origem=".$_GET['origem']."&s=".$_GET['s']."' style='text-decoration:none'><font color='$cor_submenu'>Qtd</font></a></td>";
		echo "<td rowspan='2'><b><a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='8'){echo "8 desc";}else{echo "8";} echo "&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&local=".$_GET['local']."&origem=".$_GET['origem']."&s=".$_GET['s']."' style='text-decoration:none'><font color='$cor_submenu'>Cota da OM</font></a></td>";
		echo "<td rowspan='2'><b><a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='8'){echo "8 desc";}else{echo "8";} echo "&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&local=".$_GET['local']."&origem=".$_GET['origem']."&s=".$_GET['s']."' style='text-decoration:none'><font color='$cor_submenu'>Autz Por</font></a></td>";
		echo "<td rowspan='2'><b><a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='7'){echo "7 desc";}else{echo "7";} echo "&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&local=".$_GET['local']."&origem=".$_GET['origem']."&s=".$_GET['s']."' style='text-decoration:none'><font color='$cor_submenu'>Missão</font></a></td>";
		echo "<td rowspan='2'><b><a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='11'){echo "11 desc";}else{echo "11";} echo "&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&local=".$_GET['local']."&origem=".$_GET['origem']."&s=".$_GET['s']."' style='text-decoration:none'><font color='$cor_submenu'>Tipo Abastecimento</font></a></td>";
		echo "<td rowspan='2'><b><a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='ab.post_grad, ab.nome_guerra'){echo "ab.post_grad desc, ab.nome_guerra";}else{echo "ab.post_grad, ab.nome_guerra";} echo "&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&local=".$_GET['local']."&origem=".$_GET['origem']."&s=".$_GET['s']."' style='text-decoration:none'><font color='$cor_submenu'>Abastecedor</font></a></td>";
		echo "<td rowspan='2'><b><a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='motorista'){echo "motorista desc";}else{echo "motorista";} echo "&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&local=".$_GET['local']."&origem=".$_GET['origem']."&s=".$_GET['s']."' style='text-decoration:none'><font color='$cor_submenu'>Motorista</font></a></td>";
	echo "</tr>";
	echo "<tr align='center' bgcolor='$cor1'>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='12'){echo "12 desc";}else{echo "12";} echo "&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&local=".$_GET['local']."&origem=".$_GET['origem']."&s=".$_GET['s']."' style='text-decoration:none'><font color='$cor_submenu'>Autz</font></a></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='3'){echo "3 desc";}else{echo "3";} echo "&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&local=".$_GET['local']."&origem=".$_GET['origem']."&s=".$_GET['s']."' style='text-decoration:none'><font color='$cor_submenu'>Abast</font></a></td>";
	echo "</tr>";


	$query = "SELECT o.sigla, a.viatura_eb, a.dt_abastecimento, c.nome, a.reservatorio_codigo, a.qtd_abastecida, a.missao, a.cota_om, a.local, 
				t.tipo_cota, a.tp_abastecimento, a.dt_autorizacao, u.post_grad, u.nome_guerra, c.codigo, a.cod_tp_cota, substring(concat(ab.post_grad,' ',ab.nome_guerra),2), motorista  
				FROM abastecimento a, om o, combustivel c, viatura v, tipo_cota t, reservatorio r, usuario u,
						(select * from usuario) ab
				WHERE u.idt=a.usuario_autz
					and ab.idt=a.usuario_idt
					and o.codom = v.om_codom
					AND r.codigo = a.reservatorio_codigo
					AND c.codigo = r.combustivel_codigo
					AND v.eb = a.viatura_eb
					AND t.cod_tp_cota=a.cod_tp_cota
					AND a.dt_abastecimento between '".$data_ini." 00:00' and '".$data_fim." 23:59'
					AND a.qtd_abastecida is not null ";

	if(($_GET['cota']!='Todas') and ($_GET['cota']!='')) {
		$query.="AND a.cota_om='".$_GET['cota']."' ";
	}
	
	if(($_GET['tp_cota']!='Todas') and ($_GET['tp_cota']!='')) {
		$query.=" and a.cod_tp_cota='".$_GET['tp_cota']."' ";	
	}
	
	if($_GET['origem']=='s'){
		$query.=" and a.qtd_abastecida > 0 ";	
	}
	
	if (($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='CMT DA OM')) {
		$query .= "AND (a.cota_om = '".$_SESSION['codom']."'  or a.reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."')) ";				
	}

	if($_GET['local']!='todos') {
		$query.="AND a.reservatorio_codigo in(select codigo from reservatorio where om_codom='".$_GET['local']."') ";
	}else {
		if(($_GET['cota']=='Todas') and ($_GET['origem']!='s')) {
			$query .="union
					SELECT o.sigla, a.viatura_eb, a.dt_abastecimento, c.nome, a.reservatorio_codigo, a.qtd_abastecida, a.missao, a.cota_om, a.local, 
							t.tipo_cota, a.tp_abastecimento, a.dt_autorizacao, u.post_grad, u.nome_guerra, c.codigo, a.cod_tp_cota, ifnull(usuario_idt,'-'), motorista 
						FROM abastecimento a, om o, combustivel c, viatura v, tipo_cota t, usuario u 
						WHERE u.idt=a.usuario_autz 
							and o.codom = v.om_codom
							AND v.eb = a.viatura_eb 
							AND v.combustivel_codigo=c.codigo 
							AND t.cod_tp_cota=a.cod_tp_cota 
							AND a.dt_abastecimento between '".$data_ini." 00:00' and '".$data_fim." 23:59' 
							AND a.qtd_abastecida is not null 
							AND a.cota_om is null ";
							if (($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='CMT DA OM') or ($_SESSION['perfil']=='ABASTECEDOR')) {
								$query .="AND v.om_codom='".$_SESSION['codom']."' ";	
							}
							if(($_GET['tp_cota']!='Todas') and ($_GET['tp_cota']!='')) {
								$query.=" and a.cod_tp_cota='".$_GET['tp_cota']."' ";	
							}
		}
	}
					
	$query.=" order by ".$_GET['ordem'];

	$tot_gas=$tot_die=0;
	$result = mysql_query($query);
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {

		/*Seeleciona a OM que saiu o combustivel*/
		$busca="select sigla from om where codom='".$row[7]."'";
		$resultado = mysql_query($busca,$conexao);
		$cota = mysql_fetch_row($resultado);
		if($cota[0]=='') {
			$cota[0]='-';
		}

		/*Seleciona a sigla da OM do reservatorio*/
		$busca="select o.sigla from om o, reservatorio r where o.codom=r.om_codom and r.codigo='".$row[4]."'";
		$resultado = mysql_query($busca,$conexao);
		$local = mysql_fetch_row($resultado);
		if($local[0]=='') {
			$local[0]=$row[8];
		}

		if($_GET['origem']=='s'){
			$qry_h="select * from historico_abastecimento where dt_abastecimento='".$row[2]."' and viatura_eb='".$row[1]."'";
			$res_h = mysql_query($qry_h);
			if(($_GET['s']=='true') and (mysql_num_rows($res_h)==0)) {
				echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'>";
				echo "<td><font size='2'>$row[1] - $row[0]</td>
					<td><font size='2'>".substr($row[11],8,2)."/".substr($row[11],5,2)."/".substr($row[11],0,4)." ".substr($row[11],11,5)."</td>
					<td><font size='2'>".substr($row[2],8,2)."/".substr($row[2],5,2)."/".substr($row[2],0,4)." ".substr($row[2],11,5)."</td>
					<td><font size='2'>$row[3]</td><td><font size='2'>$row[9]</td><td><font size='2'>$local[0]</td>
					<td align='right'><font size='2'>$row[5]</td><td align='left'><font size='2'>$cota[0] - Res: $row[4]</td><td align='left'><font size='2'> ".substr($row[12],1)." $row[13] </td>
					<td align='left'><font size='2'>$row[6] </td><td align='left'><font size='2'>$row[10] </td>
					<td align='center'><font size='2'>$row[16]</td><td align='center'><font size='2'>$row[17]</td></tr>";
				echo "<tr bgcolor='$cor_fundo' align='left'>";
				echo "<td colspan='4'></td><td colspan='5'>";
				echo "<table border='0' width='100%'>";
				if(mysql_num_rows($res_h)>0) {
					echo "<tr bgcolor='$cor2' align='center'><td><coluna>Nº Repasse</td><td><coluna>Quantidade</td></tr>";
					while ($hist = mysql_fetch_array($res_h, MYSQL_NUM)) {
						echo "<tr bgcolor='$cor3'><td align='center'><lin>$hist[0]</td><td align='right'><lin>$hist[4]&nbsp;</td></tr>";
					}
				} else {
					echo "<tr bgcolor='$cor3'>";
					if($_SESSION['usuario']=='0309755346') {
						echo "<td align='center'><br><lin>";
						echo "<a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem=".$_GET['ordem']."&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&origem=".$_GET['origem']."&s=".$_GET['s']."&alterar=1&reservatorio=$row[4]&om_codom=$row[7]&tipo_cota=$row[15]&qtd_abast=$row[5]&dt_abast=$row[2]&eb=$row[1]' style='text-decoration:none'>Corrigir Abastecimento com Desconto</a>";
						echo "<p></td><td align='center'><br><lin>";
						echo "<a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem=".$_GET['ordem']."&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&origem=".$_GET['origem']."&s=".$_GET['s']."&alterar=2&reservatorio=$row[4]&om_codom=$row[7]&tipo_cota=$row[15]&qtd_abast=$row[5]&dt_abast=$row[2]&eb=$row[1]' style='text-decoration:none'>Corrigir Abastecimento sem Desconto</a>";
						echo "<p></td>";
					} else {
						echo "<td align='center'><lin><br>Origem do Combustível Não Registrada <p></td>";
					}
					echo "</tr>";
				}
				echo "</table>";
				echo "</td><td></td></tr>";

				if ($row[14]=='1'){
					$tot_gas=$tot_gas+$row[5];
				} elseif ($row[14]=='2'){
					$tot_die=$tot_die+$row[5];
				} 

			} elseif($_GET['s']=='false') {
				echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'>";
				echo "<td><font size='2'>$row[1] - $row[0]</td>
					<td><font size='2'>".substr($row[11],8,2)."/".substr($row[11],5,2)."/".substr($row[11],0,4)." ".substr($row[11],11,5)."</td>
					<td><font size='2'>".substr($row[2],8,2)."/".substr($row[2],5,2)."/".substr($row[2],0,4)." ".substr($row[2],11,5)."</td>
					<td><font size='2'>$row[3]</td>
					<td><font size='2'>$row[9]</td>
					<td><font size='2'>$local[0] - Res: $row[4]</td>
					<td align='right'><font size='2'>$row[5]</td>
					<td ><font size='2'>$cota[0]</td>
					<td align='left'><font size='2'> ".substr($row[12],1)." $row[13] </td>
					<td align='left'><font size='2'>$row[6] </td>
					<td align='left'><font size='2'>$row[10] </td>
					<td align='center'><font size='2'>$row[16]</td>
					<td align='center'><font size='2'>$row[17]</td></tr>";
				echo "<tr bgcolor='$cor_fundo' align='left'>";
				echo "<td colspan='5'></td><td colspan='4'>";
				echo "<table border='0' width='100%'>";
				if(mysql_num_rows($res_h)>0) {
					echo "<tr bgcolor='$cor2' align='center'><td><coluna>Nº Repasse</td><td><coluna>Quantidade</td></tr>";
					while ($hist = mysql_fetch_array($res_h, MYSQL_NUM)) {
						echo "<tr bgcolor='$cor3'><td align='center'><lin>$hist[0]</td><td align='right'><lin>$hist[4]&nbsp;</td></tr>";
					}
				} else {
					echo "<tr bgcolor='$cor3'><td align='center'><lin><br>";
					if($_SESSION['usuario']=='0309755346') {
						echo "<a href='index.php?nomeArquivo=rel_abast.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem=".$_GET['ordem']."&local=".$_GET['local']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."&origem=".$_GET['origem']."&s=".$_GET['s']."&alterar=1&reservatorio=$row[4]&om_codom=$row[7]&tipo_cota=$row[15]&qtd_abast=$row[5]&dt_abast=$row[2]&eb=$row[1]' style='text-decoration:none'>Corrigir Abastecimento</a>";
					} else {
						echo "Origem do Combustível Não Registrada";
					}
					echo "<br><br></td></tr>";
				}
				echo "</table>";
				echo "</td><td colspan='3'></td></tr>";

				if ($row[14]=='1'){
					$tot_gas=$tot_gas+$row[5];
				} elseif ($row[14]=='2'){
					$tot_die=$tot_die+$row[5];
				} 
					
			}
			
		} else {
			echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'>";
			echo "<td><font size='2'>$row[1] - $row[0]</td>
					<td><font size='2'>".substr($row[11],8,2)."/".substr($row[11],5,2)."/".substr($row[11],0,4)." ".substr($row[11],11,5)."</td>
					<td><font size='2'>".substr($row[2],8,2)."/".substr($row[2],5,2)."/".substr($row[2],0,4)." ".substr($row[2],11,5)."</td>
					<td><font size='2'>$row[3]</td>
					<td><font size='2'>$row[9]</td>
					<td><font size='2'>$local[0] - Res: $row[4]</td>
					<td align='right'><font size='2'>$row[5]</td>
					<td ><font size='2'>$cota[0]</td>
					<td align='left'><font size='2'> ".substr($row[12],1)." $row[13] </td>
					<td align='left'><font size='2'>$row[6] </td>
					<td align='left'><font size='2'>$row[10] </td>
					<td align='center'><font size='2'>$row[16]</td>
					<td align='center'><font size='2'>$row[17]</td>
					</tr>";
			if ($row[14]=='1'){
				$tot_gas=$tot_gas+$row[5];
			} elseif ($row[14]=='2'){
				$tot_die=$tot_die+$row[5];
			} 
		}
		
		
   }
	echo "<tr><td align='right'><font size='2'><i>Total de Gasolina: </td><td><font size='2'><b><i> &nbsp;&nbsp;".number_format($tot_gas, 0, ',', '.')."</b> Litros</td><td colspan=2 align=right><font size='2'><i>Total de Diesel:</td><td><font size='2'><b><i>&nbsp;&nbsp;".number_format($tot_die, 0, ',', '.')."</b> Litros</td></tr>";
    	$num_rows = mysql_num_rows($result);
    	mysql_free_result($result);

	echo "</tr>";
echo "</table>";
?>