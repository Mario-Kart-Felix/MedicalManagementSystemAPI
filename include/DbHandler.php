<?php

require_once dirname(__FILE__).'/JWT.php';

    $JWT = new JWT;


class DbHandler
{
    private $con;
    private $userId;
    private $saleId;

    function __construct()
    {
        require_once dirname(__FILE__) . '/DbCon.php';
        $db = new DbCon;
        $this->con =  $db->Connect();
    }

    //Getter Setter For User Id Only

    function setUserId($userId)
    {
        $this->userId = $userId;
    }

    function getUserId()
    {
        return $this->userId;
    }

    function setSaleId($saleId)
    {
        $this->saleId = $saleId;
    }

    function getSaleId()
    {
        return $this->saleId;
    }

    function login($email,$password)
    {
        if($this->isEmailValid($email))
        {
            if($this->isEmailExist($email))
            {
                $hashPass = $this->getPasswordByEmail($email);
                if(password_verify($password,$hashPass))
                {
                    return LOGIN_SUCCESSFULL;
                }
                else
                    return PASSWORD_WRONG;
            }
            else
                return USER_NOT_FOUND;
        }
        else
            return EMAIL_NOT_VALID;
    }

    function verifyPassword($password)
    {
        $hashPass = $this->getPasswordById($this->getUserId());
        if (password_verify($password,$hashPass))
            return true;
        else
            return false;
    }

    function addProduct($productName,$productBrand,$productCategory,$productSize,$productLocation,$productPrice,$productQuantity,$productManufactureDate,$productExpireDate)
    {
        $query = "INSERT INTO products (product_name,brand_id,category_id,size_id,location_id,product_price,product_quantity,product_manufacture,product_expire) VALUES(?,?,?,?,?,?,?,?,?)";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("sssssssss",$productName,$productBrand,$productCategory,$productSize,$productLocation,$productPrice,$productQuantity,$productManufactureDate,$productExpireDate);
        if ($stmt->execute())
        {
            $this->addProductsRecord($productName,$productBrand,$productCategory,$productSize,$productLocation,$productPrice,$productQuantity,$productManufactureDate,$productExpireDate);
            return true;
        }
        else
            return false;
    }

    function updateProduct($productId,$productName,$productBrand,$productCategory,$productSize,$productLocation,$productPrice,$productQuantity,$productManufactureDate,$productExpireDate)
    {
        $productQuantity = $productQuantity + $this->getSalesCountByProductId($productId);
        $query = "UPDATE products SET product_name=?, brand_id=?, category_id=?, size_id=?, location_id=?, product_price=?, product_quantity=?, product_manufacture=?, product_expire=? WHERE product_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("ssssssssss",$productName,$productBrand,$productCategory,$productSize,$productLocation,$productPrice,$productQuantity,$productManufactureDate,$productExpireDate,$productId);
        if ($stmt->execute())
        {
            // $this->addProductsRecord($productName,$productBrand,$productCategory,$productSize,$productLocation,$productPrice,$productQuantity,$productManufactureDate,$productExpireDate);
            return true;
        }
        else
            return false;
    }

    function addSeller($sellerFirstName,$sellerLastName,$sellerEmail,$sellerContactNumber,$sellerContactNumber1,$sellerImage,$sellerAddress)
    {
        if (!empty($sellerImage))
            $sellerImage = $this->uploadImage($sellerImage);
        else
            $sellerImage = '';
        $query = "INSERT INTO sellers (seller_fname,seller_lname,seller_email,seller_contact,seller_contact_1,seller_image,seller_address) VALUES(?,?,?,?,?,?,?)";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("sssssss",$sellerFirstName,$sellerLastName,$sellerEmail,$sellerContactNumber,$sellerContactNumber1,$sellerImage,$sellerAddress);
        if ($stmt->execute())
        {
            return true;
        }
        else
            return false;
    }

