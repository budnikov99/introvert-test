<?php
include $_SERVER["DOCUMENT_ROOT"]."/db/db.php";


$uri = explode("/",explode("?",$_SERVER['REQUEST_URI'])[0]);
$param = $uri[count($uri)-1];
$with = [];
if(isset($_GET["with"])){
    $with = explode(",",$_GET["with"]);
}

header("Content-type: application/json");
if($param == "leads" || $param == ""){
    $list = get_json_all("leads");
    if(!in_array("contacts",$with)){
        foreach ($list as $lead) {
            if(isset($lead->_embedded->contacts)){
                unset($lead->_embedded->contacts);
            }
        }
    }
    echo json_encode(["page" => 1, "_embedded" => ["leads" => $list]]);
}elseif(intval($param)){
    $lead = get_json("leads/".intval($param).".json");
    if($lead){
        if(!in_array("contacts",$with)){
            if(isset($lead->_embedded->contacts)){
                unset($lead->_embedded->contacts);
            }
        }
        echo json_encode($lead);
    }else{
        http_response_code(404);
    }
}else{
    http_response_code(404);
}

?>