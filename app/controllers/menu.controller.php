<?php

// GET ALL CATEGORY
$app->get('/categories', function () use($app){
  $result = R::getAll(
    "SELECT category FROM `category_item` WHERE category <> '' GROUP BY category
     ORDER BY position_no"
  );
  $output = construct_outputs($result);
  header("Content-Type: application/json");
  echo json_encode($output);
});

// GET MENU BY CATEGORY
$app->get('/menu', function () use($app){
  $params = $app->request->get();
  $tmp = R::getAll(
    "SELECT m.menu_item_id, m.menu_item_name, m.menu_id, m.popup_menu_id, m.sold_out_item,
     IF(LENGTH(m.popup_menu_id), true, false) as has_popup, COALESCE(so.item_id,0) AS item_id,
     CASE WHEN so.sold_out IS NOT NULL THEN cast(so.sold_out AS SIGNED) ELSE 0 END as sold_out
     FROM `menu_item` as m LEFT OUTER JOIN sold_out_item as so ON so.item_name = m.sold_out_item
     WHERE m.item_category = '".$params['category']."'
     ORDER BY m.menu_item_name"
  );
  $result = get_childs($tmp);
  $output = construct_outputs($result);
  header("Content-Type: application/json");
  echo json_encode($output);
});

function get_childs($array){
  foreach ($array as $key => $value) {
    $result[$key] = $value;
    if ($value['popup_menu_id'] != ''){
      if ($value['item_id'] == "0"){
        $result[$key]['popups'] = R::getAll(
          "SELECT menu_item_id, menu_item_name, menu_id, popup_menu_id, '0' as sold_out
           FROM `menu_item` WHERE menu_id = '".$value['popup_menu_id']."'
           ORDER BY menu_item_name"
        );
      }else{
        $result[$key]['popups'] = R::getAll(
          "SELECT m.menu_item_id, m.menu_item_name, m.menu_id, m.popup_menu_id, m.sold_out_item,
           CASE WHEN so.sold_out IS NOT NULL THEN cast(so.sold_out AS SIGNED) ELSE 0 END as sold_out
           FROM `menu_item` as m LEFT OUTER JOIN sold_out_detail as so ON so.item_name = m.sold_out_item
           WHERE m.menu_id = '".$value['popup_menu_id']."' AND so.item_id = '".$value['item_id']."'
           ORDER BY menu_item_name"
        );
      }
    }
  }
  return $result;
}

function construct_outputs($result){
  $output = array(
    'code' => count($result) == 0 ? 'ZERO' : 'OK',
    'results_count' => count($result),
    'results' => $result 
  );
  return $output;
}