<?php

// SAVE ORDER
$app->post('/orders', function () use($app){
  $PRINT = array(
    "Dine-in_Makanan" => "",
    "Dine-In_Minuman" => "",
    "Dine-In_Sop-Buah" => "",
    "Dine-In_Es-Teler" => "",
    "Take-Away_Makanan" => "",
    "Take-Away_Minuman" => "",
    "Take-Away_Sop-Buah" => "",
    "Take-Away_Es-Teler" => "",
  );

  $success = true;
  date_default_timezone_set("Asia/Jakarta");
  $today_orders = get_today_order();
  $order_id =  get_sales_day().str_pad(strval($today_orders+1), 4, '0', STR_PAD_LEFT);
  $params_post = ($app->request->post());
  $json_data = json_decode($params_post['orders']);

  // insert order
  R::begin();
  try{
    // ORDER ITEM
    $take_away = 1;
    foreach ($json_data->menus as $key => $order_item) {
      if ($order_item->dineIn)
        $take_away = 0;
      $item_note = "";
      $detail_note = "";
      // if have no popups, note going to added in item order
      if ($order_item->has_popup == '0'){
        if (count($order_item->specialRequest) > 0){
          foreach ($order_item->specialRequest as $key => $special) {
            $item_note = $special->to_save.",".$item_note;
          }
          $item_note = trim($item_note, ",");
        }
      }else{
        if (count($order_item->specialRequest) > 0){
          foreach ($order_item->specialRequest as $key => $special) {
            $detail_note = $special->to_save.",".$detail_note;
          }
          $detail_note = trim($detail_note, ",");
        }
        // order_item_detail
        foreach ($order_item->popups as $key => $item_detail) {
          if ($item_detail->amount > 0){
            R::exec(
              "INSERT INTO `order_item_detail` (
                `order_id`, `order_item_no`, `menu_item_id`, `menu_item_name`, `quantity`, 
                `note`, `created_by`, `created_date`, `updated_by`, `updated_date`, 
                `company_id`, `branch_id`, `print_quantity`)
              VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
              array(
                $order_id, $order_item->universalSort, $item_detail->menu_item_id, $item_detail->menu_item_name, $item_detail->amount,
                $detail_note, $json_data->waitres, date("Y-m-d H:i:s"), $json_data->waitres, date("Y-m-d H:i:s"),
                $json_data->table->company_id, $json_data->table->branch_id, 0
              )
            );
          }
        }
      }

      // save order_item
      R::exec(
        "INSERT INTO `order_item` (
          `order_id`, `order_item_no`, `product_id`, `price`, `quantity`, 
          `type_of_discount`, `disc_percentage`, `disc_amount`, `parent_no`, `dine_in`, 
          `menu_id`, `menu_item_id`, `menu_item_name`, `note`, `void`, 
          `void_note`, `mgr_id`, `created_by`, `created_date`, `updated_by`, 
          `updated_date`, `company_id`, `branch_id`, `invoiced_quantity`, `unit`, 
          `seat`, `split_no`, `bill_quantity`, `event_name`, `additional`, 
          `pending`, `link_item`, `print_quantity`, `category`, `additional_no`, 
          `cost_price`, `order_by`, `flag_void`, `vat`) 
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
        array(
          $order_id, $order_item->universalSort, $order_item->product_id, $order_item->sales_price, $order_item->amount,
          0, 0, 0, 0, $order_item->dineIn == true ? 1 : 0,
          $order_item->menu_id, $order_item->menu_item_id, $order_item->menu_item_name, $item_note, (int)$order_item->void,
          $order_item->void_note, '', $json_data->waitres,date("Y-m-d H:i:s") , $json_data->waitres,
          date("Y-m-d H:i:s"), $json_data->table->company_id, $json_data->table->branch_id, 0, '',
          0, 0, 0, '', $order_item->tambahan == true ? 1 : 0,
          0, 0, 0, $order_item->category, $order_item->tambahan == true ? $order_item->localSort : 0,
          0, $json_data->waitres_name, 0, 10
        )
      );
    }

    //orders
    R::exec(
      "INSERT INTO `orders` (
        `order_date`, `order_id`, `member_id`, `note`, `type_of_discount`, 
        `disc_percentage`, `disc_amount`, `pax`, `floor_id`, `room_id`, 
        `table_id`, `served`, `created_by`, `created_date`, `updated_by`, 
        `updated_date`, `company_id`, `branch_id`, `take_away`, `queque_no`, 
        `shift_id`, `order_by`, `flag_saved`) 
      VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
      array(
        date("Y-m-d H:i:s"), $order_id, "", $json_data->note == null ? '' : $json_data->note , 0,
        0, 0, $json_data->persons, $json_data->table->floor_id, $json_data->table->room_id,
        $json_data->table->table_id, 0, $json_data->waitres, date("Y-m-d H:i:s"), $json_data->waitres,
        date("Y-m-d H:i:s"), $json_data->table->company_id, $json_data->table->branch_id, $take_away, 0, 
        0, $json_data->waitres_name, 1 
      )
    );   
    // TODO split_no item_order, link_item, vat, flag_void


    // CREATE PRINT JOB
    foreach ($json_data->menus as $key => $order_item) {
      $spesial = "";
      $menu = "";
      
      // MENU
      $menu = $order_item->amount." ".$order_item->menu_item_name;
      if ($order_item->has_popup == '1'){
        foreach ($order_item->popups as $key => $item_detail) {
          if ($item_detail->amount > 0){
            $menu = $menu."\n * ".$item_detail->amount." pcs ".$item_detail->menu_item_name;
          }
        }
      }

      //SPECIAL REQUEST
      if (count($order_item->specialRequest) > 0){
        $xx = "";
        foreach ($order_item->specialRequest as $key => $special) {
          if ($special->show_quantity == "0")
            $xx = $special->request_summary.",".$xx;
          else
            $xx = $special->amount." ".$special->request_summary.",".$xx;
        }
        $xx = trim($xx, ",");
        $xx = "( ".$xx." )";
      }
      
      // END SPECIAL REQ
      $menu = $menu." ".$xx;

      // ORDER DINE IN
      if ($order_item->dineIn){
        if ($order_item->category == "Makanan"){
          if ($PRINT['Dine-in_Makanan'] == "")
            $PRINT['Dine-in_Makanan'] = $menu;
          else
            $PRINT['Dine-in_Makanan'] = $PRINT['Dine-in_Makanan']."\n".$menu;
        }else if ($order_item->category == "Minuman"){
          if ($PRINT['Dine-In_Minuman'] == "")
            $PRINT['Dine-In_Minuman'] = $menu;
          else
            $PRINT['Dine-In_Minuman'] = $PRINT['Dine-In_Minuman']."\n".$menu;
        }else if ($order_item->category == "Es Teler"){
          if ($PRINT['Dine-In_Es-Teler'] == "")
            $PRINT['Dine-In_Es-Teler'] = $menu;
          else
            $PRINT['Dine-In_Es-Teler'] = $PRINT['Dine-In_Es-Teler']."\n".$menu;
        }else{
          if ($PRINT['Dine-In_Sop-Buah'] == "")
            $PRINT['Dine-In_Sop-Buah'] = $menu;
          else
            $PRINT['Dine-In_Sop-Buah'] = $PRINT['Dine-In_Sop-Buah']."\n".$menu;
        }

      }else{
        if ($order_item->category == "Makanan"){
          if ($PRINT['Take-Away_Makanan'] == "")
            $PRINT['Take-Away_Makanan'] = $menu;
          else
            $PRINT['Take-Away_Makanan'] = $PRINT['Take-Away_Makanan']."\n".$menu;
        }else if ($order_item->category == "Minuman"){
          if ($PRINT['Take-Away_Minuman'] == "")
            $PRINT['Take-Away_Minuman'] = $menu;
          else
            $PRINT['Take-Away_Minuman'] = $PRINT['Take-Away_Minuman']."\n".$menu;
        }else if ($order_item->category == "Es Teler"){
          if ($PRINT['Take-Away_Es-Teler'] == "")
            $PRINT['Take-Away_Es-Teler'] = $menu;
          else
            $PRINT['Take-Away_Es-Teler'] = $PRINT['Take-Away_Es-Teler']."\n".$menu;
        }else{
          if ($PRINT['Take-Away_Sop-Buah'] == "")
            $PRINT['Take-Away_Sop-Buah'] = $menu;
          else
            $PRINT['Take-Away_Sop-Buah'] = $PRINT['Take-Away_Sop-Buah']."\n".$menu;
        }
      }
    }
    // SAVE PRINT_JOB

    $checker = "";
    foreach ($PRINT as $key => $value) {

      if ($value != ""){
        $keys = explode("_", $key);

        $types = str_replace("-", " ", $keys[1]);
        $order_type = str_replace("-", " ", $keys[0]);
        if (strtoupper($order_type) == "TAKE AWAY")
          $text = "@".strtoupper($types).": ".strtoupper($order_type);
        else
          $text = "@".strtoupper($types).": ".strtoupper($order_type)." MEJA ".$json_data->table->table_name;
        $text .= "\n------------------------------------------";
        $text .= "\n". $value;
        $text .= "\n\n------------------------------------------\n";
        $text .= date("d/m/Y")." ".$json_data->waitres_name." ".date("H:i:s");
        $checker .= $value."\n";

        $printer_function = "";
        if (strtolower($types) == "makanan") 
          $printer_function = "Makanan";
        else
          $printer_function = "Minuman";

        $printer = R::getRow(
            "SELECT * FROM `printer_setting` WHERE printer_function LIKE '%?%'", array($printer_function)
        );
        if (count($printer) == 0){
          $printer = R::getRow(
            "SELECT * FROM `printer_setting` WHERE print_default = 1 ORDER BY printer_id ASC LIMIT 1"
          );
        }

        R::exec(
          "INSERT INTO `print_jobs` (
            `start_time`, `print_type`, `request_terminal`, `printer_name`, `print_text`, 
            `update_required`, `printed`, `print_category`, `order_id`, `printed_terminal`, 
            `printed_time`
          ) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
          array(
            date("Y-m-d H:i:s"), "Order", "MOBILE", $printer['printer_name'], $text,
            1,0, strtolower($types), $order_id, "",
            date("Y-m-d H:i:s")
            )
        );
        //TODO PRINTER NAME

      }
    }
    
    $printer = R::getRow(
      "SELECT * FROM `printer_setting` WHERE print_default = 1 ORDER BY printer_id ASC LIMIT 1"
    );

    $text_checker = "MEJA ".$json_data->table->table_name;
    $text_checker .= "\n * ORDER CHECKER";
    $text_checker .= "\n------------------------------------------";
    $text_checker .= "\n". $checker;
    $text_checker .= "\n\n------------------------------------------";
    $text_checker .= "\n".date("d/m/Y")." ".$json_data->waitres_name." ".date("H:i:s");
    R::exec(
      "INSERT INTO `print_jobs` (
        `start_time`, `print_type`, `request_terminal`, `printer_name`, `print_text`, 
        `update_required`, `printed`, `print_category`, `order_id`, `printed_terminal`, 
        `printed_time`
      ) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
      array(
        date("Y-m-d H:i:s"), "Order Checker", "MOBILE", $printer['printer_name'], $text_checker,
        0,0, '', $order_id, "",
        date("Y-m-d H:i:s")
        )
    );
    // END PRINT JOB

    // table status
    R::exec("UPDATE `floor_plant` SET `table_status`= 0, `order_id`= ? ,`updated_by`= ? , `updated_date`= ? WHERE `table_id`= ? AND room_id = 'Dine In'",
      array($order_id, $json_data->waitres, date("Y-m-d H:i:s"), $json_data->table->table_id));
    R::exec("UPDATE `floor_plant` SET `table_status`= 0, `order_id`= ? ,`updated_by`= ? , `updated_date`= ? WHERE `table_id`= ? AND room_id = 'Dine In'",
      array($order_id, $json_data->waitres, date("Y-m-d H:i:s"), $json_data->table->parent_id));
    R::commit();


    // TODO take away, queque_no, shift_id, pax on orders
  } catch (Exception $e) {
    print_r("gagal".$e)
;    $success = false;
    R::rollback();
  }

  if ($success){
    $app->response()->status(200);
  }else{
    $app->response()->status(500);
  }
  
});

