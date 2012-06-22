<?PHP
$db = mysql_connect( "seplag_vpr.mysql-teste.procergs.reders" , "t_seplag_vpr_si" , "vprs1!" );
mysql_select_db( "seplag_vpr" , $db );
$corede = "select id_regiao from regiao where id_regiao > 25 order by id_regiao";
$selCorede = mysql_query( $corede , $db );
while( $regCor = mysql_fetch_row( $selCorede ) ) {
	$id_corede = $regCor[0];
	echo "Corede: ".$id_corede."<br>";
	$municipio = "select cod_mun_tre from municipio where id_regiao = $id_corede order by nm_municipio";
	$selMun = mysql_query( $municipio , $db );
	while( $regMun = mysql_fetch_row( $selMun ) ) {
		$cod_mun_tre = $regMun[0];
		$munTre = "select nm_mun_tre from eleitor_tre where cod_mun_tre = $cod_mun_tre LIMIT 1";
		$selMunTre = mysql_query( $munTre , $db );
		if ($regMunTre = mysql_fetch_row( $selMunTre )) {
			//$nm_mun_tre = $regMunTre[0];
			$nm_mun_tre = ereg_replace("[^a-zA-Z0-9_.]", "", strtr($regMunTre[0], "·‡„‚ÈÍÌÛÙı˙¸Á¡¿√¬… Õ”‘’⁄‹« ",   "aaaaeeiooouucAAAAEEIOOOUUC_"));
		} else {
			$nm_mun_tre = $cod_mun_tre;
		}
		echo "Municipio: ".$cod_mun_tre." - ".$nm_mun_tre."<br>";
		$eleitor = "select nm_eleitor, nro_titulo 
				from eleitor_tre  
				where cod_mun_tre = $cod_mun_tre
				order by nm_eleitor";
//		echo $eleitor."<BR>";
		$selEleitor = mysql_query( $eleitor , $db );
		$dir = $_SERVER["DOCUMENT_ROOT"]."app/eleitores/";
		$nm_arq = $dir.$id_corede."_".$nm_mun_tre.".txt";
		$arquivo  = fopen( $nm_arq , "w+" );
		if (!$arquivo)
  		{
    		echo "<p>O arquivo ".$nm_arq." nao pode ser aberto</p>";
    		exit;
  		}
		while( $regEleitor = mysql_fetch_row( $selEleitor ) ) 
		{
    		$reg = $regEleitor[0].";".$regEleitor[1]. "\r\n";
			//echo $reg."<br>";
			//$limpar = fputs( $abrir , $reg . "\r\n" );
			fwrite($arquivo, $reg);
		}
		fclose($arquivo);
	}
}
?>