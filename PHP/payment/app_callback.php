<!DOCTYPE HTML>
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
    <body>
        <?php echo ($_GET['err'] == 0) ? '成功' : '失败';?>
        <?php     
        	require($_SERVER ['DOCUMENT_ROOT'] . "/ecmobile/library/function.php");   	        	
			$url = ecmobile_url()."/payment/app_callback.php?err=".$_GET['err']."&order_id=".$_GET['order_id'];  
			echo "<script language='javascript' type='text/javascript'>";  
			echo "window.location.href='$url'";  
			echo "< /script>";  
		?> 
    </body>
</html>