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
    <form method="get">
        <label for="user-file">User file: </label>
        <input type="text" name="user-file" id="user-file" placeholder="users.csv"/><br><br>

        <label for="call-file">Call file: </label>
        <input type="text" name="call-file" id="call-file" placeholder="platform1,2.csv"/><br><br>

        <input type="submit" name="submit" value="Importer"/><br><br>
        <input type="submit" name="display" value="Afficher les données"/>
    </form>
<?php

function connectBdd()
{
    try {
        return new PDO('mysql:host=localhost;dbname=test_equation;charset=utf8', 'root', '');
    }
    catch (Exception $e)
    {
        die('Erreur: ' . $e->getMessage());
    }
}

class User
{
    public function extractData($file_path)
    {
        $bdd = connectBdd();

        $row = 1;
        if ($file_path == "users.csv")
        {
            if (($handle = fopen($file_path, "r")) !== FALSE)
            {
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
                {
                    $bdd->query("INSERT INTO user(number, firstname, lastname) VALUES ('$data[0]', '$data[1]', '$data[2]')");
                    $row++;
                }
                fclose($handle);
                echo "Import des utilisateurs réussi !";
            }
        }

        else
        {
            echo "Désolé je ne connais pas ce fichier";
        }
    }
}


class Call
{
    protected $_number;
    protected $_start_date;
    protected $_end_date;
    protected $_duration;

    public function extractData($file_path)
    {
        $bdd = connectBdd();

        $row = 1;
        if($file_path == "platform1.csv")
        {
            if (($handle = fopen($file_path, "r")) !== FALSE)
            {
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

                    $bdd->query("INSERT INTO call_phone(number, start_date, end_date, duration)
                    VALUES ('$this->_number', STR_TO_DATE('$this->_start_date',  '%d/%m/%Y %H:%i:%s'), 
                            STR_TO_DATE('$this->_end_date',  '%d/%m/%Y %H:%i:%s'), '$this->_duration')");

                    $row++;
                }
                fclose($handle);
                echo "Import des appels (1) réussi !";
            }
        }

        elseif($file_path == "platform2.csv")
        {
            if (($handle = fopen($file_path, "r")) !== FALSE)
            {
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
                {
                    $this->_number = $data[0];
                    $this->_end_date = $data[1] + ($data[2]* 60);
                    $this->_end_date = date("d/m/Y H:i:s", $this->_end_date);
                    $this->_start_date = date("d/m/Y H:i:s", $data[1]);
                    $this->_duration = gmdate("H:i:s", $data[2] * 60);
                    $this->_duration = date_create_from_format('d/m/Y H:i:s', "00/00/0000 " . $this->_duration);
                    $this->_duration = $this->_duration->format("H:i:s");

                    $bdd->query("INSERT INTO call_phone(number, start_date, end_date, duration)
                    VALUES ('$this->_number', STR_TO_DATE('$this->_start_date',  '%d/%m/%Y %H:%i:%s'), 
                            STR_TO_DATE('$this->_end_date',  '%d/%m/%Y %H:%i:%s'), '$this->_duration')");

                    $row++;
                }
                fclose($handle);
                echo "Import des appels (2) réussi !";
            }
        }

        else
        {
            echo "Désolé je ne connais pas ce fichier";
        }

    }

    public function showFinalData()
    {
        $bdd = connectBdd();

        $response = $bdd->query("SELECT u.firstname, u.lastname, u.number, c.start_date, c.end_date, c.duration 
                                FROM call_phone c 
                                INNER JOIN user u ON u.number=c.number 
                                ORDER BY u.firstname, c.start_date ASC");
        $data = $response->fetchAll();
        echo "<table><tr><th>Nom</th><th>Prénom</th><th>Numéro</th><th>Date début d'appel</th><th>Date fin d'appel</th><th>Durée</th></tr>";

        foreach ($data as $d)
        {
            echo "<tr><td>" . $d[0] . "</td><td>" . $d[1] . "</td><td>" . $d[2] . "</td><td>" . $d[3] . "</td><td>" . $d[4] . "</td><td>" . $d[5] . "</td></tr>";
        }

        echo "</table>";

    }
}

if(isset($_GET['user-file']) AND ! empty($_GET['user-file']) AND empty($_GET['display']))
{
    $user = new User();
    $user->extractData($_GET['user-file']);
}

if(isset($_GET['call-file']) AND ! empty($_GET['call-file']) AND empty($_GET['display']))
{
    $call = new Call();
    $call->extractData($_GET['call-file']);
}

if(isset($_GET['display']) AND ! empty($_GET['display']))
{
    $call = new Call();
    $call->showFinalData();
}

?>

</div>
</body>
</html>
