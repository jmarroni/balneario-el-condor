<?php 
class PruebaController extends Zend_Controller_Action
{

	public function init()
    {
    }

    public function indexAction()
    {
		
    }

    public function enviarAction(){

		// Busco dos noticias
		$novedades = new Application_Model_DbTable_novedades();
		$arrNovedades = $novedades->get(3);
    	$this->view->arrNovedades = $arrNovedades;
    	
        // Traigo recetas
    	$recetas = new Application_Model_DbTable_recetas();
    	$arrRecetas = $recetas->get(1);
    	$this->view->arrRecetas = $arrRecetas;

        $this->view->imagen = explode("/", $arrRecetas[0]['re_imagen_bottom']);

    	// Traigo imagenes
    	$imagenes = new Application_Model_DbTable_imagenes();
    	$arrImagenes = $imagenes->get(3);

    	$this->view->arrImagenes = $arrImagenes;
		

        //Traigo la tabla de mareas
        $mareas = $this->generarTabla();
        
        $dias = array();

        //Guardo en un nuevo array los datos de los dias y las mareas
        for ($i=0; $i < count($mareas); $i++) { 
            
            //Domingo
            if (is_array($mareas['Sunday'][$i])) {
                
                $mareas[0] = $mareas['Sunday'][$i];
                
            }elseif (is_array($mareas['Sunday'][''])) {
                
                $mareas[0] = $mareas['Sunday'][''];

            }

            //Lunes
            if (is_array($mareas['Monday'][$i])) {
                
                $mareas[1] = $mareas['Monday'][$i];

            }elseif(is_array($mareas['Monday'][''])){

                $mareas[1] = $mareas['Monday'][''];

            }

            //Martes
            if (is_array($mareas['Tuesday'][$i])) {
                
                $mareas[2] = $mareas['Tuesday'][$i];
                

            }elseif(is_array($mareas['Tuesday'][''])){

                $mareas[2] = $mareas['Tuesday'][''];
                

            }

            //Miercoles
            if (is_array($mareas['Wednesday'][$i])) {
                
                $mareas[3] = $mareas['Wednesday'][$i];

            }elseif(is_array($mareas['Wednesday'][''])){
                $mareas[3] = $mareas['Wednesday'][''];

            }

            //Jueves
            if (is_array($mareas['Thursday'][$i])) {
                   
                $mareas[4] = $mareas['Thursday'][$i];

            }elseif(is_array($mareas['Thursday'][''])){

                $mareas[4] = $mareas['Thursday'][''];

            } 

            //Viernes
            if (is_array($mareas['Friday'][$i])) {
                  
                $mareas[5] = $mareas['Friday'][$i];

            }elseif(is_array($mareas['Friday'][''])){

                $mareas[5] = $mareas['Friday'][''];

            }  

            //Sabado
            if (is_array($mareas['Saturday'][$i])) {
                
                $mareas[6] = $mareas['Saturday'][$i];

            }elseif(is_array($mareas['Saturday'][''])){

                $mareas[6] = $mareas['Saturday'][''];

            }           

        }
        
        $this->view->mareas = $mareas; //Array con los datos de las mareas


		$html = $this->view;
     	$html = $this->view->render("/templatemail/newsletter.phtml");
		 
        //Guardo el HTML en un archivo  
		$path = dirname(dirname(dirname(dirname(__FILE__))));
        $fecha = date("Y-m-d H:m:s");
		$archivo = fopen($path."/htdocs/email_archivo/".$fecha."_newsletter.txt", 'a');
        fwrite($archivo, $html);
            


        echo $html;     			
		exit();
    }


     private function generarTabla(){
        $Mareas = new Application_Model_DbTable_mareas();

        $fecha = date('Y-m-j');
        $nuevafecha = strtotime ( '+1 day' , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'Y-m-j' , $nuevafecha );

        $arrMareasDiarias = $Mareas->get('El Condor',$nuevafecha,7);

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

    public function updateAction(){

        $vigente = $this->_getParam("vigente");
        $id_clasificado = $this->_getParam("id");

        echo "id: ".$id_clasificado." vigente: ".$vigente;exit();

    }
}

?>