     function getSellers()
    {
        $sellers = array();
        $query = "SELECT seller_id,seller_fname,seller_lname,seller_email,seller_contact,seller_contact_1,seller_image,seller_address from sellers ORDER BY seller_id ASC";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($sellerId,$sellerFirstName,$sellerLastName,$sellerEmail,$sellerContactNumber,$sellerContactNumber1,$sellerImage,$sellerAddress);
        while ($stmt->fetch())
        {
            $seller['sellerId'] = $sellerId;
            $seller['sellerFirstName'] = $sellerFirstName;
            $seller['sellerLastName'] = $sellerLastName;
            $seller['sellerEmail'] = $sellerEmail;
            $seller['sellerContactNumber'] = $sellerContactNumber;
            $seller['sellerContactNumber1'] = $sellerContactNumber1;
            if (isset($sellerImage) && !empty($sellerImage))
                $seller['sellerImage'] = WEBSITE_DOMAIN.$sellerImage;
            else
                $seller['sellerImage'] = null;
            $seller['sellerAddress'] = $sellerAddress;
            array_push($sellers, $seller);
        }
        return $sellers;
    }




    function uploadImage($image)
    {
        $imageUrl ="";
        if ($image!=null) 
        {
            $imageName = $image->getClientFilename();
            $image = $image->file;
            $targetDir = "uploads/";
            $targetFile = $targetDir.uniqid().'.'.pathinfo($imageName,PATHINFO_EXTENSION);
            if (move_uploaded_file($image,$targetFile))
                $imageUrl = $targetFile;
        }
        return $imageUrl;
    }

    function addProductsRecord($productName,$productBrand,$productCategory,$productSize,$productLocation,$productPrice,$productQuantity,$productManufactureDate,$productExpireDate)
    {
        $query = "INSERT INTO products_record (product_name,brand_id,category_id,size_id,location_id,product_price,product_quantity,product_manufacture,product_expire) VALUES(?,?,?,?,?,?,?,?,?)";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("sssssssss",$productName,$productBrand,$productCategory,$productSize,$productLocation,$productPrice,$productQuantity,$productManufactureDate,$productExpireDate);
        if ($stmt->execute())
            return true;
        else
            return false;
    }

    function addBrand($brandName)
    {
        $query = "INSERT INTO brands (brand_name) VALUES(?)";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("s",$brandName);
        if ($stmt->execute())
            return true;
        else
            return false;
    }

    function sellProduct($productId)
    {
        if ($this->isProductAvailable($productId))
        {
            $productPrice = $this->getProductPriceById($productId);
            $productQuantity = 1;
            $query = "INSERT INTO sells (product_id,sell_quantity,sell_price) VALUES(?,?,?)";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("sss",$productId,$productQuantity,$productPrice);
            if ($stmt->execute())
            {
                $this->setSaleId($stmt->insert_id);
                return SELL_PRODUCT;
            }
            else
                return SELL_PRODUCT_FAILED;
        }
        else
            return PRODUCT_QUANTITY_LOW;
    }

    function isProductAvailable($productId)
    {
        $productQuantity = $this->getProductQuantityById($productId);
        $salesQuantity = $this->getAllSalesQuantityOfProudctById($productId);
        if ($productQuantity-$salesQuantity>0)
            return true;
        else
            return false;
    }

    function deleteSoldProduct($sellId)
    {
        if($this->isSaleExist($sellId))
        {
            $query = "DELETE FROM sells WHERE sell_id =?";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("s",$sellId);
            if ($stmt->execute())
                return SALE_RECORD_DELETED;
            else
                return SALE_RECORD_DELETE_FAILED;
        }
        else
            return SALE_NOT_EXIST;
    }

    function isSaleExist($sellId)
    {
        $query = "SELECT sell_id from sells WHERE sell_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$sellId);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows()>0)
            return true;
        else
            return false;
    }

