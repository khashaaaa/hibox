<?
include ("header.php");

echo '<div class="main"><div class="canvas clrfix">';
echo '<h1> '.LangAdmin::get('there_was_an_error').'! </h1>';

if(isset($message)) echo $message;

echo '</div></div>';

include ("footer.php");
?>

