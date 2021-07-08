<?php

class MareasController extends Zend_Controller_Action
{
    public $a =0;
    public $b =0;
    public $c =0;
    public $d =0;
    public $e =0;
    public $f =0;
    public $g =0;

    public function init()
    {
        $Mareas = new Application_Model_DbTable_mareas();
        $arrMareas = $Mareas->get('El Condor',date('Y-m-d'),4);
        $this->view->arrMareas = $arrMareas;

        $Clima = new Application_Model_DbTable_clima();
        $arrClima = $Clima->get(1);
        $this->view->arrClima = $arrClima;

        $this->view->param = array(
                "home"          => 'class="radiusPrimero"',
                "Ubicacion"     => '',
                "Servicio"      => '',
                "Imagenes"      => '',
                "Novedades"     => '',
                "Clasificados"  => 'class="activo"',
                "Fauna"         => '',
                "Informacion"         => '',
                "Historia"      => 'class="radiusUltimo"'
                );
                
		$Cercanos = new Application_Model_DbTable_cercanos();
        $arrCercanos = $Cercanos->get(6);
        $this->view->arrCercanos = $arrCercanos;

        $Gourmet = new Application_Model_DbTable_gourmet();
        $arrGourmet = $Gourmet->get(6);
        $this->view->arrGourmet = $arrGourmet;

        $Hospedaje = new Application_Model_DbTable_hospedaje();
        $arrHospedaje1 = $Hospedaje->get(6);
        $this->view->arrHospedaje = $arrHospedaje1;
                
       	$Hospedaje = new Application_Model_DbTable_hospedaje();
        $arrHospedaje1 = $Hospedaje->get(6,1);
        $this->view->arrHospedaje1 = $arrHospedaje1;

        $Hospedaje = new Application_Model_DbTable_hospedaje();
        $arrHospedaje2 = $Hospedaje->get(6,2);
        $this->view->arrHospedaje2 = $arrHospedaje2;
        
        $Hospedaje = new Application_Model_DbTable_hospedaje();
        $arrHospedaje3 = $Hospedaje->get(6,3);
        $this->view->arrHospedaje3 = $arrHospedaje3;
        
        $Nocturno = new Application_Model_DbTable_nocturno();
        $arrNocturno = $Nocturno->get(99);
        $this->view->arrNocturno = $arrNocturno;

        $Imagenes = new Application_Model_DbTable_imagenes();
        $arrImagenes = $Imagenes->get(99);
        $this->view->arrImagenes = $arrImagenes;
    }

    public function detalleAction()
    {
    }
    
    
    public function jsonAction(){
    	header('Content-Type: application/json');
    	$clave = $this->_getParam("clave");
	    if ($clave == CLAVE_JSON){
	    	$playa = $this->_getParam("playa");
	    	if ($playa == ""){
	    		$playa = "El Condor";
	    	}
			$Mareas = new Application_Model_DbTable_mareas();
	        $arrMareas = $Mareas->get(NULL,date('Y-m-d'),7);
	    	/***
    		 Desun la marea sumo las horas necesarias
    		***/
    		$tiempoAnadir = 0;
    		$this->view->playa = $playa;
    		switch ($playa) {
    			case "loberia":
    				$tiempoAnadir = -12*60; // 12 minutos
    				break;
    			case "bahia-rosa":
    				$tiempoAnadir = -24*60; // 12 minutos
    				break;
    			case "bahia-creek":
    				$tiempoAnadir = -36*60; // 12 minutos
    				break;
    			default: // El Condor o Punta Redonda :)
    				$tiempoAnadir = 0;
    				break;
    		}
	        for ($i = 0; $i < sizeof($arrMareas); $i++) {
	        	$arrMareas[$i]["dia"] = $this->dayTodia($arrMareas[$i]["ma_fecha"]);
	        	
	        	$primera_p = $arrMareas[$i]["ma_primera_pleamar"];
	        	$arrMareas[$i]["ma_primera_bajamar"] = $arrMareas[$i]["ma_primera_bajamar"];
	        	$arrMareas[$i]["ma_primera_pleamar"] = $primera_p;
	        	
	        	$primera_p = $arrMareas[$i]["ma_segunda_plamar"];
	        	$arrMareas[$i]["ma_segunda_bajamar"] = $arrMareas[$i]["ma_segunda_bajamar"];
	        	$arrMareas[$i]["ma_segunda_plamar"] = $primera_p;	        	
	        	
	        	
	        	$horaInicial=substr($arrMareas[$i]["ma_primera_pleamar"],0,5);
	    		$segundos_horaInicial=strtotime($horaInicial);
	    		$arrMareas[$i]["ma_primera_pleamar"]=date("H:i",$segundos_horaInicial+$tiempoAnadir);
	    	
	    		$horaInicial=substr($arrMareas[$i]["ma_primera_bajamar"],0,5);
	    		$segundos_horaInicial=strtotime($horaInicial);
	    		$arrMareas[$i]["ma_primera_bajamar"]=date("H:i",$segundos_horaInicial+$tiempoAnadir);
	    	
	    		$horaInicial=substr($arrMareas[$i]["ma_segunda_plamar"],0,5);
	    		$segundos_horaInicial=strtotime($horaInicial);
	    		$arrMareas[$i]["ma_segunda_pleamar"]=date("H:i",$segundos_horaInicial+$tiempoAnadir);
	    	
	    		$horaInicial=substr($arrMareas[$i]["ma_segunda_bajamar"],0,5);
	    		$segundos_horaInicial=strtotime($horaInicial);
	    		$arrMareas[$i]["ma_segunda_bajamar"]=date("H:i",$segundos_horaInicial+$tiempoAnadir);
	    	
	        }
    		$log = new Application_Model_DbTable_log();
    		$log->insert(array("lo_fecha" => date("Y-m-d h:i:s"),"lo_msj" => "1"));
            $this->view->arrMareas = $arrMareas;
            header('Access-Control-Allow-Origin: *');
			echo json_encode($arrMareas);
	    }
		exit();
    }
    
    function dayTodia($stringDate){
    	$date = explode("-", $stringDate);
    	$day = (date("l", mktime(0, 0, 0, $date[1], $date[2], $date[0])));
    	switch ($day){
        	case "Monday":
        	     $dia = "Lunes";break;
        	case "Tuesday":
        	     $dia = "Martes";break;
        	case "Wednesday":
	             $dia = "Mi&eacute;rcoles";break;
        	case "Thursday":
        	     $dia = "Jueves";break;
        	case "Friday":
        	     $dia = "Viernes";break;
        	case "Saturday":
        	     $dia = "S&aacute;bado";break;
        	case "Sunday":
        	     $dia = "Domingo";    break;     	        
        } 

        $dia .= " ".$date[2]."-".$date[1]."-".$date[0];
        return $dia;
    }
    
    public function mareaswordAction(){
    	$this->_helper->layout->disableLayout();
    	$playa = $this->_getParam("playa");
    	 
    	if ($playa == ""){
    		$playa = "El Condor";
    	}
    	
    	// create a Zend_View instance
    	$this->view->toWord = "si";
    	$this->view->arrMareasDiarias = $this->generarTabla();
    	$this->_helper->viewRenderer('index');
    	
    }

    public function mareaspdfAction(){
    	
    	$arrMareasDiarias = $this->generarTabla();	
    	$pdf = new PDF('l','mm','A4');
        $pdf->SetMargins(30, 25 , 30);
       	$pdf->AliasNbPages();
    	$pdf->AddPage();
        $pdf->Ln(15);//ahora salta 15 lineas
        $pdf->SetXY(80,20);
    	$pdf->Write (7,"Tabla de Mareas - Balneario El Condor - http://www.balneario-el-condor.com.ar","");
    	$pdf->Ln(); //salto de linea
        $pdf->SetXY(20,40);
        $pdf->Cell(100,10,utf8_decode("*Diferencia Horaria=   La Lober�a: -12 minutos / Bah�a Rosas: -24 minutos / Bah�a Creek: -36 minutos / Bah�a San Blas: +2 horas 4 minutos"),0);
        $columna = "";	
    	$linea = "";
    	for ($col=0; $col < 5 ; $col++) {
    		
			for ($fila=0; $fila < 7 ; $fila++) { //filas 
	    	    switch ($fila) {
		    	    case '0': $dia = 'Sunday';break;
		    	    case '1': $dia = 'Monday';break;
		    	    case '2': $dia = 'Tuesday';break;
		    	    case '3': $dia = 'Wednesday';break;
		    	    case '4': $dia = 'Thursday';break;
		    	    case '5': $dia = 'Friday';break;
		    	    case '6': $dia = 'Saturday';break;
				} 

				
				if ($arrMareasDiarias[$dia][$col]["numero"]){

                    $d= $this->parserDia($dia);
                    $m=$this->parserMes($arrMareasDiarias[$dia][$col]["ma_fecha"]);
					$linea .= $arrMareasDiarias[$dia][$col]["numero"].$m."||".$d." / ".$dia;
    	            $PrimerItem = ($arrMareasDiarias[$dia][$col]["ma_primera_pleamar"] > $arrMareasDiarias[$dia][$col]["ma_primera_bajamar"])? "Bajamar ".substr($arrMareasDiarias[$dia][$col]["ma_primera_bajamar"], 0, 5): "Pleamar ".substr($arrMareasDiarias[$dia][$col]["ma_primera_pleamar"], 0, 5);
    	            $SegundoItem= ($arrMareasDiarias[$dia][$col]["ma_primera_pleamar"] < $arrMareasDiarias[$dia][$col]["ma_primera_bajamar"])? "Bajamar ".substr($arrMareasDiarias[$dia][$col]["ma_primera_bajamar"], 0, 5): "Pleamar ".substr($arrMareasDiarias[$dia][$col]["ma_primera_pleamar"], 0, 5);
    	            $TercerItem = ($arrMareasDiarias[$dia][$col]["ma_segunda_plamar"] > $arrMareasDiarias[$dia][$col]["ma_segunda_bajamar"])?  "Bajamar ".substr($arrMareasDiarias[$dia][$col]["ma_segunda_bajamar"], 0, 5): "Pleamar ".substr($arrMareasDiarias[$dia][$col]["ma_segunda_plamar"], 0, 5);
    	            $CuartoItem = ($arrMareasDiarias[$dia][$col]["ma_segunda_plamar"] < $arrMareasDiarias[$dia][$col]["ma_segunda_bajamar"])?  "Bajamar ".substr($arrMareasDiarias[$dia][$col]["ma_segunda_bajamar"], 0, 5): "Pleamar ".substr($arrMareasDiarias[$dia][$col]["ma_segunda_plamar"], 0, 5);
    	            $linea .= "||".str_replace("00:00", "-", $PrimerItem);
    	            $linea .= "||".str_replace("00:00", "-", $SegundoItem);
    	            $linea .= "||".str_replace("00:00", "-", $TercerItem);
    	            $linea .= "||".str_replace("00:00", "-", $CuartoItem);
				} 
				$linea .= "@@"; // Fin de linea
			}
			}
        //echo "<pre>";
        $pdf->SetFont('helvetica');
        $pdf->Tabla($linea);
       	$pdf->Output("tabla-mareas.pdf",'F');
    	echo "<script language='javascript'>window.open('/tabla-mareas','__balnk','');</script>";//para ver el archivo pdf generado
    	exit();
    }
    
