<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test EQUATION</title>
    <style>
        table, tr, th, td {
            padding: 5px;
            border: thin solid black;
        }
    </style>
</head>
<body>
<div style="border: thin solid black; width: 1000px; padding: 15px;">

<?php

class User
{
    public $_users = array();

    public function getUsers()
    {
        $row = 1;
        if (($handle = fopen('users.csv', "r")) !== FALSE)
        {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
            {
                array_push($this->_users,  array($data[0], $data[1], $data[2]));
                $row++;
            }
            fclose($handle);
        }
        return $this->_users;
    }
}


class Call
{
    protected $_number;
    protected $_start_date;
    protected $_end_date;
    protected $_duration;
    protected $_users = array();

    public function extractData()
    {
        $row = 1;

        if (($handle = fopen('platform1.csv', "r")) !== FALSE)
        {
            $users = new User();
            $this->_users = $users->getUsers();


            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
            {
                $this->_start_date = date_create_from_format("d/m/Y H:i:s", $data[0]);
                $this->_end_date = date_create_from_format("d/m/Y H:i:s", $data[1]);
                $this->_number = $data[2];
                $this->_duration = date_diff($this->_end_date, $this->_start_date);
                $this->_duration = $this->_duration->format("%D/%M/%Y %H:%I:%S");
                $this->_start_date = $this->_start_date->format("d/m/Y H:i:s");
                $this->_end_date = $this->_end_date->format("d/m/Y H:i:s");
                $this->_duration = explode(" ", $this->_duration, "2")[1];

                for($i = 0; $i < count($this->_users); $i++)
                {
                    if(in_array($this->_number, array($this->_users[$i][0])))
                    {
                        array_push($this->_users[$i], $this->_start_date, $this->_end_date, $this->_duration);
                    }

                }
                $row++;
            }
            fclose($handle);
//            print_r($this->_users);
        }

//        if (($handle = fopen('platform2.csv', "r")) !== FALSE)
//        {
//            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
//            {
//                $this->_number = $data[0];
//                $this->_end_date = $data[1] + ($data[2]* 60);
//                $this->_end_date = date("d/m/Y H:i:s", $this->_end_date);
//                $this->_start_date = date("d/m/Y H:i:s", $data[1]);
//                $this->_duration = gmdate("H:i:s", $data[2] * 60);
//                $this->_duration = date_create_from_format('d/m/Y H:i:s', "00/00/0000 " . $this->_duration);
//                $this->_duration = $this->_duration->format("H:i:s");
//
//                array_push($this->_users[$this->_number], $this->_start_date, $this->_end_date, $this->_duration);
//
//                $row++;
//            }
//            fclose($handle);
//
//        }
    }

//    public function showFinalData()
//    {
//        $response = $bdd->query("SELECT u.firstname, u.lastname, u.number, c.start_date, c.end_date, c.duration
//                                FROM call_phone c
//                                INNER JOIN user u ON u.number=c.number
//                                ORDER BY u.firstname, c.start_date ASC");
//        $data = $response->fetchAll();
//        echo "<table><tr><th>Nom</th><th>Prénom</th><th>Numéro</th><th>Date début d'appel</th><th>Date fin d'appel</th><th>Durée</th></tr>";
//
//        foreach ($data as $d)
//        {
//            echo "<tr><td>" . $d[0] . "</td><td>" . $d[1] . "</td><td>" . $d[2] . "</td><td>" . $d[3] . "</td><td>" . $d[4] . "</td><td>" . $d[5] . "</td></tr>";
//        }
//
//        echo "</table>";
//
//    }
}


//$user = new User();
//$user->getUsers();

$call = new Call();
$call->extractData();

//$call = new Call();
//$call->showFinalData();

?>

</div>
</body>
</html>