    function getProductById($productId)
    {
        $products = array();
        $pro = array();
        if (!$this->isProductExist($productId))
           return $pro;
        $query = "SELECT product_id,category_id,product_name,size_id,brand_id,product_price,location_id,product_manufacture,product_expire FROM products WHERE product_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$productId);
        $stmt->execute();
        $stmt->bind_result($productId,$categoryId,$productName,$sizeId,$brandId,$productPrice,$locationId,$productManufacture,$productExpire);
        $stmt->fetch();
        $product['productId']           = $productId;
        $product['categoryId']          = $categoryId;
        $product['productName']         = $productName;
        $product['sizeId']              = $sizeId;
        $product['brandId']             = $brandId;
        $product['productPrice']        = $productPrice;
        $product['locationId']          = $locationId;
        $product['productManufacture']        = $productManufacture;
        $product['productExpire']        = $productExpire;
        array_push($products, $product);
        $stmt->close();
        foreach ($products as  $product)
        {
            $pro['productId']               = $product['productId'];
            $pro['saleId']                  = $this->getSaleId(); 
            $pro['productCategory']         = $this->getCategoryById($product['categoryId']);
            $pro['productName']             = $product['productName'];
            $pro['productSize']             = $this->getSizeById($product['sizeId']);
            $pro['productBrand']            = $this->getBrandById($product['brandId']);
            $pro['productPrice']            = $product['productPrice'];
            $pro['productLocation']         = $this->getLocationById($product['locationId']);
            $pro['productQuantity']         = $this->getProductCurrentQuantityById($product['productId']);
            $pro['productManufacture']      = substr($product['productManufacture'], 0, 7);
            $pro['productExpire']           = substr($product['productExpire'], 0, 7);
        }
        return $pro;
    }

    function updateSellProduct($saleId,$productQuantity,$productSalePrice)
    {
        if ($this->isSaleExist($saleId))
        {
            $productId = $this->getProductIdBySaleId($saleId);
            $currentSaleQuantity = $this->getSalesProductQuantityById($saleId);
            if ($this->getProductCurrentQuantityById($productId)+$currentSaleQuantity-$productQuantity>=0) 
            {
                 $query = "UPDATE sells SET sell_quantity=?, sell_price=? WHERE sell_id=?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param('sss',$productQuantity,$productSalePrice,$saleId);
                if ($stmt->execute())
                {
                    return SALE_UPDATED;
                }
                else
                    return SALE_UPDATE_FAILED;
            }
            else
                return PRODUCT_QUANTITY_LOW;
        }
        else
            return SALE_NOT_EXIST;
    }

    function getProductCurrentQuantityById($productId)
    {
        $currentSaleId = $this->getAllSalesQuantityOfProudctById($productId);
        $productQuantity = $this->getProductQuantityById($productId);
        return $productQuantity-$currentSaleId;
    }

    function getSalesProductQuantityById($saleId)
    {
        $query = "SELECT sell_quantity from sells WHERE sell_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$saleId);
        $stmt->execute();
        $stmt->bind_result($saleQuanitty);
        $stmt->fetch();
        return $saleQuanitty;
    }

    function getAllSalesQuantityOfProudctById($productId)
    {
        $allCount = 0;
        $query = "SELECT sell_quantity from sells WHERE product_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$productId);
        $stmt->execute();
        $stmt->bind_result($saleQuanitty);
        while($stmt->fetch())
        {
            $allCount = $allCount+$saleQuanitty;
        }
        return $allCount;
    }

    function getProductIdBySaleId($saleId)
    {
        $query = "SELECT product_id from sells WHERE sell_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$saleId);
        $stmt->execute();
        $stmt->bind_result($productId);
        $stmt->fetch();
        return $productId;
    }

    function addSize($sizeName)
    {
        $query = "INSERT INTO sizes (size_name) VALUES(?)";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("s",$sizeName);
        if ($stmt->execute())
            return true;
        else
            return false;
    }

    function addCategory($categoryName)
    {
        $query = "INSERT INTO categories (category_name) VALUES(?)";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("s",$categoryName);
        if ($stmt->execute())
            return true;
        else
            return false;
    }

    function addLocation($locationName)
    {
        $query = "INSERT INTO locations (location_name) VALUES(?)";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("s",$locationName);
        if ($stmt->execute())
            return true;
        else
            return false;
    }

    function isEmailExist($email)
    {
        $query = "SELECT id FROM admin WHERE email=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows>0 ;
    }

    function isProductExist($productId)
    {
        $query = "SELECT product_id FROM products WHERE product_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$productId);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows>0 ;
    }

