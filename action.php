<?php
	
	
	
	include_once "conexao.php";
	
	
	if(isset($_POST['opcao']) AND $_POST['opcao'] == "logar" ){
	
		$idt = $_POST['idt'];
		$senha = crypt($_POST['pass'], substr(md5($_POST['pass']), 0, 2));
		
		$select = "SELECT u.post_grad, u.nome_guerra, u.om_codom, o.sigla 
					FROM usuario as u, om as o 
					WHERE u.om_codom = o.codom 
					AND u.login = '$idt'
					AND u.senha = '$senha' 
					AND perfil= 'ABASTECEDOR' 
					AND situacao = 'PRONTO NA OM'";

		if( !mysql_query($select)){ echo mysql_error(); }
		else{ 
			
			$usuario= array();
			
			$queryUsuario = mysql_query($select);	
			
			while($linha = mysql_fetch_assoc($queryUsuario)){
			
			  array_push($usuario,array("grad"=>utf8_encode($linha['post_grad']),"sigla"=>utf8_encode($linha['sigla']), "om"=>utf8_encode($linha['om_codom']), "nome"=>utf8_encode($linha['nome_guerra'])));
			
			}
			
			array_push($usuario,array("identidade"=>$idt));
			
			echo json_encode($usuario);

		}
	}elseif(isset($_POST['opcao']) AND $_POST['opcao'] == "validar" ){
	
		$msg 		= array("codigo"=>"","msg"=>"","result"=>"");
					//Codigos//
					//0 -> OM n�o contem reservatorio cadastrado
					//1 -> N�o tem autoriza��o pra abstecer nesse Reservatorio
					//2 -> QrCode J� Utilizado
					//3 -> Autorizado Abastecimento
					
		$valida 	= 0;
		
		$omUsuario 	= $_POST['omUsuario'];
		$res = $_POST['res'];
		$eb=$_POST['eb'];
		$tempo=$_POST['tempo'];
		
	
		$selectRes 	= "SELECT r.codigo, r.om_codom , o.sigla
						FROM reservatorio as r, om as o
						WHERE r.om_codom='$omUsuario'
						AND r.om_codom=o.codom";
						
		$queryRes 	= mysql_query($selectRes);
		
		
		if(mysql_num_rows($queryRes) > 0){ //Primeira Verifica��o Se � do Posto
		
			while ($linha = mysql_fetch_assoc($queryRes)) {
				
				if( $linha['codigo'] == $res ){ $valida++; }
				
			}
			
			if($valida > 0){
			
				 $selectAbast = "SELECT * FROM abastecimento 
								WHERE viatura_eb='$eb' 
								AND dt_autorizacao='$tempo'
								AND qtd_abastecida is null";
		
				$queryAbast  = mysql_query($selectAbast);
				
				if( mysql_num_rows($queryAbast) > 0){
				
					$result = mysql_fetch_assoc($queryAbast);
				
					$msg['codigo']=3;
					$msg['msg']=utf8_encode("");
					$msg['result']=$result['od_atual'];
				
				}else{
				
					$msg['codigo']=2;
					$msg['msg']=utf8_encode("QrCode já utilizado para abastecimento.");
				
				}
				
			
			}else{
			
				$msg['codigo']=1;
				$msg['msg']=utf8_encode("Abastercimento não Autorizado ou Cancelado.");
			
			}
		
		
		}else{
		

			$msg['codigo']=0;
			$msg['msg']=utf8_encode("Sua Om não possui reservatório cadastrado.");
	
		
		}
		
		echo json_encode($msg);
		
	
	}elseif(isset($_POST['opcao']) AND $_POST['opcao'] == "cadastrar"){
		
		$qtd 	= $_POST['qtd'];
		$odo 	= $_POST['odo'];
		$eb  	= $_POST['eb'];
		$tempo  = $_POST['tempo'];
		$idt    = $_POST['idt'];
		$om		= $_POST['om'];
		$numCota = $_POST['cota'];
		$tpCota	= $_POST['tpCota'];
		$res	= $_POST['res'];
		
		$dt_abast = date('Y-m-d H:i:s');
		
			$updateAbast = "UPDATE abastecimento 
						SET  od_atual  	 = '".$odo."',
						qtd_abastecida 	 = '".$qtd."',
						dt_abastecimento = '".$dt_abast."',
						usuario_idt	     = '".$idt."'
						WHERE viatura_eb = '".$eb."'
						AND dt_autorizacao='".$tempo."'";

		if( !mysql_query($updateAbast) ){ 
		
			//Registra atividade executada pelo usu�rio para auditoria
			registrarAtividade('Tentou abastecer da viatura EB '.$eb.' do(a) '.$numCota.'. Codigo Segurança Inválido!',$idt);

			echo json_encode(0); 
			
		}
		else{
			

			//Atualiza a quantidade atual no estoque da OM
			
			$qry="SELECT nr_repasse, om_codom, qtd_atual, dt_repasse 
					FROM estoque 
					WHERE reservatorio_codigo='".$res."'
					and om_codom='".$numCota."'
					and cod_tp_cota='".$tpCota."' 
					and qtd_atual > 0
					order by dt_repasse";
				
			$result1 = mysql_query($qry);
			
			$num_lin = 0;
			
			if(mysql_num_rows($result1)>0) { /* Se existir cotas*/
			
				while(($qtd > 0) and (mysql_num_rows($result1)>=$num_lin)) 
				{ //Enquanto n�o for debitado das cotas todo o valor abastecido e o numermo de cotas for maior ou igual ao contador
				
					$cota = mysql_fetch_array($result1, MYSQL_NUM);
					

					if ($cota[2]>=$qtd){
					
						//Atualiza o estoque da OM
						
						$query2 = "UPDATE estoque 
									SET qtd_atual='".($cota[2]-$qtd)."' 
									WHERE nr_repasse='".$cota[0]."' 
									AND om_codom='".$numCota."'";
									
						$resultado = mysql_query($query2);
						
						//Registra historico abastecimento
						
						$query3 = "INSERT INTO historico_abastecimento 
									(
										nr_repasse, 
										om_codom, 
										dt_abastecimento, 
										viatura_eb, 
										qtd_abastecida
									)
									VALUES(
										'".$cota[0]."',
										'".$numCota."',
										'".$dt_abast."',
										'".$eb."',
										'".$qtd."'
									)";
									
						$resultado = mysql_query($query3);
						
						$qtd=0;	
						
					}else{
					
						//Atualiza o estoque da OM
						
						$query2 = "UPDATE estoque 
									SET qtd_atual='0' 
									WHERE nr_repasse='".$cota[0]."' 
									AND om_codom='".$numCota."' ";	
									
						$resultado = mysql_query($query2);
						
						//Registra historico abastecimento
						
						$query3 = "INSERT INTO historico_abastecimento
									(
										nr_repasse, 
										om_codom, 
										dt_abastecimento, 
										viatura_eb, 
										qtd_abastecida
									)
									VALUES('".$cota[0]."', 
											'".$numCota."',
											'".$dt_abast."',
											'".$eb."',
											'".$cota[2]."'
										   )";
										   
						$resultado = mysql_query($query3);
						
						$qtd = $qtd-$cota[2];				
					}
					
					$resultado = mysql_query($query2);
					
					$num_lin++;
				}
			}
			
			//Registra atividade executada pelo usuário para auditoria
			registrarAtividade("Abasteceu da viatura EB ".$eb." do(a) ".$numCota."",$idt);
				
			echo json_encode(1);
	
		}
	
	}

function registrarAtividade($atividade,$idt)
{	
	/*Registra atividade do usu�rio	*/
	
	$query = "insert into auditoria(dt_atividade,usuario_idt,atividade)
	values (now(),'".$idt."',upper('".$atividade."'))";
	
	$resultado = mysql_query($query);
	
}

?>