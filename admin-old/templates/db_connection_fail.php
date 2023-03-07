<?
include ("header.php");

?>
<script type="text/javascript" src="js/lang.js"></script>

<div class="main"><div class="canvas clrfix">
        
        <div class="col700">
            <div class="tuning">
                <span id="error" style="color:red;font-weight: bold;">
                    <? if(isset($error)) { print $error; } ?>
                </span>
                <p><?=LangAdmin::get('error_connecting_to_database')?>.</p>
                <p><?=LangAdmin::get('check_configcustom_for_correct_db_accesses')?>.</p>
                <p><?=LangAdmin::get('example')?>:</p>
                <pre>
                define('DB_HOST', 'localhost');
                define('DB_USER', 'otbox');
                define('DB_PASS', '*******');
                define('DB_BASE', 'otbox');
                </pre>
            </div>
        </div>

</div></div><!-- /.main -->
<?
include ("footer.php");
?>