    function getBrands()
    {
        $brands = array();
        $query = "SELECT brand_id,brand_name FROM brands";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($brandId,$brandName);
        while ($stmt->fetch())
        {
            $brand['brandId'] = $brandId;
            $brand['brandName'] = $brandName;
            array_push($brands, $brand);
        }
        return $brands;
    }

    function getSizes()
    {
        $sizes = array();
        $query = "SELECT size_id,size_name FROM sizes";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($sizeId,$sizeName);
        while ($stmt->fetch())
        {
            $size['sizeId'] = $sizeId;
            $size['sizeName'] = $sizeName;
            array_push($sizes, $size);
        }
        return $sizes;
    }

    function getCategories()
    {
        $categories = array();
        $query = "SELECT category_id,category_name FROM categories";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($categoryId,$categoryName);
        while ($stmt->fetch())
        {
            $category['categoryId'] = $categoryId;
            $category['categoryName'] = $categoryName;
            array_push($categories, $category);
        }
        return $categories;
    }

    function getTodaysSalesRecord()
    {
        $products = array();
        $pr = array();
        date_default_timezone_set('Asia/Kolkata');
        $date = new DateTime();
        $date->setTime(00,00);
        $startDT = $date->format('Y-m-d H:i:s');
        $date->setTime(23,59);
        $endDT = $date->format('Y-m-d H:i:s');
        $query = "SELECT sell_id,product_id,sell_quantity,sell_price,created_at FROM sells where created_at between '$startDT' and '$endDT' ORDER By sell_id DESC";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($sellId,$productId,$saleQuanitty,$sellPrice,$createdAt);
        while ($stmt->fetch()) 
        {
            $pro['sellId'] = $sellId;
            $pro['productId'] = $productId;
            $pro['saleQuanitty'] = $saleQuanitty;
            $pro['sellPrice'] = $sellPrice;
            $pro['createdAt'] = $createdAt;
            array_push($products, $pro);
        }
        $stmt->close();
        foreach ($products as $product)
        {
            $pro = $this->getProductById($product['productId']);
            $pro['saleId'] = $product['sellId'];
            $pro['saleQuanitty'] = $product['saleQuanitty'];
            $pro['salePrice'] = $product['sellPrice'];
            $pro['createdAt'] = $product['createdAt'];
            array_push($pr, $pro);
        }
        return $pr;
    }

    function getSalesCountByProductId($productId)
    {
        $productQuantity = 0;
        $query = "SELECT sell_quantity FROM sells WHERE product_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$productId);
        $stmt->execute();
        $stmt->bind_result($quantity);
        while ($stmt->fetch())
        {
            $productQuantity = $productQuantity+$quantity;
        }
        return $productQuantity;
    }

    function getAllSalesRecord()
    {
        $products = array();
        $pr = array();
        date_default_timezone_set('Asia/Kolkata');
        $date = new DateTime();
        $date->setTime(00,00);
        $startDT = $date->format('Y-m-d H:i:s');
        $date->setTime(23,59);
        $endDT = $date->format('Y-m-d H:i:s');
        $query = "SELECT sell_id,product_id,sell_quantity,sell_price,created_at FROM sells ORDER By sell_id DESC";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($sellId,$productId,$saleQuanitty,$sellPrice,$createdAt);
        while ($stmt->fetch()) 
        {
            $pro['sellId'] = $sellId;
            $pro['productId'] = $productId;
            $pro['saleQuanitty'] = $saleQuanitty;
            $pro['sellPrice'] = $sellPrice;
            $pro['createdAt'] = $createdAt;
            array_push($products, $pro);
        }
        $stmt->close();
        foreach ($products as $product)
        {
            $pro = $this->getProductById($product['productId']);
            $pro['saleId'] = $product['sellId'];
            $pro['saleQuanitty'] = $product['saleQuanitty'];
            $pro['salePrice'] = $product['sellPrice'];
            $pro['createdAt'] = $product['createdAt'];
            array_push($pr, $pro);
        }
        return $pr;
    }

