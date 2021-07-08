<?php 
include_once 'email_clasificados.php';


try {
    
    $conexion = new PDO('mysql:host=localhost;dbname=balneario-el-condor', 'balneari', 'dbm348J');
    
} catch (PDOException $e) {
    print "¡Error!: " . $e->getMessage() . "<br/>";
    die();
}



function getClasificados(){

	global $conexion;

	//Obtengo el mes actual
	$date = strtotime("now");
	$mes =  date("m", $date);

	//Creo la nueva fecha para buscar en la tabla
	$time = strtotime("2014-".$mes."-30");
	$fecha_hasta = date('Y-m-d 23:59:59',$time);

	//obtengo los 3 meses anteriores
	$anterior_meses = $mes - 3;
	$mes_sin_formato = strtotime("2014-".$anterior_meses."-30");
	$fecha_desde = date('Y-m-d 23:59:59', $mes_sin_formato);



	//Obtengo los clasificados de los ultimos 3 meses
	$sql = "SELECT * FROM clasificados WHERE cla_fechahora BETWEEN '".$fecha_desde."' AND '".$fecha_hasta."'";
	$rt = $conexion->prepare($sql);
	$rt->execute();
	$resultado = $rt->fetchAll();

	return $resultado;
}


function enviarEmail($datos_clasificados){

	global $email_cla;

	foreach ($datos_clasificados as $clave => $valor) {

			//Fecha en formato d-m-y
			$fecha = strtotime($valor['cla_fechahora']);
			$dia = date("d",$fecha);
			$mes = date("m",$fecha);
			$anio = date("Y",$fecha);
			$fecha_formato = strtotime($dia."-".$mes."-".$anio);
			$fecha_formateada = date("d-m-Y", $fecha_formato);	
			

			//link para clasificados
			$link_si = "http://www.balneario-el-condor.com.ar/prueba/update/id/".$valor['cla_id']."/vigente/1";	
			$link_no = "http://www.balneario-el-condor.com.ar/prueba/update/id/".$valor['cla_id']."/vigente/0";

			$email_clasificados = str_replace("<<NOMBREUSUARIO>>", $valor['cla_nombre_contacto'], $email_cla);
			$email_clasificados = str_replace("<<CLANOMBRE>>", $valor['cla_titulo'], $email_clasificados);
			$email_clasificados = str_replace("<<FECHACLASIFICADO>>", $fecha_formateada, $email_clasificados);
			$email_clasificados = str_replace("<<LINKSI>>", $link_si, $email_clasificados);
			$email_clasificados = str_replace("<<LINKNO>>", $link_no, $email_clasificados);
			


			//Envio el email
			// Para enviar un correo HTML mail, la cabecera Content-type debe fijarse
			$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
			$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$cabeceras .= 'From: Balneario el condor <contacto@balneario-el-condor.com>' . "\r\n";

			mail('cm.guille1@gmail.com', 'Lo contactamos por su clasificado', $email_clasificados,$cabeceras);

	}
	


}

//Obtengo los clasificados
$datos_clasificados = getClasificados();

//mando los email a los usuarios
enviarEmail($datos_clasificados);


?>