// UPDATE ORDER
$app->post('/update_orders', function () use($app){
  $PRINT = array(
    "Dine-in_Makanan" => "",
    "Dine-In_Minuman" => "",
    "Dine-In_Sop-Buah" => "",
    "Dine-In_Es-Teler" => "",
    "Take-Away_Makanan" => "",
    "Take-Away_Minuman" => "",
    "Take-Away_Sop-Buah" => "",
    "Take-Away_Es-Teler" => "",
  );

  $success = true;
  date_default_timezone_set("Asia/Jakarta");
  $params_post = ($app->request->post());
  $json_data = json_decode($params_post['orders']);
  $order_id = $json_data->orderId;
  $total = (int)$json_data->total + 1;

  // insert order
  R::begin();
  try{
    // ORDER ITEM
    $take_away = 1;
    foreach ($json_data->menus as $key => $order_item) {
      if ($order_item->saved){
        if ($order_item->has_popup == '1'){
          // order item detail
          foreach ($order_item->popups as $k => $item_detail) {
            R::exec("UPDATE `order_item_detail` 
              SET `quantity`= ?, `updated_by`= ? , `updated_date`= ? 
              WHERE `order_id`= ? AND order_item_no = ?",
            array($item_detail->amount, $json_data->waitres, date("Y-m-d H:i:s"), $order_id, $order_item->order_item_no));
          }
        }
        R::exec("UPDATE `order_item` 
          SET `quantity`= ?, `updated_by`= ? , `updated_date`= ? , `void` = ?, `void_note` = ?
          WHERE `order_id`= ? AND order_item_no = ?",
        array($order_item->amount, $json_data->waitres, date("Y-m-d H:i:s"), (int)$order_item->void, $order_item->void_note, $order_id, $order_item->order_item_no));
      }else{

          if ($order_item->dineIn)
            $take_away = 0;
          $item_note = "";
          $detail_note = "";
          // if have no popups, note going to added in item order
          if ($order_item->has_popup == '0'){
            if (count($order_item->specialRequest) > 0){
              foreach ($order_item->specialRequest as $key => $special) {
                $item_note = $special->to_save.",".$item_note;
              }
              $item_note = trim($item_note, ",");
            }
          }else{
            if (count($order_item->specialRequest) > 0){
              foreach ($order_item->specialRequest as $key => $special) {
                $detail_note = $special->to_save.",".$detail_note;
              }
              $detail_note = trim($detail_note, ",");
            }
            // order_item_detail
            foreach ($order_item->popups as $key => $item_detail) {
              if ($item_detail->amount > 0){
                R::exec(
                  "INSERT INTO `order_item_detail` (
                    `order_id`, `order_item_no`, `menu_item_id`, `menu_item_name`, `quantity`, 
                    `note`, `created_by`, `created_date`, `updated_by`, `updated_date`, 
                    `company_id`, `branch_id`, `print_quantity`)
                  VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
                  array(
                    $order_id, $total, $item_detail->menu_item_id, $item_detail->menu_item_name, $item_detail->amount,
                    $detail_note, $json_data->waitres, date("Y-m-d H:i:s"), $json_data->waitres, date("Y-m-d H:i:s"),
                    $json_data->table->company_id, $json_data->table->branch_id, 0
                  )
                );
              }
            }
          }

          // save order_item
          R::exec(
            "INSERT INTO `order_item` (
              `order_id`, `order_item_no`, `product_id`, `price`, `quantity`, 
              `type_of_discount`, `disc_percentage`, `disc_amount`, `parent_no`, `dine_in`, 
              `menu_id`, `menu_item_id`, `menu_item_name`, `note`, `void`, 
              `void_note`, `mgr_id`, `created_by`, `created_date`, `updated_by`, 
              `updated_date`, `company_id`, `branch_id`, `invoiced_quantity`, `unit`, 
              `seat`, `split_no`, `bill_quantity`, `event_name`, `additional`, 
              `pending`, `link_item`, `print_quantity`, `category`, `additional_no`, 
              `cost_price`, `order_by`, `flag_void`, `vat`) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            array(
              $order_id, $total, $order_item->product_id, $order_item->sales_price, $order_item->amount,
              0, 0, 0, 0, $order_item->dineIn == true ? 1 : 0,
              $order_item->menu_id, $order_item->menu_item_id, $order_item->menu_item_name, $item_note, (int)$order_item->void,
              $order_item->void_note, '', $json_data->waitres,date("Y-m-d H:i:s") , $json_data->waitres,
              date("Y-m-d H:i:s"), $json_data->table->company_id, $json_data->table->branch_id, 0, '',
              0, 0, 0, '', $order_item->tambahan == true ? 1 : 0,
              0, 0, 0, $order_item->category, $order_item->tambahan == true ? $order_item->localSort : 0,
              0, $json_data->waitres_name, 0, 10
            )
          );
        $total = $total + 1;
      }
    }


    // CREATE PRINT JOB
    foreach ($json_data->menus as $key => $order_item) {
      if ($order_item->saved == false){
        $spesial = "";
        $menu = "";
        
        // MENU
        $menu = $order_item->amount." ".$order_item->menu_item_name;
        if ($order_item->has_popup == '1'){
          foreach ($order_item->popups as $key => $item_detail) {
            if ($item_detail->amount > 0){
              $menu = $menu."\n * ".$item_detail->amount." pcs ".$item_detail->menu_item_name;
            }
          }
        }

        //SPECIAL REQUEST
        if (count($order_item->specialRequest) > 0){
          $xx = "";
          foreach ($order_item->specialRequest as $key => $special) {
            if ($special->show_quantity == "0")
              $xx = $special->request_summary.",".$xx;
            else
              $xx = $special->amount." ".$special->request_summary.",".$xx;
          }
          $xx = trim($xx, ",");
          $xx = "( ".$xx." )";
        }
        
        // END SPECIAL REQ
        $menu = $menu." ".$xx;

        // ORDER DINE IN
        if ($order_item->dineIn){
          if ($order_item->category == "Makanan"){
            if ($PRINT['Dine-in_Makanan'] == "")
              $PRINT['Dine-in_Makanan'] = $menu;
            else
              $PRINT['Dine-in_Makanan'] = $PRINT['Dine-in_Makanan']."\n".$menu;
          }else if ($order_item->category == "Minuman"){
            if ($PRINT['Dine-In_Minuman'] == "")
              $PRINT['Dine-In_Minuman'] = $menu;
            else
              $PRINT['Dine-In_Minuman'] = $PRINT['Dine-In_Minuman']."\n".$menu;
          }else if ($order_item->category == "Es Teler"){
            if ($PRINT['Dine-In_Es-Teler'] == "")
              $PRINT['Dine-In_Es-Teler'] = $menu;
            else
              $PRINT['Dine-In_Es-Teler'] = $PRINT['Dine-In_Es-Teler']."\n".$menu;
          }else{
            if ($PRINT['Dine-In_Sop-Buah'] == "")
              $PRINT['Dine-In_Sop-Buah'] = $menu;
            else
              $PRINT['Dine-In_Sop-Buah'] = $PRINT['Dine-In_Sop-Buah']."\n".$menu;
          }

        }else{
          if ($order_item->category == "Makanan"){
            if ($PRINT['Take-Away_Makanan'] == "")
              $PRINT['Take-Away_Makanan'] = $menu;
            else
              $PRINT['Take-Away_Makanan'] = $PRINT['Take-Away_Makanan']."\n".$menu;
          }else if ($order_item->category == "Minuman"){
            if ($PRINT['Take-Away_Minuman'] == "")
              $PRINT['Take-Away_Minuman'] = $menu;
            else
              $PRINT['Take-Away_Minuman'] = $PRINT['Take-Away_Minuman']."\n".$menu;
          }else if ($order_item->category == "Es Teler"){
            if ($PRINT['Take-Away_Es-Teler'] == "")
              $PRINT['Take-Away_Es-Teler'] = $menu;
            else
              $PRINT['Take-Away_Es-Teler'] = $PRINT['Take-Away_Es-Teler']."\n".$menu;
          }else{
            if ($PRINT['Take-Away_Sop-Buah'] == "")
              $PRINT['Take-Away_Sop-Buah'] = $menu;
            else
              $PRINT['Take-Away_Sop-Buah'] = $PRINT['Take-Away_Sop-Buah']."\n".$menu;
          }
        }
      }
    }
    // SAVE PRINT_JOB

    foreach ($PRINT as $key => $value) {
      if ($value != ""){
        $keys = explode("_", $key);
        $types = str_replace("-", " ", $keys[1]);
        $order_type = str_replace("-", " ", $keys[0]);
        if (strtoupper($order_type) == "TAKE AWAY")
          $text = "@".strtoupper($types).": ".strtoupper($order_type);
        else
          $text = "@".strtoupper($types).": TAMBAHAN MEJA ".$json_data->table->table_name;
        $text .= "\n------------------------------------------";
        $text .= "\n�". $value;
        $text .= "\n\n------------------------------------------\n";
        $text .= date("d/m/Y")." ".$json_data->waitres_name." ".date("H:i:s");

        $printer = R::getRow(
            "SELECT * FROM `printer_setting` WHERE printer_function LIKE '%?%'", array("Tambahan")
        );
        if (count($printer) == 0){
          $printer = R::getRow(
            "SELECT * FROM `printer_setting` WHERE print_default = 1 ORDER BY printer_id ASC LIMIT 1"
          );
        }

        R::exec(
          "INSERT INTO `print_jobs` (
            `start_time`, `print_type`, `request_terminal`, `printer_name`, `print_text`, 
            `update_required`, `printed`, `print_category`, `order_id`, `printed_terminal`, 
            `printed_time`
          ) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
          array(
            date("Y-m-d H:i:s"), "Order", "MOBILE", $printer['printer_name'], $text,
            1,0, strtolower($types), $order_id, "",
            date("Y-m-d H:i:s")
            )
        );
        //TODO PRINTER NAME

      }
    }
    // END PRINT JOB

    // table status
    R::exec("UPDATE `orders` SET `updated_by`= ? , `updated_date`= ? WHERE `order_id`= ?",
      array($json_data->waitres, date("Y-m-d H:i:s"), $order_id));
    R::commit();


    // TODO take away, queque_no, shift_id, pax on orders
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

