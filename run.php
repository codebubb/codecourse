<?php

$code = $_GET['code'];
$lang = $_GET['lang'];

// PHP doesn't like $ signs!
if ($lang == 'php'){
  $code = str_replace("$", "\\$", $code);
}

// Code to run
$str = "cat << EOF | " . $lang ." 2>&1
".
  $code . "

EOF
";

// Test for error_get_last
$error_test = "cat << EOF | " . $lang ." 2>&1 >/dev/null
".
  $code . "

EOF
";

// Error state
$error = nl2br(shell_exec($error_test));

header('Content-type: text/json');
if($error){
  echo json_encode(array("status" => "error", "response" => $error));
} else{
  $out = nl2br(shell_exec($str));
  echo json_encode(array("status" => "OK", "response" => $out));
}

?>
