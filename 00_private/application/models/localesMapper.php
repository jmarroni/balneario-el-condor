<?php

class Application_Model_localesMapper extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'locales';
    
    public function get($lo_id = NULL, $param = array()){
    	$select = $this->select();
    	$select->setIntegrityCheck(false)
    			->from(array('lo' => 'locales'),array(
    									new Zend_Db_Expr("(acos(sin(radians({$param["latitud"]})) * sin(radians(lo_latitud)) + 
cos(radians({$param["latitud"]})) * cos(radians(lo_latitud)) * 
cos(radians({$param["longitud"]}) - radians(lo_longitud))) * 6378) as distancia"),
    								'lo_id',
									'lo_nombre',
									'lo_latitud',
									'lo_longitud',
    								new Zend_Db_Expr("IF(1>2,2,3) as peso"),
									'lo_es_principal',
									'lo_razon_social',
									'lo_calle',
									'lo_altura',
									'lo_piso',
									'lo_oficina',
									'lo_depto',
									'lo_codigo_postal',
									'lo_mail',
									'lo_telefono_1',
									'lo_telefono_2',
									'lo_telefono_3',
									'em_id',
									'ci_id'))
    			->joinLeft(array('rlc' => 'relacion_local_categoria'),'rlc.lo_id = lo.lo_id',array())
    			->joinLeft(array('ca' => 'categorias'),'rlc.ca_id = ca.ca_id',array());
        if (isset($lo_id)){
            {$select->where('lo_id = ?',$lo_id);}
        }
        
        if (isset($param["palabra"])){
        	$select->where("lo_nombre like '%{$param["palabra"]}%' or ca_nombre like '%{$param["palabra"]}%'");
        	
        } 
        $select->where("lo_latitud <> ''");
        $select->where("lo_longitud <> ''");
        $select->having("distancia < 1000000");
        $select->limit((isset($param["limit"]))?$param["limit"]:10,(isset($param["offset"]))?$param["offset"]:0);
        $select->order($param["order"]);
        $arrLocales = $this->_fetch($select);

        if (sizeof($arrLocales) > 0) 
            if (isset($lo_id))
                    return new Application_Model_DbTable_locales($arrLocales[0]);
            else{
                    $arrBudget = array();
                    foreach ($arrLocales as $key => $value) {
                       $arrBudget[] = new Application_Model_DbTable_locales($value);
                    }
                    return $arrBudget;
                }
        else return array();
    }
}