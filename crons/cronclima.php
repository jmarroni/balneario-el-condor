<?php
// require_once 'conection.php';
error_reporting(E_ALL);
$dbUser= "balneari";
$dbPass = "dbm348J";
$dbHost = "localhost";
$dbName = "balneario-el-condor";
$conexion = mysql_connect($dbHost,$dbUser,$dbPass) or die("no me puedo conectar:".mysql_error());
$db_selected = mysql_select_db($dbName);


    $BASE_URL = "http://query.yahooapis.com/v1/public/yql";
    $yql_query = 'select wind from weather.forecast where woeid in (select woeid from geo.places(1) where text="chicago, il")';
    $yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&format=json";
    // Make call with cURL
    $session = curl_init($yql_query_url);
    curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
    $json = curl_exec($session);
    // Convert JSON to PHP object
     $phpObj =  json_decode($json);
    var_dump($phpObj);
exit();

class YahooWeatherAPI {
    private $city_code = '';
    private $domain = 'weather.yahooapis.com/';
    private $current_conditions = array();
    private $forecast_conditions = array();
    private $is_found = true;
     private $mensajes = array(
                        0 => 'Tornado',
                        1 => 'Tormenta Tropical',
                        2 => 'Hurac&aacute;n',
                        3 => 'Tormentas El&eacute;ctricas Severas',
                        4 => 'Tormentas El&eacute;ctricas',
                        5 => 'Lluvia y Nieve',
                        6 => 'Lluvia y Aguanieve',
                        7 => 'Nieve y Aguanieve',
                        8 => 'Llovizna congelada',
                        9 => 'Llovizna',
                        10 => 'Lluvia congelada',
                        11 => 'Lluvia',
                        12 => 'Lluvia',
                        13 => 'R&aacute;fagas de Nieve',
                        14 => 'Nevada ligera',
                        15 => 'Nieve con viento',
                        16 => 'Nieve',
                        17 => 'Granizo',
                        18 => 'Aguanieve',
                        19 => 'Polvo',
                        20 => 'Neblina',
                        21 => 'Niebla ligera',
                        22 => 'Neblina', //no creo que sea exacta esta traduccion
                        23 => 'Vendaval',
                        24 => 'Con viento',
                        25 => 'Helado',
                        26 => 'Nublado',
                        27 => 'Muy nublado',
                        28 => 'Muy nublado',
                        29 => 'Parcialmente nublado',
                        30 => 'Parcialmente nublado',
                        31 => 'Despejado',
                        32 => 'Soleado',
                        33 => 'Despejado',
                        34 => 'Despejado',
                        35 => 'Lluvia y Granizo',
                        36 => 'Caluroso',
                        37 => 'Tormentas el&eacute;ctricas aisladas',
                        38 => 'Tormentas el&eacute;ctricas dispersas',
                        39 => 'Tormentas el&eacute;ctricas dispersas',
                        40 => 'Lluvia dispersa',
                        41 => 'Nieve densa',
                        42 => 'Nieve y lluvia dispersas',
                        43 => 'Nieve densa',
                        44 => 'Parcialmente nublado',
                        45 => 'Tormentas el&eacute;ctricas',
                        46 => 'Nieve',
                        47 => 'Tormentas el&eacute;ctricas aisladas',
                        3200 => 'No disponible',
                    );

    /**
    * Class constructor
    * @param $city_code is the label of the city
    * @param $lang the lang of the return weather labels
    * @return ...
    */

