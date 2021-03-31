<?php
    class database{
        private $db;
        function connect(){
            $this->db = new mysqli("localhost","root","","shopshock");
            $this->db->set_charset("UTF8");
            if($this->db->connect_errno) echo "Error something";
        } 
        function query($sql, $option=MYSQLI_NUM){
            $result = $this->db->query($sql);
            return $result->fetch_all($option);
        }
        function exec($sql){ return $this->db->query($sql);}
        function close(){ $this->db->close();}
    }


    //return $result;
?>