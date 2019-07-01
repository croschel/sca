<?php
  if(!isset($_SESSION['usuario']))
    include "verifica_sessao.php";
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<script language="JavaScript" type="text/javascript">

$(document).ready(function () {
	$("input[name='eb']").css({background:'transparent', border:'none',})
	$("input[name='data']").css({background:'transparent', border:'none',})
	$("input[name='hora']").css({background:'transparent', border:'none',})
	$("input[name='comb']").css({background:'transparent', border:'none',})
	$("input[name='local']").css({background:'transparent', border:'none',})
	$("input[name='qtd_carr']").css({background:'transparent', border:'none',})
	$("input[name='qtd_abast']").css({width:'60px',background:'transparent', border:'none',})
	$("input[name='qtd_abast']").attr("value",(parseInt(document.devolvimento.qtd_carr.value) - parseInt(document.devolvimento.qtd_devol.value)));
	$("input[name='qtd_abast1']").attr("value",(parseInt(document.devolvimento.qtd_carr.value) - parseInt(document.devolvimento.qtd_devol.value))+' Lts');
	$("#qtd_devol").change(function () {
		$("input[name='qtd_abast']").attr("value",(parseInt(document.devolvimento.qtd_carr.value) - parseInt(document.devolvimento.qtd_devol.value)));
		document.devolvimento.qtd_abast.value = (parseInt(document.devolvimento.qtd_carr.value) - parseInt(document.devolvimento.qtd_devol.value));
		document.devolvimento.qtd_abast1.value = (parseInt(document.devolvimento.qtd_carr.value) - parseInt(document.devolvimento.qtd_devol.value))+' Lts';
	})
	$("#idt").change(function () {
		$("input[name='senha']").attr("value",$("option:selected",this).attr("id"));
	})
	$("input[name='qtd_max']").css({background:'transparent', border:'none', color:'red',})
})
</script>

<?php
	echo "<b><br><t1>Devolução de Comubstível com Retorno de Cisterna</t1><hr>";
	echo "<table width='100%' border='0'>";
	echo "<tr><td>";

	echo "<table border='0' width='100%' align='left' valign='top'>";
	echo "<tr align='center' bgcolor='$cor1'><td COLSPAN='10'><coluna>CARREGAMENTOS DE CISTERNAS EFETUADOS NA COTA DO(A) ".$_SESSION['unidade']."</td></tr>";
	echo "<tr align='center' bgcolor='$cor1'>
				<td rowspan='2'><coluna>Cisterna</td>
				<td rowspan='2'><coluna>Combustível</td>
				<td rowspan='2'><coluna>Qtd Autz</td>
				<td colspan='2'><coluna>Data</td>
				<td rowspan='2'><coluna>Reservatório</td>
				<td rowspan='2'><coluna>Destino</td>
				<td rowspan='2'><coluna>Responsável</td>
				<td rowspan='2'><coluna>Abastecedor</td>
				<td rowspan='2'><coluna>Remover</td>
			</tr>";
	echo "<tr align='center' bgcolor='$cor1'>
				<td><coluna>Autorização</td>
				<td><coluna>Abastecimento</td>
			</tr>";
			
	/*Seleciona os abastecimentos autorizados*/
	$qry_disp="SELECT a.viatura_eb, a.qtd_abastecida, a.dt_abastecimento, a.dt_autorizacao, c.nome, a.motorista, o.sigla, r.codigo, a.missao, a.od_atual, 
						t.tipo_cota, a.tp_abastecimento, u.post_grad, u.nome_guerra, u.om_codom, u.senha
					FROM combustivel c, viatura v, abastecimento a, om o, reservatorio r, tipo_cota t, usuario u 
					WHERE o.codom=r.om_codom
						and v.eb=a.viatura_eb
						and c.codigo = v.combustivel_codigo
						and r.codigo=a.reservatorio_codigo
						and t.cod_tp_cota=a.cod_tp_cota
						and a.usuario_idt=u.idt
						and a.qtd_abastecida is not null 
						and a.dt_abastecimento > DATE_SUB(CURDATE(), INTERVAL 1 MONTH) 
						and a.tp_abastecimento='Carregamento de Cisterna'
						and a.cota_om = '".$_SESSION['codom']."'";
