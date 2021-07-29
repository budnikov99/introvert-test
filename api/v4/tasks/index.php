<?php
include $_SERVER["DOCUMENT_ROOT"]."/db/db.php";


$uri = explode("/",explode("?",$_SERVER['REQUEST_URI'])[0]);
$param = $uri[count($uri)-1];

header("Content-type: application/json");
if($_SERVER["REQUEST_METHOD"] == "GET"){
    if($param == "tasks" || $param == ""){
        $list = get_json_all("tasks");
        echo json_encode(["page" => 1, "_embedded" => ["tasks" => $list]]);
    }else{
        http_response_code(404);
    }
}elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(($param == "tasks" || $param == "") && $_SERVER["CONTENT_TYPE"] == "application/json"){
        $raw = file_get_contents('php://input');
        $content = json_decode($raw);

        if(!is_array($content)){
            $content = [$content];
        }

        $newid = 0;
        foreach(get_json_all("tasks") as $task){
            if($task->id > $newid){
                $newid = $task->id;
            }
        }
        $response = [
            "_embedded" => [
                "tasks" => []
            ]
        ];

        foreach ($content as $taskdata) {
            if(isset($taskdata->text) && isset($taskdata->complete_till)){
                $newid++;
                $task = [
                    "id" => $newid,
                    "text" => $taskdata->text,
                    "complete_till" => $taskdata->complete_till,
                ];
                if(isset($taskdata->entity_id) && isset($taskdata->entity_type)){
                    $task["entity_id"] = $taskdata->entity_id;
                    $task["entity_type"] = $taskdata->entity_type;
                }
                $response["_embedded"]["tasks"][] = [
                    "id" => $newid, 
                    "request_id" => $taskdata->request_id ?? "taskreq".$newid
                ];
                put_json("tasks/".$newid, $task);
            }
        }
    }else{
        http_response_code(400);
    }
}

?>