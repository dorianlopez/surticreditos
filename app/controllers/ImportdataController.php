<?php

class ImportdataController extends ControllerBase
{
    public function indexAction()
    {
        
    }
    
    public function importfileoneAction()
    {
        if($this->request->isPost()){  
            try {  
                $update = $this->request->getPost("update");
                $update = ($update == 'on' ? 1 : 0);     
                
                if ($_FILES['csvone']['size'] > 3145728){
                    return $this->set_json_response(array('El archivo CSV no puede ser mayor a 3 MB de peso'), 403);
                }

                if ($_FILES['csvone']['size'] > 0) {

                    $fileinfo = pathinfo($_FILES['csvone']['name']);

                    if(strtolower(trim($fileinfo["extension"])) != "csv")
                    {
                        return $this->set_json_response(array('Por favor seleccione un archivo de tipo CSV'), 403);
                    }

                    $csv = $_FILES['csvone']['tmp_name'];
                    $handle = fopen($csv,'r');

                    $values = array();
                    $txt = array();

                    while($data = fgetcsv($handle,1000,";","'")){
                        if($data[0]){
                            $values[] = "$data[0]";
                        }
                    }                                

                    foreach ($values as $key => $value) {
                        $c = substr($value, 0, 11);
                        $n = substr($value, 11, 40);
                        $cl = substr($value, 51, 1);
                        $d = substr($value, 52, 30);
                        $t = substr($value, 82, 7);
                        $cel = substr($value, 90, 11);
                        $e = substr($value, 102, 59);                    
                        $ci = substr($value, 163, 5);

                        $ce = ltrim($c,'0');

                        $id = trim($ce);
                        $name = trim($n);
                        $class = trim($cl);
                        $address = trim($d);
                        $phone = trim($t);
                        $celphone = trim($cel);
                        $email = trim($e);
                        $city = trim($ci);

                        if(!empty($ce)){
                            $txt[] = "($id,2," . time() . "," . time() . ",0,'$id','$name','$class','$address','$phone - $celphone','$email','$city')";
                            $text = implode(", ", $txt);
                        }                    
                    }

                    if(!$update){
                        $sql = "INSERT IGNORE INTO user (idUser, idRole, created, updated, status, password, name, class, address, phone, email, city) VALUES {$text}";                    
                    }  
                    else {
                        $sql = "INSERT IGNORE INTO user (idUser, idRole, created, updated, status, password, name, class, address, phone, email, city) VALUES {$text} ON DUPLICATE KEY UPDATE updated = VALUES(updated), name = VALUES(name), class = VALUES(class), address = VALUES(address), phone = VALUES(phone), email = VALUES(email), city = VALUES(city)";                   
                    } 

                    $result = $this->db->execute($sql);
                    
                    return $this->set_json_response(array('El archivo se importo exitosamente'), 200);                                               
                }
            }
            catch(Exception $e) {
                $this->logger->log("Exception while inserting users: {$e->getMessage()}");
                return $this->set_json_response(array("Ha ocurrido un error, por favor contacte al administrador"), 403);            
            }
        }
    }
    
