<?php

// GET ALL TABLES
$app->get('/tables', function () use($app){
  $tmp = R::getAll(
    'SELECT MAX(table_id) AS table_id, table_name, table_status, floor_id, room_id, branch_id, company_id
     FROM floor_plant WHERE room_id = "Dine In" AND has_child = 1 GROUP BY table_name 
     ORDER BY table_id, cast(table_name as unsigned)'
  );
  $result = get_child($tmp);
  $output = construct_output($result);
  header("Content-Type: application/json");
  echo json_encode($output);
});

// GET BUSY TABLES
$app->get('/busy_table', function () use($app){
  $tmp = R::getAll(
    'SELECT MAX(table_id) AS table_id, table_name, table_status, floor_id, room_id, branch_id, company_id
     FROM floor_plant WHERE room_id = "Dine In" AND has_child = 1 AND table_status = 0
     GROUP BY table_name ORDER BY table_id, cast(table_name as unsigned)'
  );
  $result = get_child($tmp);
  $output = construct_output($result);
  header("Content-Type: application/json");
  echo json_encode($output);
});

// GET AVAILABLE TABLES
$app->get('/available_table', function () use($app){
  $tmp = R::getAll(
    'SELECT MAX(table_id) AS table_id, table_name, table_status, floor_id, room_id, branch_id, company_id
     FROM floor_plant WHERE room_id = "Dine In" AND has_child = 1 AND table_status = 1
     GROUP BY table_name ORDER BY table_id, cast(table_name as unsigned)'
  );
  $result = get_child($tmp);
  $output = construct_output($result);
  header("Content-Type: application/json");
  echo json_encode($output);
});

function get_child($array){
  foreach ($array as $key => $value) {
    $result[$key] = $value;
    $result[$key]['childs'] = R::getAll(
      'SELECT MAX(table_id) AS table_id, table_name, table_status, floor_id, room_id, branch_id, "'.$value["table_id"].'" as parent_id, company_id
       FROM floor_plant
       WHERE room_id = "Dine In" AND table_name REGEXP "^'.$value["table_name"].'[abc]" OR table_name = "'.$value["table_name"].'" 
       GROUP BY table_name ORDER BY table_id, cast(table_name as unsigned)'
    );
    $result[$key]['occupied_count'] = construct_occupied($result[$key]['childs']);
  }
  return $result;
}

function construct_occupied($child){
  $to_return = 0;
  foreach ($child as $key => $value) {
    if ($value['table_status'] == 0)
      $to_return = $to_return + 1;
  }
  return $to_return;
} 

function construct_output($result){
  $tmp = R::getAll(
    'SELECT MAX(table_id) AS table_id, table_name, table_status, floor_id, room_id, branch_id
     FROM floor_plant WHERE room_id = "Dine In" AND has_child = 1 GROUP BY table_name 
     ORDER BY table_id, cast(table_name as unsigned)'
  );
  $output = array(
    'code' => count($tmp) == 0 ? 'ZERO' : 'OK', 
    'total_results_count' => count($tmp)*count($result[0]['childs']),
    'results_count' => count($tmp),
    'results' => $result 
  );
  return $output;
}

