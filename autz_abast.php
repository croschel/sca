<?php
  if(!isset($_SESSION['usuario']))
    include "verifica_sessao.php";
?>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<Script Language="JavaScript">
function getStates(what) {
   if (what.selectedIndex != '') {
      var ebv = what.value;
      document.location=('index.php?nomeArquivo=autz_abast.php&ebv=' + ebv);
   }
}

$(document).ready(function () {
	$("input[name='qtd_comb']").css({width:'60px',})
	$("#posto").change(function () {
		$("input[name='qtd_max']").attr("value",Math.floor($("option:selected",this).attr("id"))+' Lts');
		$("input[name='cr_max']").attr("value",Math.floor($("option:selected",this).attr("cr"))+' Lts');
		if (document.frm_autz.cr_max.value>document.frm_autz.qtd_max.value) {
			$("input[name='qtd_comb']").attr("max",Math.floor($("option:selected",this).attr("cr")));			
		} else {
			$("input[name='qtd_comb']").attr("max",Math.floor($("option:selected",this).attr("id")));			
		}
	})
	$("input[name='qtd_max']").css({background:'transparent', border:'none', color:'red',})
	$("input[name='cr_max']").css({background:'transparent', border:'none', color:'red',})

})

</Script>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<?php  
	echo "<font size='5'><b> <br>Autorização para Abastecimento de Viatura<hr></font>";
	echo "<table width='100%'>";
	echo "<tr><td>";
	echo "<table border='0' width='90%' align='left' valign='top'>";
	echo "<tr align='center' bgcolor='$cor1'><td COLSPAN='5'><b><I>DISPONIBILIDADE DO(A) ".$_SESSION['unidade']."</I></td></tr>";
	echo "<tr align='center' bgcolor='$cor2'>
				<td width='20%'><b>Combustível</td>
				<td width='20%'><b>Tipo</td>
				<td width='20%'><b>Reservatório</td>
				<td width='30%'><b>Local</td>
				<td width='20%'><b>Quantidade</td>
			</tr>";
  
	/*/Seleciona o posto e a qtd de combustivel disponível para a OM*/
	$qry="select r.codigo, o.sigla, c.nome, truncate(sum(e.qtd_atual),0), t.tipo_cota, t.cod_tp_cota, e.om_codom
				from reservatorio r, om o, combustivel c, tipo_cota t, estoque e
				where o.codom=r.om_codom
					and r.codigo=e.reservatorio_codigo
					and c.codigo=r.combustivel_codigo
					and t.cod_tp_cota=e.cod_tp_cota
					and t.cod_tp_cota <> '7'
					and e.qtd_atual>0
					and e.om_codom='".$_SESSION['codom']."'
				group by r.codigo, e.cod_tp_cota
				order by c.codigo, e.cod_tp_cota, abs(r.codigo)";
							
	$result = mysql_query($qry);
	$i=0;
	while ($linha = mysql_fetch_array($result, MYSQL_NUM)) {
		//Verifica o somatório dos abastecimentos autorizados e não realizados
		$qrya = "select sum(qtd_autorizada) from abastecimento where reservatorio_codigo='$linha[0]' and cod_tp_cota in($linha[5]) and cota_om='$linha[6]' and qtd_abastecida is null";
		$resa = mysql_query($qrya);
		$qtda = mysql_fetch_row($resa);
		$qtd_c = str_replace(".", ",",($linha[3]-$qtda[0]));
		echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'><td>$linha[2]</td><td>$linha[4]</td><td> Nº $linha[0]</td><td align='left'>$linha[1]</td><td align='right'>$qtd_c Lts&nbsp; </td></tr>";
		$reservatorio[$i] = $linha[0];
		$local[$i] = $linha[1];
		$comb[$i] = $linha[2];
		$qtd[$i] = $linha[3];
		$i++;
	}
	echo "<tr bgcolor='$cor_fundo'><td COLSPAN='11'><b><I>&nbsp;&nbsp;&nbsp;OBS:</b> As quantidades aqui descritas já estão descontadadas os abastecimentos autorizados e não executados.</I></td></tr>";

	echo "</table>";
	
	echo "</td></tr>";
	echo "<tr><td>";
	
	echo "<table border='0' width='100%' align='left' valign='top'>";
	echo "<tr align='center' bgcolor='$cor_fundo'><td COLSPAN='11'><hr></td></tr>";
	echo "<tr align='center' bgcolor='$cor1'><td COLSPAN='11'><b><I>ABASTECIMENTOS AUTORIZADOS PELO(A) ".$_SESSION['unidade']."</I></td></tr>";
	echo "<tr align='center' bgcolor='$cor2'>
				<td><b>OM</td><td><b>Viatura</td><td><b>EB</td><td><b>Od Atual</td><td><b>Qtd</td><td><b>Data Autz</td><td><b>Reservatório</td><td><b>Combustível</td><td><b>Motorista/Responsável</td><td><b>Código</td><td><b>Remover</td>
			</tr>";
   
	/*Seleciona a relação de abastecimentos autorizados e não abastecidos*/
	$qry_disp="SELECT concat(v.marca,' - ', v.modelo), a.viatura_eb, a.qtd_autorizada, a.dt_autorizacao, c.codigo, a.motorista, o.sigla, a.cod_seguranca, r.codigo, a.missao, a.od_atual, t.tipo_cota, c.nome, a.tp_abastecimento, a.cota_om, a.cod_tp_cota
					FROM combustivel c, viatura v, abastecimento a, om o, reservatorio r, tipo_cota t, usuario u 
					WHERE o.codom=r.om_codom
						and v.eb=a.viatura_eb
						and c.codigo = v.combustivel_codigo
						and r.codigo=a.reservatorio_codigo
						and t.cod_tp_cota=a.cod_tp_cota
						and a.qtd_abastecida is null 
						and u.idt=a.usuario_autz
						and u.om_codom = '".$_SESSION['codom']."'";

	$resultado = mysql_query($qry_disp);				
	while ($row = mysql_fetch_array($resultado, MYSQL_NUM)) {
		$qry_om = "select sigla from om, viatura v where om.codom=v.om_codom and v.eb='$row[1]'";
		$res_om = mysql_query($qry_om);
		$om = mysql_fetch_row($res_om);
		$data = substr($row[3],8,2)."/".substr($row[3],5,2)."/".substr($row[3],0,4);
		if($row[13]=='Carregamento de Cisterna') {
			$row[10]='-';
			$row[0]=$row[13];
		}
		echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'><td align='left'>$om[0]</td><td align='left'>$row[0]</td><td>$row[1]</td><td>$row[10]</td><td>$row[2] Lts</td><td>$data</td><td>NÇ $row[8] - $row[6]</td><td>$row[12] - $row[11]</td><td align='left'>$row[5]</td>";
//			echo "<td><a href=\"#\" title='Imprimir Autorização' onClick=\"window.open('qrcode/impr_autz_abast.php?om=$om[0]&eb=$row[1]&dt=$row[3]&res=$row[8]&cota=$row[14]&tp_cota=$row[15]&motorista=$row[5]&data=$data&cod=$row[7]&qtd=$row[2]&posto=$row[8] - $row[6]&odometro=$row[10]&tp_abastecimento=$row[13]&usuario=".substr($_SESSION['post_grad'],1)." ".$_SESSION['nome_guerra']."&sigla=".$_SESSION['unidade']."','janela','toolbar=no,location=no,directories=no,status=eys,menubar=no,scrollbars=yes,resizable=yes,width=450,height=550');\">$row[7]</a></td>
			echo "<td><a href='./qrcode/impr_autz_abast.php?om=$om[0]&eb=$row[1]&dt=$row[3]&res=$row[8]&cota=$row[14]&tp_cota=$row[15]&motorista=$row[5]&data=$data&cod=$row[7]&qtd=$row[2]&posto=$row[8] - $row[12] - $row[6]&odometro=$row[10]&tp_abastecimento=$row[13]&usuario=".substr($_SESSION['post_grad'],1)." ".$_SESSION['nome_guerra']."&sigla=".$_SESSION['unidade']."' title='Imprimir Autorização' target='_blank'>$row[7]</a></td>
				<td><a href='index.php?nomeArquivo=remover_registro.php&tab=abastecimento&ch=viatura_eb&valor=$row[1]&ch1=dt_abastecimento&valor1=$row[3]&valor2=1&registro=a autorização de abastecimento de $row[2] Lts da Viatura $row[0] - EB $row[1] &arquivo=remover_autz.php' title='Remover esta autorização'>Remover</td>";
		echo "</tr>";
		for ($x=0; $x<$i; $x++) {
			if (($comb[$x]==$row[2]) && ($reservatorio[$x]==$row[8]))
				$qtd[$x] = $qtd[$x] - $row[1];
		}
	}
	echo "</table>";
	echo "</td></tr>";
	echo "<tr align='center' bgcolor='$cor_fundo'><td COLSPAN='8'><hr></td></tr>";
	
	echo "<tr><td>";
	echo "<table border='0' width='80%' align='left' valign='top'>";
	echo "<tr align='center' bgcolor='$cor1'><td colspan='2'><b><I>NOVA AUTORIZAÇÃO</I></td></tr>";
	echo "<form name='frm_autz' method='POST' action='index.php?nomeArquivo=gravar_autz.php' >";
	echo "<tr><td align='right' width='20%'>EB:&nbsp;</td><td width='80%'><select name='eb' onChange='getStates(this);' required>";
				echo "<option value='' selected disabled> ESCOLHA A VIATURA </option>";
						/*Seleciona a relação de viaturas*/
						$result = mysql_query("select v.eb, c.nome, v.combustivel_codigo, v.marca, v.modelo, v.cap_tanque, o.sigla 
									from viatura v, combustivel c, om o
									where o.codom=v.om_codom
									and c.codigo=v.combustivel_codigo
									and v.om_codom='".$_SESSION['posto']."' 
									order by c.nome, v.eb");
						while ($vtr = mysql_fetch_array($result, MYSQL_NUM)) {
							$eb = $vtr[0];
							echo "<option value='$eb' "; if ($_GET['ebv']==$eb){ echo "SELECTED";$tp_comb = $vtr[2]; $viatura=$vtr[3]." - ".$vtr[4]; $capac=$vtr[5]; $tpcomb=$vtr[1]; $sigla=$vtr[6];} echo " > $eb - $vtr[1] </option> ";
						}
						echo " </select></td></tr>";
	echo "<tr><td align='right'>Viatura:&nbsp;</td><td><font color='blue'><i>$viatura</td></tr>";
	echo "<tr><td align='right'>Combustível:&nbsp;</td><td><font color='blue'><i>$tpcomb</td></tr>";
	echo "<tr><td align='right'>Capacidade do Tanque:&nbsp;</td><td><i>$capac Litros</td></tr>";
	echo "<input type='hidden' name='om_vtr' value='".$_SESSION['unidade']."'>";
	
	/*/Seleciona a relaçao de reservatorios com combustiveis maior que zero*/
	$qry="select r.codigo, o.sigla, c.nome, truncate(sum(e.qtd_atual),0), t.tipo_cota, t.cod_tp_cota, e.om_codom
				from reservatorio r, om o, combustivel c, tipo_cota t, estoque e
				where o.codom=r.om_codom
					and r.codigo=e.reservatorio_codigo
					and c.codigo=r.combustivel_codigo
					and t.cod_tp_cota=e.cod_tp_cota ";
		if($$abast_c_saldo_neg!='s') {
			$qry .= "and e.qtd_atual>0 ";
		}
		$qry .= "and t.cod_tp_cota <> '7'
					and e.om_codom='".$_SESSION['codom']."'
					and c.codigo='$tp_comb'
				group by r.codigo, e.cod_tp_cota
				order by c.codigo, e.cod_tp_cota, abs(r.codigo)";
	$result = mysql_query($qry);

	$cont='1';
	echo "<tr><td align='right' width='20%'>Local e Tipo:&nbsp;</td><td width='30%'><select name='reservatorio' id='posto' required >";
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		//Verifica o somatório dos abastecimentos autorizados e não realizados
		$qrya = "select sum(qtd_autorizada) from abastecimento where reservatorio_codigo='$row[0]' and cod_tp_cota in($row[5]) and cota_om='$row[6]' and qtd_abastecida is null";
		$resa = mysql_query($qrya);
		$qtda = mysql_fetch_row($resa);
		$row[3] = $row[3] - $qtda[0];

		if($abast_c_saldo_neg=='s') {
			//Verifica o crédito da OM
			$qrycr = "select sum(qtd_atual) from credito where cod_tp_cota='$row[5]' and om_codom='$row[6]' and combustivel_codigo='$tp_comb' ";
			$rescr = mysql_query($qrycr);
			$qtdcr = mysql_fetch_row($rescr);  
			$cota_cred .= ",".$row[5];
		}

		echo "<option id='$row[3]' cr='$qtdcr[0]' value='$row[0],$row[5],$row[1],$row[2]'>Res: $row[0] - $row[1] - $row[2] $row[4]</option>";
		if($cont=='1') {
			$valor=$row[3];
			$cred=$qtdcr[0]-$qtda[0];
		}
		$cont++;
	}
	
	echo "</select>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Disponibilidade:&nbsp;<input type='text' name='qtd_max' size='10' value='$valor Lts' readonly>"; 
	if($abast_c_saldo_neg=='s') {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Crédito:&nbsp;<input type='text' name='cr_max' size='10' value='$cred Lts' readonly>"; 
		if($cred>$valor) {
			$valor=$cred;
		}
	} else {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='hidden' name='cr_max' size='10' value='0 Lts' readonly>"; 
	}
	echo "</td></tr>";

	echo "<tr><td align='right'>Quantidade:&nbsp;</td><td width='18%'><input type='number' min='1' max='$valor' name='qtd_comb' required> Lts</td></tr>";
	echo "<tr><td align='right'>Odometro Atual:&nbsp;</td><td><input type='text' size='10' maxlength='10' name='odometro' required><br></td></tr>";
	echo "<tr><td align='right'>Motorista:&nbsp;</td><td><input type='text' size='25' maxlength='50' name='motorista' required><br></td></tr>";
	echo "<tr><td align='right'>Missão:&nbsp;</td><td><input type='text' size='40' maxlength='50' name='missao' required><br></td></tr>";
	echo "<input type='hidden' name='sigla' value='$sigla'>";
	echo "<input type='hidden' name='tp_abastecimento' value='Abastecimento de Viatura'>";
	echo "<input type='hidden' name='combustivel' value='$tpcomb'>";
	echo "<input type='hidden' name='operacao' value='1'>";
	echo "<tr><td COLSPAN='2'>&nbsp;</td></tr>";
	echo "<tr><td align='right'><input type='submit' value= 'Cadastrar'></td><td><input type='reset' value= '  Limpar  '></td></tr>";
	echo "</form>";
	echo "</table>";
	echo "</td></tr>";
	echo "</table>";
?>
