<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/', function (Request $request, Response $response, array $args) use ($container) {
        echo "Home page";
    });


    $app->post('/demo', function (Request $request, Response $response, array $args) use ($container) {
        // รับค่าจากผู้ใช้
         $data = $this->request->getParsedBody();
         // echo $data['id'];
         return "ได้ข้อมูลจังหวัดเป็น " .$data['province'];
    });

    // Get All Products (ดึงรายชื่อสินค้าทั้งหมดออกมา)
    $app->get('/products', function (Request $request, Response $response, array $args) use ($container) {
       $sql =  $this->db->prepare("SELECT * FROM products");
       $sql->execute();
       $result = $sql->fetchAll();
       // print_r($result);
       // แปลง array เป็น JSON
       return $this->response->withJson($result);
    });

    // Get Products By ID (ดึงรายชื่อสินค้าระบุ id)
    $app->get('/products/{id}', function (Request $request, Response $response, array $args) use ($container) {
        $sql =  $this->db->prepare("SELECT * FROM products WHERE id='$args[id]'");
        $sql->execute();
        $result = $sql->fetchAll();
        // print_r($result);
        // แปลง array เป็น JSON
        return $this->response->withJson($result);
     });

     // Add Product (การเพิ่มสินค้าใหม่)
     $app->post('/products', function (Request $request, Response $response, array $args) use ($container) {
         // รับค่าจากผู้ใช้
         $data = $this->request->getParsedBody();

        $sql = "INSERT INTO products(
            product_name,
            product_barcode,
            product_qty,
            product_price,
            product_date,
            product_image,
            product_category,
            product_status) VALUES(
                :product_name, 
                :product_barcode, 
                :product_qty,
                :product_price,
                :product_date,
                :product_image,
                :product_category,
                :product_status)";

        $current_date = date('Y-m-d H:i:s', time());
        $status = 1;

        $sth =  $this->db->prepare($sql);
        $sth->bindParam("product_name",$data['product_name']);
        $sth->bindParam("product_barcode",$data['product_barcode']);
        $sth->bindParam("product_qty",$data['product_qty']);
        $sth->bindParam("product_price",$data['product_price']);
        $sth->bindParam("product_date",$current_date);
        $sth->bindParam("product_image",$data['product_image']);
        $sth->bindParam("product_category",$data['product_category']);
        $sth->bindParam("product_status",$status);
        $sth->execute();

        // ดึงรายการ id ล่าสุดที่เพิ่มเข้าไปออกมาแสดง
        $result = $this->db->lastInsertId();

        // แปลง array เป็น JSON
        return $this->response->withJson($result);
     });


};