    public function parserDia($dia){
        switch ($dia) {

                    case 'Sunday': $d = 'Domingo';break;
                    case 'Monday': $d = 'Lunes';break;
                    case 'Tuesday': $d = 'Martes';break;
                    case 'Wednesday': $d = 'Miercoles';break;
                    case 'Thursday': $d = 'Jueves';break;
                    case 'Friday': $d = 'Viernes';break;
                    case 'Saturday': $d = 'Sabado';break;
                }
        return $d; 
                
    }

    public function parserMes($fecha){
    	$arrFecha = explode("-", $fecha);
    	switch ($arrFecha[1]) {
                    case '01': $m = ' Enero / January';break;
                    case '02': $m = ' Febrero / February';break;
                    case '03': $m = ' Marzo / March';break;
                    case '04': $m = ' Abril / April';break;
                    case '05': $m = ' Mayo / May';break;
                    case '06': $m = ' Junio / June';break;
                    case '07': $m = ' Julio / July';break;
                    case '08': $m = ' Agosto / August';break;
                    case '09': $m = ' Septiembre / September';break;
                    case '10': $m = ' Octubre / October';break;
                    case '11': $m = ' Noviembre / November';break;
                    case '12': $m = ' Diciembre / December';break;
                }
        return $m; 
                
    }
    public function indexAction()
    {
    	/*
    	  $json_string = file_get_contents("http://api.wunderground.com/api/247527dbfc24dde2/geolookup/conditions/q/IA/Cedar_Rapids.json");
  $parsed_json = json_decode($json_string);
  $location = $parsed_json->{'location'}->{'city'};
  $temp_f = $parsed_json->{'current_observation'}->{'temp_f'};
    	*/
    	$playa = $this->_getParam("playa");
    	$this->view->playas = (isset($playa))?strtoupper(substr($playa,0,1)).substr($playa,1):"El Condor";
        $this->view->metaKeywords="Ensenada, Mareas,Pleamar,Pesca,Bajamar,El Condor,Balneario,Espigon,Rio Negro,La Boca,Desembocadura,Playa Bonita,Loberia,Caleta de los Loros";
        $this->view->metaDescription="Encuentra Aqui todas las tablas de Mareas de la Boca, Ensenada, Playa Bonita, Loberia, Bahia Creek";
        $this->view->title = "Tabla de Mareas, Pleamar y Bajamar";       
        $this->view->arrMareasDiarias = $this->generarTabla();

        $ce_model = new Application_Model_climaextendidoMapper();
        $climaExt = $ce_model ->get();
        $this->view->clima = $climaExt;
    }

    public function mareasconalturaAction()
    {
        $ce_model = new Application_Model_climaextendidoMapper(); //agregar
        $climaExt = $ce_model ->get(); //agregar
        $this->view->clima = $climaExt; //agregar
        $playa = $this->_getParam("playa"); 
        $this->view->playas = (isset($playa))?strtoupper(substr($playa,0,1)).substr($playa,1):"El Condor";
        $this->view->metaKeywords="Mareas,Pleamar,Pleamares,Pesca,Pleamar,Bajamar,El Condor,Balneario,Espigon,Rio Negro,Playa,Verano,Costa Atlantica,La Boca,Desembocadura,Playa Bonita,Balenario Massini,Loberia,Caleta de los Loros,Villa Maritima El Condor,El Faro,Los Trentinos,Picoto";
        $this->view->metaDescription="Encuentra Aqui todas las tablas de Mareas del Camino de la Costa, Desde La Villa Maritima hasta las Grutas";
        $this->view->title = "Tabla de Mareas, Pleamar y Bajamar";
        
        $this->view->arrMareasDiarias = $this->generarTabla();
    }

    private function inicializarContadores($inicializacion){
        switch ($inicializacion) {
            case '6':
                $this->g =1;
            case '5':
                $this->f =1;
            case '4':
                $this->e =1;
            case '3':
                $this->d =1;
            case '3':
                $this->c =1;
            case '1':
                $this->b =1;
            case '0':
                $this->a =1;
        }

    }
   
    private function generarTabla($fechaDesde = NULL,$dias = NULL){
    	$Mareas = new Application_Model_DbTable_mareas();
    	$fechaDesde = (isset($fechaDesde))?$fechaDesde:date('Y-m-01');
    	$arrMareasDiarias = $Mareas->get('El Condor',$fechaDesde,$dias);
    	
    	$tablaMes = array();
    	for ($i=0; $i < 5; $i++) {
    		$tablaMes["Sunday"][$i]     = '';
    		$tablaMes["Monday"][$i]     = '';
    		$tablaMes["Tuesday"][$i]    = '';
    		$tablaMes["Wednesday"][$i]  = '';
    		$tablaMes["Thursday"][$i]   = '';
    		$tablaMes["Friday"][$i]     = '';
    		$tablaMes["Saturday"][$i]   = '';
    	
    	}
    	// Primera ver debo contar cuando empiezo
    	$fecha = explode("-", $arrMareasDiarias[0]['ma_fecha']);
    	$value["numero"] = $fecha[2];
    	$diaDeLaSemana = date('l',mktime(0,0,0,$fecha[1],$fecha[2],$fecha[0]));
    	switch ($diaDeLaSemana) {
    		case 'Sunday'   : $contEspero = 7;break;
    		case 'Monday'   : $contEspero = 6;break;
    		case 'Tuesday'  : $contEspero = 5;break;
    		case 'Wednesday': $contEspero = 4;break;
    		case 'Thursday' : $contEspero = 3;break;
    		case 'Friday'   : $contEspero = 2;break;
    		case 'Saturday' : $contEspero = 1;break;
    	}
    	$cont = 0;
    	$noEncontreInicio =  true;
    	$avanzoDias = 0;
    	foreach ($arrMareasDiarias as $key => $value) {
    		if ($contEspero == 0 && $cont == 0){
    			$this->a ++;
    			$this->b ++;
    			$this->c ++;
    			$this->d ++;
    			$this->e ++;
    			$this->f ++;
    			$this->g ++;
    		}
    		/*
    		 while ($noEncontreInicio){
    	
    		$fecha = explode("-", $value["ma_fecha"]);
    	
    		$value["numero"] = $fecha[2];
    		if (intval($avanzoDias) != intval($value["numero"])){
    		$avanzoDias ++;
    		}else{
    		$noEncontreInicio = false;
    		$this->inicializarContadores($avanzoDias);
    		}
    	
    		}
    		*/
    		$fecha = explode("-", $value["ma_fecha"]);
    		$value["numero"] = $fecha[2];
    		$diaDeLaSemana = date('l',mktime(0,0,0,$fecha[1],$fecha[2],$fecha[0]));
    	
    	
    	
    	
    		if ($cont % 7 == 0 && $cont > 0){
    			$this->a ++;
    			$this->b ++;
    			$this->c ++;
    			$this->d ++;
    			$this->e ++;
    			$this->f ++;
    			$this->g ++;
    		}
    		switch ($diaDeLaSemana) {
    			case 'Sunday'   : $ubicacion = $this->a;break;
    			case 'Monday'   : $ubicacion = $this->b;break;
    			case 'Tuesday'  : $ubicacion = $this->c;break;
    			case 'Wednesday': $ubicacion = $this->d;break;
    			case 'Thursday' : $ubicacion = $this->e;break;
    			case 'Friday'   : $ubicacion = $this->f;break;
    			case 'Saturday' : $ubicacion = $this->g;break;
    		}
    		/***
    		 Desun la marea sumo las horas necesarias
    		***/
    		$tiempoAnadir = 0;
    		$param["playa"] = $this->_getParam("playa");
    		$this->view->playa = $param["playa"];
    		switch ($param["playa"]) {
    			case "loberia":
    				$tiempoAnadir = -12*60; // 12 minutos
    				break;
    			case "bahia-rosa":
    				$tiempoAnadir = -24*60; // 12 minutos
    				break;
    			case "bahia-creek":
    				$tiempoAnadir = -36*60; // 12 minutos
    				break;
    			default: // El Condor o Punta Redonda :)
    				$tiempoAnadir = 0;
    				break;
    		}
    		$horaInicial=substr($value["ma_primera_pleamar"],0,5);
    		$segundos_horaInicial=strtotime($horaInicial);
    		$value["ma_primera_pleamar"]=date("H:i",$segundos_horaInicial+$tiempoAnadir);
    	
    		$horaInicial=substr($value["ma_primera_bajamar"],0,5);
    		$segundos_horaInicial=strtotime($horaInicial);
    		$value["ma_primera_bajamar"]=date("H:i",$segundos_horaInicial+$tiempoAnadir);
    	
    		$horaInicial=substr($value["ma_segunda_plamar"],0,5);
    		$segundos_horaInicial=strtotime($horaInicial);
    		$value["ma_segunda_plamar"]=date("H:i",$segundos_horaInicial+$tiempoAnadir);
    	
    		$horaInicial=substr($value["ma_segunda_bajamar"],0,5);
    		$segundos_horaInicial=strtotime($horaInicial);
    		$value["ma_segunda_bajamar"]=date("H:i",$segundos_horaInicial+$tiempoAnadir);
    	
    		$tablaMes[$diaDeLaSemana][$ubicacion] = $value;
    	
    		if ($contEspero == 0) $cont ++;
    		else $contEspero --;
    	}
    	
    	return $tablaMes;
    }

}