    public function importfiletwoAction()
    {
        try {
            if ($_FILES['csvtwo']['size'] > 3145728){
                return $this->set_json_response(array('El archivo CSV no puede ser mayor a 3 MB de peso'), 403);
            }
            
            if ($_FILES['csvtwo']['size'] > 0) {

                $fileinfo = pathinfo($_FILES['csvtwo']['name']);
                
                if(strtolower(trim($fileinfo["extension"])) != "csv")
                {
                    return $this->set_json_response(array('Por favor seleccione un archivo de tipo CSV'), 403);
                }
                   
                $csv = $_FILES['csvtwo']['tmp_name'];
                $handle = fopen($csv,'r');
                
                $values = array();
                $text = array();
                
                while($data = fgetcsv($handle,1000,";","'")){
                    if($data[0]){
                        $values[] = "$data[0]";
                    }
                }
                
                foreach ($values as $key => $value) {
                    $c = substr($value, 0, 11);
                    $cu = substr($value, 11, 7);
                    $v = substr($value, 18, 10);
                    $aa = substr($value, 28, 4);
                    $mm = substr($value, 32, 2);
                    $dd = substr($value, 34, 2);
                    $s = substr($value, 36, 10);
                    
                    $ced = ltrim($c,'0');
                    $cue = ltrim($cu,'0');
                    $val = ltrim($v,'0');
                    $sal = ltrim($s,'0');
                    
                    $cedula = trim($ced);
                    $cuenta = trim($cue);
                    $valor = trim($val);
                    $año = trim($aa);
                    $mes = trim($mm);
                    $dia = trim($dd);
                    $saldo = trim($sal);
                    
                    $fecha = "$año-$mes-$dia";
                    
                    $txt[] = "($cuenta,$cedula,'$fecha',$valor,$saldo)";
                    $text = implode(", ", $txt);
                                        
                }
                
                $sql1 = "SET FOREIGN_KEY_CHECKS = 0";
                $result1 = $this->db->execute($sql1);
                
                $sqlremove = "TRUNCATE TABLE buy";
                $resultremove = $this->db->execute($sqlremove);
                
                $sql = "INSERT IGNORE INTO buy (idBuy, idUser, date, value, debt) VALUES {$text} ON DUPLICATE KEY UPDATE date = VALUES(date), value = VALUES(value), debt = VALUES(debt)";
                $result = $this->db->execute($sql);
                
                $sql2 = "SET FOREIGN_KEY_CHECKS = 1";
                $result1 = $this->db->execute($sql2);

                return $this->set_json_response(array('El archivo se importo exitosamente'), 200);                                               
            }
        }
        catch(Exception $e) {
            $this->logger->log("Exception while inserting buys: {$e->getMessage()}");
            return $this->set_json_response(array("Ha ocurrido un error, por favor contacte al administrador"), 403);
        }        
    }
    
    public function importfilethreeAction()
    {
         try {
            if ($_FILES['csvthree']['size'] > 20971520){
                return $this->set_json_response(array('El archivo CSV no puede ser mayor a 20 MB de peso'), 403);
            }
            
            if ($_FILES['csvthree']['size'] > 0) {

                $fileinfo = pathinfo($_FILES['csvthree']['name']);
                
                if(strtolower(trim($fileinfo["extension"])) != "csv")
                {
                    return $this->set_json_response(array('Por favor seleccione un archivo de tipo CSV'), 403);
                }
                   
                $csv = $_FILES['csvthree']['tmp_name'];
                $handle = fopen($csv,'r');
                
                $values = array();
                
                while($data = fgetcsv($handle,1000,";","'")){
                    if($data[0]){
                        $values[] = "$data[0]";
                    }
                }                
                
                foreach ($values as $key => $value) {
                    $ce = substr($value, 0, 11);
                    $cu = substr($value, 11, 7);
                    $r = substr($value, 18, 7);
                    $rm = substr($value, 25, 7);
                    $aa = substr($value, 32, 4);
                    $mm = substr($value, 36, 2);
                    $dd = substr($value, 38, 2);
                    $v = substr($value, 40, 11);
                    
                    $ced = ltrim($ce,'0');
                    $cue = ltrim($cu,'0');
                    $rec = ltrim($r,'0');
                    $rem = ltrim($rm,'0');
                    $va = ltrim($v,'0');
                    
                    $cedula = trim($ced);
                    $cuenta = trim($cue);                    
                    $recibo = trim($rec);
                    $valor = trim($va);
                    $año = trim($aa);
                    $mes = trim($mm);
                    $dia = trim($dd);
                    $rmanual = trim($rem);
                    
                    $fecha = "$año-$mes-$dia";
                    
                    if(!empty($recibo)){
                        $txt[] = "$recibo,$cuenta,$valor,$fecha";   
                    }
                }
                
                $filename = $this->path->path . $this->path->tmpfolder . uniqid() . time() . ".csv";
                $fp = fopen($filename , 'w');

                foreach ($txt as $data) {
                    $val = explode(",", $data);
                    fputcsv($fp, $val);
                }                    

                fclose($fp);
                
                $sql1 = "SET FOREIGN_KEY_CHECKS = 0";
                $result1 = $this->db->execute($sql1);
                
                $sqlremove = "TRUNCATE TABLE payment";
                $resultremove = $this->db->execute($sqlremove);
                
                $sql_db_mode = "SET session sql_mode=''";
                
                $importfile = "LOAD DATA INFILE '{$filename}' IGNORE INTO TABLE payment CHARACTER SET UTF8 FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'"
                . " (idPayment, idBuy, receiptValue, date)";
                
                $sql_db_mode_strict = "SET session sql_mode='strict_all_tables'";
                
                $this->db->execute($sql_db_mode);
                $this->db->execute($importfile);
                $this->db->execute($sql_db_mode_strict);
                
                $sql2 = "SET FOREIGN_KEY_CHECKS = 1";
                $result2 = $this->db->execute($sql2);

                return $this->set_json_response(array('El archivo se importo exitosamente'), 200);                                               
            }
        }
        catch(Exception $e) {
            $this->logger->log("Exception while inserting buys: {$e->getMessage()}");
            return $this->set_json_response(array("Ha ocurrido un error, por favor contacte al administrador"), 403);
        }
    }
    
