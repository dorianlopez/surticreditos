<?php

namespace Surticreditos\Misc;

class ValidateFile
{
    public $logger;
    public $path;
    public $file;
    public $txt;
    public $db;
    public $filename;

    public function __construct() {
        $this->logger = \Phalcon\DI::getDefault()->get('logger');
        $this->path = \Phalcon\DI::getDefault()->get('path');
        $this->db = \Phalcon\DI::getDefault()->get('db');
    }
    
    public function setFile($file)
    {
        $this->file = $file;
    }
    
    public function validate()
    {
        if ($this->file['size'] > 20971520){
            throw new \InvalidArgumentException('El archivo CSV no puede ser mayor a 20 MB de peso');
        }
        
        if ($this->file['size'] < 0){
            throw new \InvalidArgumentException('No ha enviado un archivo a procesar');            
        }
        
        $fileinfo = pathinfo($this->file['name']);                
        
        if(strtolower(trim($fileinfo["extension"])) != "csv"){
            throw new \InvalidArgumentException('Por favor seleccione un archivo de tipo CSV');
        }
    }
    
    public function formatFileForPayment()
    {
        $csv = $this->file['tmp_name'];
        $handle = \fopen($csv,'r');

        $values = array();

        while($data = \fgetcsv($handle,1000,";","'")){
            if($data[0]){
                $values[] = "$data[0]";
            }
        }                

        foreach ($values as $key => $value) {
            $ce = \substr($value, 0, 11);
            $cu = \substr($value, 11, 7);
            $r = \substr($value, 18, 7);
            $rm = \substr($value, 25, 7);
            $aa = \substr($value, 32, 4);
            $mm = \substr($value, 36, 2);
            $dd = \substr($value, 38, 2);
            $v = \substr($value, 40, 11);

            $ced = \ltrim($ce,'0');
            $cue = \ltrim($cu,'0');
            $rec = \ltrim($r,'0');
            $rem = \ltrim($rm,'0');
            $va = \ltrim($v,'0');

            $cedula = \trim($ced);
            $cuenta = \trim($cue);                    
            $recibo = \trim($rec);
            $valor = \trim($va);
            $año = \trim($aa);
            $mes = \trim($mm);
            $dia = \trim($dd);
            $rmanual = \trim($rem);

            $fecha = "$año-$mes-$dia";

            if(!empty($recibo)){
                $txt[] = "$recibo,$cuenta,$valor,$fecha";   
            }            
        }
        
        $this->txt = $txt;
    }
    
    public function generateFinalFile()
    {
        $filename = $this->path->path . $this->path->tmpfolder . \uniqid() . \time() . ".csv";
        // Abre el fichero para obtener el contenido existente
        $actual = \file_get_contents($filename);
        // Añade una nueva persona al fichero
        $actual .= \implode(\PHP_EOL, $this->txt);
        // Escribe el contenido al fichero
        \file_put_contents($filename, $actual);
        
        $this->filename = $filename;
    }
    
    public function loadDataOnDb($table) {
        $this->disableForeingKeys();
        $this->truncateTable($table);
        $fields = $this->getFields($table);
        $this->load($table, $fields);
        $this->enableForeingKeys();
    }

    private function getFields($table) {
        $fields = null;
        
        if ($table == "payment") {
            $fields = " (idPayment, idBuy, receiptValue, date)";
        }
        
        return $fields;
    }
    
    private function load($table, $fields) {
        $sql_db_mode = "SET session sql_mode=''";
                
        $importfile = "LOAD DATA INFILE '{$this->filename}' IGNORE INTO TABLE {$table} CHARACTER SET UTF8 FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'"
        . " {$fields}";

        $sql_db_mode_strict = "SET session sql_mode='strict_all_tables'";

        $this->db->execute($sql_db_mode);
        $this->db->execute($importfile);
        $this->db->execute($sql_db_mode_strict);
    }


    private function disableForeingKeys()
    {
        $sql1 = "SET FOREIGN_KEY_CHECKS = 0";
        $result1 = $this->db->execute($sql1);
    }
    
    private function enableForeingKeys()
    {
        $sql1 = "SET FOREIGN_KEY_CHECKS = 1";
        $result1 = $this->db->execute($sql1);
    }
    
    private function truncateTable($table)
    {
        $sqlremove = "TRUNCATE TABLE {$table}";
        $resultremove = $this->db->execute($sqlremove);
    }
}