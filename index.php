<?php
require_once 'DatabaseGateway.php';

$database_gateway = new DatabaseGateway();

//Mainam šeit datubāzes nosaukumu
$databasename = "";
$tablename = "order_total";
$timestamp = "";

$tableNameIndex = [
    "lv_customer" => 0,
    "lv_order" => 1,
    "lv_product" => 2,
    "lv_user" => 3,
    "lv_address" => 4,
    "lv_order_total" => 5
];

//Atbilstoši table name izveidojam mainīgos ar vaicājumiem
switch ($tableNameIndex[$tablename]) {
    case 0:
        $sql_list = "SELECT firstname, lastname, telephone, ip, date_added FROM lv_customer LIMIT 0";
        $sql = "SELECT firstname, lastname, telephone, ip, date_added FROM lv_customer";
        break;
    case 1:
        $sql_list = "SELECT
    invoice_prefix,
    firstname,
    lastname,
    email,
    telephone,
    payment_firstname,
    payment_lastname,
    payment_company,
    payment_address_1,
    payment_method,
    shipping_firstname,
    shipping_lastname,
    shipping_address_1,
    shipping_method,
    comment,
    lv_order_product.name,
    lv_order.total,
    currency_code,
    ip,
    date_added,
    date_modified
FROM
    lv_order
LEFT JOIN lv_order_product
on lv_order.order_id = lv_order_product.order_id LIMIT 0";
        $sql = "SELECT
    invoice_prefix,
    firstname,
    lastname,
    email,
    telephone,
    payment_firstname,
    payment_lastname,
    payment_company,
    payment_address_1,
    payment_method,
    shipping_firstname,
    shipping_lastname,
    shipping_address_1,
    shipping_method,
    COMMENT,
    lv_order_product.name,
    lv_order.total,
    currency_code,
    ip,
    date_added,
    date_modified
FROM
    lv_order
LEFT JOIN lv_order_product
on lv_order.order_id = lv_order_product.order_id ";
        break;
    case 2:
        $sql_list = "SELECT model, price, viewed, date_added, date_modified FROM lv_product LIMIT 0";
        $sql = "SELECT model, price, viewed, date_added, date_modified  FROM lv_product";
        break;
    case 3:
        $sql_list = "SELECT username, firstname, lastname, email FROM lv_user LIMIT 0";
        $sql = "SELECT username, firstname, lastname, email  FROM lv_user";
        break;
    case 4:
        $sql_list = "SELECT firstname, lastname, company, address_1 FROM lv_address LIMIT 0";
        $sql = "SELECT firstname, lastname, company, address_1  FROM lv_address";
        break;
    case 5:
        $sql_list = "SELECT order_total_id, order_id, code, title, `value` FROM lv_order_total LIMIT 0";
        $sql = "SELECT order_total_id, order_id, code, title, `value`  FROM lv_order_total";
        break;
}

$response = $database_gateway->getColumnNames($sql_list);
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Atskaite (laika zīmogs - <?=$timestamp?> no datubāzes <?= $databasename ?> tabulas - <?= $tablename ?></title>
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/r/dt/jq-2.1.4,jszip-2.5.0,pdfmake-0.1.18,dt-1.10.9,af-2.0.0,b-1.0.3,b-colvis-1.0.3,b-html5-1.0.3,b-print-1.0.3,se-1.0.1/datatables.min.css"/>

    <script type="text/javascript"
            src="https://cdn.datatables.net/r/dt/jq-2.1.4,jszip-2.5.0,pdfmake-0.1.18,dt-1.10.9,af-2.0.0,b-1.0.3,b-colvis-1.0.3,b-html5-1.0.3,b-print-1.0.3,se-1.0.1/datatables.min.js"></script>
    <div class="container" style="padding:20px;20px;">
        <div class="">
            <h1>Interneta veikala datu atspoguļošana</h1>
            <div class="">
                <table id="orders" class="display">
                    <thead>
                    <tr>
                        <?php
                        foreach ($response as $key) {
                            echo '<th>' . $key . '</th>';
                        }
                        ?>
                    </tr>
                    </thead>

                    <tfoot>
                    <tr>
                        <?php
                        foreach ($response as $key) {
                            echo '<th>' . $key . '</th>';
                        }
                        ?>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#orders').DataTable({
                "processing": true,
                "aLengthMenu": [[15, 25, 45, 100, -1], [15, 25, 45, 100, "Visi"]],
                "sAjaxSource": "response.php?query=<?=trim(preg_replace('/\s+/', ' ', $sql))?>",
                "dom": 'lBfrtip',
                "buttons": [
                    {
                        extend: 'collection',
                        text: 'Export',
                        buttons: [
                            'copy',
                            'excel',
                            'csv',
                            'pdf',
                            'print'
                        ]
                    }
                ]
            });
        });
    </script>
