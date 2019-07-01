<?php
  if(empty($_SESSION['usuario']))
    include "verifica_sessao.php";
?>
<script language="JavaScript" type="text/javascript">
function mascara(dt_abast){
              var mdata = '';
              mdata = mdata + dt_abast;
              if (mdata.length == 2){
                  mdata = mdata + ':';
                  document.forms[1].hora_abast.value = mdata;
              }
               if (mdata.length == 5){
                  return true;
              }
          }

$(document).ready(function () {
	$("input[name='qtd_abast']").css({width:'60px',})
})

</script>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<center>
<br>
<font size='5'><t1>Abastecimento de Viatura Em Trânsito</t1></font>
<hr><br>
<?php
echo "<table width='100%' align='left' valign='top'>";
echo "<tr><td>";
echo "<table border='0' width='60%' align='left' valign='top'>";
echo "<form name='frm_autz' method='POST' action='index.php?nomeArquivo=abast_vtr_transito.php' >";
	echo "<tr><td align='right'><b><I>Digite o EB da Viatura: </I></td><td><input type='text' size='10' maxlength='10' name='eb'  value='".$_POST['eb']."'>&nbsp;<input type='submit' value= 'Localizar'></td></tr>";
	echo "<tr><td COLSPAN=2><hr></td></tr>";

echo "</form>";

echo "<form name='frm_autz_vale' method='POST' action='index.php?nomeArquivo=gravar_abast_vtr_transito.php' >";

/*Conexão ao Banco de Dados*/
include "conexao.php";
/*Seleciona a Viatura*/
 $query = "SELECT o.codom, o.sigla, v.marca, v.modelo, c.nome, v.eb, c.codigo, v.cap_tanque
			FROM om o, viatura v, combustivel c
			WHERE o.codom=v.om_codom
			and c.codigo=v.combustivel_codigo
			and v.eb='".$_POST['eb']."'";
/*/echo $query;*/
$result = mysql_query($query,$conexao);
	if ($_POST['eb'] != ''){
		$row = mysql_fetch_row($result);
	}

	/*/Seleciona o OC*/
	$result = mysql_query("select codom, sigla from om where codom='$cod_oc_geral'");
	$oc = mysql_fetch_row($result);
	echo "<input type='hidden' name='sigla' value='$oc[1]'";
	echo "<tr><td align='right'><b><I>Combustível na Cota do(a): </td><td><b><I> $oc[1] 	</td></tr>";

	echo "<tr><td align='right'><b><I>OM da Viatura: </I></td><td> <font color='blue'> $row[1]</td></tr>";
	echo "<tr><td align='right'><b><I>EB da Viatura: </I></td><td> <font color='blue'>$row[5]</td></tr>";
	echo "<input type='hidden' name='ebv' value='$row[5]'>";
	echo "<tr><td align='right'><b><I>Viatura: </I></td><td> <font color='blue'>$row[2] - $row[3]</td></tr>";
	echo "<tr><td align='right'><b><I>Combustível: </I></td><td> <font color='blue'>$row[4]</td></tr>";

	/*/Seleciona o Reservatório*/
echo	$query = "SELECT qtd_atual, reservatorio_codigo, om_codom
			FROM estoque 
			WHERE om_codom = '$cod_oc_geral'
			and combustivel_codigo = '".$row[6]."'";
	$result = mysql_query($query,$conexao);

	if ($_POST['eb'] != ''){
		$comb = mysql_fetch_row($result);
	}

	echo "<tr><td align='right'><b><I>Disponibilidade: </I></td><td> <font color='red'> ".number_format($comb[0], 3, ',', '.')." Lts</td></tr>";
	echo "<input type='hidden' name='reservatorio' value='$comb[1]'>";
	echo "<input type='hidden' name='codom' value='$cod_oc_geral'>";
	echo "<tr><td align='right'><b><I>Quantidade Abastecida: </I></td><td> <input type='number' size='3' name='qtd_autz' min='1' max='$comb[0]'> Lts</td></tr>";
	echo "<tr><td align='right'><b><I>Motorista: </I></td><td> <input type='text' size='30' maxlength='30' name='motorista'></td></tr>";
	echo "<tr><td align='right'><b><I>Odômetro Atual: </I></td><td> <input type='text' size='10' maxlength='10' name='odometro'></td></tr>";
	echo "<tr><td align='right'><b><I>Missão: </td><td><input type='text' size='50' maxlength='50' name='missao'></td></tr>";
	echo "<tr><td COLSPAN=2>&nbsp;</td></tr>";
	if($comb[0]>'0') {
		echo "<tr><td align='right'><input type='submit' value= 'Confirmar'>&nbsp;&nbsp;</td><td>&nbsp;&nbsp;<input type='reset' value= '  Cancelar  '></td></tr>";
	} else {
		echo "<tr><td align='right'><b><font color='red'>Atenção!</font>&nbsp;</td><td><font color='red'>Saldo Insuficiente de Combustível.</font></td></tr>";
	}
	echo "<tr><td COLSPAN=2><hr></td></tr>";
