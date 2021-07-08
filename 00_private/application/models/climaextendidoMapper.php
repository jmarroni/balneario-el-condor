<?php 

class Application_Model_climaextendidoMapper extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string 
     */
    protected $_name = 'clima_extendido'; 

    public function get($id = NULL){

        $select = $this->select(); 
        if (isset($id)){
            {$select->where('id = ?',$id);}
        } 
        $climaextendidoArray = $this->_fetch($select);
        if (sizeof($climaextendidoArray) > 0) 
            if (isset($id))
                    return new Application_Model_DbTable_climaextendido($climaextendidoArray[0]);
            else{
                    $arrCe = array();
                    foreach ($climaextendidoArray as $key => $value) {
                       $arrCe[] = new Application_Model_DbTable_climaextendido($value);
                    }
                    return $arrCe;
                }
        else return array();
    }


    public function save(array $option,$id = NULL) {
        if (isset($id)) {
            try {
                $this->_db->update('clima_extendido',$option,"id = ".$id);
            } catch (Exception $e) {
                echo "Caught exception: " . get_class($e) . "\n";
                echo "Message: " . $e->getMessage() . "\n";
            }
        }else{
            try {
                $this->_db->insert('clima_extendido',$option);
                return $this->_db->lastInsertId('clima_extendido');
            } catch (Exception $e) {
                echo "Caught exception: " . get_class($e) . "\n";
                echo "Message: " . $e->getMessage() . "\n";
            }
        }
    }

    public function remove($id){
        try {
            $this->_db->delete('clima_extendido',"id = ".$id);
            return true;    
        } catch (Exception $e) {
            return false;
        }
        
    }

}

