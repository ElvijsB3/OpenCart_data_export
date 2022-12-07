<?php

class DatabaseGateway
{
    public $error = NULL;

    private function dbConnect(){
        try {
            //Datubāzes pieslēgums
            $db_name = '';
            $db_user = 'root';
            $db_password = '';
            $db_host = 'localhost';
            $options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4']; // Lai strādātu garumzīmes utf8mb4

            $pdo = new PDO("mysql:host=" . $db_host . ";dbname=" . $db_name, $db_user, $db_password, $options);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            return $pdo;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function rowCount($sql){
        $pdo  = $this->dbConnect();

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $result = $stmt->rowCount();
        return $result;

    }

    public function query($sql, $data = '', $fetchType = 1){

        try {
            $pdo  = $this->dbConnect();
            if ($this->error != '') {
                return $this->error;
            }

            $stmt = $pdo->prepare($sql);

            if (!empty($data)) {
                foreach ($data as $key => &$val) {
                    $stmt->bindParam($key, $val);
                }
            }

            $stmt->execute();
            $response = [];

            //Fetch type ieviests, jo jQuery pieņemt tikai array as array. Respektīvi jābūt ID nevis asociatīvajam
            switch ($fetchType){
                case 1:
                    while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
                        $response[] = $row;
                    }
                    break;
                case 2:
                    while (($row = $stmt->fetch(PDO::FETCH_NUM)) !== false) {
                        $response[] = $row;
                    }
            }
            $pdo = null;
            return $response;

        } catch(PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function executeTransaction($sql, $data)

    {
        try {
            $pdo = $this->dbConnect();
            if ($this->error != '') {
                return $this->error;
            }

            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute($data);
            } catch(PDOException $e) {
                $this->error = $e->getMessage();
            }
        } catch(PDOException $e) {
            $this->error =  $e->getMessage();
        }

    }

    public function getColumnNames($sql, $data = ''){

        // Pamēģinam pieslēgties pie DB
        try {
            $pdo  = $this->dbConnect();
            if ($this->error != '') {
                return $this->error;
            }

            //Paņemam padoto SQL vaicājumu un sagatavojam izpildei
            $stmt = $pdo->prepare($sql);

            //Ja $data nav tukšs, pievienojam parametrus
            // ja vaicājums satur, tad ir jāliek data where customer_id = :customer_id
            if (!empty($data)) {
                foreach ($data as $key => &$val) {
                    $stmt->bindParam($key, $val);
                }
            }

            //Izpilda vaicājumu
            $stmt->execute();
            $response = [];

            // columnCount pēc execute ļauj iegūt cik kolonas ir PDO objektā
            //getColumnMeta atgriež dažāda veida vērtības, kā piem. kolonas nosaukumu [name], [table] - tabulas nosaukumu
            for ($i = 0; $i < $stmt->columnCount(); $i++) {
                $col = $stmt->getColumnMeta($i);
                $response[] = $col['name'];
            }

            $pdo = null;
            return $response;
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
        }
    }
}