echo "</form>";
echo "</table>";
echo "</td></tr>";
echo "<tr><td>";
echo "<table border='0' width='100%' align='left' valign='top'>";
echo "<tr align='center' bgcolor='$cor_fundo'><td COLSPAN='9'><hr></td></tr>";
echo "<tr align='center' bgcolor='$cor1'><td COLSPAN='9'><b><I>ABASTECIMENTOS AUTORIZADOS EM CISTERNA PELO(A) ".$_SESSION['unidade']."</I></td></tr>";
echo "<tr align='center' bgcolor='$cor2'>";
echo "<td><b>Viatura</td><td><b>EB</td><td><b>Data Autz</td><td><b>Reservatório</td><td><b>Qtd Autz</td><td><b>Motorista/Responsável</td><td><b>Missão</td><td><b>Código</td><td><b>Remover</td>";
echo "</tr>";
/*Seleciona os abastecimentos autorizados*/
 $qry_disp=" SELECT concat(v.marca,' - ', v.modelo), a.viatura_eb, a.qtd_autorizada, a.dt_autorizacao, c.codigo, a.motorista, o.sigla, a.cod_seguranca, r.codigo, a.missao, u.post_grad, u.nome_guerra
					FROM combustivel c, viatura v, abastecimento a, om o, reservatorio r, usuario u
					WHERE c.codigo = v.combustivel_codigo
					and o.codom=r.om_codom
					and r.codigo=a.disponibilidade_reservatorio_codigo
					and v.eb=a.viatura_eb
					and u.idt=a.usuario_autz
					and a.disponibilidade_om_codom = '$cod_oc_geral'
					and r.codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."')
					and qtd_abastecida is null
					ORDER BY 4";
$resultado = mysql_query($qry_disp);
while ($row = mysql_fetch_array($resultado, MYSQL_NUM)) {
	$data = substr($row[3],8,2)."/".substr($row[3],5,2)."/".substr($row[3],0,4);
	echo "<tr bgcolor='$cor3' align='center'><td align='left'>$row[0]</td><td>$row[1]</td><td>$data</td><td>Nº $row[4] - $row[6]</td><td>$row[2] Lts</td><td>$row[5]</td><td>$row[9]</td>";
	echo "<td><a href=\"#\" title='Imprimir Autorização' onClick=\"window.open('impr_autz_abast_cisterna.php?eb=$row[1]&responsavel=$row[5]&data=$data &cod=$row[7]&qtd=$row[2]&posto=$row[8] - $row[6]&usuario_autz=$row[10] $row[11]','janela','toolbar=no,location=no,directories=no,status=eys,menubar=no,scrollbars=yes,resizable=yes,width=450,height=350');\">$row[7]</a></td>
				<td><a href='index.php?nomeArquivo=remover_registro.php&tab=abastecimento&ch=viatura_eb&valor=$row[1]&ch1=dt_abastecimento&valor1=$row[3]&valor2=4&registro=a autorização de abastecimento da Viatura $row[0] - EB $row[1] &arquivo=remover_autz.php' title='Remover esta autorização'>Remover</a></td>";
	echo "</tr>";
	for ($x=0; $x<$i; $x++) {
		if (($comb[$x]==$row[2]) && ($reservatorio[$x]==$row[8]))
			$qtd[$x] = $qtd[$x] - $row[1];
	}
}
		
echo "</table>";
echo "</td></tr>";
echo "</table>";
?>

