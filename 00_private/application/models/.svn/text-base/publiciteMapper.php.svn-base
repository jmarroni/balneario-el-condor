<?php

class Application_Model_publiciteMapper extends Zend_Db_Table_Abstract
{
    /**
     * Variable que contiene el nombre de la tabla.
     * @var string
     */
    protected $_name = 'publicite'; 

    public function get($pu_id = NULL){

        $select = $this->select(); 
        if (isset($pu_id)){
            {$select->where('pu_id = ?',$pu_id);}
        }
        //echo $select;exit();
        $publiciteArray = $this->_fetch($select);
        
        if (sizeof($publiciteArray) > 0) 
            if (isset($pu_id))
                    return new Application_Model_DbTable_publicite($publiciteArray[0]);
            else{
                    $arrPu = array();
                    foreach ($publiciteArray as $key => $value) {
                       $arrPu[] = new Application_Model_DbTable_publicite($value);
                    }
                    return $arrPu;
                }
        else return array();
    }


    public function save(array $option,$id = NULL) {
        if (isset($id)) {
            try {
                $this->_db->update('publicite',$option,"pu_id = ".$id);
            } catch (Exception $e) {
                echo "Caught exception: " . get_class($e) . "\n";
                echo "Message: " . $e->getMessage() . "\n";
            }
        }else{
            try {
                $this->_db->insert('publicite',$option);
                return $this->_db->lastInsertId('publicite');
            } catch (Exception $e) {
                echo "Caught exception: " . get_class($e) . "\n";
                echo "Message: " . $e->getMessage() . "\n";
            }
        }
    }

    public function remove($id){
        try {
            $this->_db->delete('publicite',"pu_id = ".$id);
            return true;    
        } catch (Exception $e) {
            return false;
        }
        
    }
    
}

