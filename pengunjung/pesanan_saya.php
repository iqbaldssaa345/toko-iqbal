<?php
$query_params = $_GET;
header("Location: pesanan.php?" . http_build_query($query_params));
exit;