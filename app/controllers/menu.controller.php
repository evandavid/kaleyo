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
        "SELECT '0' as void, '' as void_note,m.menu_item_id, m.product_id, m.menu_item_name, m.menu_id, m.popup_menu_id, m.sold_out_item,m.sales_price, m.category,
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

// GET SPECIAL REQUEST
$app->get('/special_menu', function () use($app){
    $result = R::getAll(
        "SELECT m.request_name, cast(m.show_quantity AS UNSIGNED) as show_quantity, 
        m.request_summary, m.request_no, '0' as amount
        FROM `special_request` as m
        WHERE m.link_menu = 0
        ORDER BY m.request_no"
    );

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
                    "SELECT menu_item_id, menu_item_name, menu_id, popup_menu_id, 
                    '0' as sold_out, sales_price, category
                    FROM `menu_item` as m WHERE menu_id = '".$value['popup_menu_id']."'
                    ORDER BY menu_item_name"
                );
            }else{
                $result[$key]['popups'] = R::getAll(
                    "SELECT m.menu_item_id, m.menu_item_name, m.menu_id, m.popup_menu_id, 
                    m.sold_out_item,m.sales_price, m.category,
                    CASE WHEN so.sold_out IS NOT NULL THEN cast(so.sold_out AS SIGNED) ELSE 0 END as sold_out
                    FROM `menu_item` as m LEFT OUTER JOIN sold_out_detail as so ON so.item_name = m.sold_out_item
                    WHERE m.menu_id = '".$value['popup_menu_id']."' AND so.item_id = '".$value['item_id']."'
                    ORDER BY menu_item_name"
                );
            }
        }
        if (count($result[$key]['popups']) == 0){
            $result[$key]['has_popup'] = '0';
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

// GET SOLDOUT
$app->get('/sold_out', function () use($app){
    $tmp = R::getAll(
        "SELECT item_id, item_name, category, cast(sold_out AS SIGNED) as sold_out, has_detail 
        FROM `sold_out_item` 
        ORDER BY category"
    );
    $result = construct_sold_out_detail($tmp);
    $output = construct_outputs($result);
    header("Content-Type: application/json");
    echo json_encode($output);
});

function construct_sold_out_detail($tmp){
    foreach ($tmp as $key => $value) {
        $result[$key] = $value;
        if ($value['has_detail'] == "1"){
            $result[$key]['details'] = R::getAll(
                "SELECT detail_id, item_name, cast(sold_out AS SIGNED) as sold_out
                FROM `sold_out_detail` WHERE item_id = '".$value['item_id']."'
                ORDER BY item_name"
            );
        }
    }
    return $result;
}

$app->post('/sold_outs', function () use($app){
    date_default_timezone_set("Asia/Jakarta");
    $success = true;
    $params_post = ($app->request->post());
    $json = json_decode($params_post['sold_outs']);
    R::begin();
    try{
        foreach ($json as $key => $value) {
            if ($value->has_detail == "1"){
                foreach ($value as $key => $detail) {
                    R::exec(
                        "UPDATE `sold_out_detail` SET 
                        sold_out = ?, updated_by = ?, updated_date = ? 
                        WHERE detail_id = ?",
                        array((int)$value->sold_out, $params_post['updated_by'], date("Y-m-d H:i:s"),(int) $value->detail_id)
                    );
                }
            }
            R::exec("UPDATE sold_out_item SET sold_out = ?, updated_by = ?, updated_date = ? WHERE item_id = ?",
                array((int)$value->sold_out, $params_post['updated_by'], date("Y-m-d H:i:s"),(int)$value->item_id)
            );
        }
        print_r("sukses");
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