    function __construct ($city_code) {
        $this->city_code = $city_code;
        $this->url = 'http://'.$this->domain.'forecastrss?w=' . $this->city_code .'&u=c&d=10';

        $namespace_yweather = 'http://xml.weather.yahoo.com/ns/rss/1.0';

        $xml_string = file_get_contents($this->url);
        $xml = new DOMDocument();
        $xml->loadXML($xml_string);
        /*echo '<pre>';print_r($list = explode("<br />",nl2br($xml->textContent)));exit();*/
        $clima = $xml->getElementsByTagNameNS($namespace_yweather, 'forecast');
        
        $array_clima = $clima->item(0);
        $array_clima1 = $clima->item(1);
        $array_clima2 = $clima->item(2);
        $array_clima3 = $clima->item(3);
        $array_clima4 = $clima->item(4);
        $array_clima5 = $clima->item(5);
        $array_clima6 = $clima->item(6);
        $array_clima7 = $clima->item(7);
        $array_clima8 = $clima->item(8);
        $array_clima9 = $clima->item(9);
        
       $extendido[0]= array('codigo' => $array_clima->getAttribute('code'),
                            'temp_max' => $array_clima->getAttribute('high'),
                            'temp_min' => $array_clima->getAttribute('low'),
                            'descripcion' => $this->mensajes[$array_clima->getAttribute('code')],
                            'icono' => "/css/images/iconos_clima/" . $array_clima->getAttribute('code').".png" );
        $extendido[1]= array('codigo' => $array_clima1->getAttribute('code'),
                            'temp_max' => $array_clima1->getAttribute('high'),
                            'temp_min' => $array_clima1->getAttribute('low'),
                            'descripcion' => $this->mensajes[$array_clima1->getAttribute('code')],
                            'icono' => "/css/images/iconos_clima/" . $array_clima1->getAttribute('code').".png" );
        $extendido[2]= array('codigo' => $array_clima2->getAttribute('code'),
                            'temp_max' => $array_clima2->getAttribute('high'),
                            'temp_min' => $array_clima2->getAttribute('low'),
                            'descripcion' => $this->mensajes[$array_clima2->getAttribute('code')],
                            'icono' => "/css/images/iconos_clima/" . $array_clima2->getAttribute('code').".png" );
        $extendido[3]= array('codigo' => $array_clima3->getAttribute('code'),
                            'temp_max' => $array_clima3->getAttribute('high'),
                            'temp_min' => $array_clima3->getAttribute('low'),
                            'descripcion' => $this->mensajes[$array_clima3->getAttribute('code')],
                            'icono' => "/css/images/iconos_clima/" . $array_clima3->getAttribute('code').".png" );
        $extendido[4]= array('codigo' => $array_clima4->getAttribute('code'),
                            'temp_max' => $array_clima4->getAttribute('high'),
                            'temp_min' => $array_clima4->getAttribute('low'),
                            'descripcion' => $this->mensajes[$array_clima4->getAttribute('code')],
                            'icono' => "/css/images/iconos_clima/" . $array_clima4->getAttribute('code').".png" );
        $extendido[5]= array('codigo' => $array_clima5->getAttribute('code'),
                            'temp_max' => $array_clima5->getAttribute('high'),
                            'temp_min' => $array_clima5->getAttribute('low'),
                            'descripcion' => $this->mensajes[$array_clima5->getAttribute('code')],
                            'icono' => "/css/images/iconos_clima/" . $array_clima5->getAttribute('code').".png" );
        $extendido[6]= array('codigo' => $array_clima6->getAttribute('code'),
                            'temp_max' => $array_clima6->getAttribute('high'),
                            'temp_min' => $array_clima6->getAttribute('low'),
                            'descripcion' => $this->mensajes[$array_clima6->getAttribute('code')],
                            'icono' => "/css/images/iconos_clima/" . $array_clima6->getAttribute('code').".png" );
        $extendido[7]= array('codigo' => $array_clima7->getAttribute('code'),
                            'temp_max' => $array_clima7->getAttribute('high'),
                            'temp_min' => $array_clima7->getAttribute('low'),
                            'descripcion' => $this->mensajes[$array_clima7->getAttribute('code')],
                            'icono' => "/css/images/iconos_clima/" . $array_clima7->getAttribute('code').".png" );
        $extendido[8]= array('codigo' => $array_clima8->getAttribute('code'),
                            'temp_max' => $array_clima8->getAttribute('high'),
                            'temp_min' => $array_clima8->getAttribute('low'),
                            'descripcion' => $this->mensajes[$array_clima8->getAttribute('code')],
                            'icono' => "/css/images/iconos_clima/" . $array_clima8->getAttribute('code').".png" );
        $extendido[9]= array('codigo' => $array_clima9->getAttribute('code'),
                            'temp_max' => $array_clima9->getAttribute('high'),
                            'temp_min' => $array_clima9->getAttribute('low'),
                            'descripcion' => $this->mensajes[$array_clima9->getAttribute('code')],
                            'icono' => "/css/images/iconos_clima/" . $array_clima9->getAttribute('code').".png" );

            /*$resultado[0]['codigo'] = $array_clima->getAttribute('code'); 
            $this->current_conditions['condition'] = $this->mensajes[$resultado[0]['codigo']];
            $this->current_conditions['temp_c'] = $array_clima->getAttribute('temp');
            $this->current_conditions['icon'] =  "/css/images/iconos_clima/" . $resultado[0]['codigo'] . ".png";

           

        $clima = $xml->getElementsByTagNameNS($namespace_yweather, 'forecast');
        /*$array_clima = $clima->item(0);*/
/*
            $this->forecast_conditions['high'] = $array_clima->getAttribute('high');
            $this->forecast_conditions['low'] = $array_clima->getAttribute('low');

        $clima = $xml->getElementsByTagNameNS($namespace_yweather, 'wind');
        /*$array_clima = $clima->item(0);

        
            $this->forecast_conditions['wind'] = $array_clima->getAttribute('speed');*/
        $this->ce = $extendido;
 /*       return $extendido;*/
/*print_r($extendido[0]);*/

    }
    function getCity() {
        return $this->city;
    }
    function getCurrent() {
        return $this->ce;
    }
    function getForecast() {
        return $this->ce;
    }

