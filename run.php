<?php
/*  Runs the code that is pased from the main page
    Returns JSON object with :
      "status"    : "OK" | "error",
      "response"  : <result_of_code> | <error_message>
*/
header('Content-type: text/json');
$general_error = "Oops! Something went wrong.  Review your code and try again.";

/* Sanitise the input as best as can be */
$any_sanity_errors    = false;
$permitted_languages  = ['nodejs', 'php', 'python'];
$code = $_GET['code'];
$lang = $_GET['lang'];

if (!in_array($lang, $permitted_languages)){
  $any_sanity_errors = true;
}

if($any_sanity_errors){
  echo json_encode(array("status" => "error", "response" => $general_error));
  exit(0);
}

/* Passed sanity checks, setup code to run */

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

// Test for errors
$error_test = "cat << EOF | " . $lang ." 2>&1 >/dev/null
".
  $code . "

EOF
";

/* Run error testing code */
$error = nl2br(shell_exec($error_test));

/* Final output either error_message or code_result */
if($error){
  echo json_encode(array("status" => "error", "response" => $error));
} else{
  $out = nl2br(shell_exec($str));
  echo json_encode(array("status" => "OK", "response" => $out));
}

?>
