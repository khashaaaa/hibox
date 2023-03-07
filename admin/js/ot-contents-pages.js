var Contents = new Backbone.Collection();
var ContentsPage = Backbone.View.extend(
{
    "el": ".contents-wrapper",
    "events": {
		"click #ot-content-pages-tab li.edit-page": "editPage",
		"click #ot-content-pages-tab li.delete-page": "removeContentPage",
		"click #ot-content-pages-tab li.add-page": "addContentPage",
		"click #ot-content-service-pages-tab a.edit-page": "editPage",
		"click .ot-edit-page-form .btn-primary": "savePage",
		"click .ot-edit-page-form .save-and-continue": "savePageAndContinue",
		"change .ot-edit-page-form #page-level" : "changePageLevel",
		"change .ot-edit-page-form #titleh1": "titleH1Change",
		"change .ot-edit-page-form #page-title": "pageTitleChange"
    },
    titleH1Change: function(e) {
    	var value = $('.ot-edit-page-form #titleh1').val();
    	$('.ot-edit-page-form #titleh1').attr('original-value', value);
    },
    pageTitleChange: function(e) {
    	var value = $('.ot-edit-page-form #page-title').val();
    	$('.ot-edit-page-form #page-title').attr('original-value', value);
    },
    changePageLevel: function(e)
    {
    	var value = $('option:selected',$(e.currentTarget)).val();
    	if (value == 'page') {
    		$('.page-menu-section').show();
    		$('.page-parent-section').hide();
    	} else {
    		$('.page-menu-section').hide();
    		$('.page-parent-section').show();
    	}
    },
    savePageAndContinue: function (e) {
        this.savePage(e, false);
    },
    savePage: function(e, reload)
    {
    	if (reload === undefined) {
    	    reload = true;
        }
        var form = $(e.currentTarget).closest('form');
    	var content = tinyMCE.editors[0].getContent();//$('.confirmDialog .modal-body #seoText1').html());

        form.find('.children').remove();
        $('.page_children_list').each(function( index ){
            form.append('<input class="children" name="children[]" type="hidden" value="'+ $(this).data('pageid')+ '" >');
        });

        $('#page-content', form).val(content);
    	
    	$(form).ajaxSubmit({
            url     :   $(this).attr('action'),
            type    :   'POST',
            dataType:   'json',
            success :   function(data) {
            	if(data && data.result && data.result == 'ok') {
            		showMessage(trans.get('contents::Page_saved_successfully'));
            		if (reload) {
            		    document.location.href = '?cmd=contents&do=default';
            		}
            	} else {
            		showError(data);
            	}
             }
        });
    },
    render: function()
    {
    	return this;
    },
    initialize: function()
    {
    	var self = this;
        this.render();
        
        initializeTinyMCE('#page-content');

        var a = $('#activeLanguagesContainer ul.dropdown-menu li a[data-value=""]');
        var li = $(a).closest('li');
        $(li).remove();
        
        $(document).on('input', '#title', function () {
			var $item = $(this);
			var value = $item.val();
      		if ($('.ot-edit-page-form #page-title').attr('original-value').length == 0) {
    			$('.ot-edit-page-form #page-title').val(value);
    		}
    		if ($('.ot-edit-page-form #titleh1').attr('original-value').length == 0) {
    			$('.ot-edit-page-form #titleh1').val(value);
    		}
    		if ($('.ot-edit-page-form #alias').attr('original-value').length == 0) {
    			$('.ot-edit-page-form #alias').val(self.cyr2lat(value));
    		}
    	});
    },
    editPage: function(e)
    {
    	
    	var tr = $(e.currentTarget).closest('tr');
    	var pageId = $(tr).attr('id');
    	document.location.href = 'index.php?cmd=contents&do=editPage&id='+pageId;
    	return false;
    },
    removeContentPage: function(e)
    {
    	var tr = $(e.currentTarget).closest('tr');
    	var pageId = $(tr).attr('id');
    	
    	modalDialog(trans.get('Confirm_needed'), trans.get('contents::Really_remove_this_page'), function() {
    		$.post('?cmd=contents&do=deletePage', { 'id' : pageId}, function (data) {
                if (data.result == 'ok') {
                	$(tr).remove();
                }
            }, 'json');    		
        });
    },
    addContentPage: function(e)
    {
    	var tr = $(e.currentTarget).closest('tr');
    	var id = $(tr).attr('id');
    	
    	document.location.href = 'index.php?cmd=contents&do=addNewPage&parentId='+id + '&type=sub_page';
    	return false;
    },
    cyr2lat: function(str) {
    	 
        var cyr2latChars = new Array(
    ['а', 'a'], ['б', 'b'], ['в', 'v'], ['г', 'g'],
    ['д', 'd'],  ['е', 'e'], ['ё', 'yo'], ['ж', 'zh'], ['з', 'z'],
    ['и', 'i'], ['й', 'y'], ['к', 'k'], ['л', 'l'],
    ['м', 'm'],  ['н', 'n'], ['о', 'o'], ['п', 'p'],  ['р', 'r'],
    ['с', 's'], ['т', 't'], ['у', 'u'], ['ф', 'f'],
    ['х', 'h'],  ['ц', 'c'], ['ч', 'ch'],['ш', 'sh'], ['щ', 'shch'],
    ['ъ', ''],  ['ы', 'y'], ['ь', ''],  ['э', 'e'], ['ю', 'yu'], ['я', 'ya'],
     
    ['А', 'A'], ['Б', 'B'],  ['В', 'V'], ['Г', 'G'],
    ['Д', 'D'], ['Е', 'E'], ['Ё', 'YO'],  ['Ж', 'ZH'], ['З', 'Z'],
    ['И', 'I'], ['Й', 'Y'],  ['К', 'K'], ['Л', 'L'],
    ['М', 'M'], ['Н', 'N'], ['О', 'O'],  ['П', 'P'],  ['Р', 'R'],
    ['С', 'S'], ['Т', 'T'],  ['У', 'U'], ['Ф', 'F'],
    ['Х', 'H'], ['Ц', 'C'], ['Ч', 'CH'], ['Ш', 'SH'], ['Щ', 'SHCH'],
    ['Ъ', ''],  ['Ы', 'Y'],
    ['Ь', ''],
    ['Э', 'E'],
    ['Ю', 'YU'],
    ['Я', 'YA'],
     
    ['a', 'a'], ['b', 'b'], ['c', 'c'], ['d', 'd'], ['e', 'e'],
    ['f', 'f'], ['g', 'g'], ['h', 'h'], ['i', 'i'], ['j', 'j'],
    ['k', 'k'], ['l', 'l'], ['m', 'm'], ['n', 'n'], ['o', 'o'],
    ['p', 'p'], ['q', 'q'], ['r', 'r'], ['s', 's'], ['t', 't'],
    ['u', 'u'], ['v', 'v'], ['w', 'w'], ['x', 'x'], ['y', 'y'],
    ['z', 'z'],
     
    ['A', 'A'], ['B', 'B'], ['C', 'C'], ['D', 'D'],['E', 'E'],
    ['F', 'F'],['G', 'G'],['H', 'H'],['I', 'I'],['J', 'J'],['K', 'K'],
    ['L', 'L'], ['M', 'M'], ['N', 'N'], ['O', 'O'],['P', 'P'],
    ['Q', 'Q'],['R', 'R'],['S', 'S'],['T', 'T'],['U', 'U'],['V', 'V'],
    ['W', 'W'], ['X', 'X'], ['Y', 'Y'], ['Z', 'Z'],
     
    [' ', '-'],['0', '0'],['1', '1'],['2', '2'],['3', '3'],
    ['4', '4'],['5', '5'],['6', '6'],['7', '7'],['8', '8'],['9', '9'],
    ['-', '-']
     
        );
     
        var newStr = new String();
     
        for (var i = 0; i < str.length; i++) {
     
            ch = str.charAt(i);
            var newCh = '';
     
            for (var j = 0; j < cyr2latChars.length; j++) {
                if (ch == cyr2latChars[j][0]) {
                    newCh = cyr2latChars[j][1];
     
                }
            }
            newStr += newCh;
     
        }
        return newStr.replace(/[-]{2,}/gim, '-').replace(/\n/gim, '');
    }
});

$(function()
{
    var P = new ContentsPage();
});
