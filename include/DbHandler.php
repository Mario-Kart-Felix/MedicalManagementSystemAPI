<?php

require_once dirname(__FILE__).'/JWT.php';

    $JWT = new JWT;


class DbHandler
{
    private $con;
    private $userId;
    private $saleId;
    private $invoiceNumber;
    private $invoiceId;

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

    function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;
    }

    function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    function setInvoiceId($invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    function getInvoiceId()
    {
        return $this->invoiceId;
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
        $productQuantity = $productQuantity + $this->getSalesCountByProductId($productId)+$this->getSellerSalesCountByProductId($productId);
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
            return true;
        else
            return false;
    }

    function getSalesStatusOfEveryMonth()
    {
        $rec = array();
        $record = array();
        $query = "select date_format(created_at,'%M'),sum(sell_price) from sells group by year(created_at),month(created_at) order by year(created_at),month(created_at)";
        $stmt = $this->con->prepare($query);
        $stmt->execute();  
        $stmt->bind_result($month,$sellPrice);
        while($stmt->fetch())
        {
            $rec['month']= $month;
            $rec['totalSales'] = $sellPrice;
            array_push($record, $rec);
        }
        return $record;
    }

    function getSalesStatusOfEveryDay()
    {
        $rec = array();
        $record = array();
        $query = "select date_format(created_at,'%d'),sum(sell_price) from sells WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE()) group by week(created_at),day(created_at) order by month(created_at),day(created_at) ASC";
        $stmt = $this->con->prepare($query);
        $stmt->execute();  
        $stmt->bind_result($day,$sellPrice);
        while($stmt->fetch())
        {
            $rec['day']= $day;
            $rec['totalSales'] = $sellPrice;
            array_push($record, $rec);
        }
        return $record;
    }

    function getMonthName($monthNumber)
    {
        switch ($monthNumber) {
            case '01':
                return 'January';
                break;
            case '02':
                return 'February';
                break;
            case '03':
                return 'March';
                break;
            case '04':
                return 'April';
                break;
            case '05':
                return 'May';
                break;
            case '06':
                return 'June';
                break;
            case '07':
                return 'July';
                break;
            case '08':
                return 'August';
                break;
            case '09':
                return 'September';
                break;
            case '10':
                return 'October';
                break;
            case '11':
                return 'November';
                break;
            case '12':
                return 'December';
                break;
            
            default:
                # code...
                break;
        }
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

    function getSellerById($sellerId)
    {
        $sellers = array();
        $query = "SELECT seller_id,seller_fname,seller_lname,seller_email,seller_contact,seller_contact_1,seller_image,seller_address from sellers WHERE seller_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$sellerId);
        $stmt->execute();
        $stmt->bind_result($sellerId,$sellerFirstName,$sellerLastName,$sellerEmail,$sellerContactNumber,$sellerContactNumber1,$sellerImage,$sellerAddress);
        $stmt->fetch();
        $seller['sellerId'] = $sellerId;
        $seller['sellerFirstName'] = $sellerFirstName;
        $seller['sellerLastName'] = $sellerLastName;
        $seller['sellerEmail'] = $sellerEmail;
        $seller['sellerContactNumber'] = $sellerContactNumber;
        $seller['sellerContactNumber1'] = $sellerContactNumber1;
        if (isset($sellerImage) && !empty($sellerImage))
            $seller['sellerImage'] = WEBSITE_DOMAIN.$sellerImage;
        else
            $seller['sellerImage'] = WEBSITE_DOMAIN.'uploads/api/user.png';
        $seller['sellerAddress'] = $sellerAddress;
        // array_push($sellers, $seller);
        return $seller;
    }

    function isSellerExist($sellerId)
    {
        $sellers = array();
        $query = "SELECT seller_id FROM sellers WHERE seller_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$sellerId);
        $stmt->execute();
        $stmt->bind_result($sId);
        $stmt->fetch();
        if (!empty($sId))
            return true;
        else
            return false;
    }

    function isInvoiceExist($invoiceNumber)
    {
        $sellers = array();
        $query = "SELECT invoice_id FROM invoices WHERE invoice_number=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$invoiceNumber);
        $stmt->execute();
        $stmt->bind_result($iId);
        $stmt->fetch();
        if (!empty($iId))
            return true;
        else
            return false;
    }

    function getInvoiceUrlByInvoiceNumber($invoiceNumber)
    {
        $sellers = array();
        $query = "SELECT invoice_url FROM invoices WHERE invoice_number=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$invoiceNumber);
        $stmt->execute();
        $stmt->bind_result($invoiceUrl);
        $stmt->fetch();
        return $invoiceUrl;
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

    function getNewInvoiceNumber()
    {
        $query = "SELECT invoice_number from invoices ORDER BY invoice_id DESC LIMIT 1";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($invoiceNumber);
        $stmt->fetch();
        if (empty($invoiceNumber))
            $invoiceNumber = "FHC10000";
        $companyTag = substr($invoiceNumber,0, 3);
        $invoiceNumber = (int) substr($invoiceNumber,3, 10)+1;
        $invoiceNumber = $companyTag.$invoiceNumber;
        return $invoiceNumber;
    }

    function addPayment($sellerId,$invoiceNumber,$paymentAmount)
    {
        $tokenId = $this->getUserId();
        date_default_timezone_set('Asia/Kolkata');
        $paymentMode = 'CASH';
        $date = date('y/m/d H:i:s', time());
        $query = "INSERT INTO payments (payment_mode,payment_date,payment_amount,payment_receiver,invoice_number,seller_id) VALUES(?,?,?,?,?,?)";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("ssssss",$paymentMode,$date,$paymentAmount,$tokenId,$invoiceNumber,$sellerId);
        if ($stmt->execute())
            return true;
        else
            return false;
    }

    function isPaymentAmountLessThanInvoiceAmount($invoiceNumber,$paymentAmount)
    {
        $invoiceAmount = (int) $this->getTotalAmountByInvoiceNumber($invoiceNumber);
        $paymentAmount = (int) $paymentAmount;
        $paidAmount = (int) $this->getAllPaidAmountByInvoiceNumber($invoiceNumber);
        if ($invoiceAmount-$paidAmount-$paymentAmount>=0)
        {
            return true;
        }
        else
            return false;
    }

    function getAllPaidAmountByInvoiceNumber($invoiceNumber)
    {
        $query = "SELECT SUM(payment_amount) FROM payments WHERE invoice_number=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$invoiceNumber);
        $stmt->execute();
        $stmt->bind_result($totalAmount);
        $stmt->fetch();
        return $totalAmount;
    }

    function getTotalAmountByInvoiceNumber($invoiceNumber)
    {
        $query = "SELECT SUM(sell_price) FROM sellers_sells WHERE invoice_number=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$invoiceNumber);
        $stmt->execute();
        $stmt->bind_result($totalAmount);
        $stmt->fetch();
        return $totalAmount;
    }

    //CAFUSION : This function is not completed yet, We are not using this funtion to anywhere
    function getTotalOrignalAmountByInvoiceNumber($invoiceNumber)
    {
        $productIds = $this->getAllProductIdAndSellQuantityByInvoiceNumber($invoiceNumber);
        $query = "SELECT SUM(sell_price) FROM sellers_sells WHERE invoice_number=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$invoiceNumber);
        $stmt->execute();
        $stmt->bind_result($totalAmount);
        $stmt->fetch();
        return $productIds;
    }

    function getCurrentAmountOfInvoiceByInvoiceNumber($invoiceNumber)
    {
        $paidAmount = (int) $this->getAllPaidAmountByInvoiceNumber($invoiceNumber);
        $invoiceAmount = (int) $this->getTotalAmountByInvoiceNumber($invoiceNumber);
        return $invoiceAmount-$paidAmount;
    }

    function addInvoice($sellerId)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('y/m/d', time());
        $invoiceNumber = $this->getNewInvoiceNumber();
        $query = "INSERT INTO invoices (invoice_number,seller_id,invoice_date) VALUES(?,?,?)";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("sss",$invoiceNumber,$sellerId,$date);
        if ($stmt->execute())
        {
            $this->setInvoiceNumber($invoiceNumber);
            return true;
        }
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

    function sellProductToSeller($productId,$invoiceNumber)
    {
        if ($this->isProductAvailable($productId))
        {
            $productPrice = $this->getProductPriceById($productId);
            $productQuantity = 1;
            $query = "INSERT INTO sellers_sells (invoice_number,product_id,sell_quantity,sell_price) VALUES(?,?,?,?)";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("ssss",$invoiceNumber,$productId,$productQuantity,$productPrice);
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
        $sellerSalesQuantity = $this->getAllSellerSalesQuantityOfProudctById($productId);
        if ($productQuantity-$salesQuantity-$sellerSalesQuantity>0)
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

    function deleteSellerSoldProduct($sellId)
    {
        if($this->isSellerSaleExist($sellId))
        {
            $query = "DELETE FROM sellers_sells WHERE sellers_sell_id =?";
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

    function isSellerSaleExist($sellId)
    {
        $query = "SELECT sellers_sell_id from sellers_sells WHERE sellers_sell_id =?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$sellId);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows()>0)
            return true;
        else
            return false;
    }

    function getInvoiceByInvoiceNumber($invoiceNumber)
    {
        $invoice = array();
        $invoices = array();
        $invoicess = array();
        $pro = array();
        if (!$this->isInvoiceExist($invoiceNumber))
           return $pro;
        $query = "SELECT invoice_id,invoice_number,seller_id,invoice_date FROM invoices WHERE invoice_number=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$invoiceNumber);
        $stmt->execute();
        $stmt->bind_result($invoiceId,$invoiceNumber,$sellerId,$invoiceDate);
        $stmt->fetch();
        $invoice['invoiceId']           = $invoiceId;
        $invoice['invoiceNumber']       = $invoiceNumber;
        $invoice['sellerId']            = $sellerId;
        $invoice['invoiceDate']         = $invoiceDate;
        array_push($invoices, $invoice);
        $stmt->close();
        foreach ($invoices as  $invoice)
        {
            $paidAmount                     = (int) $this->getAllPaidAmountByInvoiceNumber($invoice['invoiceNumber']);
            $invoiceRemainingAmount         = $this->getTotalAmountByInvoiceNumber($invoice['invoiceNumber']) - (int) $this->getAllPaidAmountByInvoiceNumber($invoice['invoiceNumber']);
            $seller                         = $this->getSellerById($invoice['sellerId']);
            $sellerImage                    = $seller['sellerImage'];
            if (empty($sellerImage))
                $sellerImage = WEBSITE_DOMAIN.'uploads/api/user.png';
            if (empty($paidAmount))
                $paidAmount = 0;
            if (empty($invoiceRemainingAmount ))
                $invoiceRemainingAmount  = 0;
            $inv['invoiceId']               = $invoice['invoiceId'];
            $inv['invoiceNumber']           = $invoice['invoiceNumber'];
            $inv['invoiceDate']             = $invoice['invoiceDate'];
            $inv['invoiceAmount']           = $this->getTotalAmountByInvoiceNumber($invoice['invoiceNumber']);
            $inv['invoiceTotalPrice']       = $this->getTotalPriceOfInvoiceByInvoiceNumber($invoice['invoiceNumber']);
            $inv['invoicePaidAmount']       = $paidAmount;
            $inv['invoiceRemainingAmount']  = $invoiceRemainingAmount;
            $inv['invoiceStatus']           = $this->isInvoicePaid($inv['invoiceNumber']);
            $inv['sellerName']              = $seller['sellerFirstName'].' '.$seller['sellerLastName'];
            
            $inv['sellerImage']             = $sellerImage;
            $inv['sellerId']     = $seller['sellerId'];
            $inv['sellerContactNumber']     = $seller['sellerContactNumber'];
            $inv['sellerContactNumber1']    = $seller['sellerContactNumber1'];
            $inv['sellerAddress']           = $seller['sellerAddress'];
        }
        return $inv;
    }

    function getInvoicesBySellerId($sellerId)
    {
        $invoice = array();
        $invoices = array();
        $invoicess = array();
        $pro = array();
        $query = "SELECT invoice_id,invoice_number,seller_id,invoice_date FROM invoices WHERE seller_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$sellerId);
        $stmt->execute();
        $stmt->bind_result($invoiceId,$invoiceNumber,$sellerId,$invoiceDate);
        while($stmt->fetch())
        {
            $invoice['invoiceId']           = $invoiceId;
            $invoice['invoiceNumber']       = $invoiceNumber;
            $invoice['sellerId']            = $sellerId;
            $invoice['invoiceDate']         = $invoiceDate;
            array_push($invoices, $invoice);
        }
        $stmt->close();
        foreach ($invoices as  $invoice)
        {
            $paidAmount                     = (int) $this->getAllPaidAmountByInvoiceNumber($invoice['invoiceNumber']);
            $invoiceRemainingAmount         = $this->getTotalAmountByInvoiceNumber($invoice['invoiceNumber']) - (int) $this->getAllPaidAmountByInvoiceNumber($invoice['invoiceNumber']);
            $seller                         = $this->getSellerById($invoice['sellerId']);
            // $seller                         = $seller[0];
            $sellerImage                    = $seller['sellerImage'];
            if (empty($sellerImage))
                $sellerImage = WEBSITE_DOMAIN.'uploads/api/user.png';
            if (empty($paidAmount))
                $paidAmount = 0;
            if (empty($invoiceRemainingAmount ))
                $invoiceRemainingAmount  = 0;
            $inv['invoiceId']               = $invoice['invoiceId'];
            $inv['invoiceNumber']           = $invoice['invoiceNumber'];
            $inv['invoiceDate']             = $invoice['invoiceDate'];
            $inv['invoiceAmount']           = $this->getTotalAmountByInvoiceNumber($invoice['invoiceNumber']);
            $inv['invoiceTotalPrice']       = $this->getTotalPriceOfInvoiceByInvoiceNumber($invoice['invoiceNumber']);
            $inv['invoicePaidAmount']       = $paidAmount;
            $inv['invoiceRemainingAmount']  = $invoiceRemainingAmount;
            $inv['invoiceStatus']           = $this->isInvoicePaid($inv['invoiceNumber']);
            $inv['sellerName']              = $seller['sellerFirstName'].' '.$seller['sellerLastName'];
            $inv['sellerImage']             = $sellerImage;
            $inv['sellerId']     = $seller['sellerId'];
            $inv['sellerContactNumber']     = $seller['sellerContactNumber'];
            $inv['sellerContactNumber1']    = $seller['sellerContactNumber1'];
            $inv['sellerAddress']           = $seller['sellerAddress'];
            array_push($invoicess, $inv);
        }
        return $invoicess;
    }

    function getPaymentsByInvoiceNumber($invoiceNumber)
    {
        $invoice = array();
        $payments = array();
        $paymentss = array();
        $pro = array();
        $query = "SELECT payment_id,payment_date,payment_amount,invoice_number,seller_id FROM payments WHERE invoice_number=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$invoiceNumber);
        $stmt->execute();
        $stmt->bind_result($paymentId,$paymentDate,$paymentAmount,$invoiceNumber,$sellerId);
        while($stmt->fetch())
        {
            $payment['paymentId']       = $paymentId;
            $payment['paymentDate']     = $paymentDate;
            $payment['paymentAmount']   = $paymentAmount;
            $payment['invoiceNumber']   = $invoiceNumber;
            $payment['sellerId']        = $sellerId;
            array_push($payments, $payment);
        }
        $stmt->close();
        foreach ($payments as  $payment)
        {
            $pay['paymentId']               = $payment['paymentId'];
            $pay['invoiceNumber']           = $payment['invoiceNumber'];
            $pay['paymentDate']             = $payment['paymentDate'];
            $pay['paymentAmount']           = $payment['paymentAmount'];
            $pay['sellerId']                = $payment['sellerId'];
            array_push($paymentss, $pay);
        }
        return $paymentss;
    }

    function getSellerSellProductsByInvoiceNumber($invoiceNumber)
    {
        $products = array();
        $productss = array();
        $query = "SELECT product_id, sell_quantity, sell_discount, sell_price FROM sellers_sells WHERE invoice_number=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$invoiceNumber);
        $stmt->execute();
        $stmt->bind_result($productId,$sellQuantity,$sellDiscount,$sellPrice);
        while ($stmt->fetch())
        {
            $product['productId'] = $productId;
            $product['sellQuantity'] = $sellQuantity;
            $product['sellDiscount'] = $sellDiscount;
            $product['sellPrice'] = $sellPrice;
            array_push($products, $product);
        }
        $stmt->close();
        foreach ($products as $product)
        {
            $pro = $this->getProductById($product['productId']);
            $pro['productId']       = $product['productId'];
            $pro['sellQuantity']    = $product['sellQuantity'];
            $pro['sellDiscount']    = $product['sellDiscount'];
            $pro['sellPrice']       = $product['sellPrice'];
            array_push($productss, $pro);
        }
        return $productss;
    }

    function setInvoiceUrlByInvoiceNumber($invoiceUrl,$invoiceNumber)
    {
        $query = "UPDATE invoices SET invoice_url=? WHERE invoice_number=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('ss',$invoiceUrl,$invoiceNumber);
        if($stmt->execute())
            return true;
        else
            return false;
    }

    // Working on this one function or from this ones function ok, first need to fetch the full details item of a product which is sold
    // whihch information should we have to return we will return it ok,
    function getInvoiceProductsByInvoiceNumber($invoiceNumber)
    {
        $products = array();
        $idAndQuantityArray = $this->getAllProductIdAndSellQuantityByInvoiceNumber($invoiceNumber);
        foreach ($idAndQuantityArray as $idSelQuan)
        {
            $productss = $this->getProductById($idSelQuan['productId']);
            $product['productName'] = $productss['productName'];
            $product['productSize'] = $productss['productSize'];
            $product['productPrice'] = $productss['productPrice'];
            $product['sellQuantity'] = $idSelQuan['sellQuantity'];
            $product['productTotalPrice'] = $productss['productPrice']*$idSelQuan['sellQuantity'];
            $product['productDiscount']   = $this->getAllProductIdAndSellQuantityByInvoiceNumber($productss['productId'],$invoiceNumber);
            // $product['productSellPrice']  = $this->decPercentage()
            array_push($products, $product);
        }
        return $products;
    }

    function getProductSellPercentageByProductIdAndInvoiceNumber($productId,$invoiceNumber)
    {
        $query = "SELECT sell_discount FROM sellers_sells WHERE product_id=? AND invoice_number=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('ss',$productId,$invoiceNumber);
        $stmt->execute();
        $stmt->bind_result($sellPercentage);
        $stmt->fetch();
        return $sellPercentage;
    }

    function getPercentage($value,$values)
    {
        return 100 - ($value / $values) * 100;
    }

    function decPercentage($percent,$value)
    {
        return $value - ($percent / 100) * $value;
    }

    function getInvoicePDFByInvoiceNumber($invoiceNumber)
    {
        $invoice = $this->getInvoiceByInvoiceNumber($invoiceNumber);
    }

    function isInvoicePaid($invoiceNumber)
    {
        if ($this->getInvoiceStatus($invoiceNumber))
            return 'PAID';
        else
            return 'UNPAID';
    }

    function getInvoiceStatus($invoiceNumber)
    {
        $invoiceAmount = (int) $this->getTotalAmountByInvoiceNumber($invoiceNumber);
        $paidAmount = (int) $this->getAllPaidAmountByInvoiceNumber($invoiceNumber);
        if ($invoiceAmount-$paidAmount==0)
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

    function updateSellerSellProducts($saleId,$productQuantity,$sellDiscount,$productSalePrice)
    {
        if ($this->isSellerSaleExist($saleId))
        {
            $productId = $this->getProductIdBySellerSaleId($saleId);
            // $currentSaleQuantity = $this->getSalesProductQuantityById($saleId);
            $currentSellerSaleQuantity = $this->getSellerSalesProductQuantityById($saleId);
            if ($this->getProductCurrentQuantityById($productId)+$currentSellerSaleQuantity-$productQuantity>=0) 
            {
                 $query = "UPDATE sellers_sells SET sell_quantity=?, sell_discount=?, sell_price=? WHERE sellers_sell_id=?";
                $stmt = $this->con->prepare($query);
                $stmt->bind_param('ssss',$productQuantity,$sellDiscount,$productSalePrice,$saleId);
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
        $currentSaleQuantity = $this->getAllSalesQuantityOfProudctById($productId);
        $currentSellerSaleQuantity = $this->getAllSellerSalesQuantityOfProudctById($productId);
        $productQuantity = $this->getProductQuantityById($productId);
        return $productQuantity-$currentSaleQuantity-$currentSellerSaleQuantity;
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

    function getSellerSalesProductQuantityById($saleId)
    {
        $query = "SELECT sell_quantity from sellers_sells WHERE sellers_sell_id=?";
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

    function getAllSellerSalesQuantityOfProudctById($productId)
    {
        $allCount = 0;
        $query = "SELECT sell_quantity from sellers_sells WHERE product_id=?";
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

    function getProductIdBySellerSaleId($saleId)
    {
        $query = "SELECT product_id from sellers_sells WHERE sellers_sell_id=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$saleId);
        $stmt->execute();
        $stmt->bind_result($productId);
        $stmt->fetch();
        return $productId;
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
        $query = "SELECT brand_id,brand_name FROM brands ORDER BY brand_name ASC";
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
        $query = "SELECT size_id,size_name FROM sizes ORDER BY size_name ASC";
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
        $query = "SELECT category_id,category_name FROM categories ORDER BY category_name ASC";
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

    function getInvoices()
    {
        $invoice = array();
        $invoices = array();
        $invoicess = array();
        $seller = array();
        $pro = array();
        $query = "SELECT invoice_id,invoice_number,seller_id,invoice_date FROM invoices";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($invoiceId,$invoiceNumber,$sellerId,$invoiceDate);
        while($stmt->fetch())
        {
            $invoice['invoiceId']           = $invoiceId;
            $invoice['invoiceNumber']       = $invoiceNumber;
            $invoice['sellerId']            = $sellerId;
            $invoice['invoiceDate']         = $invoiceDate;
            array_push($invoices, $invoice);
        }
        $stmt->close();
        foreach ($invoices as  $invoice)
        {
            $seller                         = $this->getSellerById($invoice['sellerId']);
            // $seller                         = $seller[0];
            $inv['invoiceId']               = $invoice['invoiceId'];
            $inv['invoiceNumber']           = $invoice['invoiceNumber'];
            $inv['invoiceDate']             = $invoice['invoiceDate'];
            $inv['invoiceAmount']           = $this->getTotalAmountByInvoiceNumber($invoice['invoiceNumber']);
            $inv['invoicePaidAmount']       = $this->getAllPaidAmountByInvoiceNumber($invoice['invoiceNumber']);
            $inv['invoiceRemainingAmount']  = $this->getTotalAmountByInvoiceNumber($invoice['invoiceNumber']) - $this->getAllPaidAmountByInvoiceNumber($invoice['invoiceNumber']);
            $inv['invoiceStatus']           = $this->isInvoicePaid($inv['invoiceNumber']);
            $inv['sellerName']              = $seller['sellerFirstName'].' '.$seller['sellerLastName'];
            $inv['sellerImage']             = $seller['sellerImage'];
            $inv['sellerContactNumber']     = $seller['sellerContactNumber'];
            $inv['sellerContactNumber1']    = $seller['sellerContactNumber1'];
            $inv['sellerAddress']           = $seller['sellerAddress'];
            array_push($invoicess, $inv);
        }
        return $invoicess;
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

    function getSellerSalesCountByProductId($productId)
    {
        $productQuantity = 0;
        $query = "SELECT sell_quantity FROM sellers_sells WHERE product_id=?";
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
        $query = "SELECT location_id,location_name FROM locations ORDER BY location_name ASC";
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
            $pro['productQuantity']         = $this->getProductCurrentQuantityById($pro['productId']);
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

    function getExpiredProductsCount()
    {
        $count = 0;
        $query = "SELECT product_id,category_id,product_name,size_id,brand_id,product_price,product_quantity,location_id,product_manufacture,product_expire FROM products WHERE product_expire<CURDATE() ORDER by product_expire ASC";
        $stmt = $this->con->prepare($query);
        $stmt->execute();
        $stmt->bind_result($productId,$categoryId,$productName,$sizeId,$brandId,$productPrice,$productQuantity,$locationId,$productManufacture,$productExpire);
        while ($stmt->fetch())
        {
            $count++;
        }
        $products['productsExpiredCount'] = $count;
        return $products;
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

    function getAllProductIdAndSellQuantityByInvoiceNumber($invoiceNumber)
    {
        $details = array();
        $query = "SELECT product_id,sell_quantity FROM sellers_sells WHERE invoice_number=?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s',$invoiceNumber);
        $stmt->execute();
        $stmt->bind_result($productId,$sellQuantity);
        while($stmt->fetch())
        {
            $result['productId'] = $productId;
            $result['sellQuantity'] = $sellQuantity;
            array_push($details, $result);
        }
        return $details;
    }

    function getTotalPriceOfInvoiceByInvoiceNumber($invoiceNumber)
    {
        $productPrice = 0;
        $details = $this->getAllProductIdAndSellQuantityByInvoiceNumber($invoiceNumber);   
        foreach ($details as $det)
        {
            $productPrice = $productPrice+ $this->getProductPriceById($det['productId'])*$det['sellQuantity'];
        }
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