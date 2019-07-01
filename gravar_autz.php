<?php
	include "verifica_sessao.php";

	/*Conexão com arquivo para registrar atividade*/
	include "atividade.php";

	/*Codigo de Segurança*/
	$vtr = $_POST['eb'];
	$data = date("d/m/Y");
	$hora = date("h:i:s");
	$abast = $vtr.$data.$hora;
	$cod_seg = substr(md5($abast),0,5);
	$dt = date("Y-m-d h:i:s");

	$reserv = explode(",", $_POST['reservatorio']);
	
	if($_POST['qtd_autz']>substr($_POST['qtd_max'],0,-4)) {
		$_POST['qtd_autz']=substr($_POST['qtd_max'],0,-4);
	}
	
	if($_POST['odometro']=='') {
		$_POST['odometro']='null';
	} else {
		$_POST['odometro']="'".$_POST['odometro']."'";
	}

	/*Testa a operação se operacao for igual a 1 é insersão se não é alteração*/
	if ($_POST['operacao']==1) {
		$query = "insert into abastecimento (dt_abastecimento, viatura_eb, reservatorio_codigo, cod_tp_cota, usuario_autz, dt_autorizacao, qtd_autorizada, motorista, 
							cod_seguranca, missao, od_atual, cota_om, tp_abastecimento) 
			values(now(), '".$_POST['eb']."', '".$reserv[0]."', '".$reserv[1]."', '".$_SESSION['idt']. "',now(), '".$_POST['qtd_comb']."', upper('".$_POST['motorista']."'),
						upper('".$cod_seg."'), '".$_POST['missao']."', ".$_POST['odometro'].", '".$_SESSION['codom']."', '".$_POST['tp_abastecimento']."')";

		$rc = mysql_query($query);
		if($rc)	{
			/*Registra atividade executada pelo usuário para auditoria*/
			if($_POST['tp_abastecimento']=='Carregamento de Cisterna') {
				registrarAtividade('Autorizou o carregamento de '.$_POST['qtd_comb'].' litros na cisterna EB '.$_POST['eb']);
			} elseif($_POST['tp_abastecimento']=='Abastecimento Vtr Outra OM') {
				registrarAtividade('Autorizou o abastecimento de '.$_POST['qtd_comb'].' litros na viatura EB '.$_POST['eb']);
			}	else {
				registrarAtividade('Autorizou o abastecimento de '.$_POST['qtd_comb'].' litros na viatura EB '.$_POST['eb'].' do(a) '.$_SESSION['unidade']);
			}
			
			echo "<br><br><font color='red' size='5'>ATENÇÃO !</font><br>";
			echo "<br>Autorização cadastrada com sucesso.";
			echo "<p>Para a liberação do abastecimento é necessário o código de segurança. <p>Este código deverá ser entregue ao motorista da viatura para conferência durante do abastecimento.<br><br>";
			echo "<table border='1' width='35%'>";
			echo "<tr bgcolor='$cor2'><td width='50%' colspan='2' align='center'><i><b>".strtoupper($_POST['tp_abastecimento'])."</td></tr>";
			echo "<tr><td width='50%'>Cota de Combustível do(a): </td><td width='50%'><b><i>&nbsp;&nbsp;".$_POST['sigla']."</td></tr>";
			echo "<tr><td >Autorizado por: </td><td><b><i>&nbsp;&nbsp;".substr($_SESSION['post_grad'],1)." ".$_SESSION['nome_guerra']."</td></tr>";
			echo "<tr><td>Data Autorização: </td><td><b><i>&nbsp;&nbsp;".$data."</td></tr>";
			echo "<tr><td >OM da Vtr: </td><td><b><i>&nbsp;&nbsp;".$_POST['om_vtr']."</td></tr>";
			echo "<tr><td >EB da Viatura: </td><td><b><i>&nbsp;&nbsp;".$_POST['eb']."</td></tr>";
			if($_POST['odometro']!='') {
				echo "<tr><td>Odometro: </td><td><b><i>&nbsp;&nbsp;".substr($_POST['odometro'],1,-1)."</td></tr>";
			}
			echo "<tr><td>Motorista: </td><td><b><i>&nbsp;&nbsp;".strtoupper($_POST['motorista'])."</td></tr>";
			echo "<tr><td>Reservatório: </td><td><b><i>&nbsp;&nbsp;Nº ".$reserv[0]." - ".$reserv[3]." - ".$reserv[2]."</td></tr>";
			echo "<tr><td>Quantidade: </td><td><b><i>&nbsp;&nbsp;".$_POST['qtd_comb']." Lts</td></tr>";
			echo "<tr><td>Código de Segurança: </td><td><i><font color='red'> &nbsp;&nbsp;".strtoupper($cod_seg)."</td></tr>";
			echo "</table>";
		}
		else{
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('Tentou autorizar o abastecimento da viatura '.$_POST['eb'].' do(a) '.$_SESSION['unidade']);
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Erro durante o processamento do cadastro! ");
		}
	}


echo "<p>";
echo "<a href='./qrcode/impr_autz_abast.php?om=".$_POST['om_vtr']."&eb=".$_POST['eb']."&dt=".$dt."&res=".$reserv[0]."&cota=".$_SESSION['codom']."&tp_cota=".$reserv[1]."
&motorista=".strtoupper($_POST['motorista'])."&data=$data&cod=".strtoupper($cod_seg)."&qtd=".$_POST['qtd_comb']."&posto=".$reserv[0]." - ".$reserv[3]." - ".$reserv[2]."
&odometro=".substr($_POST['odometro'],1,-1)."&tp_abastecimento=".$_POST['tp_abastecimento']."&usuario=".substr($_SESSION['post_grad'],1)." ".$_SESSION['nome_guerra']."
&sigla=".$_SESSION['unidade']."' title='Imprimir Autorização' target='_blank'>
<b>IMPRIMIR</a>&nbsp;&nbsp;&nbsp;&nbsp;";

if($_POST['tp_abastecimento']=='Carregamento de Cisterna') {
	echo "<a href='index.php?nomeArquivo=autz_carr_cisterna.php'>RETORNAR</b></a></center>";
} elseif($_POST['tp_abastecimento']=='Abastecimento Vtr Outra OM') {
	echo "<a href='index.php?nomeArquivo=abast_vtr_outra_om.php'>RETORNAR</b></a></center>";
} else {
	echo "<a href='index.php?nomeArquivo=autz_abast.php'>RETORNAR</b></a></center>";
}

?>
