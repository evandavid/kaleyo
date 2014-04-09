<?php

$app->get('/', function () use($app){
    $output = array("status" => 404, "message" => "ups, looks like you got lost in this way, contact ev.kristian@gmail.com for more info");
    header("Content-Type: application/json");
    echo json_encode($output);
});


$app->get('/broadcasts', function () use($app){
    try {
        $result = R::getRow("
            SELECT CONVERT(id, CHAR(50)) as ids, text, created_by FROM broadcast 
            WHERE active = 1 ORDER BY id DESC LIMIT 1"
        ); 
    } catch (Exception $e) {
        print_r($e);die;
        $result = null;
    }
    $output = construct_output_broadcast($result);
    header("Content-Type: application/json");
    echo json_encode($output);
});

$app->post('/broadcasts', function () use($app){
    date_default_timezone_set("Asia/Jakarta");
    $success = true;
    $params_post = ($app->request->post());
    R::begin();
    try{
        R::exec("INSERT INTO `broadcast` (`text`, `created_by`) VALUES (?,?)",
            array($params_post['text'], $params_post['created_by']));
        R::commit();
    } catch (Exception $e) {
        print_r("gagal".$e);
        $success = false;
        R::rollback();
    }

    if ($success){
        $app->response()->status(200);
    }else{
        $app->response()->status(500);
    }
});

$app->post('/promos', function () use($app){
    date_default_timezone_set("Asia/Jakarta");
    $success = true;
    $params_post = ($app->request->post());
    R::begin();
    try{
        R::exec("INSERT INTO `promo` (`text`, `created_by`) VALUES (?,?)",
            array($params_post['text'], $params_post['created_by']));
        R::commit();
    } catch (Exception $e) {
        print_r("gagal".$e);
        $success = false;
        R::rollback();
    }

    if ($success){
        $app->response()->status(200);
    }else{
        $app->response()->status(500);
    }
});

function construct_output_broadcast($result){
    $output = array(
        'code' => count($result) == 0 ? 'ZERO' : 'OK',
        'results_count' => count($result),
    );
    $output = array_merge($result, $output);
    return $output;
}