function get_sales_day(){
  $sales_day = R::getRow(
    "SELECT DATE_FORMAT(open_day, '%Y%m%d') as open_day FROM `sales_day` 
    WHERE closed = 0 ORDER BY open_day ASC LIMIT 1"
  );
  if ($sales_day == null)
    return null;
  else
    return $sales_day['open_day'];
}

// GET NUMBER OF TODAY ORDER
function get_today_order(){
  $today_order = R::getRow(
    "SELECT CONVERT(SUBSTRING(order_id,9), UNSIGNED INTEGER) as count FROM `orders` 
    WHERE order_id LIKE '".get_sales_day()."%' ORDER BY order_id DESC LIMIT 1"
  );

  if ($today_order == null)
    return 0; 
  else 
    return $today_order['count'];
}


// GET ORDER TABLES
$app->get('/get_order', function () use($app){
  $params = ($app->request->get());
  $total = 0;
  // GET ORDER ID
  $table = R::getRow(
    "SELECT order_id, table_id, table_name, table_status, floor_id, room_id, branch_id, parent_id, company_id FROM floor_plant WHERE room_id = 'Dine In' AND table_id = ?", array($params['table_id'])
  );
  $order_id = $table['order_id'];
  $table['parent_id'] = (int)$table['parent_id'];

  $order = R::getRow(
    "SELECT * FROM orders where order_id = ?", array($order_id)
  );

  $order_item = R::getAll(
    "SELECT o.order_item_no, o.note, CAST(o.dine_in as UNSIGNED) as dineIn, CAST(o.additional AS UNSIGNED) as tambahan,
    cast(o.order_item_no as UNSIGNED) as universalSort,cast(o.quantity as UNSIGNED) as amount,
    cast(o.quantity as UNSIGNED) as amountTmp, cast(o.void as UNSIGNED) as void, 
    o.void_note, m.menu_item_id, m.product_id, m.menu_item_name, m.menu_id, 
    m.popup_menu_id, m.sold_out_item,m.sales_price, m.category,
    IF(LENGTH(m.popup_menu_id), true, false) as has_popup, COALESCE(so.item_id,0) AS item_id,
    CASE WHEN so.sold_out IS NOT NULL THEN cast(so.sold_out AS SIGNED) ELSE 0 END as sold_out
    FROM menu_item as m JOIN order_item as o ON m.menu_item_name = o.menu_item_name 
    LEFT OUTER JOIN sold_out_item as so ON so.item_name = m.sold_out_item
    WHERE o.order_id = ? AND m.menu_item_id = o.menu_item_id ORDER BY universalSort", array($order_id)
  );

  foreach ($order_item as $key => $value) {
    $order_item[$key]['saved'] = true;
    $order_item[$key]['amount'] = (int)$value['amount'];
    $order_item[$key]['amountTmp'] = (int)$value['amountTmp'];
    $order_item[$key]['order_item_no'] = (int)$value['order_item_no'];
    $order_item[$key]['universalSort'] = (int)$value['universalSort'];
    $order_item[$key]['dineIn'] = (int)$value['dineIn'] == 0 ? false : true;
    $order_item[$key]['orderType'] = (int)$value['dineIn'] == 0 ? "Take Away" : (int)$value['tambahan'] == 0 ? "Dine In" : "Tambahan";
    $order_item[$key]['tambahan'] = (int)$value['tambahan'] == 0 ? false : true;
    $order_item[$key]['codeOrder'] = (int)$value['dineIn'] == 0 ? 2000 : (int)$value['tambahan'] == 0 ? 1000 : 3000;
    $total = (int)$value['order_item_no'];
    // if has popups
    if ($value['has_popup'] == '1'){
      $order_detail = R::getRow(
        "SELECT * FROM order_item_detail WHERE order_id = ?", array($order_id)
      );

      if ($order_item['popup_menu_id'] != '1'){
          if ($order_item['item_id'] == "0"){
            $order_item[$key]['popups'] = R::getAll(
                "SELECT menu_item_id, menu_item_name, menu_id, popup_menu_id, 
                '0' as sold_out
                FROM `menu_item` as m WHERE menu_id = '".$order_item['popup_menu_id']."'
                ORDER BY menu_item_name"
            );
          }else{

            $order_item[$key]['popups'] = R::getAll(
                "SELECT m.menu_item_id, m.menu_item_name, m.menu_id, m.popup_menu_id, 
                m.sold_out_item,
                CASE WHEN so.sold_out IS NOT NULL THEN cast(so.sold_out AS SIGNED) ELSE 0 END as sold_out
                FROM `menu_item` as m LEFT OUTER JOIN sold_out_detail as so ON so.item_name = m.sold_out_item
                WHERE m.menu_id = '".$order_item[$key]['popup_menu_id']."' AND so.item_id = '".$order_item[$key]['item_id']."'
                ORDER BY menu_item_name"
            );
          }
        }
        if (count($order_item[$key]['popups']) == 0){
            $order_item[$key]['has_popup'] = '0';
        }

        foreach ($order_item[$key]['popups'] as $k => $val) {
            if ($val['menu_item_name'] == $order_detail['menu_item_name']){
              $order_item[$key]['popups'][$k]['amount'] = (int)$order_detail['quantity'];
              $order_item[$key]['popups'][$k]['amountTmp'] = (int)$order_detail['quantity'];
              $order_item[$key]['bagian'] = $order_detail['menu_item_name'];
            }else{
              $order_item[$key]['popups'][$k]['amount'] = 0;
              $order_item[$key]['popups'][$k]['amountTmp'] = 0;
            }
        }


      $order_item[$key]['specialNotePos'] = 0;
      // special request
      if ($order_detail['note'] != ''){
        $arrSpecial = explode(",",$order_detail['note']);
        foreach ($arrSpecial as $l => $v) {
          $jmls = explode("-", $v);
          // have count
          if (count($jmls) > 1){
            $order_item[$key]['specialRequest'][$l] = R::getRow(
                "SELECT m.request_name, cast(m.show_quantity AS UNSIGNED) as show_quantity, 
                m.request_summary, m.request_no, ? as amount, false as isNote, ? as to_save
                FROM `special_request` as m
                WHERE m.link_menu = 0 AND m.request_summary = ?
                ORDER BY m.request_no"
              , array($jmls[0], $v, $jmls[1])
            );
            $order_item[$key]['specialRequest'][$l]['isNote'] =false;
          }else{
            $t = R::getRow(
                "SELECT m.request_name, cast(m.show_quantity AS UNSIGNED) as show_quantity, 
                m.request_summary, m.request_no, ? as amount, false as isNote, ? as to_save
                FROM `special_request` as m
                WHERE m.link_menu = 0 AND m.request_summary = ?
                ORDER BY m.request_no"
              , array("0", $v, $jmls[0])
            );
            if (count($t) == 0){
              $order_item[$key]['specialRequest'][$l]['request_name'] = "Other";
              $order_item[$key]['specialRequest'][$l]['isNote'] =true;
              $order_item[$key]['specialRequest'][$l]['request_summary'] = $jmls[0];
              $order_item[$key]['specialRequest'][$l]['to_save'] = $jmls[0];
              $order_item[$key]['specialRequest'][$l]['show_quantity'] = "0";
              $order_item[$key]['specialRequest'][$l]['request_no'] = "0";
              $order_item[$key]['specialRequest'][$l]['amount'] = "0";
              $order_item[$key]['specialNotePos'] = $l+1;

            }else{
              $order_item[$key]['specialRequest'][$l] = $t;
              $order_item[$key]['specialRequest'][$l]['isNote'] =false; 
            }
          }
        }
      }else
        $order_item[$key]['specialRequest'] = array();
    }

    // have no popup
    else{
      // special request
      if ($order_item[$key]['note'] != ''){
        $arrSpecial = explode(",",$order_item[$key]['note']);
        foreach ($arrSpecial as $l => $v) {
          $jmls = explode("-", $v);
          // have count
          if (count($jmls) > 1){
            $order_item[$key]['specialRequest'][$l] = R::getRow(
                "SELECT m.request_name, cast(m.show_quantity AS UNSIGNED) as show_quantity, 
                m.request_summary, m.request_no, ? as amount, false as isNote, ? as to_save
                FROM `special_request` as m
                WHERE m.link_menu = 0 AND m.request_summary = ?
                ORDER BY m.request_no"
              , array($jmls[0], $v, $jmls[1])
            );
            $order_item[$key]['specialRequest'][$l]['isNote'] =false;
          }else{
            $t = R::getRow(
                "SELECT m.request_name, cast(m.show_quantity AS UNSIGNED) as show_quantity, 
                m.request_summary, m.request_no, ? as amount, false as isNote, ? as to_save
                FROM `special_request` as m
                WHERE m.link_menu = 0 AND m.request_summary = ?
                ORDER BY m.request_no"
              , array("0", $v, $jmls[0])
            );
            if (count($t) == 0){
              $order_item[$key]['specialRequest'][$l]['request_name'] = "Other";
              $order_item[$key]['specialRequest'][$l]['isNote'] =true;
              $order_item[$key]['specialRequest'][$l]['request_summary'] = $jmls[0];
              $order_item[$key]['specialRequest'][$l]['to_save'] = $jmls[0];
              $order_item[$key]['specialRequest'][$l]['show_quantity'] = "0";
              $order_item[$key]['specialRequest'][$l]['request_no'] = "0";
              $order_item[$key]['specialRequest'][$l]['amount'] = "0";
              $order_item[$key]['specialNotePos'] = $l+1;
            }else{
              $order_item[$key]['specialRequest'][$l] = $t;
              $order_item[$key]['specialRequest'][$l]['isNote'] =false; 
            }
          }
        }
      }else
        $order_item[$key]['specialRequest'] = array();
    }

    

  }

  $output = array(
    'code' => $order_id == '' ? "ZERO" : "OK",
    'table' => $table,
    'waitres' => $order['created_by'],
    'waitres_name' => $order['order_by'],
    'persons' => $order['pax'],
    'menus' => $order_item,
    'orderId' => $order_id,
    'total' => $total
  );

  // echo json_encode($output);
  // $result = get_child($tmp);
  // $output = construct_output($result);
  header("Content-Type: application/json");
  echo json_encode($output);
});