    function grabarWeather(){
        $contenido=$this->getCurrent();
        /*$pronostico=$this->getForecast();*/
        /*print_r($contenido);*/
        $dia=array(
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes');

        /*if(empty($contenido['condition'])){
            $condicion=$pronostico['0']['condition'];
            $icono=$pronostico['0']['icon'];
        }else{
            $condicion=$contenido['condition'];
            $icono=$contenido['icon'];
        }
*/
        /*$alta= mysql_query("IF EXISTS(SELECT id FROM `clima_extendido` WHERE `id` = 1)
                UPDATE `clima_extendido` SET 'fecha' = @Valor WHERE ID = @ID"
INSERT INTO TABLA (Campo1,ID) VALUES (@Valor,@ID)
ELSE
UPDATE TABLA SET Campo1 = @Valor WHERE ID = @ID");*/
/*print_r($extendido[0]);*/
        $fecha = date('Y-m-d h:i:s');
        $res1 = mysql_query("SELECT clima_extendido.id FROM clima_extendido WHERE clima_extendido.fecha_numero=1");
        if (mysql_num_rows($res1) !=0 ){
            $update= mysql_query("UPDATE clima_extendido SET fecha_actual='".$fecha."' ,fecha_numero = 1 , condicion ='".$contenido[0]['descripcion']."' , temp_alta =".$contenido[0]['temp_max']." , temp_baja =".$contenido[0]['temp_min']." , icono ='".$contenido[0]['icono']."' WHERE fecha_numero=1");
        } else {
            $insert= mysql_query("INSERT INTO clima_extendido (id, fecha_actual, fecha_numero, condicion, temp_alta, temp_baja, icono) VALUES (NULL,'".$fecha."' , 1 , '".$contenido[0]['descripcion']."' , ".$contenido[0]['temp_max']." , ".$contenido[0]['temp_min']." , '".$contenido[0]['icono']."')");
        }

        $res2 = mysql_query("SELECT clima_extendido.id FROM clima_extendido WHERE clima_extendido.fecha_numero=2");
        if (mysql_num_rows($res2) !=0 ){
            $update= mysql_query("UPDATE clima_extendido SET fecha_actual='".$fecha."' ,fecha_numero = 2 , condicion ='".$contenido[1]['descripcion']."' , temp_alta =".$contenido[1]['temp_max']." , temp_baja =".$contenido[1]['temp_min']." , icono ='".$contenido[1]['icono']."' WHERE fecha_numero=2");
        } else {
            $insert= mysql_query("INSERT INTO clima_extendido (id, fecha_actual, fecha_numero, condicion, temp_alta, temp_baja, icono) VALUES (NULL,'".$fecha."' , 2 , '".$contenido[1]['descripcion']."' , ".$contenido[1]['temp_max']." , ".$contenido[1]['temp_min']." , '".$contenido[1]['icono']."')");
        }

        $res3 = mysql_query("SELECT clima_extendido.id FROM clima_extendido WHERE clima_extendido.fecha_numero=3");
        if (mysql_num_rows($res3) !=0 ){
            $update= mysql_query("UPDATE clima_extendido SET fecha_actual='".$fecha."' ,fecha_numero = 3 , condicion ='".$contenido[2]['descripcion']."' , temp_alta =".$contenido[2]['temp_max']." , temp_baja =".$contenido[2]['temp_min']." , icono ='".$contenido[2]['icono']."' WHERE fecha_numero=3");
        } else {
            $insert= mysql_query("INSERT INTO clima_extendido (id, fecha_actual, fecha_numero, condicion, temp_alta, temp_baja, icono) VALUES (NULL,'".$fecha."' , 3 , '".$contenido[2]['descripcion']."' , ".$contenido[2]['temp_max']." , ".$contenido[2]['temp_min']." , '".$contenido[2]['icono']."')");
        }

        $res4 = mysql_query("SELECT clima_extendido.id FROM clima_extendido WHERE clima_extendido.fecha_numero=4");
        if (mysql_num_rows($res4) !=0 ){
            $update= mysql_query("UPDATE clima_extendido SET fecha_actual='".$fecha."' ,fecha_numero = 4 , condicion ='".$contenido[3]['descripcion']."' , temp_alta =".$contenido[3]['temp_max']." , temp_baja =".$contenido[3]['temp_min']." , icono ='".$contenido[3]['icono']."' WHERE fecha_numero=4");
        } else {
            $insert= mysql_query("INSERT INTO clima_extendido (id, fecha_actual, fecha_numero, condicion, temp_alta, temp_baja, icono) VALUES (NULL,'".$fecha."' , 4 , '".$contenido[3]['descripcion']."' , ".$contenido[3]['temp_max']." , ".$contenido[3]['temp_min']." , '".$contenido[3]['icono']."')");
        }

        $res5 = mysql_query("SELECT clima_extendido.id FROM clima_extendido WHERE clima_extendido.fecha_numero=5");
        if (mysql_num_rows($res5) !=0 ){
            $update= mysql_query("UPDATE clima_extendido SET fecha_actual='".$fecha."' ,fecha_numero = 5 , condicion ='".$contenido[4]['descripcion']."' , temp_alta =".$contenido[4]['temp_max']." , temp_baja =".$contenido[4]['temp_min']." , icono ='".$contenido[4]['icono']."' WHERE fecha_numero=5");
        } else {
            $insert= mysql_query("INSERT INTO clima_extendido (id, fecha_actual, fecha_numero, condicion, temp_alta, temp_baja, icono) VALUES (NULL,'".$fecha."' , 5 , '".$contenido[4]['descripcion']."' , ".$contenido[4]['temp_max']." , ".$contenido[4]['temp_min']." , '".$contenido[4]['icono']."')");
        }

        $res6 = mysql_query("SELECT clima_extendido.id FROM clima_extendido WHERE clima_extendido.fecha_numero=6");
        if (mysql_num_rows($res6) !=0 ){
            $update= mysql_query("UPDATE clima_extendido SET fecha_actual='".$fecha."' ,fecha_numero = 6 , condicion ='".$contenido[5]['descripcion']."' , temp_alta =".$contenido[5]['temp_max']." , temp_baja =".$contenido[5]['temp_min']." , icono ='".$contenido[5]['icono']."' WHERE fecha_numero=6");
        } else {
            $insert= mysql_query("INSERT INTO clima_extendido (id, fecha_actual, fecha_numero, condicion, temp_alta, temp_baja, icono) VALUES (NULL,'".$fecha."' , 6 , '".$contenido[5]['descripcion']."' , ".$contenido[5]['temp_max']." , ".$contenido[5]['temp_min']." , '".$contenido[5]['icono']."')");
        }

        $res7 = mysql_query("SELECT clima_extendido.id FROM clima_extendido WHERE clima_extendido.fecha_numero=7");
        if (mysql_num_rows($res7) !=0 ){
            $update= mysql_query("UPDATE clima_extendido SET fecha_actual='".$fecha."' ,fecha_numero = 7 , condicion ='".$contenido[6]['descripcion']."' , temp_alta =".$contenido[6]['temp_max']." , temp_baja =".$contenido[6]['temp_min']." , icono ='".$contenido[6]['icono']."' WHERE fecha_numero=7");
        } else {
            $insert= mysql_query("INSERT INTO clima_extendido (id, fecha_actual, fecha_numero, condicion, temp_alta, temp_baja, icono) VALUES (NULL,'".$fecha."' , 7 , '".$contenido[6]['descripcion']."' , ".$contenido[6]['temp_max']." , ".$contenido[6]['temp_min']." , '".$contenido[6]['icono']."')");
        }

        $res8 = mysql_query("SELECT clima_extendido.id FROM clima_extendido WHERE clima_extendido.fecha_numero=8");
        if (mysql_num_rows($res1) !=0 ){
            $update= mysql_query("UPDATE clima_extendido SET fecha_actual='".$fecha."' ,fecha_numero = 8 , condicion ='".$contenido[7]['descripcion']."' , temp_alta =".$contenido[7]['temp_max']." , temp_baja =".$contenido[7]['temp_min']." , icono ='".$contenido[7]['icono']."' WHERE fecha_numero=8");
        } else {
            $insert= mysql_query("INSERT INTO clima_extendido (id, fecha_actual, fecha_numero, condicion, temp_alta, temp_baja, icono) VALUES (NULL,'".$fecha."' , 8 , '".$contenido[7]['descripcion']."' , ".$contenido[7]['temp_max']." , ".$contenido[7]['temp_min']." , '".$contenido[7]['icono']."')");
        }

        $res9 = mysql_query("SELECT clima_extendido.id FROM clima_extendido WHERE clima_extendido.fecha_numero=9");
        if (mysql_num_rows($res9) !=0 ){
            $update= mysql_query("UPDATE clima_extendido SET fecha_actual='".$fecha."' ,fecha_numero = 9 , condicion ='".$contenido[8]['descripcion']."' , temp_alta =".$contenido[8]['temp_max']." , temp_baja =".$contenido[8]['temp_min']." , icono ='".$contenido[8]['icono']."' WHERE fecha_numero=9");
        } else {
            $insert= mysql_query("INSERT INTO clima_extendido (id, fecha_actual, fecha_numero, condicion, temp_alta, temp_baja, icono) VALUES (NULL,'".$fecha."' , 9 , '".$contenido[8]['descripcion']."' , ".$contenido[8]['temp_max']." , ".$contenido[8]['temp_min']." , '".$contenido[8]['icono']."')");
        }

        $res10 = mysql_query("SELECT clima_extendido.id FROM clima_extendido WHERE clima_extendido.fecha_numero=10");
        if (mysql_num_rows($res10) !=0 ){
            $update= mysql_query("UPDATE clima_extendido SET fecha_actual='".$fecha."' ,fecha_numero = 10 , condicion ='".$contenido[9]['descripcion']."' , temp_alta =".$contenido[9]['temp_max']." , temp_baja =".$contenido[9]['temp_min']." , icono ='".$contenido[9]['icono']."' WHERE fecha_numero=10");
        } else {
            $insert= mysql_query("INSERT INTO clima_extendido (id, fecha_actual, fecha_numero, condicion, temp_alta, temp_baja, icono) VALUES (NULL,'".$fecha."' , 10 , '".$contenido[9]['descripcion']."' , ".$contenido[9]['temp_max']." , ".$contenido[9]['temp_min']." , '".$contenido[9]['icono']."')");
        }

        /*
        $insert="INSERT INTO `clima_extendido` (`id`, `fecha_actual`, `temp_c`, `condicion`, `icono`, `temp_alta`, `temp_baja`) VALUES
                                                    (NULL,'".date('Y-m-d')."', '{$contenido['temp_c']}', '{$condicion}', '{$pronostico['wind']}', '{$icono}', '{$pronostico['high']}', '{$pronostico['low']}');";

        $resultado = mysql_query($insert) or die($cartel = 'La consulta fallo: ' . mysql_error());
    $lasId = mysql_insert_id();
        return 9;*/
        return 'ok';
    }

}

//467187 - cipoletti
//467077 - viedma
    $gweather = new YahooWeatherAPI('464746');
    /*print_r($gweather);exit();*/

        if($gweather->grabarWeather()){
            echo'inserto';
        }

?>
