<?php
$cma='Comando Militar do Sudeste';
$rm='2';
$oc_geral='11ª Brigada de Infantaria Leve';
$sigla_oc_geral='11ª Bda Inf L';
$cod_oc_geral='024794';
$logo_oc='logo.jpg';
$w_logo=800;
$h_logo=200;
$versao='6.0';
$abast_sem_saldo='s';
$controle_credito='s';
$abast_c_saldo_neg='s';
$cor_cabecalho = '#496404';
$cor_fundo_menu = '#0b752d';
$cor_fundo = '#6bd58c';
$cor_menu = '#ffff00';
$cor_submenu = '#CDCD00';
$link = '#79791f';
$cor1 ='#496404';
$cor2 = '#0b752d';
$cor3 ='#5fc47f';
$destaque ='#00FF00';



/*Especificação de Menus*/
		
if (($_SESSION['perfil']=='ADMINISTRADOR *') OR ($_SESSION['perfil']=='ADMINISTRADOR')){
	$menu_cadastro=array('OM'=>'cons_om','Reservatório'=>'cons_reservatorio','Usuário'=>'cons_usuario','Viatura'=>'cons_viatura','Senha'=>'alterar_senha');
	if($_SESSION['codom']==$_SESSION['oc']) {
		if($controle_credito=='s') {
			$menu_combustiveis=array('Créditos'=>'cons_credito','Repasses'=>'cons_recebimento','Disponibilidade'=>'cons_disponibilidade','Tancagem'=>'tancagem','Atendimentos'=>'rel_atendimento');
		}else {
			$menu_combustiveis=array('Repasses'=>'cons_recebimento','Disponibilidade'=>'cons_disponibilidade','Tancagem'=>'tancagem','Atendimentos'=>'rel_atendimento');
		}
	} else {
		$menu_combustiveis=array('Disponibilidade'=>'cons_disponibilidade','Tancagem'=>'tancagem','Atendimentos'=>'rel_atendimento');
	} 
	$menu_abastecimento=array('Autorizados'=>'rel_autz','Autorização'=>'autz_abast','Carregamento Cisterna'=>'autz_carr_cisterna','Retorno Cisterna'=>'retorno_cisterna','Viatura Outra OM'=>'abast_vtr_outra_om');
	$menu_consultas=array('Abastecimentos'=>'rel_abast','Repasses'=>'rel_recebimento','Consumo Médio'=>'cons_consumo');
	$menu_graficos=array('Abastecimentos'=>'grafico_abastecimentos','Repasses'=>'grafico_repasse','Disponibilidade'=>'grafico_disponibilidade','Armazenamento'=>'grafico_tancagem');
	$menu_adm=array('Auditoria de Usu&aacute;rio'=>'auditoria','Auditoria Nota Fiscal'=>'aud_nf','Evaporação'=>'cons_evaporacao','Sangria'=>'cons_sangria');
} elseif (($_SESSION['perfil']=='CONSULTOR') OR ($_SESSION['perfil']=='CONSULTOR *')){
	$menu_cadastro=array('OM'=>'cons_om','Reservatório'=>'cons_reservatorio','Usuário'=>'cons_usuario','Viatura'=>'cons_viatura','Senha'=>'alterar_senha');
	$menu_combustiveis=array('Disponibilidade'=>'cons_disponibilidade','Tancagem'=>'tancagem','Atendimentos'=>'rel_atendimento');
	$menu_abastecimento=array('Abastecimentos'=>'rel_abast');
	$menu_consultas=array('Abastecimentos'=>'rel_abast','Repasses'=>'rel_recebimento','Consumo Médio'=>'cons_consumo');
	$menu_graficos=array('Abastecimentos'=>'grafico_abastecimentos','Repasses'=>'grafico_repasse','Disponibilidade'=>'grafico_disponibilidade','Armazenamento'=>'grafico_tancagem');
	$menu_adm=array('Auditoria de Usu&aacute;rio'=>'auditoria','Auditoria Nota Fiscal'=>'aud_nf','Evaporação'=>'cons_evaporacao','Sangria'=>'cons_sangria');
} elseif (($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='CMT DA OM')) {
	$menu_cadastro=array('OM'=>'cons_om','Usuário'=>'cons_usuario','Viatura'=>'cons_viatura','Senha'=>'alterar_senha');
	if($_SESSION['gerente']=='SIM') {
		if($controle_credito=='s') {
			$menu_combustiveis=array('Créditos'=>'cons_credito','Recebimento'=>'cons_recebimento','Disponibilidade'=>'cons_disponibilidade','Tancagem'=>'tancagem','Atendimentos'=>'rel_atendimento');
		} else {
			$menu_combustiveis=array('Recebimento'=>'cons_recebimento','Disponibilidade'=>'cons_disponibilidade','Tancagem'=>'tancagem','Atendimentos'=>'rel_atendimento');
		}
		$menu_abastecimento=array('Autorizados'=>'rel_autz','Autorização'=>'autz_abast','Carregamento Cisterna'=>'autz_carr_cisterna','Retorno Cisterna'=>'retorno_cisterna','Viatura Outra OM'=>'abast_vtr_outra_om','Fora do Sistema'=>'abast_fora_sistema');
		$menu_consultas=array('Abastecimentos'=>'rel_abast','Repasses'=>'rel_recebimento','Consumo Médio'=>'cons_consumo');
		$menu_graficos=array('Abastecimentos'=>'grafico_abastecimentos','Repasses'=>'grafico_repasse','Disponibilidade'=>'grafico_disponibilidade','Armazenamento'=>'grafico_tancagem');
	} else {
		if($controle_credito=='s') {
			$menu_combustiveis=array('Créditos'=>'cons_credito','Disponibilidade'=>'cons_disponibilidade');
		} else {
			$menu_combustiveis=array('Disponibilidade'=>'cons_disponibilidade');
		}
		$menu_abastecimento=array('Autorizados'=>'rel_autz','Autorização'=>'autz_abast','Carregamento Cisterna'=>'autz_carr_cisterna','Retorno Cisterna'=>'retorno_cisterna','Viatura Outra OM'=>'abast_vtr_outra_om','Fora do Sistema'=>'abast_fora_sistema');
		$menu_consultas=array('Abastecimentos'=>'rel_abast','Repasses'=>'rel_recebimento','Consumo Médio'=>'cons_consumo');
		$menu_graficos=array('Abastecimentos'=>'grafico_abastecimentos','Repasses'=>'grafico_repasse','Disponibilidade'=>'grafico_disponibilidade');
	}
	$menu_adm=array('Auditoria de Usu&aacute;rio'=>'auditoria','Auditoria Nota Fiscal'=>'aud_nf','Evaporação'=>'cons_evaporacao','Sangria'=>'cons_sangria');
} elseif ($_SESSION['perfil']=='ABASTECEDOR') {
	$menu_cadastro=array('OM'=>'cons_om','Viatura'=>'cons_viatura','Senha'=>'alterar_senha');
	$menu_combustiveis=array('Disponibilidade'=>'cons_disponibilidade','Tancagem'=>'tancagem','Atendimentos'=>'rel_atendimento');
	$menu_abastecimento=array('Autorizados'=>'rel_autz','Registro no Posto'=>'abastecimento','Autorização Escrita'=>'abast_vale');
	$menu_consultas=array('Abastecimentos'=>'rel_abast','Repasses'=>'rel_recebimento');
	$menu_graficos=array('Abastecimentos'=>'grafico_abastecimentos','Repasses'=>'grafico_repasse','Disponibilidade'=>'grafico_disponibilidade','Armazenamento'=>'grafico_tancagem');
	$menu_adm=array('Evaporação'=>'cons_evaporacao','Sangria'=>'cons_sangria');
}
?>




