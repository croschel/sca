<?php
  if(!isset($_SESSION['usuario']))
    include "verifica_sessao.php";
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<script language="JavaScript" type="text/javascript">
function mascara(dt_abast){
              var mdata = '';
              mdata = mdata + dt_abast;
              if (mdata.length == 2){
                  mdata = mdata + '/';
                  document.forms[1].dt_abast.value = mdata;
              }
              if (mdata.length == 5){
                  mdata = mdata + '/';
                  document.forms[1].dt_abast.value = mdata;
              }
              if (mdata.length == 10){
                  return true;
              }
          }

$(document).ready(function () {
	$("input[name='qtd_comb']").css({width:'60px',})
	$("#posto").change(function () {
		$("input[name='qtd_comb']").attr("max",Math.floor($("option:selected",this).attr("id")));
		$("input[name='qtd_max']").attr("value",Math.floor($("option:selected",this).attr("id"))+' Lts');
	})
	$("input[name='qtd_max']").css({background:'transparent', border:'none', color:'red',})
	$("input[name='cr_max']").css({background:'transparent', border:'none', color:'red',})

})
</script>

<?php
	echo "<font size='5'><b><br>Autorizção para Carregamento de Cisterna</font><hr>";
	echo "<table width='100%'>";
	echo "<tr><td>";
	echo "<table border='0' width='70%' align='left' valign='top'>";
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
					and t.cod_tp_cota<>'7'
					and e.qtd_atual>0
					and e.om_codom='".$_SESSION['codom']."'
				group by r.codigo, e.cod_tp_cota
				order by c.codigo, e.cod_tp_cota, abs(r.codigo)";
				
	$result = mysql_query($qry);
	$i=0;
	while ($linha = mysql_fetch_array($result, MYSQL_NUM)) {
		//Verifica o somatório dos abastecimentos autorizados e não realizados
		$qryc = "select sum(qtd_autorizada) from abastecimento where reservatorio_codigo='$linha[0]' and cod_tp_cota='$linha[5]' and cota_om='$linha[6]' and qtd_abastecida is null";
		$resc = mysql_query($qryc);
		$qtdc = mysql_fetch_row($resc);
		$qtd_c = str_replace(".", ",",($linha[3]-$qtdc[0]));
		echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'><td>$linha[2]</td><td>$linha[4]</td><td> Nº $linha[0]</td><td align='left'>$linha[1]</td><td align='right'>$qtd_c Lts&nbsp; </td></tr>";
		$reservatorio[$i] = $linha[0];
		$local[$i] = $linha[1];
		$comb[$i] = $linha[2];
		$qtd[$i] = $linha[3];
		$i++;
	}


	echo "</table>";

	echo "</td></tr>";
	echo "<tr><td>";

	echo "<table border='0' width='100%' align='left' valign='top'>";
	echo "<tr align='center' bgcolor='$cor_fundo'><td COLSPAN='10'><hr></td></tr>";
	echo "<tr align='center' bgcolor='$cor1'><td COLSPAN='10'><b><I>ABASTECIMENTOS AUTORIZADOS PELO(A) ".$_SESSION['unidade']."</I></td></tr>";
	echo "<tr align='center' bgcolor='$cor1'>
				<td><b>OM</td><td><b>Viatura</td><td><b>EB</td><td><b>Od Atual</td><td><b>Qtd</td><td><b>Data Autz</td><td><b>Reservatório</td><td><b>Motorista</td><td><b>Código</td><td><b>Remover</td>
			</tr>";
			
	/*Seleciona os abastecimentos autorizados*/
	$qry_disp="SELECT concat(v.marca,' - ', v.modelo), a.viatura_eb, a.qtd_autorizada, a.dt_autorizacao, c.codigo, a.motorista, o.sigla, a.cod_seguranca, r.codigo, a.missao, a.od_atual, t.tipo_cota, c.nome, a.tp_abastecimento
					FROM combustivel c, viatura v, abastecimento a, om o, reservatorio r, tipo_cota t 
					WHERE o.codom=r.om_codom
						and v.eb=a.viatura_eb
						and c.codigo = v.combustivel_codigo
						and r.codigo=a.reservatorio_codigo
						and t.cod_tp_cota=a.cod_tp_cota
						and a.qtd_abastecida is null 
						and a.cota_om = '".$_SESSION['codom']."'";
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
	echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'><td align='left'>$om[0]</td><td align='left'>$row[0]</td><td>$row[1]</td><td>$row[10]</td><td>$row[2] Lts</td><td>$data</td><td>Nº $row[8] - $row[6]</td><td align='left'>$row[5]</td>";

		echo "<td><a href='./qrcode/impr_autz_abast.php?om=$om[0]&eb=$row[1]&dt=$row[3]&res=$row[8]&cota=$row[14]&tp_cota=$row[15]&motorista=$row[5]&data=$data&cod=$row[7]&qtd=$row[2]&posto=$row[8] - $row[6]&odometro=$row[10]&tp_abastecimento=$row[13]&usuario=".substr($_SESSION['post_grad'],1)." ".$_SESSION['nome_guerra']."&sigla=".$_SESSION['unidade']."' title='Imprimir Autorização' target='_blank'>$row[7]</a></td>
			<td><a href='index.php?nomeArquivo=remover_registro.php&tab=abastecimento&ch=viatura_eb&valor=$row[1]&ch1=dt_abastecimento&valor1=$row[3]&valor2=5&registro=a autorização de abastecimento da Viatura $row[0] - EB $row[1] &arquivo=remover_autz.php' title='Remover esta autorização'>Remover</td>";

		echo "</tr>";
}
		