$resultado = mysql_query($qry_disp);
while ($row = mysql_fetch_array($resultado, MYSQL_NUM)) {
	$data = substr($row[3],8,2)."/".substr($row[3],5,2)."/".substr($row[3],0,4);

	echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'>
				<td align='left'>&nbsp;EB$row[0]</td>
				<td>$row[4]</td>
				<td>$row[1] Lts</td>
				<td>".substr($row[3],8,2)."/".substr($row[3],5,2)."/".substr($row[3],0,4)." ".substr($row[3],11,5)."</td>
				<td>".substr($row[2],8,2)."/".substr($row[2],5,2)."/".substr($row[2],0,4)." ".substr($row[2],11,5)."</td>
				<td>Nº $row[7] - $row[6]</td>
				<td>$row[8]</td>
				<td>$row[5]</td>
				<td>".substr($row[12],1)." $row[13]</td>
				<td>
					<a href='index.php?nomeArquivo=retorno_cisterna.php&eb=$row[0]&dt_abast=$row[2]' style='text-decoration:none' title='Devolver Combustível'>Devolver</a>
				</td>";

	echo "</tr>";
	if(($_GET['eb']==$row[0]) and ($_GET['dt_abast']==$row[2])) {
		$abst = $row;
	}
}
		
echo "</table>";
echo "</td></tr>";
echo "<tr><td>&nbsp;</td></tr>";

if(($_GET['eb']) and ($_GET['dt_abast'])) {
	echo "<tr><td>";
	echo "<table border='0' width='70%' align='center' valign='top'>";
	echo "<form name='devolvimento' method='POST' action='index.php?nomeArquivo=gravar_retorno_cisterna.php' >";
	echo "<tr bgcolor='$cor2'><td align='center' colspan='2'><coluna>DADOS DO CARREGAMENTO A SER DEVOLVIDO</td></tr>";
	echo "<tr><td COLSPAN='2'>&nbsp;</td></tr>";
	echo "<tr><td align='right' width='50%'><coluna>EB da Cisterna: </I></td><td width='50%'><input type='text' name='eb' value='$abst[0]' readonly></td></tr>";
	echo "<input type='hidden' name='eb' value='$abst[0]'>";
	echo "<input type='hidden' name='dtabast' value='".$_GET['dt_abast']."'> ";
	echo "<tr><td align='right'><coluna>Data do Carregamento: </I></td><td> <input type='text' name='data' value='".substr($abst[2],8,2)."/".substr($abst[2],5,2)."/".substr($abst[2],0,4)."' readonly></td></tr>";
	echo "<tr><td align='right'><coluna>Hora do Carregamento: </I></td><td> <input type='text' name='hora' value='".substr($abst[2],11,5)."' readonly></td></tr>";
	echo "<tr><td align='right'><coluna>Combustível Carregado: </I></td><td> <input type='text' name='comb' value='$abst[4]' readonly></td></tr>";
	echo "<tr><td align='right'><coluna>Local do Carregamento: </I></td><td> <input type='text' name='local' size='40' value='Res nº $abst[7] do $abst[6]' readonly></td></tr>";
	echo "<tr><td align='right'><coluna>Quantidade Carregada: </I></td><td> <input type='text' name='qtd_carr' value='$abst[1] Lts' readonly></td></tr>";
	echo "<tr><td align='right'><coluna>Quantidade Devolvida: </I></td><td> <input type='number' id='qtd_devol' name='qtd_devol' min='1' max='$abst[1]' value='0'> Lts</td></tr>";
	echo "<tr><td align='right'><coluna>Quantidade Utilizada: </I></td><td> <input type='text' name='qtd_abast1' readonly></td></tr>";  
	echo "<input type='hidden' name='qtd_abast' value='$abst[0]'>";
	//Seleciona a relacao de abastecedores
	$query="select * from usuario where om_codom='$abst[14]' and perfil='ABASTECEDOR' order by post_grad";
	$result = mysql_query($query);
	echo "<tr><td align='right'><coluna>Responsável pelo Recebimento: </td><td><select name='idt' id='idt' required title='Abastecedor Responsável pelo Recebimento no Posto'>";
					echo "<option value='' selected disabled>:-: SELECIONE:-:</option>";
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option id='$row[8]' value='$row[0]'"; if ($row[0]==$_SESSION['codom']){ echo "selected";} echo ">".substr($row[1],1)." $row[3] </option>";
						}
						echo "</select></td></tr>";
	echo "<tr><td align='right'><coluna>Senha Abastecedor: </I></td><td> <input type='password' name='senha1' value='' required></td></tr>";
	echo "<input type='hidden' name='senha' value=''>";
	echo "<tr><td COLSPAN='2'>&nbsp;</td></tr>";
	echo "<tr><td align='right'><input type='submit' value= 'Confirmar'>&nbsp;&nbsp;</td><td>&nbsp;&nbsp;<input type='reset' value= '  Cancelar  '></td></tr>";	
	echo "<tr><td COLSPAN='2'>&nbsp;</td></tr>";
	echo "<tr bgcolor='$cor3'><td align='center' colspan='2'>&nbsp;</td></tr>";
	echo "</form>";
	echo "</table>";
	echo "</td></tr>";
}
echo "</table>";
?>
