<?php
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json'); 
$data = file_get_contents('db.json'); 
$json = json_decode($data, true); 
$jsonObj = json_decode($data); 

if ($_GET['type'] == 'read') {
    echo '{ "status": "success", "data": ' . $data . ' }'; 
}else if ($_GET['type'] == 'write') {
    $id = 1; 
    for ($i = 0; $i < count($jsonObj); $i++) {
        if ($jsonObj[$i]->id >= $id) {
            $id = $jsonObj[$i]->id + 1; 
        }
    }
    $getdata = array(
        'id' => $id, 
        'name' => rawurldecode($_GET['name']), 
        'birthday' => rawurldecode($_GET['birthday']), 
        'address' => rawurldecode($_GET['address']), 
        'bio' => rawurldecode($_GET['bio']), 
        'picture' => rawurldecode($_GET['picture']), 
        'email' => rawurldecode($_GET['email'])); 
    array_push($json, $getdata); 
    $newjsondata = json_encode($json, JSON_PRETTY_PRINT); 
    if (file_put_contents('db.json', $newjsondata)) {
        echo '{"status": "success", "message":"Het profiel van ' . rawurldecode($_GET['name']) . ' is opgeslagen onder ID ' . $id . '", "data": ' . $newjsondata . '}'; 
    }else {
        echo '{"status": "fail", "message": "Er is een fout opgetreden bij het toevoegen van dit profiel. Probeer het nog eens!"}'; 
    }
}else if ($_GET['type'] == 'remove') {
    if (!$_GET['id']) {
        echo '{ "status": "error", "message": "Het ID van het is niet gespecificeerd." }'; 
    }else if ($_GET['id'] && !is_numeric($_GET['id'])) {
        echo '{ "status": "error", "message": "Het ID is geen geldig getal. Een ID mag enkel numerieke tekens bevatten en geen decimalen." }'; 
    }else {
        $idIsHere = false; 
        for ($i = 0; $i < count($jsonObj); $i++) {
            if ($jsonObj[$i]->id == $_GET['id']) {
                $idIsHere = true; 
                array_splice($json, $i, 1); 
            }
        }
        
        if (!$idIsHere) {
            echo '{ "status": "error", "message": "Het ID komt niet voor in de database" }'; 
        }else {
            if (file_put_contents('db.json', json_encode($json, JSON_PRETTY_PRINT))) {
                echo '{"status": "success", "message": "Succesfully removed profile with id ' . $_GET['id'] . '", "data": ' . file_get_contents('db.json') . '}'; 
            }else {
                echo '{"status": "fail", "message": "Er is een fout opgetreden bij het verwijderen van dit profiel. Probeer het nog eens!"}'; 
            }
        }
    }
}else {
    echo '{ "status": "error", "message": "Gebruik read, write of remove" }'; 
}?>