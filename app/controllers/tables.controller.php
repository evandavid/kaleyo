<?php

// GET ALL TABLES
$app->get('/tables', function () use($app){
  $tmp = R::getAll(
    'SELECT MAX(table_id) AS table_id, table_name, table_status, floor_id, room_id, branch_id
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
    'SELECT MAX(table_id) AS table_id, table_name, table_status, floor_id, room_id, branch_id
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
    'SELECT MAX(table_id) AS table_id, table_name, table_status, floor_id, room_id, branch_id
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
      'SELECT MAX(table_id) AS table_id, table_name, table_status, floor_id, room_id, branch_id 
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
