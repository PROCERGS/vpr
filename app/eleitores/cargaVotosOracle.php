<?PHP
if(is_int(isset($_POST['ultVoto']))){
    $ult_voto = isset($_POST['ultVoto']);
} else {
    $ult_voto = 0;
}
$db = mysql_connect( "seplag_vpr.mysql-teste.procergs.reders" , "t_seplag_vpr_si" , "vprs1!" );
mysql_select_db( "seplag_vpr" , $db );
$dir = $_SERVER["DOCUMENT_ROOT"]."app/eleitores/";
$nm_arq = $dir.$ult_voto."votosonline2012prod.txt";
$arquivo  = fopen( $nm_arq , "w+" );
if (!$arquivo)
{
    echo "<p>O arquivo ".$nm_arq." nao pode ser aberto</p>";
    exit;
}
$votos = "select * from voto where id_voto > $ult_voto order by id_voto";
$selVotos = mysql_query( $votos , $db );
while( $regVotos = mysql_fetch_row( $selVotos ) ) {
	$id_voto = $regVotos[0];
	$ult_reg = $id_voto;
	$id_cedula = $regVotos[1];
	$id_municipio = $regVotos[2];
	$id_meio_votacao = $regVotos[3];
	$dth_voto = $regVotos[4];
	$nro_ip_inc = $regVotos[5];
//	echo "Cedula: ".$id_cedula." - Municipio: ".$id_municipio."<br>";
	$cedula = "SELECT cod_projeto FROM cedula WHERE id_cedula = $id_cedula";
	$selCel = mysql_query( $cedula , $db );
	if( $regCel = mysql_fetch_row( $selCel ) ) {
		$cod_projeto = $regCel[0];
	} else {
    	echo "<p>A cedula ".$id_cedula." nao foi encontrada</p>";
    	exit;
  	}
	$munic = "select cod_mun_pop, id_regiao from municipio where id_municipio = $id_municipio";
	$selMunic = mysql_query( $munic , $db );
	if ($regMunic = mysql_fetch_row( $selMunic )) {
		$cod_mun_pop = $regMunic[0];
		$id_regiao = $regMunic[1];
	} else {
    	echo "<p>O Município ".$id_municipio." nao foi encontrado</p>";
    	exit;
  	}
    $reg = "INSERT INTO POP_VOTO_WEB "
					 . " select pop_seq_cod_voto_web.nextval, cod_desc_cedula, to_date('".$dth_voto."', 'yyyy-mm-dd hh24:mi:ss'), 0, ".$cod_mun_pop.", 2012 from POP_DESCRICAO_CEDULA t where nro_ano_corrente = 2012 and cod_corede = ".$id_regiao." and nro_ordem = ".$cod_projeto;
	$reg .= "". "\r\n";
			//$limpar = fputs( $abrir , $reg . "\r\n" );
	echo $reg;
	fwrite($arquivo, $reg);
}
fwrite($arquivo, $ult_reg);
fclose($arquivo);
//desenvolvimento
echo "//////<br />/////<br />";
$dir = $_SERVER["DOCUMENT_ROOT"]."app/eleitores/";
$nm_arq = $dir.$ult_voto."votosonline2012desenv.txt";
$arquivo  = fopen( $nm_arq , "w+" );
if (!$arquivo)
{
    echo "<p>O arquivo ".$nm_arq." nao pode ser aberto</p>";
    exit;
}
$votos = "select * from voto where id_voto > $ult_voto order by id_voto";
$selVotos = mysql_query( $votos , $db );
while( $regVotos = mysql_fetch_row( $selVotos ) ) {
	$id_voto = $regVotos[0];
	$ult_reg = $id_voto;
	$id_cedula = $regVotos[1];
	$id_municipio = $regVotos[2];
	$id_meio_votacao = $regVotos[3];
	$dth_voto = $regVotos[4];
	$nro_ip_inc = $regVotos[5];
//	echo "Cedula: ".$id_cedula." - Municipio: ".$id_municipio."<br>";
	$cedula = "SELECT cod_projeto FROM cedula WHERE id_cedula = $id_cedula";
	$selCel = mysql_query( $cedula , $db );
	if( $regCel = mysql_fetch_row( $selCel ) ) {
		$cod_projeto = $regCel[0];
	} else {
    	echo "<p>A cedula ".$id_cedula." nao foi encontrada</p>";
    	exit;
  	}
	$munic = "select cod_mun_pop, id_regiao from municipio where id_municipio = $id_municipio";
	$selMunic = mysql_query( $munic , $db );
	if ($regMunic = mysql_fetch_row( $selMunic )) {
		$cod_mun_pop = $regMunic[0];
		$id_regiao = $regMunic[1];
	} else {
    	echo "<p>O Município ".$id_municipio." nao foi encontrado</p>";
    	exit;
  	}
    $reg = "INSERT INTO POP_VOTO_WEB "
				 . " select pop_seq_cod_voto_web.nextval, 2012, cod_desc_cedula, to_date('".$dth_voto."', 'yyyy-mm-dd hh24:mi:ss'), 0, $cod_mun_pop from POP_DESCRICAO_CEDULA t where nro_ano_corrente = 2012 and cod_corede = ".$id_regiao." and nro_ordem = ".$cod_projeto;
	$reg .= ";". "\r\n";
			//$limpar = fputs( $abrir , $reg . "\r\n" );
	echo $reg;
	fwrite($arquivo, $reg);
}
fwrite($arquivo, $ult_reg);
fclose($arquivo);

?>