echo "</table>";
echo "</td></tr>";
echo "<tr><td>";
echo "<table border='0' width='80%' align='left' valign='top'>";
echo "<form name='frm_autz' method='POST' action='index.php?nomeArquivo=autz_carr_cisterna.php' >";
echo "<tr><td COLSPAN='2'><hr></td></tr>";
echo "<tr><td align='right'><b><I>Digite o EB da Viatura: </I></td><td><input type='text' size='10' maxlength='10' name='eb' value='".$_POST['eb']."'>&nbsp;<input type='submit' value= 'Localizar'></td></tr>";
echo "<tr><td COLSPAN='2'><hr></td></tr>";
echo "</form>";
echo "<form name='frm_autz_vale' method='POST' action='index.php?nomeArquivo=gravar_autz.php' >";
	/*Seleciona a Viatura*/
	$query = "SELECT o.codom, o.sigla, v.marca, v.modelo, c.nome, v.eb, c.codigo, v.cap_tanque
			FROM om o, viatura v, combustivel c
			WHERE o.codom=v.om_codom
			and c.codigo=v.combustivel_codigo
			and v.eb='".$_POST['eb']."'";
	$result = mysql_query($query,$conexao);
	if ($_POST['eb'] != ''){
		$row = mysql_fetch_row($result);
	}

	echo "<input type='hidden' name='sigla' value='".$_SESSION['unidade']."'>";
	echo "<tr><td align='right'><b><I>OM da Viatura: </I></td><td> <font color='blue'> ".$row[1]."</td></tr>";
	echo "<input type='hidden' name='codom' value='$row[0]'>";
	echo "<tr><td align='right'><b><I>EB da Viatura: </I></td><td> <font color='blue'>$row[5]</td></tr>";
	echo "<input type='hidden' name='om_vtr' value='".$row[1]."'>";
	echo "<input type='hidden' name='eb' value='$row[5]'>";
	echo "<tr><td align='right'><b><I>Viatura: </I></td><td> <font color='blue'>$row[2] - $row[3]</td></tr>";

	/*/Seleciona a relaçao de reservatorios com combustiveis maior que zero*/
	$qry="select r.codigo, o.sigla, c.nome, truncate(sum(e.qtd_atual),0), t.tipo_cota, t.cod_tp_cota, e.om_codom, c.codigo
				from reservatorio r, om o, combustivel c, tipo_cota t, estoque e
				where o.codom=r.om_codom
					and r.codigo=e.reservatorio_codigo
					and c.codigo=r.combustivel_codigo
					and t.cod_tp_cota=e.cod_tp_cota ";
		if($abast_c_saldo_neg!='s') {
			$qry .= "and e.qtd_atual>0 ";
		}
		$qry .= "and t.cod_tp_cota<>'7'
					and e.om_codom='".$_SESSION['codom']."'
				group by r.codigo, e.cod_tp_cota
				order by c.codigo, e.cod_tp_cota, abs(r.codigo)";
	$result = mysql_query($qry);
	$cont='1';
	echo "<tr><td align='right'><b><I>Local e Tipo:&nbsp;</td><td><select name='reservatorio' id='posto' required >";
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if($c_ant!=$row[2]) {
			echo "<option disabled>:-:-: Reservatórios de $row[2] :-:-:</option>";
			$c_ant=$row[2];
		}
		//Verifica o somatório dos abastecimentos autorizados e não realizados
		$qrya = "select sum(qtd_autorizada) from abastecimento where reservatorio_codigo='$row[0]' and cod_tp_cota='$row[5]' and cota_om='$row[6]' and qtd_abastecida is null";
		$resa = mysql_query($qrya);
		$qtda = mysql_fetch_row($resa);
		$row[3] = $row[3] - $qtda[0];
		
		if($abast_c_saldo_neg=='s') {
			//Verifica o crédito da OM
			$qrycr = "select sum(qtd_atual) from credito where cod_tp_cota='$row[5]' and om_codom='$row[6]' and combustivel_codigo='$row[7]' ";
			$rescr = mysql_query($qrycr);
			$qtdcr = mysql_fetch_row($rescr);  
			$cota_cred .= ",".$row[5];
		}

		echo "<option id='$row[3]' value='$row[0],$row[5],$row[1],$row[2]'>Res: $row[0] - $row[1] - $row[2] $row[4]</option>";
		if($cont=='1') {
			$valor=$row[3];
			$cred=$qtdcr[0]-$qtda[0];
		}
		$cont++;
		$tpcomb = $row[2];
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
	
	echo "<tr><td align='right'><b><I>Quantidade Autorizada: </I></td><td> <input type='number' min='1' max='$valor' name='qtd_comb' size='4'> Lts</td></tr>";  
	echo "<tr><td align='right'><b><I>Responsável: </I></td><td> <input type='text' size='30' maxlength='30' name='motorista'></td></tr>";
	echo "<tr><td align='right'><b><I>Missão: </td><td><input type='text' size='30' maxlength='50' name='missao'></td></tr>";
	echo "<input type='hidden' name='tp_abastecimento' value='Carregamento de Cisterna'>";
	echo "<input type='hidden' name='operacao' value='1'>";
	echo "<tr><td COLSPAN='2'>&nbsp;</td></tr>";
	echo "<tr><td align='right'><input type='submit' value= 'Confirmar'>&nbsp;&nbsp;</td><td>&nbsp;&nbsp;<input type='reset' value= '  Cancelar  '></td></tr>";	
	echo "</form>";
	echo "</table>";
	echo "</td></tr>";
	echo "</table>";
?>
