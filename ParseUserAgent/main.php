<?php
include 'ParseUserAgent.php';

$UserAgent = getallheaders()['User-Agent'];
echo infoRequest::Get($UserAgent);