class PDF extends FPDF 
		{
		
		// Cabecera de p�gina
		function Header()
		{   // Logo Ancho: 40, alto:50
            //*Diferencia Horaria= La Loberia: -12 / Bah&iacute;a Rosa: -24 / Bah&iacute;a Creek: -36 
            $this->SetFont('helvetica');
            $this->SetXY(60, 25);
            $this->Image('http://www.balneario-el-condor.com.ar/css/images/logo.png',10,10,30,25);
            $this->Ln(15);
			
		}
		
		// Pie de p�gina
		function Footer()
		{
			// Posici�n: a 1,5 cm del final
			$this->SetY(-20);
			// N�mero de p�gina
			$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		}
		
        function Tabla($cookie)
        {
        $this->SetXY(10, 15);
	        if (isset($cookie)){
	                $cookierecorro = $cookie;
	                $colCookie = explode("@@", $cookierecorro);
	                //print_r($colCookie);
	                $i==0;
	                if (sizeof($colCookie) > 1) {
	                    foreach ($colCookie as $key => $valueCol){
	                        $filCookie = explode("||", $valueCol);
                            foreach ($filCookie as $valueFila) {
	                            if ($valueFila!='') {  
                                    $this->CellFitSpace(40,7,$valueFila,1);
                                }
                                else{
                                    $this->Ln();
                                }
	                                
	                        }
	                        $this->Ln();
                            
	                    }
	                }
	            }
        }
   
	}   
		

