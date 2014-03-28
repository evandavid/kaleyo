<?php

function init(){
    header("Content-Type: application/json");
}

// LOGIN USER
$app->get('/login', function () use($app){
    $params = $app->request->get();
    try {
        $result = R::getAll("SELECT * FROM users WHERE user_id = '".$params['username']."' AND mobile_password = '".$params['password']."'"); 
        $staff = R::getAll("SELECT * FROM users "); 
    } catch (Exception $e) {
        $result = null;
        $staff = null;
    }
    $output = construct_output_login($result, $staff);
    header("Content-Type: application/json");
    echo json_encode($output);
});

function construct_output_login($result, $staff){
    $output = array(
        'code' => count($result) == 0 ? 'ZERO' : 'OK',
        'results_count' => count($result),
        'results' => $result,
        'staffs' => $staff
    );
    return $output;
}

// LIST USER
$app->get('/users', function () use($app){
    try {
        $result = R::getAll("SELECT * FROM users "); 
    } catch (Exception $e) {
        $result = null;
    }
    $output = $result;
    header("Content-Type: application/json");
    echo json_encode($output);
});


// Get Promos
$app->get('/promos', function () use($app){
    try {
        $result = R::getAll("SELECT * FROM promo WHERE active = 1 ORDER BY id DESC LIMIT 1"); 
    } catch (Exception $e) {
        $result = null;
    }
    $output = construct_output_promo($result);
    header("Content-Type: application/json");
    echo json_encode($output);
});

function construct_output_promo($result){
    $output = array(
        'code' => count($result) == 0 ? 'ZERO' : 'OK',
        'results_count' => count($result),
        'promo' => count($result) == 0 ? null : $result[0]['text'],
    );
    return $output;
}

$app->get('/api/comment/json', function () use ($app) {

    $result = R::getAll('SELECT * FROM guest ORDER BY modify_date DESC');
    header("Content-Type: application/json");
    echo json_encode($result);
})->name('api_comment_json');

//POST route
$app->post('/guest/comment', function () use($app) {

    $guest = R::dispense('guest');

    $name = $app->request->post('name');
    if (empty($name))
        $name = 'anonymous';

    $guest->name = $name;
    $guest->message = $app->request->post('message');
    $guest->ip = $app->request->getIp();

            // prepare to delete old comments
    $yesterday = date('Y-m-d' , strtotime('-1 day'));

            // start transaction
    R::begin();
    try {
        R::exec('DELETE FROM guest WHERE modify_date < ?', array($yesterday));   
        R::store($guest);
        R::commit();
        $app->flash('success', 'Nice to hear from you!');
    } catch (Exception $e) {
        R::rollback();
        $app->flash('error', 'Oops... seems something goes wrong.');
    }
    $app->redirect($app->request->getReferrer());
})->name('guest_comment');

//PUT route
$app->put('/put', function () use($app) {
    echo 'This is a PUT route';
});

//DELETE route
$app->delete('/delete', function () use($app) {
    echo 'This is a DELETE route';
});
?>