    public function importfilefourAction()
    {
         try {
            if ($_FILES['csvfour']['size'] > 3145728){
                return $this->set_json_response(array('El archivo CSV no puede ser mayor a 3 MB de peso'), 403);
            }
            
            if ($_FILES['csvfour']['size'] > 0) {

                $fileinfo = pathinfo($_FILES['csvfour']['name']);
                
                if(strtolower(trim($fileinfo["extension"])) != "csv")
                {
                    return $this->set_json_response(array('Por favor seleccione un archivo de tipo CSV'), 403);
                }
                   
                $csv = $_FILES['csvfour']['tmp_name'];
                $handle = fopen($csv,'r');
                
                $values = array();
                $text = array();
                
                while($data = fgetcsv($handle,1000,";","'")){
                    if($data[0]){
                        $values[] = "$data[0]";
                    }
                }
                
                foreach ($values as $key => $value) {
                    $cu = substr($value, 0, 7);
                    $re = substr($value, 7, 14);
                    $no = substr($value, 21, 20);
                    $ca = substr($value, 41, 5);                   
                    
                    $cue = ltrim($cu,'0');
                    $ref = ltrim($re,'0');
                    $nom = ltrim($no,'0');
                    $can = ltrim($ca,'0');                                        
                                        
                    $cuenta = trim($cue);                    
                    $referencia = trim($ref);
                    $nombre = trim($nom);
                    $cantidad = trim($can);
                    
                    $caracteres = array('"',"'");
                    $replace = array("p","p");
                    
                    $name = str_replace($caracteres, $replace, $nombre);
                    $reference = str_replace($caracteres, $replace, $referencia);
                    
                    $txt[] = "(null,$cuenta,'$reference','$name',$cantidad)";
                    $text = implode(", ", $txt);
                                        
                }                                
                
                $sql1 = "SET FOREIGN_KEY_CHECKS = 0";
                $result1 = $this->db->execute($sql1);
                
                $sqlremove = "TRUNCATE TABLE article";
                $resultremove = $this->db->execute($sqlremove);
                
                $sql = "INSERT IGNORE INTO article (idArticle, idBuy, reference, name, quantity) VALUES {$text}";
                $result = $this->db->execute($sql);
                
                $sql2 = "SET FOREIGN_KEY_CHECKS = 1";
                $result2 = $this->db->execute($sql2);

                return $this->set_json_response(array('El archivo se importo exitosamente'), 200);                                               
            }
        }
        catch(Exception $e) {
            $this->logger->log("Exception while inserting buys: {$e->getMessage()}");
            return $this->set_json_response(array("Ha ocurrido un error, por favor contacte al administrador"), 403);
        }
    }        
}