    function getLocations()
    {
        $locations = array();
        $query = "SELECT location_id,location_name FROM locations";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($locationId,$locationName);
        while ($stmt->fetch())
        {
            $location['locationId'] = $locationId;
            $location['locationName'] = $locationName;
            array_push($locations, $location);
        }
        return $locations;
    }

    function getProducts()
    {
        $products = array();
        $productss = array();
        $query = "SELECT product_id,category_id,product_name,size_id,brand_id,product_price,product_quantity,location_id,product_manufacture,product_expire FROM products ORDER by product_name ASC";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($productId,$categoryId,$productName,$sizeId,$brandId,$productPrice,$productQuantity,$locationId,$productManufacture,$productExpire);
        while ($stmt->fetch())
        {
            $product['productId']           = $productId;
            $product['categoryId']          = $categoryId;
            $product['productName']         = $productName;
            $product['sizeId']              = $sizeId;
            $product['brandId']             = $brandId;
            $product['productPrice']        = $productPrice;
            $product['productQuantity']     = $productQuantity;
            $product['locationId']          = $locationId;
            $product['productManufacture']  = $productManufacture;
            $product['productExpire']       = $productExpire;
            array_push($products, $product);
        }
        foreach ($products as  $product)
        {
            $pro['productId']               = $product['productId'];
            $pro['productCategory']         = $this->getCategoryById($product['categoryId']);
            $pro['productName']             = $product['productName'];
            $pro['productSize']             = $this->getSizeById($product['sizeId']);
            $pro['productBrand']            = $this->getBrandById($product['brandId']);
            $pro['productPrice']            = $product['productPrice'];
            $pro['productQuantity']         = $product['productQuantity']-$this->getSellQuantityByProductId($pro['productId']);
            $pro['productLocation']         = $this->getLocationById($product['locationId']);
            $pro['productManufacture']      = substr($product['productManufacture'], 0, 7);
            $pro['productExpire']           = substr($product['productExpire'], 0, 7);
            array_push($productss, $pro);
        }
        return $productss;
    }

    function getProductsRecord()
    {
        $products = array();
        $productss = array();
        $query = "SELECT product_id,category_id,product_name,size_id,brand_id,product_price,product_quantity,location_id,product_manufacture,product_expire FROM products_record ORDER by created_at DESC";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($productId,$categoryId,$productName,$sizeId,$brandId,$productPrice,$productQuantity,$locationId,$productManufacture,$productExpire);
        while ($stmt->fetch())
        {
            $product['productId']           = $productId;
            $product['categoryId']          = $categoryId;
            $product['productName']         = $productName;
            $product['sizeId']              = $sizeId;
            $product['brandId']             = $brandId;
            $product['productPrice']        = $productPrice;
            $product['productQuantity']     = $productQuantity;
            $product['locationId']          = $locationId;
            $product['productManufacture']  = $productManufacture;
            $product['productExpire']       = $productExpire;
            array_push($products, $product);
        }
        foreach ($products as  $product)
        {
            $pro['productId']               = $product['productId'];
            $pro['productCategory']         = $this->getCategoryById($product['categoryId']);
            $pro['productName']             = $product['productName'];
            $pro['productSize']             = $this->getSizeById($product['sizeId']);
            $pro['productBrand']            = $this->getBrandById($product['brandId']);
            $pro['productPrice']            = $product['productPrice'];
            $pro['productQuantity']         = $product['productQuantity'];
            $pro['productLocation']         = $this->getLocationById($product['locationId']);
            $pro['productManufacture']      = substr($product['productManufacture'], 0, 7);
            $pro['productExpire']           = substr($product['productExpire'], 0, 7);
            array_push($productss, $pro);
        }
        return $productss;
    }

