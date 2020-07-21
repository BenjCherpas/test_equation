<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test EQUATION</title>
    <style>
        table, tr, td, th {
            padding: 5px;
            border: thin solid black;
            border-collapse: collapse;
        }
    </style>
</head>
<body>
<div style="width: auto; padding: 15px;">

<?php

class User
{
    public $_users = array();

    // On crée un premier tableau {numero => {prenom, nom}}
    public function getUsers()
    {
        $row = 1;
        if (($handle = fopen('users.csv', "r")) !== FALSE)
        {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
            {
                $this->_users[$data[0]] =  array($data[1], $data[2]);
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
    protected $_call = array();

    // On ajout chaque donnée d'appel au numero correspondant sous forme de tableau de tableau
    public function createArray()
    {
        $row = 1;

        // {numero => {prenom, nom, {{appel1}, {appel2}, ...}}}
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

                array_push($this->_call, array($this->_number, $this->_start_date, $this->_end_date, $this->_duration));

                $row++;
            }
            fclose($handle);
        }

        // Comme au dessus mais avec les 2e fichier
        if (($handle = fopen('platform2.csv', "r")) !== FALSE)
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

                array_push($this->_call, array($this->_number, $this->_start_date, $this->_end_date, $this->_duration));

                $row++;
            }
            fclose($handle);
        }

        // Initialisation d'un tableau en 3e position (tableau contenant les tableaux des appels)
        foreach ($this->_users as $key => $value)
        {
            $this->_users[$key][2] = array();
        }

        foreach($this->_call as $elt)
        {
            array_push($this->_users[$elt[0]][2], array($elt[1], $elt[2], $elt[3]));
        }

        return $this->_users;
    }

    public function showFinalData()
    {
        $call = new Call();
        $call = $call->createArray();
        ksort($call);  // Tri du tableau principal (par numéro)

        echo "<table><tr><th>Numéro</th><th>Prénom</th><th>Nom</th><th>Date début d'appel</th><th>Date fin d'appel</th><th>Durée</th></tr>";

        foreach($call as $key => $value)
        {
            if(! empty($value[2])) // Ne pas garder les utilisateurs qui n'ont pas appelé
            {
                foreach ($value[2] as $elt)
                {
                    echo "<tr><td>" . $key . "</td><td>" . $value[0] . "</td><td>" . $value[1] . "</td><td>" . $elt[0] . "</td><td>" . $elt[1] . "</td><td>" . $elt[2] . "</td></tr>";
                }
            }
        }
        echo "</table>";
    }
}

$call = new Call();
$call->showFinalData();

?>

</div>
</body>
</html>
