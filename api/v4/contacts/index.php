<?php
include $_SERVER["DOCUMENT_ROOT"]."/db/db.php";


$uri = explode("/",explode("?",$_SERVER['REQUEST_URI'])[0]);
$param = $uri[count($uri)-1];
$with = [];
if(isset($_GET["with"])){
    $with = explode(",",$_GET["with"]);
}

header("Content-type: application/json");
if($param == "contacts" || $param == ""){
    $list = get_json_all("contacts");
    if(!in_array("leads",$with)){
        foreach ($list as $contact) {
            if(isset($contact->_embedded->leads)){
                unset($contact->_embedded->leads);
            }
        }
    }
    echo json_encode(["page" => 1, "_embedded" => ["contacts" => $list]]);
}elseif(intval($param)){
    $contact = get_json("contacts/".intval($param).".json");
    if($contact){
        if(!in_array("leads",$with)){
            if(isset($contact->_embedded->leads)){
                unset($contact->_embedded->leads);
            }
        }
    }else{
        http_response_code(404);
    }
}else{
    http_response_code(404);
}
    
?>