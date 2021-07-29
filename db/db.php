<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]);

function get_json($path){
    $content = file_get_contents(ROOT."/db/".$path);
    if(!$content){
        return null;
    }
    return json_decode($content);
}

function get_json_all($dir){
    $files = array_diff(scandir(ROOT."/db/".$dir."/"), array('.', '..'));
    $list = [];
    foreach ($files as $file) {
        $content = get_json($dir."/".$file);
        if($content){
            $list[] = $content;
        }
    }
    return $list;
}

function put_json($path, $contents){
    file_put_contents(ROOT."/db/".$path.".json", json_encode($contents));
}

?>