    function getNoticeProducts()
    {
        $products = array();
        $productss = array();
        $query = "SELECT product_id,category_id,product_name,size_id,brand_id,product_price,product_quantity,location_id,product_manufacture,product_expire FROM products WHERE product_expire >= DATE_ADD(CURDATE(), INTERVAL 1 DAY) ORDER by product_expire DESC";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($productId,$categoryId,$productName,$sizeId,$brandId,$productPrice,$productQuantity,$locationId,$productManufacture,$productExpire);
        while ($stmt->fetch())
        {
            $product['productId']           = $productId;
            $product['categoryId']          = $categoryId;
            $product['productName']         = $productName;
            $product['sizeId']              = $sizeId;
            $product['brandId']             = $brandId;
            $product['productPrice']        = $productPrice;
            $product['productQuantity']     = $productQuantity;
            $product['locationId']          = $locationId;
            $product['productManufacture']  = $productManufacture;
            $product['productExpire']       = $productExpire;
            array_push($products, $product);
        }
        foreach ($products as  $product)
        {
            $salesQuantity = $this->getAllSalesQuantityOfProudctById($product['productId']);
            if ($product['productQuantity']-$salesQuantity<5)
            {
                $pro['productId']               = $product['productId'];
                $pro['productCategory']         = $this->getCategoryById($product['categoryId']);
                $pro['productName']             = $product['productName'];
                $pro['productSize']             = $this->getSizeById($product['sizeId']);
                $pro['productBrand']            = $this->getBrandById($product['brandId']);
                $pro['productPrice']            = $product['productPrice'];
                $pro['productQuantity']         = $product['productQuantity']-$this->getSellQuantityByProductId($pro['productId']);
                $pro['productLocation']         = $this->getLocationById($product['locationId']);
                $pro['productManufacture']      = substr($product['productManufacture'], 0, 7);
                $pro['productExpire']           = substr($product['productExpire'], 0, 7);
                array_push($productss, $pro);
            }
        }
        return $productss;
    }

    function getNoticeProductsCount()
    {
        $count = 0;
        $products = array();
        $productss = array();
        $query = "SELECT product_id,product_quantity FROM products";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($productId,$productQuantity);
        while ($stmt->fetch())
        {
            $product['productId']           = $productId;
            $product['productQuantity']     = $productQuantity;
            array_push($products, $product);
        }
        foreach ($products as  $product)
        {
            $salesQuantity = $this->getAllSalesQuantityOfProudctById($product['productId']);
            if ($product['productQuantity']-$salesQuantity<5)
            {
                $count++;
            }
        }
        $pro['productsNoticeCount'] = $count;
        return $pro;
    }

    function getExpiringProducts()
    {
        $products = array();
        $productss = array();
        $query = "SELECT product_id,category_id,product_name,size_id,brand_id,product_price,product_quantity,location_id,product_manufacture,product_expire FROM products WHERE product_expire <= DATE_ADD(CURDATE(), INTERVAL 6 MONTH) && product_expire >= DATE_ADD(CURDATE(), INTERVAL 1 DAY) ORDER by product_expire ASC";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($productId,$categoryId,$productName,$sizeId,$brandId,$productPrice,$productQuantity,$locationId,$productManufacture,$productExpire);
        while ($stmt->fetch())
        {
            $product['productId']           = $productId;
            $product['categoryId']          = $categoryId;
            $product['productName']         = $productName;
            $product['sizeId']              = $sizeId;
            $product['brandId']             = $brandId;
            $product['productPrice']        = $productPrice;
            $product['productQuantity']     = $productQuantity;
            $product['locationId']          = $locationId;
            $product['productManufacture']  = $productManufacture;
            $product['productExpire']       = $productExpire;
            array_push($products, $product);
        }
        foreach ($products as  $product)
        {
            $salesQuantity = $this->getAllSalesQuantityOfProudctById($product['productId']);
            $pro['productId']               = $product['productId'];
            $pro['productCategory']         = $this->getCategoryById($product['categoryId']);
            $pro['productName']             = $product['productName'];
            $pro['productSize']             = $this->getSizeById($product['sizeId']);
            $pro['productBrand']            = $this->getBrandById($product['brandId']);
            $pro['productPrice']            = $product['productPrice'];
            $pro['productQuantity']         = $product['productQuantity']-$this->getSellQuantityByProductId($pro['productId']);
            $pro['productLocation']         = $this->getLocationById($product['locationId']);
            $pro['productManufacture']      = substr($product['productManufacture'], 0, 7);
            $pro['productExpire']           = substr($product['productExpire'], 0, 7);
            array_push($productss, $pro);
        }
        return $productss;
    }

