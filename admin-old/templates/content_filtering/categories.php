
                <div id="incat0">
                <? $parentid = 0; ?>
                <? include(TPL_DIR.'pricing/categotylist.php'); ?>
                </div>



<script>

var is_sortable = true;
var ed_pcid = 0;
var editid = '';
var dcid = 0;
var dpcid = 0;

function showdelform(cid, pcid){}

$(function() 
{
	function updateTips( t ) {
		tips
			.text( t )
			.addClass( "ui-state-highlight" );
		setTimeout(function() {
			tips.removeClass( "ui-state-highlight", 1500 );
		}, 500 );
	}

	function checkLength( o, n, min, max ) {
		if ( o.val().length > max || o.val().length < min ) {
			o.addClass( "ui-state-error" );
			updateTips( "Length of " + n + " must be between " +
				min + " and " + max + "." );
			return false;
		} else {
			return true;
		}
	}

	function checkRegexp( o, regexp, n ) {
		if ( !( regexp.test( o.val() ) ) ) {
			o.addClass( "ui-state-error" );
			updateTips( n );
			return false;
		} else {
			return true;
		}
	}
	
	var name = $( "#name" ),
		allFields = $( [] ).add( name ),
		tips = $( ".validateTips" );

    $( "#dialog-form" ).dialog({
		autoOpen: false,
		height: 360,
		width: 350,
		modal: true,
		buttons: {
			"<?=LangAdmin::get('add_a_category')?>": function() {
                var bValid = true;
				allFields.removeClass( "ui-state-error" );
				bValid = bValid && checkLength( name, "name", 1, 1000 );
				if ( bValid ) add();
			},
            "<?=LangAdmin::get('cancel')?>": function() {
				$( this ).dialog( "close" );
			}
		},
		close: function() {
			allFields.val( "" ).removeClass( "ui-state-error" );
		}
	});
	
	var ename = $( "#ename" ),
		eallFields = $( [] ).add( ename ),
		tips = $( ".validateTips" );

	$( "#dialog-form2" ).dialog({
		autoOpen: false,
		height: 380,
		width: 350,
		modal: true,
		buttons: {
			"<?=LangAdmin::get('save')?>": function() {
                var bValid = true;
				eallFields.removeClass( "ui-state-error" );
				bValid = bValid && checkLength( ename, "ename", 1, 1000 );
				if ( bValid ) edit();
			},
            "<?=LangAdmin::get('cancel')?>": function() {
				$( this ).dialog( "close" );
			}
		},
		close: function() {
			eallFields.val( "" ).removeClass( "ui-state-error" );
		}
	});
});

function add(){}
function edit() {}
function showedit(cid, pcid){}
function showaddform(pcid, pcid2) {}
if (is_sortable) {}

function jq(myid)
{
    return '#' + myid.replace(/(:|\.)/g,'\\$1');
}

var shstat = <?=$hiddenstat?>;
function shhidden(){}
//shhidden();

function subcat(cid, parent)
{
    //var incat = document.getElementById('incat'+cid);
    //alert(incat.innerHTML);
    var incat = $(jq('incat'+cid));
    if (incat.html() != '')
    {
        if (incat.css('display') == 'none')
        {
            incat.show('blind');
        } else {
            incat.hide('blind');
        }
    } else {
        //alert(cid);
        var spinner = $(jq('spinner_'+cid));
        if (spinner.attr('alt') == 'Wait') return;
        spinner.attr('src', "<?=TPL_DIR;?>i/spinner.gif");
        spinner.attr('alt', "Wait");
        $.ajax({
                url: 'index.php?cmd=category&do=getforpricing&catid='+cid+'&sid=<?=$GLOBALS['ssid']?>',
                success: function(data) {
                    if (data == 'RELOGIN') location.href='index.php';
                    $(jq('incat'+cid)).html(data);
                    spinner.attr('src', "<?=TPL_DIR;?>i/folder.jpg");
                    $(jq('incat'+cid)).show('blind');
                    if (is_sortable) 
                    {
                        $(jq('incat'+cid)).sortable({
                            stop: function(event, ui) {
                                var data = $(jq('incat'+cid)).sortable('toArray');
                                order2(ui.item[0].id, this.id, data, ui.position.top - ui.originalPosition.top);
                            },
                            placeholder: "ui-state-highlight",
                        });
                    }
                    //$(jq('incat'+cid)).disableSelection();
                },
                error: function() {

                }
            });
    }
} 
function order2(cid, pcid, data, direct){}
function order(cid, pcid, i, direct){}
function change_visible(cid, pcid){}
</script>
