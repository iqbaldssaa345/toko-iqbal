<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query_params = $_GET;
header("Location: pesan.php?" . http_build_query($query_params));
exit;