    function getExpiringProductsCount()
    {
        $count = 0;
        $products = array();
        $productss = array();
        $query = "SELECT product_id FROM products WHERE product_expire <= DATE_ADD(CURDATE(), INTERVAL 6 MONTH) && product_expire >= DATE_ADD(CURDATE(), INTERVAL 1 DAY) ORDER by product_expire ASC";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($productId);
        while ($stmt->fetch())
        {
            $count++;
        }
        $products['productsExpiringCount'] = $count;
        return $products;
    }

    function getExpiredProducts()
    {
        $products = array();
        $productss = array();
        $query = "SELECT product_id,category_id,product_name,size_id,brand_id,product_price,product_quantity,location_id,product_manufacture,product_expire FROM products WHERE product_expire<CURDATE() ORDER by product_expire ASC";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($productId,$categoryId,$productName,$sizeId,$brandId,$productPrice,$productQuantity,$locationId,$productManufacture,$productExpire);
        while ($stmt->fetch())
        {
            $product['productId']           = $productId;
            $product['categoryId']          = $categoryId;
            $product['productName']         = $productName;
            $product['sizeId']              = $sizeId;
            $product['brandId']             = $brandId;
            $product['productPrice']        = $productPrice;
            $product['productQuantity']     = $productQuantity;
            $product['locationId']          = $locationId;
            $product['productManufacture']  = $productManufacture;
            $product['productExpire']       = $productExpire;
            array_push($products, $product);
        }
        foreach ($products as  $product)
        {
            // $salesQuantity = $this->getAllSalesQuantityOfProudctById($product['productId']);
            // if ($product['productQuantity']-$salesQuantity<1)
            // {
                $pro['productId']               = $product['productId'];
                $pro['productCategory']         = $this->getCategoryById($product['categoryId']);
                $pro['productName']             = $product['productName'];
                $pro['productSize']             = $this->getSizeById($product['sizeId']);
                $pro['productBrand']            = $this->getBrandById($product['brandId']);
                $pro['productPrice']            = $product['productPrice'];
                $pro['productQuantity']         = $product['productQuantity']-$this->getSellQuantityByProductId($pro['productId']);
                $pro['productLocation']         = $this->getLocationById($product['locationId']);
                $pro['productManufacture']      = substr($product['productManufacture'], 0, 7);
                $pro['productExpire']           = substr($product['productExpire'], 0, 7);
                array_push($productss, $pro);
            // }
        }
        return $productss;
    }

    function getProductsCount()
    {
        $products = array();
        $query = "SELECT COUNT(product_id) FROM products";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($productsCount);
        $stmt->fetch();
        $products['productsCount'] = $productsCount;
        return $products;
    }

    function getBrandsCount()
    {
        $brands = array();
        $query = "SELECT COUNT(brand_id) FROM brands";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($brandsCount);
        $stmt->fetch();
        $brands['brandsCount'] = $brandsCount;
        return $brands;
    }

    function getTodaysSalesCount()
    {
        $sales = array();
        date_default_timezone_set('Asia/Kolkata');
        $date = new DateTime();
        $date->setTime(00,00);
        $startDT = $date->format('Y-m-d H:i:s');
        $date->setTime(23,59);
        $endDT = $date->format('Y-m-d H:i:s');
        $query = "SELECT COUNT(sell_id) FROM sells where created_at between '$startDT' and '$endDT'";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($salesCount);
        $stmt->fetch();
        $sales['salesCount'] = $salesCount;
        return $sales;
    }

    function getCategoryById($categoryId)
    {
        $query = "SELECT category_name FROM categories WHERE category_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$categoryId);
        $stmt->execute();
        $stmt->bind_result($categoryName);
        $stmt->fetch();
        return $categoryName;
    }

    function getSizeById($sizeId)
    {
        $query = "SELECT size_name FROM sizes WHERE size_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$sizeId);
        $stmt->execute();
        $stmt->bind_result($sizeName);
        $stmt->fetch();
        return $sizeName;
    }

