<?php
session_start();
?>
<?php
include_once("class.db.php");
if ($_SERVER["REQUEST_METHOD"] == 'GET') {
    echo json_encode(product_list(), JSON_UNESCAPED_UNICODE);
} else if ($_SERVER["REQUEST_METHOD"] == 'POST') {

    echo json_encode (open_bill());
}
function product_list()
{
    $db = new database();
    $db->connect();
    $sql = "SELECT Product_id,Product_code,Product_Name,
                       brand.Brand_name, unit.Unit_name,
                       product.Cost, product.Stock_Quantity
                FROM  product,brand,unit 
                WHERE product.Brand_ID = brand.Brand_id
                and   product.Unit_ID  = unit.Unit_id";
    $result = $db->query($sql);
    $db->close();
    return $result;
}
function open_bill()
{
     //1. check have spme openbill?
    //1.1a no  : create new open_bile
    //1.1b yes : check staus openbill = 1
    //1.1a yes:
    //1.2.1 check product id exist no : add product to bill_detail
    //1.2.2 check product id exist yes: update aty in bill_detail
    //1.1b no:
    $db = new database();
    $db->connect();
    $sql = "SELECT Bill_id,Bill_status FROM bill WHERE Cus_ID = '{$_SESSION['cus_id']}' ORDER BY Bill_id DESC LIMIT 1";
    $bull_result = $db->query($sql);
    $p_id = $_POST['p_id'];
    $p_qty = $_POST['p_qty'];
    $p_price = $_POST['p_price'];
    if (sizeof($bull_result) == 0) {
        //insert new
        $sql = "INSERT INTO `bill`(`Bill_id`, `Cus_ID`, `Bill_Status`) VALUES (1,'{$_SESSION['cus_id']},,0)";
        $result = $db->exec($sql);
        $sql = "INSERT INTO bill_detail (Bill_id, Product_ID, Quantity, Unit_Price) 
                VALUES (1,'{$p_id}','{$p_qty}','{$p_price}')";
        $result = $db->exec($sql);
    } else {
        //check [0][0] bill_id
        //      [0][1] bill status
        if ($bull_result[0][1] == 0) {
            $sql = "SELECT Bill_id, Product_ID FROM bill_detail 
                    WHERE Bill_id ='{$_SESSION['cus_id']}' 
                    AND Product_ID = '{$p_id}'";
            $result = $db->query($sql);
            if (sizeof($result) == 0) {
                //add new product
                $sql = "INSERT INTO bill_detail (Bill_id, Product_ID, Quantity, Unit_Price) 
                        VALUES ({$bull_result[0][0]},'{$p_id}','{$p_qty}','{$p_price}')";
                $result = $db->exec($sql);
            } else {
            }
            //update current item
        }
    }
    return $bull_result;
}

?>