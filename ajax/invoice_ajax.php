<?php
require '../includes/config.php';
global $conn;
if (isset($_POST['action']) && $_POST['action'] == "list") {
    header('Content-type: application/json');
    $inv_obj = new Invoice;
    $inv_data = $inv_obj->GET_ALL_INVOICES();
    $response_array['data'] = array();
    foreach ($inv_data as $val) {
        array_push($response_array['data'], $val);
    }
    echo json_encode($response_array);
}
if (isset($_POST['action']) && $_POST['action'] == "add") {
    $invoice = $_REQUEST['invoice'];
    $sname = $_REQUEST['sname'];
    $supplier_obj = new Supplier;
    $supplier_data = $supplier_obj->GET_SUPPLIER_BY_NAME($sname);
    $supplier_id = $supplier_data->supplier_master_id;
    $supplier_current_balance = $supplier_data->supplier_master_current_balance;
    $op = $_REQUEST['op_form'];
    $total = $supplier_current_balance + $op;
    // $bname = $_REQUEST['bname'];
    // $bac = $_REQUEST['accnt'];
    $dt = $_REQUEST['date_form'];
    $date = date("d-m-Y", strtotime($dt));
    $date1 = date("d-m-Y H:i:s A");
    $trans_type = 2;
    $arr = array(
        "invoice_number" => $invoice,
        "supplier_id" => $supplier_id,
        "date" => $date,
        "amount" => $op,

    );
    $query = InsertData($arr, "invoice_master");
    $sql1 = "INSERT INTO transaction_master(supplier_id,trans_amnt,date,trans_type,invoice_no,create_on,updated_on) VALUES('" . $supplier_id . "','" . $op . "','" . $date . "','" . $trans_type . "','" . $invoice . "','" . $date1 . "','" . $date1 . "')";
    $result2 = mysqli_query($conn, $sql1);
    // echo $query1=InsertData($arr1,"transaction_master");


    if ($query == 1 && $result2 == 1) {
        // $result = UpdateData($update, "supplier_master", $where_upd);
        // if ($result == 1) {
        $sql = "UPDATE supplier_master SET supplier_master_current_balance = supplier_master_current_balance + $op , updated_on='" . date('d-m-y H:i A') . "' WHERE supplier_master_id= $supplier_id";
        $result = mysqli_query($conn, $sql);
        echo "success";
        // }
    } else {
        echo "error";
    }
}
if (isset($_POST['action']) && ($_POST['action'] == 'delete')) {
    $id = $_REQUEST['id'];
    echo $id;
    echo "\n";
    $inv_obj = new Invoice;
    $inv_data = $inv_obj->GET_INVOICE_BY_ID($id);
    echo $invoice_no = $inv_data->invoice_number;
    echo "\n";
    echo $sup_id = $inv_data->supplier_id;
    echo "\n";
    echo $amnt = $inv_data->amount;
    echo "\n";
    $inv_obj->DELETE_INVOICE($id);


    $sql = "SELECT * FROM supplier_master where supplier_master_id =$sup_id";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $sup_bal = $row['supplier_master_current_balance'];
        }
    }

    // $sup_obj = new Supplier;
    // $sup_data = $sup_obj->GET_SUPPLIER_BY_ID($sup_id);
    // echo $sup_bal = $sup_data->supplier_master_current_balance;
    echo $sup_bal;

    $trans_obj = new Transaction;
    $trans_data = $trans_obj->DELETE_TRANSACTION_FOR_INVOICE($invoice_no);

    $arr = array(
        'supplier_master_current_balance' => $sup_bal - $inv_amnt,
        'updated_on' => date('d-m-Y H:i A'),

    );

    $where_arr = array(
        'supplier_master_id' => $sup_id,
    );

    $result = UpdateData($arr, "supplier_master", $where_arr);

    if ($result == 1) {
        echo "success";
    }

    // $sql="SELECT * FROM invoice_master where id=$id";


}