// Handle special IE contype request
if(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT']=='contype')
{
    header('Content-Type: application/pdf');
    exit;
}

class FPDF
{
var $page;               // current page number
var $n;                  // current object number
var $offsets;            // array of object offsets
var $buffer;             // buffer holding in-memory PDF
var $pages;              // array containing pages
var $state;              // current document state
var $compress;           // compression flag
var $k;                  // scale factor (number of points in user unit)
var $DefOrientation;     // default orientation
var $CurOrientation;     // current orientation
var $StdPageSizes;       // standard page sizes
var $DefPageSize;        // default page size
var $CurPageSize;        // current page size
var $PageSizes;          // used for pages with non default sizes or orientations
var $wPt, $hPt;          // dimensions of current page in points
var $w, $h;              // dimensions of current page in user unit
var $lMargin;            // left margin
var $tMargin;            // top margin
var $rMargin;            // right margin
var $bMargin;            // page break margin
var $cMargin;            // cell margin
var $x, $y;              // current position in user unit
var $lasth;              // height of last printed cell
var $LineWidth;          // line width in user unit
var $fontpath;           // path containing fonts
var $CoreFonts;          // array of core font names
var $fonts;              // array of used fonts
var $FontFiles;          // array of font files
var $diffs;              // array of encoding differences
var $FontFamily;         // current font family
var $FontStyle;          // current font style
var $underline;          // underlining flag
var $CurrentFont;        // current font info
var $FontSizePt;         // current font size in points
var $FontSize;           // current font size in user unit
var $DrawColor;          // commands for drawing color
var $FillColor;          // commands for filling color
var $TextColor;          // commands for text color
var $ColorFlag;          // indicates whether fill and text colors are different
var $ws;                 // word spacing
var $images;             // array of used images
var $PageLinks;          // array of links in pages
var $links;              // array of internal links
var $AutoPageBreak;      // automatic page breaking
var $PageBreakTrigger;   // threshold used to trigger page breaks
var $InHeader;           // flag set when processing header
var $InFooter;           // flag set when processing footer
var $ZoomMode;           // zoom display mode
var $LayoutMode;         // layout display mode
var $title;              // title
var $subject;            // subject
var $author;             // author
var $keywords;           // keywords
var $creator;            // creator
var $AliasNbPages;       // alias for total number of pages
var $PDFVersion;         // PDF version number

/*******************************************************************************
*                                                                              *
*                               Public methods                                 *
*                                                                              *
*******************************************************************************/
function FPDF($orientation='P', $unit='mm', $size='A4')
{
    // Some checks
    $this->_dochecks();
    // Initialization of properties
    $this->page = 0;
    $this->n = 2;
    $this->buffer = '';
    $this->pages = array();
    $this->PageSizes = array();
    $this->state = 0;
    $this->fonts = array();
    $this->FontFiles = array();
    $this->diffs = array();
    $this->images = array();
    $this->links = array();
    $this->InHeader = false;
    $this->InFooter = false;
    $this->lasth = 0;
    $this->FontFamily = '';
    $this->FontStyle = '';
    $this->FontSizePt = 12;
    $this->underline = false;
    $this->DrawColor = '0 G';
    $this->FillColor = '0 g';
    $this->TextColor = '0 g';
    $this->ColorFlag = false;
    $this->ws = 0;
    // Font path
    if(defined('FPDF_FONTPATH'))
    {
        $this->fontpath = FPDF_FONTPATH;
        if(substr($this->fontpath,-1)!='/' && substr($this->fontpath,-1)!='\\')
            $this->fontpath .= '/';
    }
    elseif(is_dir(dirname(__FILE__).'/font'))
        $this->fontpath = dirname(__FILE__).'/font/';
    else
        $this->fontpath = '';
    // Core fonts
    $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
    // Scale factor
    if($unit=='pt')
        $this->k = 1;
    elseif($unit=='mm')
        $this->k = 72/25.4;
    elseif($unit=='cm')
        $this->k = 72/2.54;
    elseif($unit=='in')
        $this->k = 72;
    else
        $this->Error('Incorrect unit: '.$unit);
    // Page sizes
    $this->StdPageSizes = array('a3'=>array(841.89,1190.55), 'a4'=>array(595.28,841.89), 'a5'=>array(420.94,595.28),
        'letter'=>array(612,792), 'legal'=>array(612,1008));
    $size = $this->_getpagesize($size);
    $this->DefPageSize = $size;
    $this->CurPageSize = $size;
    // Page orientation
    $orientation = strtolower($orientation);
    if($orientation=='p' || $orientation=='portrait')
    {
        $this->DefOrientation = 'P';
        $this->w = $size[0];
        $this->h = $size[1];
    }
    elseif($orientation=='l' || $orientation=='landscape')
    {
        $this->DefOrientation = 'L';
        $this->w = $size[1];
        $this->h = $size[0];
    }
    else
        $this->Error('Incorrect orientation: '.$orientation);
    $this->CurOrientation = $this->DefOrientation;
    $this->wPt = $this->w*$this->k;
    $this->hPt = $this->h*$this->k;
    // Page margins (1 cm)
    $margin = 28.35/$this->k;
    $this->SetMargins($margin,$margin);
    // Interior cell margin (1 mm)
    $this->cMargin = $margin/10;
    // Line width (0.2 mm)
    $this->LineWidth = .567/$this->k;
    // Automatic page break
    $this->SetAutoPageBreak(true,2*$margin);
    // Default display mode
    $this->SetDisplayMode('default');
    // Enable compression
    $this->SetCompression(true);
    // Set default PDF version number
    $this->PDFVersion = '1.3';
}

function SetMargins($left, $top, $right=null)
{
    // Set left, top and right margins
    $this->lMargin = $left;
    $this->tMargin = $top;
    if($right===null)
        $right = $left;
    $this->rMargin = $right;
}

function SetLeftMargin($margin)
{
    // Set left margin
    $this->lMargin = $margin;
    if($this->page>0 && $this->x<$margin)
        $this->x = $margin;
}

function SetTopMargin($margin)
{
    // Set top margin
    $this->tMargin = $margin;
}

function SetRightMargin($margin)
{
    // Set right margin
    $this->rMargin = $margin;
}

function SetAutoPageBreak($auto, $margin=0)
{
    // Set auto page break mode and triggering margin
    $this->AutoPageBreak = $auto;
    $this->bMargin = $margin;
    $this->PageBreakTrigger = $this->h-$margin;
}

function SetDisplayMode($zoom, $layout='default')
{
    // Set display mode in viewer
    if($zoom=='fullpage' || $zoom=='fullwidth' || $zoom=='real' || $zoom=='default' || !is_string($zoom))
        $this->ZoomMode = $zoom;
    else
        $this->Error('Incorrect zoom display mode: '.$zoom);
    if($layout=='single' || $layout=='continuous' || $layout=='two' || $layout=='default')
        $this->LayoutMode = $layout;
    else
        $this->Error('Incorrect layout display mode: '.$layout);
}

function SetCompression($compress)
{
    // Set page compression
    if(function_exists('gzcompress'))
        $this->compress = $compress;
    else
        $this->compress = false;
}

function SetTitle($title, $isUTF8=false)
{
    // Title of document
    if($isUTF8)
        $title = $this->_UTF8toUTF16($title);
    $this->title = $title;
}

function SetSubject($subject, $isUTF8=false)
{
    // Subject of document
    if($isUTF8)
        $subject = $this->_UTF8toUTF16($subject);
    $this->subject = $subject;
}

function SetAuthor($author, $isUTF8=false)
{
    // Author of document
    if($isUTF8)
        $author = $this->_UTF8toUTF16($author);
    $this->author = $author;
}

function SetKeywords($keywords, $isUTF8=false)
{
    // Keywords of document
    if($isUTF8)
        $keywords = $this->_UTF8toUTF16($keywords);
    $this->keywords = $keywords;
}

function SetCreator($creator, $isUTF8=false)
{
    // Creator of document
    if($isUTF8)
        $creator = $this->_UTF8toUTF16($creator);
    $this->creator = $creator;
}

function AliasNbPages($alias='{nb}')
{
    // Define an alias for total number of pages
    $this->AliasNbPages = $alias;
}

function Error($msg)
{
    // Fatal error
    die('<b>FPDF error:</b> '.$msg);
}

function Open()
{
    // Begin document
    $this->state = 1;
}

function Close()
{
    // Terminate document
    if($this->state==3)
        return;
    if($this->page==0)
        $this->AddPage();
    // Page footer
    $this->InFooter = true;
    $this->Footer();
    $this->InFooter = false;
    // Close page
    $this->_endpage();
    // Close document
    $this->_enddoc();
}

function AddPage($orientation='', $size='')
{
    // Start a new page
    if($this->state==0)
        $this->Open();
    $family = $this->FontFamily;
    $style = $this->FontStyle.($this->underline ? 'U' : '');
    $fontsize = $this->FontSizePt;
    $lw = $this->LineWidth;
    $dc = $this->DrawColor;
    $fc = $this->FillColor;
    $tc = $this->TextColor;
    $cf = $this->ColorFlag;
    if($this->page>0)
    {
        // Page footer
        $this->InFooter = true;
        $this->Footer();
        $this->InFooter = false;
        // Close page
        $this->_endpage();
    }
    // Start new page
    $this->_beginpage($orientation,$size);
    // Set line cap style to square
    $this->_out('2 J');
    // Set line width
    $this->LineWidth = $lw;
    $this->_out(sprintf('%.2F w',$lw*$this->k));
    // Set font
    if($family)
        $this->SetFont($family,$style,$fontsize);
    // Set colors
    $this->DrawColor = $dc;
    if($dc!='0 G')
        $this->_out($dc);
    $this->FillColor = $fc;
    if($fc!='0 g')
        $this->_out($fc);
    $this->TextColor = $tc;
    $this->ColorFlag = $cf;
    // Page header
    $this->InHeader = true;
    $this->Header();
    $this->InHeader = false;
    // Restore line width
    if($this->LineWidth!=$lw)
    {
        $this->LineWidth = $lw;
        $this->_out(sprintf('%.2F w',$lw*$this->k));
    }
    // Restore font
    if($family)
        $this->SetFont($family,$style,$fontsize);
    // Restore colors
    if($this->DrawColor!=$dc)
    {
        $this->DrawColor = $dc;
        $this->_out($dc);
    }
    if($this->FillColor!=$fc)
    {
        $this->FillColor = $fc;
        $this->_out($fc);
    }
    $this->TextColor = $tc;
    $this->ColorFlag = $cf;
}

function Header()
{
    // To be implemented in your own inherited class
}

function Footer()
{
    // To be implemented in your own inherited class
}

function PageNo()
{
    // Get current page number
    return $this->page;
}

function SetDrawColor($r, $g=null, $b=null)
{
    // Set color for all stroking operations
    if(($r==0 && $g==0 && $b==0) || $g===null)
        $this->DrawColor = sprintf('%.3F G',$r/255);
    else
        $this->DrawColor = sprintf('%.3F %.3F %.3F RG',$r/255,$g/255,$b/255);
    if($this->page>0)
        $this->_out($this->DrawColor);
}

function SetFillColor($r, $g=null, $b=null)
{
    // Set color for all filling operations
    if(($r==0 && $g==0 && $b==0) || $g===null)
        $this->FillColor = sprintf('%.3F g',$r/255);
    else
        $this->FillColor = sprintf('%.3F %.3F %.3F rg',$r/255,$g/255,$b/255);
    $this->ColorFlag = ($this->FillColor!=$this->TextColor);
    if($this->page>0)
        $this->_out($this->FillColor);
}

function SetTextColor($r, $g=null, $b=null)
{
    // Set color for text
    if(($r==0 && $g==0 && $b==0) || $g===null)
        $this->TextColor = sprintf('%.3F g',$r/255);
    else
        $this->TextColor = sprintf('%.3F %.3F %.3F rg',$r/255,$g/255,$b/255);
    $this->ColorFlag = ($this->FillColor!=$this->TextColor);
}

function GetStringWidth($s)
{
    // Get width of a string in the current font
    $s = (string)$s;
    $cw = &$this->CurrentFont['cw'];
    $w = 0;
    $l = strlen($s);
    for($i=0;$i<$l;$i++)
        $w += $cw[$s[$i]];
    return $w*$this->FontSize/1000;
}

function SetLineWidth($width)
{
    // Set line width
    $this->LineWidth = $width;
    if($this->page>0)
        $this->_out(sprintf('%.2F w',$width*$this->k));
}

function Line($x1, $y1, $x2, $y2)
{
    // Draw a line
    $this->_out(sprintf('%.2F %.2F m %.2F %.2F l S',$x1*$this->k,($this->h-$y1)*$this->k,$x2*$this->k,($this->h-$y2)*$this->k));
}

function Rect($x, $y, $w, $h, $style='')
{
    // Draw a rectangle
    if($style=='F')
        $op = 'f';
    elseif($style=='FD' || $style=='DF')
        $op = 'B';
    else
        $op = 'S';
    $this->_out(sprintf('%.2F %.2F %.2F %.2F re %s',$x*$this->k,($this->h-$y)*$this->k,$w*$this->k,-$h*$this->k,$op));
}

function AddFont($family, $style='', $file='')
{
    // Add a TrueType, OpenType or Type1 font
    $family = strtolower($family);
    if($file=='')
        $file = str_replace(' ','',$family).strtolower($style).'.php';
    $style = strtoupper($style);
    if($style=='IB')
        $style = 'BI';
    $fontkey = $family.$style;
    if(isset($this->fonts[$fontkey]))
        return;
    $info = $this->_loadfont($file);
    $info['i'] = count($this->fonts)+1;
    if(!empty($info['diff']))
    {
        // Search existing encodings
        $n = array_search($info['diff'],$this->diffs);
        if(!$n)
        {
            $n = count($this->diffs)+1;
            $this->diffs[$n] = $info['diff'];
        }
        $info['diffn'] = $n;
    }
    if(!empty($info['file']))
    {
        // Embedded font
        if($info['type']=='TrueType')
            $this->FontFiles[$info['file']] = array('length1'=>$info['originalsize']);
        else
            $this->FontFiles[$info['file']] = array('length1'=>$info['size1'], 'length2'=>$info['size2']);
    }
    $this->fonts[$fontkey] = $info;
}

function SetFont($family, $style='', $size=0)
{
    // Select a font; size given in points
    if($family=='')
        $family = $this->FontFamily;
    else
        $family = strtolower($family);
    $style = strtoupper($style);
    if(strpos($style,'U')!==false)
    {
        $this->underline = true;
        $style = str_replace('U','',$style);
    }
    else
        $this->underline = false;
    if($style=='IB')
        $style = 'BI';
    if($size==0)
        $size = $this->FontSizePt;
    // Test if font is already selected
    if($this->FontFamily==$family && $this->FontStyle==$style && $this->FontSizePt==$size)
        return;
    // Test if font is already loaded
    $fontkey = $family.$style;
    if(!isset($this->fonts[$fontkey]))
    {
        // Test if one of the core fonts
        if($family=='arial')
            $family = 'helvetica';
        if(in_array($family,$this->CoreFonts))
        {
            if($family=='symbol' || $family=='zapfdingbats')
                $style = '';
            $fontkey = $family.$style;
            if(!isset($this->fonts[$fontkey]))
                $this->AddFont($family,$style);
        }
        else
            $this->Error('Undefined font: '.$family.' '.$style);
    }
    // Select it
    $this->FontFamily = $family;
    $this->FontStyle = $style;
    $this->FontSizePt = $size;
    $this->FontSize = $size/$this->k;
    $this->CurrentFont = &$this->fonts[$fontkey];
    if($this->page>0)
        $this->_out(sprintf('BT /F%d %.2F Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
}

function SetFontSize($size)
{
    // Set font size in points
    if($this->FontSizePt==$size)
        return;
    $this->FontSizePt = $size;
    $this->FontSize = $size/$this->k;
    if($this->page>0)
        $this->_out(sprintf('BT /F%d %.2F Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
}

function AddLink()
{
    // Create a new internal link
    $n = count($this->links)+1;
    $this->links[$n] = array(0, 0);
    return $n;
}

function SetLink($link, $y=0, $page=-1)
{
    // Set destination of internal link
    if($y==-1)
        $y = $this->y;
    if($page==-1)
        $page = $this->page;
    $this->links[$link] = array($page, $y);
}
 function CellFit($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $scale=false, $force=true)
    {
        //Get string width
        $str_width=$this->GetStringWidth($txt);
 
        //Calculate ratio to fit cell
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $ratio = ($w-$this->cMargin*2)/$str_width;
 
        $fit = ($ratio < 1 || ($ratio > 1 && $force));
        if ($fit)
        {
            if ($scale)
            {
                //Calculate horizontal scaling
                $horiz_scale=$ratio*100.0;
                //Set horizontal scaling
                $this->_out(sprintf('BT %.2F Tz ET',$horiz_scale));
            }
            else
            {
                //Calculate character spacing in points
                $char_space=($w-$this->cMargin*2-$str_width)/max($this->MBGetStringLength($txt)-1,1)*$this->k;
                //Set character spacing
                $this->_out(sprintf('BT %.2F Tc ET',$char_space));
            }
            //Override user alignment (since text will fill up cell)
            $align='';
        }
 
        //Pass on to Cell method
        $this->Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
 
        //Reset character spacing/horizontal scaling
        if ($fit)
            $this->_out('BT '.($scale ? '100 Tz' : '0 Tc').' ET');
    }
    function MBGetStringLength($s)
    {
        if($this->CurrentFont['type']=='Type0')
        {
            $len = 0;
            $nbbytes = strlen($s);
            for ($i = 0; $i < $nbbytes; $i++)
            {
                if (ord($s[$i])<128)
                    $len++;
                else
                {
                    $len++;
                    $i++;
                }
            }
            return $len;
        }
        else
            return strlen($s);
    }
function CellFitSpace($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,false);
    }
function Link($x, $y, $w, $h, $link)
{
    // Put a link on the page
    $this->PageLinks[$this->page][] = array($x*$this->k, $this->hPt-$y*$this->k, $w*$this->k, $h*$this->k, $link);
}

function Text($x, $y, $txt)
{
    // Output a string
    $s = sprintf('BT %.2F %.2F Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
    if($this->underline && $txt!='')
        $s .= ' '.$this->_dounderline($x,$y,$txt);
    if($this->ColorFlag)
        $s = 'q '.$this->TextColor.' '.$s.' Q';
    $this->_out($s);
}

function AcceptPageBreak()
{
    // Accept automatic page break or not
    return $this->AutoPageBreak;
}

function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
{
    // Output a cell
    $k = $this->k;
    if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
    {
        // Automatic page break
        $x = $this->x;
        $ws = $this->ws;
        if($ws>0)
        {
            $this->ws = 0;
            $this->_out('0 Tw');
        }
        $this->AddPage($this->CurOrientation,$this->CurPageSize);
        $this->x = $x;
        if($ws>0)
        {
            $this->ws = $ws;
            $this->_out(sprintf('%.3F Tw',$ws*$k));
        }
    }
    if($w==0)
        $w = $this->w-$this->rMargin-$this->x;
    $s = '';
    if($fill || $border==1)
    {
        if($fill)
            $op = ($border==1) ? 'B' : 'f';
        else
            $op = 'S';
        $s = sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
    }
    if(is_string($border))
    {
        $x = $this->x;
        $y = $this->y;
        if(strpos($border,'L')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
        if(strpos($border,'T')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
        if(strpos($border,'R')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        if(strpos($border,'B')!==false)
            $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
    }
    if($txt!=='')
    {
        if($align=='R')
            $dx = $w-$this->cMargin-$this->GetStringWidth($txt);
        elseif($align=='C')
            $dx = ($w-$this->GetStringWidth($txt))/2;
        else
            $dx = $this->cMargin;
        if($this->ColorFlag)
            $s .= 'q '.$this->TextColor.' ';
        $txt2 = str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
        $s .= sprintf('BT %.2F %.2F Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txt2);
        if($this->underline)
            $s .= ' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
        if($this->ColorFlag)
            $s .= ' Q';
        if($link)
            $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetStringWidth($txt),$this->FontSize,$link);
    }
    if($s)
        $this->_out($s);
    $this->lasth = $h;
    if($ln>0)
    {
        // Go to next line
        $this->y += $h;
        if($ln==1)
            $this->x = $this->lMargin;
    }
    else
        $this->x += $w;
}

function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
{
    // Output text with automatic or explicit line breaks
    $cw = &$this->CurrentFont['cw'];
    if($w==0)
        $w = $this->w-$this->rMargin-$this->x;
    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = str_replace("\r",'',$txt);
    $nb = strlen($s);
    if($nb>0 && $s[$nb-1]=="\n")
        $nb--;
    $b = 0;
    if($border)
    {
        if($border==1)
        {
            $border = 'LTRB';
            $b = 'LRT';
            $b2 = 'LR';
        }
        else
        {
            $b2 = '';
            if(strpos($border,'L')!==false)
                $b2 .= 'L';
            if(strpos($border,'R')!==false)
                $b2 .= 'R';
            $b = (strpos($border,'T')!==false) ? $b2.'T' : $b2;
        }
    }
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $ns = 0;
    $nl = 1;
    while($i<$nb)
    {
        // Get next character
        $c = $s[$i];
        if($c=="\n")
        {
            // Explicit line break
            if($this->ws>0)
            {
                $this->ws = 0;
                $this->_out('0 Tw');
            }
            $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            $ns = 0;
            $nl++;
            if($border && $nl==2)
                $b = $b2;
            continue;
        }
        if($c==' ')
        {
            $sep = $i;
            $ls = $l;
            $ns++;
        }
        $l += $cw[$c];
        if($l>$wmax)
        {
            // Automatic line break
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
                if($this->ws>0)
                {
                    $this->ws = 0;
                    $this->_out('0 Tw');
                }
                $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
            }
            else
            {
                if($align=='J')
                {
                    $this->ws = ($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
                    $this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
                }
                $this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
                $i = $sep+1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            $ns = 0;
            $nl++;
            if($border && $nl==2)
                $b = $b2;
        }
        else
            $i++;
    }
    // Last chunk
    if($this->ws>0)
    {
        $this->ws = 0;
        $this->_out('0 Tw');
    }
    if($border && strpos($border,'B')!==false)
        $b .= 'B';
    $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
    $this->x = $this->lMargin;
}

function Write($h, $txt, $link='')
{
    // Output text in flowing mode
    $cw = &$this->CurrentFont['cw'];
    $w = $this->w-$this->rMargin-$this->x;
    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = str_replace("\r",'',$txt);
    $nb = strlen($s);
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while($i<$nb)
    {
        // Get next character
        $c = $s[$i];
        if($c=="\n")
        {
            // Explicit line break
            $this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1)
            {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            }
            $nl++;
            continue;
        }
        if($c==' ')
            $sep = $i;
        $l += $cw[$c];
        if($l>$wmax)
        {
            // Automatic line break
            if($sep==-1)
            {
                if($this->x>$this->lMargin)
                {
                    // Move to next line
                    $this->x = $this->lMargin;
                    $this->y += $h;
                    $w = $this->w-$this->rMargin-$this->x;
                    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
                    $i++;
                    $nl++;
                    continue;
                }
                if($i==$j)
                    $i++;
                $this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
            }
            else
            {
                $this->Cell($w,$h,substr($s,$j,$sep-$j),0,2,'',0,$link);
                $i = $sep+1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1)
            {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            }
            $nl++;
        }
        else
            $i++;
    }
    // Last chunk
    if($i!=$j)
        $this->Cell($l/1000*$this->FontSize,$h,substr($s,$j),0,0,'',0,$link);
}

function Ln($h=null)
{
    // Line feed; default value is last cell height
    $this->x = $this->lMargin;
    if($h===null)
        $this->y += $this->lasth;
    else
        $this->y += $h;
}

function Image($file, $x=null, $y=null, $w=0, $h=0, $type='', $link='')
{
    // Put an image on the page
    if(!isset($this->images[$file]))
    {
        // First use of this image, get info
        if($type=='')
        {
            $pos = strrpos($file,'.');
            if(!$pos)
                $this->Error('Image file has no extension and no type was specified: '.$file);
            $type = substr($file,$pos+1);
        }
        $type = strtolower($type);
        if($type=='jpeg')
            $type = 'jpg';
        $mtd = '_parse'.$type;
        if(!method_exists($this,$mtd))
            $this->Error('Unsupported image type: '.$type);
        $info = $this->$mtd($file);
        $info['i'] = count($this->images)+1;
        $this->images[$file] = $info;
    }
    else
        $info = $this->images[$file];

    // Automatic width and height calculation if needed
    if($w==0 && $h==0)
    {
        // Put image at 96 dpi
        $w = -96;
        $h = -96;
    }
    if($w<0)
        $w = -$info['w']*72/$w/$this->k;
    if($h<0)
        $h = -$info['h']*72/$h/$this->k;
    if($w==0)
        $w = $h*$info['w']/$info['h'];
    if($h==0)
        $h = $w*$info['h']/$info['w'];

    // Flowing mode
    if($y===null)
    {
        if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
        {
            // Automatic page break
            $x2 = $this->x;
            $this->AddPage($this->CurOrientation,$this->CurPageSize);
            $this->x = $x2;
        }
        $y = $this->y;
        $this->y += $h;
    }

    if($x===null)
        $x = $this->x;
    $this->_out(sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']));
    if($link)
        $this->Link($x,$y,$w,$h,$link);
}

function GetX()
{
    // Get x position
    return $this->x;
}

function SetX($x)
{
    // Set x position
    if($x>=0)
        $this->x = $x;
    else
        $this->x = $this->w+$x;
}

function GetY()
{
    // Get y position
    return $this->y;
}

function SetY($y)
{
    // Set y position and reset x
    $this->x = $this->lMargin;
    if($y>=0)
        $this->y = $y;
    else
        $this->y = $this->h+$y;
}

function SetXY($x, $y)
{
    // Set x and y positions
    $this->SetY($y);
    $this->SetX($x);
}

function Output($name='', $dest='')
{
    // Output PDF to some destination
    if($this->state<3)
        $this->Close();
    $dest = strtoupper($dest);
    if($dest=='')
    {
        if($name=='')
        {
            $name = 'doc.pdf';
            $dest = 'I';
        }
        else
            $dest = 'F';
    }
    switch($dest)
    {
        case 'I':
            // Send to standard output
            $this->_checkoutput();
            if(PHP_SAPI!='cli')
            {
                // We send to a browser
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="'.$name.'"');
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
            }
            echo $this->buffer;
            break;
        case 'D':
            // Download file
            $this->_checkoutput();
            header('Content-Type: application/x-download');
            header('Content-Disposition: attachment; filename="'.$name.'"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            echo $this->buffer;
            break;
        case 'F':
            // Save to local file
            $f = fopen($name,'wb');
            if(!$f)
                $this->Error('Unable to create output file: '.$name);
            fwrite($f,$this->buffer,strlen($this->buffer));
            fclose($f);
            break;
        case 'S':
            // Return as a string
            return $this->buffer;
        default:
            $this->Error('Incorrect output destination: '.$dest);
    }
    return '';
}

/*******************************************************************************
*                                                                              *
*                              Protected methods                               *
*                                                                              *
*******************************************************************************/
function _dochecks()
{
    // Check availability of %F
    if(sprintf('%.1F',1.0)!='1.0')
        $this->Error('This version of PHP is not supported');
    // Check mbstring overloading
    if(ini_get('mbstring.func_overload') & 2)
        $this->Error('mbstring overloading must be disabled');
    // Ensure runtime magic quotes are disabled
    if(get_magic_quotes_runtime())
        @set_magic_quotes_runtime(0);
}

function _checkoutput()
{
    if(PHP_SAPI!='cli')
    {
        if(headers_sent($file,$line))
            $this->Error("Some data has already been output, can't send PDF file (output started at $file:$line)");
    }
    if(ob_get_length())
    {
        // The output buffer is not empty
        if(preg_match('/^(\xEF\xBB\xBF)?\s*$/',ob_get_contents()))
        {
            // It contains only a UTF-8 BOM and/or whitespace, let's clean it
            ob_clean();
        }
        else
            $this->Error("Some data has already been output, can't send PDF file");
    }
}

function _getpagesize($size)
{
    if(is_string($size))
    {
        $size = strtolower($size);
        if(!isset($this->StdPageSizes[$size]))
            $this->Error('Unknown page size: '.$size);
        $a = $this->StdPageSizes[$size];
        return array($a[0]/$this->k, $a[1]/$this->k);
    }
    else
    {
        if($size[0]>$size[1])
            return array($size[1], $size[0]);
        else
            return $size;
    }
}

function _beginpage($orientation, $size)
{
    $this->page++;
    $this->pages[$this->page] = '';
    $this->state = 2;
    $this->x = $this->lMargin;
    $this->y = $this->tMargin;
    $this->FontFamily = '';
    // Check page size and orientation
    if($orientation=='')
        $orientation = $this->DefOrientation;
    else
        $orientation = strtoupper($orientation[0]);
    if($size=='')
        $size = $this->DefPageSize;
    else
        $size = $this->_getpagesize($size);
    if($orientation!=$this->CurOrientation || $size[0]!=$this->CurPageSize[0] || $size[1]!=$this->CurPageSize[1])
    {
        // New size or orientation
        if($orientation=='P')
        {
            $this->w = $size[0];
            $this->h = $size[1];
        }
        else
        {
            $this->w = $size[1];
            $this->h = $size[0];
        }
        $this->wPt = $this->w*$this->k;
        $this->hPt = $this->h*$this->k;
        $this->PageBreakTrigger = $this->h-$this->bMargin;
        $this->CurOrientation = $orientation;
        $this->CurPageSize = $size;
    }
    if($orientation!=$this->DefOrientation || $size[0]!=$this->DefPageSize[0] || $size[1]!=$this->DefPageSize[1])
        $this->PageSizes[$this->page] = array($this->wPt, $this->hPt);
}

function _endpage()
{
    $this->state = 1;
}

function _loadfont($font)
{
    // Load a font definition file from the font directory
    include($this->fontpath.$font);
    $a = get_defined_vars();
    if(!isset($a['name']))
        $this->Error('Could not include font definition file');
    return $a;
}

function _escape($s)
{
    // Escape special characters in strings
    $s = str_replace('\\','\\\\',$s);
    $s = str_replace('(','\\(',$s);
    $s = str_replace(')','\\)',$s);
    $s = str_replace("\r",'\\r',$s);
    return $s;
}

function _textstring($s)
{
    // Format a text string
    return '('.$this->_escape($s).')';
}

function _UTF8toUTF16($s)
{
    // Convert UTF-8 to UTF-16BE with BOM
    $res = "\xFE\xFF";
    $nb = strlen($s);
    $i = 0;
    while($i<$nb)
    {
        $c1 = ord($s[$i++]);
        if($c1>=224)
        {
            // 3-byte character
            $c2 = ord($s[$i++]);
            $c3 = ord($s[$i++]);
            $res .= chr((($c1 & 0x0F)<<4) + (($c2 & 0x3C)>>2));
            $res .= chr((($c2 & 0x03)<<6) + ($c3 & 0x3F));
        }
        elseif($c1>=192)
        {
            // 2-byte character
            $c2 = ord($s[$i++]);
            $res .= chr(($c1 & 0x1C)>>2);
            $res .= chr((($c1 & 0x03)<<6) + ($c2 & 0x3F));
        }
        else
        {
            // Single-byte character
            $res .= "\0".chr($c1);
        }
    }
    return $res;
}

function _dounderline($x, $y, $txt)
{
    // Underline text
    $up = $this->CurrentFont['up'];
    $ut = $this->CurrentFont['ut'];
    $w = $this->GetStringWidth($txt)+$this->ws*substr_count($txt,' ');
    return sprintf('%.2F %.2F %.2F %.2F re f',$x*$this->k,($this->h-($y-$up/1000*$this->FontSize))*$this->k,$w*$this->k,-$ut/1000*$this->FontSizePt);
}

function _parsejpg($file)
{
    // Extract info from a JPEG file
    $a = getimagesize($file);
    if(!$a)
        $this->Error('Missing or incorrect image file: '.$file);
    if($a[2]!=2)
        $this->Error('Not a JPEG file: '.$file);
    if(!isset($a['channels']) || $a['channels']==3)
        $colspace = 'DeviceRGB';
    elseif($a['channels']==4)
        $colspace = 'DeviceCMYK';
    else
        $colspace = 'DeviceGray';
    $bpc = isset($a['bits']) ? $a['bits'] : 8;
    $data = file_get_contents($file);
    return array('w'=>$a[0], 'h'=>$a[1], 'cs'=>$colspace, 'bpc'=>$bpc, 'f'=>'DCTDecode', 'data'=>$data);
}

function _parsepng($file)
{
    // Extract info from a PNG file
    $f = fopen($file,'rb');
    if(!$f)
        $this->Error('Can\'t open image file: '.$file);
    $info = $this->_parsepngstream($f,$file);
    fclose($f);
    return $info;
}

function _parsepngstream($f, $file)
{
    // Check signature
    if($this->_readstream($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10))
        $this->Error('Not a PNG file: '.$file);

    // Read header chunk
    $this->_readstream($f,4);
    if($this->_readstream($f,4)!='IHDR')
        $this->Error('Incorrect PNG file: '.$file);
    $w = $this->_readint($f);
    $h = $this->_readint($f);
    $bpc = ord($this->_readstream($f,1));
    if($bpc>8)
        $this->Error('16-bit depth not supported: '.$file);
    $ct = ord($this->_readstream($f,1));
    if($ct==0 || $ct==4)
        $colspace = 'DeviceGray';
    elseif($ct==2 || $ct==6)
        $colspace = 'DeviceRGB';
    elseif($ct==3)
        $colspace = 'Indexed';
    else
        $this->Error('Unknown color type: '.$file);
    if(ord($this->_readstream($f,1))!=0)
        $this->Error('Unknown compression method: '.$file);
    if(ord($this->_readstream($f,1))!=0)
        $this->Error('Unknown filter method: '.$file);
    if(ord($this->_readstream($f,1))!=0)
        $this->Error('Interlacing not supported: '.$file);
    $this->_readstream($f,4);
    $dp = '/Predictor 15 /Colors '.($colspace=='DeviceRGB' ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w;

    // Scan chunks looking for palette, transparency and image data
    $pal = '';
    $trns = '';
    $data = '';
    do
    {
        $n = $this->_readint($f);
        $type = $this->_readstream($f,4);
        if($type=='PLTE')
        {
            // Read palette
            $pal = $this->_readstream($f,$n);
            $this->_readstream($f,4);
        }
        elseif($type=='tRNS')
        {
            // Read transparency info
            $t = $this->_readstream($f,$n);
            if($ct==0)
                $trns = array(ord(substr($t,1,1)));
            elseif($ct==2)
                $trns = array(ord(substr($t,1,1)), ord(substr($t,3,1)), ord(substr($t,5,1)));
            else
            {
                $pos = strpos($t,chr(0));
                if($pos!==false)
                    $trns = array($pos);
            }
            $this->_readstream($f,4);
        }
        elseif($type=='IDAT')
        {
            // Read image data block
            $data .= $this->_readstream($f,$n);
            $this->_readstream($f,4);
        }
        elseif($type=='IEND')
            break;
        else
            $this->_readstream($f,$n+4);
    }
    while($n);

    if($colspace=='Indexed' && empty($pal))
        $this->Error('Missing palette in '.$file);
    $info = array('w'=>$w, 'h'=>$h, 'cs'=>$colspace, 'bpc'=>$bpc, 'f'=>'FlateDecode', 'dp'=>$dp, 'pal'=>$pal, 'trns'=>$trns);
    if($ct>=4)
    {
        // Extract alpha channel
        if(!function_exists('gzuncompress'))
            $this->Error('Zlib not available, can\'t handle alpha channel: '.$file);
        $data = gzuncompress($data);
        $color = '';
        $alpha = '';
        if($ct==4)
        {
            // Gray image
            $len = 2*$w;
            for($i=0;$i<$h;$i++)
            {
                $pos = (1+$len)*$i;
                $color .= $data[$pos];
                $alpha .= $data[$pos];
                $line = substr($data,$pos+1,$len);
                $color .= preg_replace('/(.)./s','$1',$line);
                $alpha .= preg_replace('/.(.)/s','$1',$line);
            }
        }
        else
        {
            // RGB image
            $len = 4*$w;
            for($i=0;$i<$h;$i++)
            {
                $pos = (1+$len)*$i;
                $color .= $data[$pos];
                $alpha .= $data[$pos];
                $line = substr($data,$pos+1,$len);
                $color .= preg_replace('/(.{3})./s','$1',$line);
                $alpha .= preg_replace('/.{3}(.)/s','$1',$line);
            }
        }
        unset($data);
        $data = gzcompress($color);
        $info['smask'] = gzcompress($alpha);
        if($this->PDFVersion<'1.4')
            $this->PDFVersion = '1.4';
    }
    $info['data'] = $data;
    return $info;
}

function _readstream($f, $n)
{
    // Read n bytes from stream
    $res = '';
    while($n>0 && !feof($f))
    {
        $s = fread($f,$n);
        if($s===false)
            $this->Error('Error while reading stream');
        $n -= strlen($s);
        $res .= $s;
    }
    if($n>0)
        $this->Error('Unexpected end of stream');
    return $res;
}

function _readint($f)
{
    // Read a 4-byte integer from stream
    $a = unpack('Ni',$this->_readstream($f,4));
    return $a['i'];
}

function _parsegif($file)
{
    // Extract info from a GIF file (via PNG conversion)
    if(!function_exists('imagepng'))
        $this->Error('GD extension is required for GIF support');
    if(!function_exists('imagecreatefromgif'))
        $this->Error('GD has no GIF read support');
    $im = imagecreatefromgif($file);
    if(!$im)
        $this->Error('Missing or incorrect image file: '.$file);
    imageinterlace($im,0);
    $f = @fopen('php://temp','rb+');
    if($f)
    {
        // Perform conversion in memory
        ob_start();
        imagepng($im);
        $data = ob_get_clean();
        imagedestroy($im);
        fwrite($f,$data);
        rewind($f);
        $info = $this->_parsepngstream($f,$file);
        fclose($f);
    }
    else
    {
        // Use temporary file
        $tmp = tempnam('.','gif');
        if(!$tmp)
            $this->Error('Unable to create a temporary file');
        if(!imagepng($im,$tmp))
            $this->Error('Error while saving to temporary file');
        imagedestroy($im);
        $info = $this->_parsepng($tmp);
        unlink($tmp);
    }
    return $info;
}

function _newobj()
{
    // Begin a new object
    $this->n++;
    $this->offsets[$this->n] = strlen($this->buffer);
    $this->_out($this->n.' 0 obj');
}

function _putstream($s)
{
    $this->_out('stream');
    $this->_out($s);
    $this->_out('endstream');
}

function _out($s)
{
    // Add a line to the document
    if($this->state==2)
        $this->pages[$this->page] .= $s."\n";
    else
        $this->buffer .= $s."\n";
}

function _putpages()
{
    $nb = $this->page;
    if(!empty($this->AliasNbPages))
    {
        // Replace number of pages
        for($n=1;$n<=$nb;$n++)
            $this->pages[$n] = str_replace($this->AliasNbPages,$nb,$this->pages[$n]);
    }
    if($this->DefOrientation=='P')
    {
        $wPt = $this->DefPageSize[0]*$this->k;
        $hPt = $this->DefPageSize[1]*$this->k;
    }
    else
    {
        $wPt = $this->DefPageSize[1]*$this->k;
        $hPt = $this->DefPageSize[0]*$this->k;
    }
    $filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
    for($n=1;$n<=$nb;$n++)
    {
        // Page
        $this->_newobj();
        $this->_out('<</Type /Page');
        $this->_out('/Parent 1 0 R');
        if(isset($this->PageSizes[$n]))
            $this->_out(sprintf('/MediaBox [0 0 %.2F %.2F]',$this->PageSizes[$n][0],$this->PageSizes[$n][1]));
        $this->_out('/Resources 2 0 R');
        if(isset($this->PageLinks[$n]))
        {
            // Links
            $annots = '/Annots [';
            foreach($this->PageLinks[$n] as $pl)
            {
                $rect = sprintf('%.2F %.2F %.2F %.2F',$pl[0],$pl[1],$pl[0]+$pl[2],$pl[1]-$pl[3]);
                $annots .= '<</Type /Annot /Subtype /Link /Rect ['.$rect.'] /Border [0 0 0] ';
                if(is_string($pl[4]))
                    $annots .= '/A <</S /URI /URI '.$this->_textstring($pl[4]).'>>>>';
                else
                {
                    $l = $this->links[$pl[4]];
                    $h = isset($this->PageSizes[$l[0]]) ? $this->PageSizes[$l[0]][1] : $hPt;
                    $annots .= sprintf('/Dest [%d 0 R /XYZ 0 %.2F null]>>',1+2*$l[0],$h-$l[1]*$this->k);
                }
            }
            $this->_out($annots.']');
        }
        if($this->PDFVersion>'1.3')
            $this->_out('/Group <</Type /Group /S /Transparency /CS /DeviceRGB>>');
        $this->_out('/Contents '.($this->n+1).' 0 R>>');
        $this->_out('endobj');
        // Page content
        $p = ($this->compress) ? gzcompress($this->pages[$n]) : $this->pages[$n];
        $this->_newobj();
        $this->_out('<<'.$filter.'/Length '.strlen($p).'>>');
        $this->_putstream($p);
        $this->_out('endobj');
    }
    // Pages root
    $this->offsets[1] = strlen($this->buffer);
    $this->_out('1 0 obj');
    $this->_out('<</Type /Pages');
    $kids = '/Kids [';
    for($i=0;$i<$nb;$i++)
        $kids .= (3+2*$i).' 0 R ';
    $this->_out($kids.']');
    $this->_out('/Count '.$nb);
    $this->_out(sprintf('/MediaBox [0 0 %.2F %.2F]',$wPt,$hPt));
    $this->_out('>>');
    $this->_out('endobj');
}

function _putfonts()
{
    $nf = $this->n;
    foreach($this->diffs as $diff)
    {
        // Encodings
        $this->_newobj();
        $this->_out('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences ['.$diff.']>>');
        $this->_out('endobj');
    }
    foreach($this->FontFiles as $file=>$info)
    {
        // Font file embedding
        $this->_newobj();
        $this->FontFiles[$file]['n'] = $this->n;
        $font = file_get_contents($this->fontpath.$file,true);
        if(!$font)
            $this->Error('Font file not found: '.$file);
        $compressed = (substr($file,-2)=='.z');
        if(!$compressed && isset($info['length2']))
            $font = substr($font,6,$info['length1']).substr($font,6+$info['length1']+6,$info['length2']);
        $this->_out('<</Length '.strlen($font));
        if($compressed)
            $this->_out('/Filter /FlateDecode');
        $this->_out('/Length1 '.$info['length1']);
        if(isset($info['length2']))
            $this->_out('/Length2 '.$info['length2'].' /Length3 0');
        $this->_out('>>');
        $this->_putstream($font);
        $this->_out('endobj');
    }
    foreach($this->fonts as $k=>$font)
    {
        // Font objects
        $this->fonts[$k]['n'] = $this->n+1;
        $type = $font['type'];
        $name = $font['name'];
        if($type=='Core')
        {
            // Core font
            $this->_newobj();
            $this->_out('<</Type /Font');
            $this->_out('/BaseFont /'.$name);
            $this->_out('/Subtype /Type1');
            if($name!='Symbol' && $name!='ZapfDingbats')
                $this->_out('/Encoding /WinAnsiEncoding');
            $this->_out('>>');
            $this->_out('endobj');
        }
        elseif($type=='Type1' || $type=='TrueType')
        {
            // Additional Type1 or TrueType/OpenType font
            $this->_newobj();
            $this->_out('<</Type /Font');
            $this->_out('/BaseFont /'.$name);
            $this->_out('/Subtype /'.$type);
            $this->_out('/FirstChar 32 /LastChar 255');
            $this->_out('/Widths '.($this->n+1).' 0 R');
            $this->_out('/FontDescriptor '.($this->n+2).' 0 R');
            if(isset($font['diffn']))
                $this->_out('/Encoding '.($nf+$font['diffn']).' 0 R');
            else
                $this->_out('/Encoding /WinAnsiEncoding');
            $this->_out('>>');
            $this->_out('endobj');
            // Widths
            $this->_newobj();
            $cw = &$font['cw'];
            $s = '[';
            for($i=32;$i<=255;$i++)
                $s .= $cw[chr($i)].' ';
            $this->_out($s.']');
            $this->_out('endobj');
            // Descriptor
            $this->_newobj();
            $s = '<</Type /FontDescriptor /FontName /'.$name;
            foreach($font['desc'] as $k=>$v)
                $s .= ' /'.$k.' '.$v;
            if(!empty($font['file']))
                $s .= ' /FontFile'.($type=='Type1' ? '' : '2').' '.$this->FontFiles[$font['file']]['n'].' 0 R';
            $this->_out($s.'>>');
            $this->_out('endobj');
        }
        else
        {
            // Allow for additional types
            $mtd = '_put'.strtolower($type);
            if(!method_exists($this,$mtd))
                $this->Error('Unsupported font type: '.$type);
            $this->$mtd($font);
        }
    }
}

function _putimages()
{
    foreach(array_keys($this->images) as $file)
    {
        $this->_putimage($this->images[$file]);
        unset($this->images[$file]['data']);
        unset($this->images[$file]['smask']);
    }
}

function _putimage(&$info)
{
    $this->_newobj();
    $info['n'] = $this->n;
    $this->_out('<</Type /XObject');
    $this->_out('/Subtype /Image');
    $this->_out('/Width '.$info['w']);
    $this->_out('/Height '.$info['h']);
    if($info['cs']=='Indexed')
        $this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
    else
    {
        $this->_out('/ColorSpace /'.$info['cs']);
        if($info['cs']=='DeviceCMYK')
            $this->_out('/Decode [1 0 1 0 1 0 1 0]');
    }
    $this->_out('/BitsPerComponent '.$info['bpc']);
    if(isset($info['f']))
        $this->_out('/Filter /'.$info['f']);
    if(isset($info['dp']))
        $this->_out('/DecodeParms <<'.$info['dp'].'>>');
    if(isset($info['trns']) && is_array($info['trns']))
    {
        $trns = '';
        for($i=0;$i<count($info['trns']);$i++)
            $trns .= $info['trns'][$i].' '.$info['trns'][$i].' ';
        $this->_out('/Mask ['.$trns.']');
    }
    if(isset($info['smask']))
        $this->_out('/SMask '.($this->n+1).' 0 R');
    $this->_out('/Length '.strlen($info['data']).'>>');
    $this->_putstream($info['data']);
    $this->_out('endobj');
    // Soft mask
    if(isset($info['smask']))
    {
        $dp = '/Predictor 15 /Colors 1 /BitsPerComponent 8 /Columns '.$info['w'];
        $smask = array('w'=>$info['w'], 'h'=>$info['h'], 'cs'=>'DeviceGray', 'bpc'=>8, 'f'=>$info['f'], 'dp'=>$dp, 'data'=>$info['smask']);
        $this->_putimage($smask);
    }
    // Palette
    if($info['cs']=='Indexed')
    {
        $filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
        $pal = ($this->compress) ? gzcompress($info['pal']) : $info['pal'];
        $this->_newobj();
        $this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
        $this->_putstream($pal);
        $this->_out('endobj');
    }
}

function _putxobjectdict()
{
    foreach($this->images as $image)
        $this->_out('/I'.$image['i'].' '.$image['n'].' 0 R');
}

function _putresourcedict()
{
    $this->_out('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
    $this->_out('/Font <<');
    foreach($this->fonts as $font)
        $this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
    $this->_out('>>');
    $this->_out('/XObject <<');
    $this->_putxobjectdict();
    $this->_out('>>');
}

function _putresources()
{
    $this->_putfonts();
    $this->_putimages();
    // Resource dictionary
    $this->offsets[2] = strlen($this->buffer);
    $this->_out('2 0 obj');
    $this->_out('<<');
    $this->_putresourcedict();
    $this->_out('>>');
    $this->_out('endobj');
}

function _putinfo()
{
    $this->_out('/Producer '.$this->_textstring('FPDF '.FPDF_VERSION));
    if(!empty($this->title))
        $this->_out('/Title '.$this->_textstring($this->title));
    if(!empty($this->subject))
        $this->_out('/Subject '.$this->_textstring($this->subject));
    if(!empty($this->author))
        $this->_out('/Author '.$this->_textstring($this->author));
    if(!empty($this->keywords))
        $this->_out('/Keywords '.$this->_textstring($this->keywords));
    if(!empty($this->creator))
        $this->_out('/Creator '.$this->_textstring($this->creator));
    $this->_out('/CreationDate '.$this->_textstring('D:'.@date('YmdHis')));
}

function _putcatalog()
{
    $this->_out('/Type /Catalog');
    $this->_out('/Pages 1 0 R');
    if($this->ZoomMode=='fullpage')
        $this->_out('/OpenAction [3 0 R /Fit]');
    elseif($this->ZoomMode=='fullwidth')
        $this->_out('/OpenAction [3 0 R /FitH null]');
    elseif($this->ZoomMode=='real')
        $this->_out('/OpenAction [3 0 R /XYZ null null 1]');
    elseif(!is_string($this->ZoomMode))
        $this->_out('/OpenAction [3 0 R /XYZ null null '.sprintf('%.2F',$this->ZoomMode/100).']');
    if($this->LayoutMode=='single')
        $this->_out('/PageLayout /SinglePage');
    elseif($this->LayoutMode=='continuous')
        $this->_out('/PageLayout /OneColumn');
    elseif($this->LayoutMode=='two')
        $this->_out('/PageLayout /TwoColumnLeft');
}

function _putheader()
{
    $this->_out('%PDF-'.$this->PDFVersion);
}

function _puttrailer()
{
    $this->_out('/Size '.($this->n+1));
    $this->_out('/Root '.$this->n.' 0 R');
    $this->_out('/Info '.($this->n-1).' 0 R');
}

function _enddoc()
{
    $this->_putheader();
    $this->_putpages();
    $this->_putresources();
    // Info
    $this->_newobj();
    $this->_out('<<');
    $this->_putinfo();
    $this->_out('>>');
    $this->_out('endobj');
    // Catalog
    $this->_newobj();
    $this->_out('<<');
    $this->_putcatalog();
    $this->_out('>>');
    $this->_out('endobj');
    // Cross-ref
    $o = strlen($this->buffer);
    $this->_out('xref');
    $this->_out('0 '.($this->n+1));
    $this->_out('0000000000 65535 f ');
    for($i=1;$i<=$this->n;$i++)
        $this->_out(sprintf('%010d 00000 n ',$this->offsets[$i]));
    // Trailer
    $this->_out('trailer');
    $this->_out('<<');
    $this->_puttrailer();
    $this->_out('>>');
    $this->_out('startxref');
    $this->_out($o);
    $this->_out('%%EOF');
    $this->state = 3;
}
// End of class
}

// Handle special IE contype request
if(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT']=='contype')
{
    header('Content-Type: application/pdf');
    exit;
}
?>

		