// MOVE TABLE
$app->post('/move_table', function () use($app){
    
    date_default_timezone_set("Asia/Jakarta");
    $success = true;
    $params_post = ($app->request->post());
    $original = json_decode($params_post['from']);
    $target = json_decode($params_post['target']);

    $table = R::getRow("SELECT * FROM floor_plant WHERE table_id = ?"
        , array($original->table_id)
    ); 

    $parent_table = R::getRow("SELECT * FROM floor_plant WHERE table_id = ?"
        , array($original->parent_id)
    ); 

    R::begin();
    try{
        // if occupied only one, change parent also
        if ($params_post['occupied'] == "1"){
          R::exec("
              UPDATE `floor_plant` SET 
              `table_status`= 1, `order_id`= ? ,`updated_by`= ? , `updated_date`= ? 
              WHERE `table_id`= ? AND room_id = 'Dine In'",
           array(
              "", $params_post['updated_by'], date("Y-m-d H:i:s"), $original->parent_id)
          );

          R::exec("
            UPDATE `floor_plant` SET 
            `table_status`= 1, `order_id`= ? ,`updated_by`= ? , `updated_date`= ? 
            WHERE `table_id`= ? AND room_id = 'Dine In'",
          array(
            "", $params_post['updated_by'], date("Y-m-d H:i:s"), $original->table_id)
          );
        }else{

          R::exec("
              UPDATE `floor_plant` SET `table_status`= 1
              WHERE parent_id = ? AND room_id = 'Dine In'",
           array(
              $original->parent_id)
          );

          R::exec("
              UPDATE `floor_plant` SET `table_status`= 1
              WHERE table_id = ? AND room_id = 'Dine In'",
           array(
              $original->parent_id)
          );

          // reorder table
          $tables = R::getAll("SELECT * FROM floor_plant WHERE parent_id = ? AND room_id = 'Dine In' AND parent_id <> '' ORDER BY table_id ASC"
              , array($original->parent_id)
          );

          $orders = R::getAll("SELECT * FROM floor_plant WHERE parent_id = ? AND order_id <> '' AND room_id = 'Dine In' AND parent_id <> '' ORDER BY table_id ASC"
              , array($original->parent_id)
          );

          $i = 0;
          foreach ($orders as $key => $value) {

            if ($table['order_id'] != $value['order_id']){
              R::exec("
                UPDATE `orders` SET 
                `floor_id`= ?, `room_id`= ? ,`table_id`= ? , `updated_date`= ? 
                WHERE `order_id`= ?",
              array(
                $tables[$i]['floor_id'], $tables[$i]['room_id'], $tables[$i]['table_id'], date("Y-m-d H:i:s"), $value['order_id'])
              );
              
              R::exec("UPDATE `floor_plant` SET `table_status`= 0, `order_id`= ? ,`updated_by`= ? , `updated_date`= ? WHERE `table_id`= ? AND room_id = 'Dine In'",
                array($value['order_id'], $params_post['updated_by'], date("Y-m-d H:i:s"), $tables[$i]['table_id']));

              R::exec("UPDATE `floor_plant` SET `table_status`= 0, `order_id`= ? ,`updated_by`= ? , `updated_date`= ? WHERE `table_id`= ? AND room_id = 'Dine In'",
                array($value['order_id'], $params_post['updated_by'], date("Y-m-d H:i:s"), $original->parent_id));
              $i = $i + 1;
            } 
          }
        }

        // change order table id
        R::exec("
          UPDATE `orders` SET 
          `floor_id`= ?, `room_id`= ? ,`table_id`= ? , `updated_date`= ? 
          WHERE `order_id`= ?",
        array(
          $target->floor_id, $target->room_id, $target->table_id, date("Y-m-d H:i:s"), $table['order_id'])
        );

        R::exec("
          UPDATE `floor_plant` SET 
          `table_status`= 0, `order_id`= ? ,`updated_by`= ? , `updated_date`= ? 
          WHERE `table_id`= ? AND room_id = 'Dine In'",
        array(
          $table['order_id'], $params_post['updated_by'], date("Y-m-d H:i:s"), $target->table_id)
        );

        R::exec("
          UPDATE `floor_plant` SET 
          `table_status`= 0, `order_id`= ? ,`updated_by`= ? , `updated_date`= ? 
          WHERE `table_id`= ? AND room_id = 'Dine In'",
        array(
          $table['order_id'], $params_post['updated_by'], date("Y-m-d H:i:s"), $target->parent_id)
        );
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

// LINK TABLE
$app->post('/link_table', function () use($app){
    date_default_timezone_set("Asia/Jakarta");
    $success = true;
    $params_post = ($app->request->post());
    $original = json_decode($params_post['from']);
    $target = json_decode($params_post['target']);

    $table = R::getRow("SELECT * FROM floor_plant WHERE table_id = ?"
        , array($original->table_id)
    ); 

    R::begin();
    try{
       // if occupied only one, change parent also
        R::exec("
          UPDATE `floor_plant` SET 
          `table_status`= 0, `order_id`= ? ,`updated_by`= ? , `updated_date`= ? 
          WHERE `table_id`= ? AND room_id = 'Dine In'",
        array(
          $table['order_id'], $params_post['updated_by'], date("Y-m-d H:i:s"), $target->table_id)
        );

        R::exec("
          UPDATE `floor_plant` SET 
          `table_status`= 0, `order_id`= ? ,`updated_by`= ? , `updated_date`= ? 
          WHERE `table_id`= ? AND room_id = 'Dine In'",
        array(
          $table['order_id'], $params_post['updated_by'], date("Y-m-d H:i:s"), $target->parent_id)
        );
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