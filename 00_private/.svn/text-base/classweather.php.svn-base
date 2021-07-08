<?php
require_once 'conection.php';


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
                        27 => 'Muy nublado (noche)',
                        28 => 'Muy nublado (dia)',
                        29 => 'Parcialmente nublado (noche)',
                        30 => 'Parcialmente nublado (dia)',
                        31 => 'Despejado (noche)',
                        32 => 'Soleado',
                        33 => 'Despejado (noche)',
                        34 => 'Despejado (dia)',
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
        $this->url = 'http://'.$this->domain.'forecastrss?w=' . $this->city_code .'&u=c';

        $namespace_yweather = 'http://xml.weather.yahoo.com/ns/rss/1.0';

        $xml_string = file_get_contents($this->url);
        $xml = new DOMDocument();
        $xml->loadXML($xml_string);

        $clima = $xml->getElementsByTagNameNS($namespace_yweather, 'condition');
        $array_clima = $clima->item(0);

            $resultado['codigo'] = $array_clima->getAttribute('code');
            $this->current_conditions['condition'] = $this->mensajes[$resultado['codigo']];
            $this->current_conditions['temp_c'] = $array_clima->getAttribute('temp');
            $this->current_conditions['icon'] =  "/css/images/iconos_clima/" . $resultado['codigo'] . ".png";

        $clima = $xml->getElementsByTagNameNS($namespace_yweather, 'forecast');
        $array_clima = $clima->item(0);

            $this->forecast_conditions['high'] = $array_clima->getAttribute('high');
            $this->forecast_conditions['low'] = $array_clima->getAttribute('low');
 
        $clima = $xml->getElementsByTagNameNS($namespace_yweather, 'wind');
        $array_clima = $clima->item(0);

            $this->forecast_conditions['wind'] = $array_clima->getAttribute('speed');

    }
    function getCity() {
        return $this->city;
    }
    function getCurrent() {
        return $this->current_conditions;
    }
    function getForecast() {
        return $this->forecast_conditions;
    }

    function grabarWeather(){
        $contenido=$this->getCurrent();
        $pronostico=$this->getForecast();
        $dia=array(
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes');

        if(empty($contenido['condition'])){
            $condicion=$pronostico['0']['condition'];
            $icono=$pronostico['0']['icon'];
        }else{
            $condicion=$contenido['condition'];
            $icono=$contenido['icon'];
        }
        $insert="INSERT INTO `clima` (`id`, `dia_nombre`, `fecha`, `temp_c`, `condicion`, `viento`, `icono`, `temp_alta`, `temp_baja`) VALUES
                                                    (NULL, '".utf8_decode($dia[date('l')])."', '".date('Y-m-d')."', '{$contenido['temp_c']}', '{$condicion}', '{$pronostico['wind']}', '{$icono}', '{$pronostico['high']}', '{$pronostico['low']}');";

        $resultado = mysql_query($insert) or die($cartel = 'La consulta fallo: ' . mysql_error());
    $lasId = mysql_insert_id();
        return 1;
    }

}

//467187 - cipoletti
//467077 - viedma
    $gweather = new YahooWeatherAPI('464746');

        if($gweather->grabarWeather()){
            echo'inserto';
        }

?>