    function getSellQuantityByProductId($productId)
    {
        $query = "SELECT SUM(sell_quantity) FROM sells WHERE product_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$productId);
        $stmt->execute();
        $stmt->bind_result($productQuantity);
        $stmt->fetch();
        return $productQuantity;
    }

    function getBrandById($brandId)
    {
        $query = "SELECT brand_name FROM brands WHERE brand_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$brandId);
        $stmt->execute();
        $stmt->bind_result($brandName);
        $stmt->fetch();
        return $brandName;
    }

    function getProductPriceById($productId)
    {
        $query = "SELECT product_price FROM products WHERE product_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$productId);
        $stmt->execute();
        $stmt->bind_result($productPrice);
        $stmt->fetch();
        return $productPrice;
    }

    function getLastSaleId()
    {
        $query = "SELECT sale_id FROM sales ORDER by sale_id DES LIMIT 1";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($saleId);
        $stmt->fetch();
        return $saleId;
    }

    function getProductQuantityById($productId)
    {
        $query = "SELECT product_quantity FROM products WHERE product_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$productId);
        $stmt->execute();
        $stmt->bind_result($productQuantity);
        $stmt->fetch();
        return $productQuantity;
    }

    function getLocationById($locationId)
    {
        $query = "SELECT location_name FROM locations WHERE location_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$locationId);
        $stmt->execute();
        $stmt->bind_result($locationName);
        $stmt->fetch();
        return $locationName;
    }

    function checkUserById($id)
    {
        $query = "SELECT email FROM admin WHERE id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows>0)
            return true;
        else
            return false;
    }

    function getPasswordByEmail($email)
    {
        $query = "SELECT password FROM admin WHERE email=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$email);
        $stmt->execute();
        $stmt->bind_result($password);
        $stmt->fetch();
        return $password;
    }

    function getUserIdByEmail($email)
    {
        $query = "SELECT id FROM users WHERE email=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$email);
        $stmt->execute();
        $stmt->bind_result($id);
        $stmt->fetch();
        return $id;
    }

    function getCode($codeType)
    {
        $tokenId = $this->getUserId();
        $query = "SELECT code FROM codes WHERE userId=? AND codeType=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('ss',$tokenId,$codeType);
        $stmt->execute();
        $stmt->bind_result($code);
        $stmt->fetch();
        return $code;
    }

    function verifyCode($code,$codeType)
    {
        $dbCode = $this->decrypt($this->getCode($codeType));
        if ($code==$dbCode)
            return true;
        else
            return false;
    }

    function getUserByEmail($email)
    {
        $query = "SELECT id,name,username,email,image,status FROM admin WHERE email=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$email);
        $stmt->execute();
        $stmt->bind_result($id,$name,$username,$email,$image,$status);
        $stmt->fetch();
        $user = array();
        $user['id'] = $id;
        $user['name'] = $name;
        $user['username'] = $username;
        $user['email'] = $email;
        $user['status'] = $status;
        if (empty($image))
            $image = DEFAULT_USER_IMAGE;
        $user['image'] = WEBSITE_DOMAIN.$image;
        return $user;
    }

    function isEmailValid($email)
    {
        if(filter_var($email,FILTER_VALIDATE_EMAIL))
            return true;
        else
            return false;
    }

    function validateToken($token)
    {
        try 
        {
            $key = JWT_SECRET_KEY;
            $payload = JWT::decode($token,$key,['HS256']);
            $id = $payload->user_id;
            if ($this->checkUserById($id)) 
            {
                $this->setUserId($payload->user_id);
                return JWT_TOKEN_FINE;
            }
            return JWT_USER_NOT_FOUND;
        } 
        catch (Exception $e) 
        {
            return JWT_TOKEN_ERROR;    
        }
    }

    function encrypt($data)
    {
        $email = openssl_encrypt($data,"AES-128-ECB",null);
        $email = str_replace('/','socialcodia',$email);
        $email = str_replace('+','mufazmi',$email);
        return $email; 
    }

    function decrypt($data)
    {
        $mufazmi = str_replace('mufazmi','+',$data);
        $email = str_replace('socialcodia','/',$mufazmi);
        $email = openssl_decrypt($email,"AES-128-ECB",null);
        return $email; 
    }
}