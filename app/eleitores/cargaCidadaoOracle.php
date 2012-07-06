<?PHP
if(is_int(isset($_POST['ultCidadao']))){
    $ult_cid = isset($_POST['ultCidadao']);
} else {
    $ult_cid = 0;
}
$db = mysql_connect( "seplag_vpr.mysql-teste.procergs.reders" , "t_seplag_vpr_si" , "vprs1!" );
mysql_select_db( "seplag_vpr" , $db );
$dir = $_SERVER["DOCUMENT_ROOT"]."app/eleitores/";
$nm_arq = $dir.$ult_cid."cidadaoonline2012prod.txt";
$arquivo  = fopen( $nm_arq , "w+" );
if (!$arquivo)
{
    echo "<p>O arquivo ".$nm_arq." nao pode ser aberto</p>";
    exit;
}
$votos = "select * from cidadao where id_cidadao > $ult_cid order by id_cidadao";
$selVotos = mysql_query( $votos , $db );
while( $regVotos = mysql_fetch_row( $selVotos ) ) {
	$id_cidadao = $regVotos[0];
	$ult_reg = $id_cidadao;
	$nro_titulo = $regVotos[1];
	$rg = $regVotos[2];
	$ds_email = $regVotos[3];
	$nro_telefone = $regVotos[4];
	$eleitor = "SELECT cod_mun_tre, nm_eleitor FROM eleitor_tre WHERE nro_titulo = $nro_titulo";
	$selEle = mysql_query( $eleitor , $db );
	if( $regEle = mysql_fetch_row( $selEle ) ) {
		$cod_mun_tre = $regEle[0];
		$nm_eleitor = $regEle[1];
	} else {
    	echo "<p>Eleitor ".$nro_titulo." nao foi encontrado</p>";
    	exit;
  	}
	$munic = "select cod_mun_pop, id_regiao from municipio where cod_mun_tre = $cod_mun_tre";
	$selMunic = mysql_query( $munic , $db );
	if ($regMunic = mysql_fetch_row( $selMunic )) {
		$cod_mun_pop = $regMunic[0];
		$id_regiao = $regMunic[1];
	} else {
    	echo "<p>O Município ".$id_municipio." nao foi encontrado</p>";
    	exit;
  	}
    $reg = "".$nro_titulo.";".$rg.";".$cod_mun_pop.";'".$nm_eleitor."';'".$ds_email."';';NULL;'RS';2012;0\r\n";
	//$reg = "INSERT INTO POP_CIDADAO (NRO_TITULO_ELEITOR, NRO_RG, COD_MUNICIPIO,TXT_NOME_CIDADAO, TXT_EMAIL_CIDADAO, CTR_DTH_INC, CTR_IP_INC, TXT_UF_RG, NRO_ANO_CORRENTE, IND_PESQUISA) "
	//				 . " VALUES (".$nro_titulo.", ".$rg.", ".$cod_mun_pop.", '".$nm_eleitor."', '".$ds_email."', sysdate, NULL, 'RS', 2012, 0);";
	//$reg .= "". "\r\n";
			//$limpar = fputs( $abrir , $reg . "\r\n" );
	echo $reg."<br />";
	fwrite($arquivo, $reg);
}
fwrite($arquivo, $ult_reg);
fclose($arquivo);
//desenvolvimento
echo "//////<br />/////<br />";
$dir = $_SERVER["DOCUMENT_ROOT"]."app/eleitores/";
$nm_arq = $dir.$ult_cid."cidadaoonline2012desenv.txt";
$arquivo  = fopen( $nm_arq , "w+" );
if (!$arquivo)
{
    echo "<p>O arquivo ".$nm_arq." nao pode ser aberto</p>";
    exit;
}
$votos = "select * from cidadao where id_cidadao > $ult_cid order by id_cidadao";
$selVotos = mysql_query( $votos , $db );
while( $regVotos = mysql_fetch_row( $selVotos ) ) {
	$id_cidadao = $regVotos[0];
	$ult_reg = $id_cidadao;
	$nro_titulo = $regVotos[1];
	$rg = $regVotos[2];
	$ds_email = $regVotos[3];
	$nro_telefone = $regVotos[4];
	$eleitor = "SELECT cod_mun_tre, nm_eleitor FROM eleitor_tre WHERE nro_titulo = $nro_titulo";
	$selEle = mysql_query( $eleitor , $db );
	if( $regEle = mysql_fetch_row( $selEle ) ) {
		$cod_mun_tre = $regEle[0];
		$nm_eleitor = $regEle[1];
	} else {
    	echo "<p>Eleitor ".$nro_titulo." nao foi encontrado</p>";
    	exit;
  	}
	$munic = "select cod_mun_pop, id_regiao from municipio where cod_mun_tre = $cod_mun_tre";
	$selMunic = mysql_query( $munic , $db );
	if ($regMunic = mysql_fetch_row( $selMunic )) {
		$cod_mun_pop = $regMunic[0];
		$id_regiao = $regMunic[1];
	} else {
    	echo "<p>O Município ".$id_municipio." nao foi encontrado</p>";
    	exit;
  	}
    $reg = "'".$nm_eleitor."';".$nro_titulo.";".$rg.";".$cod_mun_pop.";2012;'".$ds_email."';'RS';sysdate;NULL;0\r\n";
	//$reg = "INSERT INTO POP_CIDADAO (TXT_NOME_CIDADAO, NRO_TITULO_ELEITOR, NRO_RG, COD_MUNICIPIO, NRO_ANO_CORRENTE, TXT_EMAIL_CIDADAO, TXT_UF_RG, CTR_DTH_INC, CTR_IP_INC, IND_PESQUISA) "
	//				 . " VALUES ('".$nm_eleitor."', ".$nro_titulo.", ".$rg.", ".$cod_mun_pop.", 2012, '".$ds_email."', 'RS', sysdate, NULL, 0)";
	//$reg .= ";". "\r\n";
			//$limpar = fputs( $abrir , $reg . "\r\n" );
	echo $reg."<br />";
	fwrite($arquivo, $reg);
}
fwrite($arquivo, $ult_reg);
fclose($arquivo);

?>
