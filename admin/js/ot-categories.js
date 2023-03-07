var searchMethods;
var dataToSavePredefinedParams;
var categoriesByRoot;
var lastLoadedNodeCategoryRoot;
var tmpSelectedSearchCategory;
var globalDialogBody;
var xhr;
var categoriesChild;

var Categories = new Backbone.Collection();
var CategoriesPage = Backbone.View.extend({
    "el": ".categories-wrapper",
    "events": {
        "submit #import-upload-form": "checkFileSelection",
        "click #importCatalogBtn": "beforeImportCatalog",
        "click #btn-upload-file": "uploadImportFile",
        "click #btn-upload-file-import": "importFile"
    },
    "rootCategory": 'Root_category',
    "lastLoadedNode": null,
    importFile: function () {
        var form = $('#import-upload-form');
        var url = $('#btn-upload-file-import').data('link');
        window.open(url);
        $('#btn-upload-file-import', form).attr('disabled','disabled');
        $('.ot-preloader-micro', form).show();
        $(form).ajaxSubmit({
            url: $(form).attr('action'),
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                console.log('success entry');
                $('.btn-primary', form).removeAttr('disabled');
                if (data && data.result && data.result == 'ok') {
                    if ($('#importСategotyId', form).val() != '0') {
                        if (data.errors && data.errors.length > 0) {
                            for (var i in data.errors) {
                                showError(data.errors[i]);
                            }
                            showMessage(trans.get('Data_updated_with_warnings'));
                        } else {
                            showMessage(trans.get('Data_updated_successfully'));
                        }
                        var id = $('#importСategotyId', form).val();
                        var li = $('li#' + id);
                        $('#jstree').jstree('close_node', li);
                        $(li).addClass('jstree-closed');
                        $(li).removeClass('jstree-leaf');
                        setTimeout(function () {
                            $('#jstree').jstree('open_node', li);
                        }, 1000);
                        $('.ot-preloader-micro', form).hide();
                        $('#ot_import_cat').hide();
                        $(form)[0].reset();
                        $('#btn-upload-file-import').hide();
                        $('#btn-upload-file-import').data('link','');
                        $('#btn-upload-file').show();
                        $('#importСategotyId', form).val('0');
                        console.log('hide form');
                    } else {
                        $(form)[0].reset();
                        $('#btn-upload-file-import').hide();
                        $('#btn-upload-file-import').data('link','');
                        $('#btn-upload-file').show();
                        $('#importСategotyId', form).val('0');
                        $('.ot-preloader-micro', form).hide();
                        showMessage(trans.get('Import_done_succes'));
                        setTimeout(function () {
                            console.log('reload page');
                            location.reload();
                        }, 10000)
                    }
                } else {
                    console.log('error');
                    showError(data);
                    $('.ot-preloader-micro', form).hide();
                }
            }
        });
        return false;
    },
    uploadImportFile: function () {
        //$('#btn-upload-file').button('loading').siblings('button').addClass('disabled');
    	var form = $('#import-upload-form');
        $('.ot-preloader-micro', form).show();
        $('#btn-upload-file', form).attr('disabled','disabled');
        var categoryId = $('#importСategotyId', form).val();
        $.get('?cmd=categories&do=file&save=1', {'categoryId': categoryId}, JSON).done(function(data) {
            if (!data.error && data.link) {
                $('.ot-preloader-micro', form).hide();
                $('#btn-upload-file', form).hide();
                $('#btn-upload-file-import')
                    .data('link', '?cmd=categories&do=file&download=' + data.link)
                    .show();
            } else {
                $('.ot-preloader-micro', form).hide();
                $('#btn-upload-file', form).removeAttr('disabled');
                showError(data);
            }
        });
    	return false;
    },
    beforeImportCatalog: function () {
        $('#import-upload-form')[0].reset();
    	$('#import-upload-form #importСategotyId').val('0');
    	return true;
    },
    checkFileSelection: function(){
        var file = $("#import-upload-form input[type=file]").val();
        if( file == '') {
            showError(trans.get('Please_select_file_before_upload'));
            $('#import-upload-form .ot-preloader-micro').hide();
            $('#import-upload-form .btn-primary').removeAttr('disabled');

            return false;
        }
        return true;
    },
    render: function()
    {
        return this;
    },
    initAliasGenerator: function(body) {
    	var self = this;
  	  	var alias = $('#alias', body);
  	  	if (alias.length > 0) {
  	  		$('#categoryName', body).unbind('input').bind('input', function() {
  				if (! $(alias).attr('original-value') || $(alias).attr('original-value') == '') {
  					var categoryName = $('#categoryName', body).val();
  					var aliasValue = self.cyr2lat(categoryName);
  					$(alias).val(aliasValue);
  				}
  	  		});
  	  		$(alias).unbind('input').bind('input', function(){
  	  			var aliasValue = $(alias).val();
  	  			$(alias).attr('original-value', aliasValue);
  	  			return true;
  	  		});
  		}
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
    },
    initialize: function()
    {
        var self = this;
        this.render();

        $("#jstree")
            .bind("before.jstree", function(e, data) {
            })
            .bind("open_node.jstree", function(e, data) {
            })
            .jstree({
                // Подключаем плагины
                "plugins" : ["themes","json_data","ui","crrm","dnd","search","types"],
                "themes" : {
                    "theme" : "classic",
                    "dots" : true,
                    "icons" : true
                },
                "dnd" : {
                    "open_timeout" : 1000
                },
                "json_data" : {
                    "data" : getPreparedCategories(CategoriesCategories.models), // 1
                    'correct_state': true,
                    'progressive_render': true,
                    'progressive_unload': true,
                    "ajax" : {
                        "url" : '?cmd=Categories&do=getCategories',
                        // the `data` function is executed in the instance's scope
                        // the parameter is the node being loaded
                        // (may be -1, 0, or undefined when loading the root nodes)
                        "data" : function (node) {
                            // the result is fed to the AJAX request `data` option
                            return {
                                "parentId" : node.attr ? node.attr("id") : 0
                            };
                        },
                        "success" : function (data) {
                            if (data.categories) {
                                if (data.categories.length) {
                                    categoriesChild = data.categories;
                                    return getPreparedCategories(data.categories);
                                } else if (self.lastLoadedNode) {
                                    self.lastLoadedNode.removeClass('jstree-open').addClass('jstree-leaf');
                                }
                            } else {
                                showError(data);
                            }
                        },
                        "complete": function () {
                            self.setCategoryStatusIcon(getPreparedCategories(categoriesChild));
                        }
                    }
                }
            })
            .bind("create.jstree", function (e, node) {
                var parentId = 0;
                var categoryId = $(node.rslt.obj).attr('externalid');
                var alias = $(node.rslt.obj).attr('alias');
                var approxWeight = $(node.rslt.obj).attr('approxweight');
                var meta_pagetitle = $(node.rslt.obj).attr('seo_pagetitle');
                var meta_title = $(node.rslt.obj).attr('seo_title');
                var meta_keywords = $(node.rslt.obj).attr('seo_keywords');
                var meta_description = $(node.rslt.obj).attr('seo_description');
                var seoText = $(node.rslt.obj).attr('seoText');
                var parentId = $(node.rslt.obj).attr('parentId');
                var iconimageurl = $(node.rslt.obj).attr('iconimageurl');
                var categoryIconClass = $(node.rslt.obj).attr('categoryIconClass');
                $.post(
                    "?cmd=Categories&do=createCategory",
                    {
                        "parentId" : parentId < 0 ? 0 : parentId,
                        "categoryId": categoryId,
                        "position" : node.rslt.position,
                        "name" : node.rslt.name,
                        "approxweight": approxWeight,
                        "alias": alias,
                        "meta_pagetitle": meta_pagetitle,
                        "meta_title": meta_title,
                        "meta_keywords": meta_keywords,
                        "meta_description": meta_description,
                        "seoText": seoText,
                        "predefinedParams" : dataToSavePredefinedParams,
                        "iconimageurl" : iconimageurl,
                        'categoryIconClass': categoryIconClass
                    },
                    function (data) {
                        if (data.newId) {
                            var li = $(node.rslt.obj);
                            $(li).attr("id", data.newId);
                            $(li).attr("alias", data.aliasToSave);
                            $(li).attr("isparent", data.isParent);
                            if (data.isParent) {
                                $(li).removeClass('jstree-leaf').addClass('jstree-closed');
                            }
                            if ((typeof dataToSavePredefinedParams.provider === 'undefined') || (dataToSavePredefinedParams.provider == '')) {
                                providerType = ' ';
                            } else {
                                providerType = ' [' + dataToSavePredefinedParams.provider + '] ';
                            }
                            if (!isMetrologistActive || approxWeight == '') {
                                titleApproxWeight = ' ';
                            } else {
                                titleApproxWeight = ' (' + approxWeight + ' ' + trans.get('kg') + ')';
                            }
                            var title = $.trim(node.rslt.name + titleApproxWeight + providerType);

                            if(typeof dataToSavePredefinedParams.category !== 'undefined' && dataToSavePredefinedParams.category.id) {
                                $(li).attr('externalid', dataToSavePredefinedParams.category.id);
                            }

                            $(li).attr('name', node.rslt.name);
                            $("#jstree").jstree("rename_node", li, title);
                            $(li).attr("predifenedparams", JSON.stringify(dataToSavePredefinedParams));

                            $(li).attr("availableitemratinglistcontenttypes", '');
                            if (dataToSavePredefinedParams.preDefineMode === 'virtual') {
                            	$(li).attr("isvirtual", "true");
                            }
                            $(li).attr('i', data.position);

                            self.addJSTreeItemTaskBar(li);

                            var ul =$(li).closest('ul');

                            $("#jstree").jstree("sort",ul);

                            showMessage(trans.get('Notify_success'));
                            if (typeof data.positions !== 'undefined') {
                            	for ( var id in data.positions) {
                            		$('li#' + id).attr('i', data.positions[id]);
                            	}
                            }
                        } else {
                            $.jstree.rollback(node.rlbk);
                            showError(data);
                        }
                    }, 'json'
                );
            })
            .bind("remove.jstree", function (e, node) {
                    $.post(
                        "?cmd=Categories&do=removeCategory",
                        {"id" : node.rslt.obj.attr("id")},
                        function (data) {
                            if (data.error) {
                                node.inst.refresh();
                                showError(data);
                            } else {
                                showMessage(trans.get('Notify_success'));
                            }
                        }, 'json'
                    );
                return false;
            })
            .bind("rename.jstree", function (e, node) {
                if (node.rslt.new_name == node.rslt.old_name) {
                    return;
                }
                $.post(
                    "?cmd=Categories&do=renameCategory",
                    {"id" : node.rslt.obj.attr("id"), "newName" : node.rslt.new_name},
                    function (data) {
                        if (data.error) {
                            $.jstree.rollback(node.rlbk);
                            showError(data);
                        } else {
                            showMessage(trans.get('Notify_success'));
                        }
                    }, 'json'
                );
            })
            .bind("move_node.jstree", function (e, node) {
                var categories = {
                    categories: [],
                    newOrder: node.rslt.cp,
                    newParent: 0
                };
                if ((node.rslt.cr !== -1)) {
                    categories.newParent = node.rslt.np.attr("id");
                }

                /*
                 .o - the node being moved
                 .r - the reference node in the move
                 .ot - the origin tree instance
                 .rt - the reference tree instance
                 .p - the position to move to (may be a string - "last", "first", etc)
                 .cp - the calculated position to move to (always a number)
                 .np - the new parent
                 .oc - the original node (if there was a copy)
                 .cy - boolen indicating if the move was a copy
                 .cr - same as np, but if a root node is created this is -1
                 .op - the former parent
                 .or - the node that was previously in the position of the moved node
                 */
                node.rslt.o.each(function (i) {
                    categories.categories.push($(node.rslt.o[i]).attr('id'));
                });

                $.ajax({
                    async : true,
                    type: 'POST',
                    dataType: 'json',
                    url: "?cmd=Categories&do=moveCategories",
                    data : {
                        "categories" : categories
                    },
                    success : function (data) {
                        if (data.error) {
                            $.jstree.rollback(node.rlbk);
                            showError(data);
                        } else {
                            showMessage(trans.get('Category_moved'));
                            if (node.rslt.cy && $(node.rslt.oc).children("UL").length) {
                                node.inst.refresh(node.inst._get_parent(node.rslt.oc));
                            }
                        }
                    }
                });
            })
            .one("reopen.jstree", function (event, data) {
            })
            // 3) but if you are using the cookie plugin or the UI `initially_select` option:
            .one("reselect.jstree", function (event, data) {
            })
            .bind("loaded.jstree", function(e, data) {
                $('.categories-wrapper #jstree li').each(function() {
                    self.addJSTreeItemTaskBar(this);
                });

                $('.ot_show_crud_cat_item_window').click(function() {
                    dataToSavePredefinedParams = {};
                    $('.ot_crud_cat_item_window .modal-body #categoryName').attr('value', '');

                    $('.ot_crud_cat_item_window .modal-body #alias').attr('value', '');
                    $('.ot_crud_cat_item_window .modal-body #alias').attr('alias-id', 'new');
                    $('.ot_crud_cat_item_window .modal-body #approxweight').attr('value', '');
                    $('.ot_crud_cat_item_window .modal-body #meta_title').attr('value', '');
                    $('.ot_crud_cat_item_window .modal-body #meta_title_prefix').attr('value', '');
                    $('.ot_crud_cat_item_window .modal-body #meta_title_suffix').attr('value', '');
                    $('.ot_crud_cat_item_window .modal-body #meta_keywords').html('');
                    $('.ot_crud_cat_item_window .modal-body #meta_description').html('');
                    $('.ot_crud_cat_item_window .modal-body #seoText').html('');
                    $('.ot_crud_cat_item_window #ot_cat_filters_head').hide();
                    $('.ot_crud_cat_item_window .modal-body #parentCategory').attr('value','');
                    $('.ot_crud_cat_item_window .modal-body #parentCategoryId').attr('value',0);
                    $('.ot_crud_cat_item_window .modal-body #newUrlPicture').attr('value', '');
                    $('.ot_crud_cat_item_window .modal-body .image-preview').attr('src', '');


                    var content = $('.ot_crud_cat_item_window .modal-body').html();
                    content = content.replace(new RegExp("ot_cat_data", 'g'), "ot_cat_data1");
                    content = content.replace(new RegExp("ot_cat_meta", 'g'), "ot_cat_meta1");
                    content = content.replace(new RegExp("ot_cat_content", 'g'), "ot_cat_content1");
                    content = content.replace(new RegExp("ot_cat_filters", 'g'), "ot_cat_filters1");
                    content = content.replace(new RegExp("seoText", 'g'), "seoText1");
                    content = content.replace(new RegExp('<div class="editableform-loading"></div>', 'g'), "");

                    removeTinyMCE();

                    var modal = modalDialog(trans.get('Add_category'), content, function(body) {

                        if ( $('.modal:visible .btn-primary').hasClass('disabled')) {
                            return false;
                        }

                        var categoryName = $('#categoryName', body).val();
                        var categoryId = $('#categoryId', body).val();
                        var alias = $('#alias', body).val();
                        var approxWeight = $('#approxweight', body).val();
                        var meta_pagetitle = $('#meta_title', body).val();
                        var meta_title = $('#meta_title_prefix', body).val() + '||' + $('#meta_title_suffix', body).val();
                        var meta_keywords = $('#meta_keywords', body).val();
                        var meta_description = $('#meta_description', body).val();
                        if (tinyMCE.editors.length > 0) {
                            var seoText = tinyMCE.editors[0].getContent();
                        } else {
                            var seoText = $('#seoText1', body).html();
                        }
                        if ($('#parentCategory', body).val() == '') {
                            var parentId = 0;
                        } else {
                            var parentId = $('#parentCategoryId', body).val();
                        }

                        if (! categoryName.length) {
                            showError(trans.get('Enter_category_name_promt'));
                            return false;
                        }
                        if (isSeoActive) {
                            var aliasPattern = /^[^!#$%^&*+ =~`'";:.>,<|/?\\{}\[\]]+$/i;
                            if (! aliasPattern.test(alias) && alias !== '') {
                                showError(trans.get('Alias_is_invalid'));
                                return false;
                            }
                        }
                        var iconimageurl = $('#newUrlPicture', body).val();

                        var parentLi = 0;
                        if (parentId && parentId != '0') {
                            parentLi = $('#jstree li[id="'+parentId+'"]');
                        }
                        if (! checkPredefinedParams()) {
                            return false;
                        }
                        $('.modal:visible .btn-primary').addClass('disabled');

                        $.post(
                            "?cmd=Categories&do=checkCategoryAlias",
                            {
                                "name" : categoryName,
                                "alias": alias
                            },
                            function (data) {
                                if (data.error) {
                                    showError(data);
                                } else {
                                    var addNodeFunction = function () {
                                        $("#jstree").jstree("create", parentLi, 'last', categoryName, function(node) {
                                            $(node).attr('externalid', categoryId);
                                            $(node).attr('alias', alias);
                                            $(node).attr('approxweight', approxWeight);
                                            $(node).attr('seo_pagetitle', meta_pagetitle);
                                            $(node).attr('seo_title', meta_title);
                                            $(node).attr('seo_keywords', meta_keywords);
                                            $(node).attr('seo_description', meta_description);
                                            $(node).attr('seoText', seoText);
                                            $(node).attr('parentId', parentId);
                                            $(node).attr('iconimageurl', iconimageurl);
                                        }, true);
                                    };
                                    if ($('li[id="' + parentId + '"]').length) {
                                        $.jstree
                                            ._reference('li[id="' + parentId + '"]')
                                            .open_node('li[id="' + parentId + '"]', addNodeFunction);
                                    } else {
                                        addNodeFunction();
                                    }
                                modal.find('.close').trigger('click');
                                }
                            }, 'json'
                        );
                        return false;
                      },
                      {confirm: trans.get('Save'), cancel: trans.get('Cancel') }, function(body) {
                    	  globalDialogBody = body;
                    	  self.initAliasGenerator(body);
                    	  /*$('.ot_inline_help', body).clickover();*/
                      });
                      asignProvidersActions(globalDialogBody);
                      initPopoverInsideDialog(globalDialogBody);
                      $('.ot_cat_content1_toogle', globalDialogBody).click(function() {
                          initTinyMCE();
                      });
                });
            })
            .bind("select_node.jstree", function (event, data) {
            })
            .bind("create_node.jstree", function (event, data) {
            })

            .bind("load_node.jstree", function (event, data) {
                self.setCategoryStatusIcon(getPreparedCategories(CategoriesCategories.models));

                if (typeof(data) !== 'undefined') {
                    var item = data.args[0][0];
                    if (typeof(item) !== 'undefined') {
                        $('li',item).each(function() {
                            self.addJSTreeItemTaskBar(this);
                        });
                        self.lastLoadedNode = data.rslt.obj;
                    }
                }
            });


/*        $('#import-upload-form').submit(function(){
            $('#import-upload-form .ot-preloader-micro').show();
            $('#import-upload-form .btn-primary').attr('disabled','disabled');
            return true;
        });*/

        var a = $('#activeLanguagesContainer ul.dropdown-menu li a[data-value=""]');
        var li = $(a).closest('li');
        $(li).remove();

    },

    setCategoryStatusIcon: function(categories) {
        categories.forEach(function(item) {
            if (item['attr'] !== undefined) {
                if ($('i').is('#' + item['attr']['id'])) {
                    return;
                }
                var jsTreeElem = $('li#' + item['attr']['id'] + ' a .jstree-icon');
                switch (item['attr']['deletestatus']) {
                    case 'Deleted':
                        jsTreeElem.append('<i class="icon-remove-sign" style="color:#ce5252" id="' + item['attr']['id'] + '" title="'+trans.get('category_deleted')+'"></i>');
                        break;
                    case 'ParentOfDeleted':
                        jsTreeElem.append('<i class="icon-remove-sign" style="color:#cecb35" id="' + item['attr']['id'] + '" title="'+trans.get('category_parent_deleted')+'"></i>');
                        break;
                    case 'ParentOfHiddenDeleted':
                        jsTreeElem.append('<i class="icon-remove-sign" style="color:#c0c0c0" id="' + item['attr']['id'] + '" title="'+trans.get('category_parent_hidden_deleted')+'"></i>');
                        break;
                    case 'default':
                    return true;
                }
            }
            });
    },

    issetRatingListForCategory: function(item) {
        if (isRatingListForCategoryActive) {
            var setsTypes = $(item).attr('availableitemratinglistcontenttypes');
            if (setsTypes.indexOf('Item') !== -1) {
                return true;
            }
        }
        return false;
    },
    assignItemHandlers: function(item) {
        var self = this;
        $('.ot_cat_actions .add_category_button',item).click(function() {
            var li = $(this).closest('li');
            dataToSavePredefinedParams = {};
            $('.ot_crud_cat_item_window .modal-body #categoryName').attr('value', '');

            //$('.ot_crud_cat_item_window .modal-body #categoryId').attr('value', '');

            $('.ot_crud_cat_item_window .modal-body #alias').attr('value', '');
            $('.ot_crud_cat_item_window .modal-body #alias').attr('alias-id', 'new');
            $('.ot_crud_cat_item_window .modal-body #approxweight').attr('value', '');

            $('.ot_crud_cat_item_window .modal-body #meta_title').attr('value', '');
            $('.ot_crud_cat_item_window .modal-body #meta_title_prefix').attr('value', '');
            $('.ot_crud_cat_item_window .modal-body #meta_title_suffix').attr('value', '');
            $('.ot_crud_cat_item_window .modal-body #meta_keywords').html('');
            $('.ot_crud_cat_item_window .modal-body #meta_description').html('');
            $('.ot_crud_cat_item_window .modal-body #seoText').html('');
            $('.ot_crud_cat_item_window .modal-body #parentCategory').attr('value',$(li).attr('name').trim());
            $('.ot_crud_cat_item_window .modal-body #parentCategoryId').attr('value',$(li).attr('id'));
            $('.ot_crud_cat_item_window .modal-body #newUrlPicture').attr('value', '');
            $('.ot_crud_cat_item_window .modal-body .image-preview').attr('src', '');
            $('.ot_crud_cat_item_window .modal-body .form-category-icon__input').attr('value', '');
            $('.ot_crud_cat_item_window .modal-body .form-category-icon__selected span').attr('class', '');


            $('#ot_cat_data').tab('show');

            $('.ot_crud_cat_item_window #ot_cat_filters_head').hide();

            var content = $('.ot_crud_cat_item_window .modal-body').html();
            content = content.replace(new RegExp("ot_cat_data",'g'),"ot_cat_data1");
            content = content.replace(new RegExp("ot_cat_meta",'g'),"ot_cat_meta1");
            content = content.replace(new RegExp("ot_cat_content",'g'),"ot_cat_content1");
            content = content.replace(new RegExp("ot_cat_filters",'g'),"ot_cat_filters1");
            content = content.replace(new RegExp("seoText",'g'),"seoText1");
            content = content.replace(new RegExp('<div class="editableform-loading"></div>', 'g'), "");

            removeTinyMCE();

            var modal = modalDialog(trans.get('Add_category'), content, function(body) {
                var categoryName = $('#categoryName', body).val();
                var categoryId = $('#categoryId', body).val();
                var alias = $('#alias', body).val();
                var approxWeight = $('#approxweight', body).val();
                var meta_pagetitle = $('#meta_title', body).val();
                var meta_title = $('#meta_title_prefix', body).val() + '||' + $('#meta_title_suffix', body).val();
                var meta_keywords = $('#meta_keywords', body).val();
                var meta_description = $('#meta_description', body).val();
                if (tinyMCE.editors.length > 0) {
                    var seoText = tinyMCE.editors[0].getContent();
                } else {
                    var seoText = $('#seoText1', body).html();
                }
                if ($('#parentCategory', body).val() == '') {
                    var parentId = 0;
                } else {
                    var parentId = $('#parentCategoryId', body).val();
                }
                if (isSeoActive) {
                    var aliasPattern = /^[^!#$%^&*+ =~`'";:.>,<|/?\\{}\[\]]+$/i;
                    if (! aliasPattern.test(alias) && alias !== '') {
                        showError(trans.get('Alias_is_invalid'));
                        return false;
                    }
                }
                var iconimageurl = $('#newUrlPicture', body).val();
                var categoryIconClass = $('.form-category-icon__input').val();

                if (! categoryName.length) {
                    showError(trans.get('Enter_category_name_promt'));
                    return false;
                }

                var parentLi = li;
                if (parentId && parentId != '0') {
                    parentLi = $('#jstree li[id="'+parentId+'"]');
                    if (!parentLi) {
                        parentLi = 0;
                    }
                }
                if (! checkPredefinedParams()) {
                    return false;
                }

                $.post(
                    "?cmd=Categories&do=checkCategoryAlias",
                    {
                        "name" : categoryName,
                        "alias": alias
                    },
                    function (data) {
                        if (data.error) {
                            showError(data);
                            return false;
                        } else {
                            var addNodeFunction = function () {
                                $("#jstree").jstree("create", parentLi, 'last', categoryName, function (node) {
                                    $(node).attr('externalid', categoryId);
                                    $(node).attr('alias', alias);
                                    $(node).attr('name', categoryName);
                                    $(node).attr('approxweight', approxWeight);
                                    $(node).attr('seo_pagetitle', meta_pagetitle);
                                    $(node).attr('seo_title', meta_title);
                                    $(node).attr('seo_keywords', meta_keywords);
                                    $(node).attr('seo_description', meta_description);
                                    $(node).attr('seoText', seoText);
                                    $(node).attr('parentId', parentId);
                                    $(node).attr("predifenedparams", JSON.stringify(dataToSavePredefinedParams));
                                    $(node).attr("iconimageurl", iconimageurl);
                                    $(node).attr("categoryIconClass", categoryIconClass);
                                }, true);
                            };
                            if ($('li[id="' + parentId + '"]').length) {
                                $.jstree
                                    ._reference('li[id="' + parentId + '"]')
                                    .open_node('li[id="' + parentId + '"]', addNodeFunction);
                            } else {
                                addNodeFunction();
                            }

                            modal.toggle();
                            $(".modal-backdrop").remove();
                        }
                    }, 'json'
                );
                return false;
              }, {confirm: trans.get('Save'), cancel: trans.get('Cancel') }, function(body) {
            	  globalDialogBody = body;
            	  self.initAliasGenerator(body);
            	  /*$('.ot_inline_help', body).clickover();*/
        	  });
              asignProvidersActions(globalDialogBody);
              initPopoverInsideDialog(globalDialogBody);
              $('.ot_cat_content1_toogle', globalDialogBody).click(function() {
                  initTinyMCE();
              });
        });
        $('.ot_cat_actions .rename_category_button', item).click(function() {
            var li = $(this).closest('li');
            var categoryId = $(li).attr('id');
            var parent = li.parent().closest('li');
            var parentName = parent.attr('name');
            var parentId = (typeof parent.attr('id')) !== 'undefined' ? parent.attr('id') : 0;

            if (typeof(parentName) !== 'undefined') {
                parentName = parentName.trim();
            } else {
                parentName = '';
            }
            var oldCategoryName = $(li).attr('name').trim();
            var externalId = $(li).attr('externalid');
            var oldAlias = $(li).attr('alias');
            var oldApproxWeight = $(li).attr('approxweight');

            var oldCategoryIconClass = $(li).attr('categoryIconClass');
            if (typeof oldCategoryIconClass == 'undefined') {
                oldCategoryIconClass = '';
            }

            dataToSavePredefinedParams = $.parseJSON($(li).attr('predifenedparams'));
            if (undefined == externalId && dataToSavePredefinedParams &&
            		(typeof dataToSavePredefinedParams.category !== 'undefined') && dataToSavePredefinedParams.category.id) {
            	externalId = dataToSavePredefinedParams.category.id;
            }

            $('.ot_crud_cat_item_window .modal-body #categoryName').attr('value', oldCategoryName);

            $('.ot_crud_cat_item_window .modal-body #alias').attr('value', oldAlias);

            $('.ot_crud_cat_item_window .modal-body #approxweight').attr('value', oldApproxWeight);

            $('.ot_crud_cat_item_window .modal-body #meta_title').attr('value', '');
            $('.ot_crud_cat_item_window .modal-body #meta_title_prefix').attr('value', '');
            $('.ot_crud_cat_item_window .modal-body #meta_title_suffix').attr('value', '');
            $('.ot_crud_cat_item_window .modal-body #meta_keywords').html('');
            $('.ot_crud_cat_item_window .modal-body #meta_description').html('');
            $('.ot_crud_cat_item_window .modal-body #seoText').html('');

            $('.ot_crud_cat_item_window .modal-body #parentCategory').attr('value',parentName);
            $('.ot_crud_cat_item_window .modal-body #parentCategoryId').attr('value',parentId);

            $('.ot_crud_cat_item_window .modal-body #meta_title').attr('value', $(li).attr('seo_pagetitle'));

            $('.ot_crud_cat_item_window .modal-body #newUrlPicture').attr('value', $(li).attr('iconimageurl'));
            $('.ot_crud_cat_item_window .modal-body .image-preview').attr('src', $(li).attr('iconimageurl'));

            var prefixSuffix = $(li).attr('seo_title');
            if (prefixSuffix) {
                var ps = prefixSuffix.split('||');
                if (ps.length>0) {
                    $('.ot_crud_cat_item_window .modal-body #meta_title_prefix').attr('value', ps[0]);
                }
                if (ps.length>1) {
                    $('.ot_crud_cat_item_window .modal-body #meta_title_suffix').attr('value', ps[1]);
                }
            } else {
                $('.ot_crud_cat_item_window .modal-body #meta_title_prefix').attr('value', '');
                $('.ot_crud_cat_item_window .modal-body #meta_title_suffix').attr('value', '');

            }

            $('.ot_crud_cat_item_window .modal-body #meta_keywords').html($(li).attr('seo_keywords'));
            $('.ot_crud_cat_item_window .modal-body #meta_description').html($(li).attr('seo_description'));

            $('.ot_crud_cat_item_window .modal-body .form-category-icon__input').attr('value', oldCategoryIconClass);
            $('.ot_crud_cat_item_window .modal-body .form-category-icon__selected span').attr('class', oldCategoryIconClass);

            $('.ot_crud_cat_item_window #ot_cat_filters_head').show();
            var content = $('.ot_crud_cat_item_window .modal-body').html();
            content = content.replace(new RegExp("ot_cat_data", 'g'), "ot_cat_data1");
            content = content.replace(new RegExp("ot_cat_meta", 'g'), "ot_cat_meta1");
            content = content.replace(new RegExp("ot_cat_content", 'g'), "ot_cat_content1");
            content = content.replace(new RegExp("ot_cat_filters", 'g'), "ot_cat_filters1");
            content = content.replace(new RegExp("seoText", 'g'), "seoText1");

            removeTinyMCE();

            var onConfirmCallback = function(body) {
                var categoryName = $('#categoryName', body).val();
                var alias = $('#alias', body).val();
                var approxWeight = $('#approxweight', body).val();
                var meta_pagetitle = $('#meta_title', body).val();
                var meta_title = $('#meta_title_prefix', body).val() + '||' + $('#meta_title_suffix', body).val();
                var meta_keywords = $('#meta_keywords', body).val();
                var meta_description = $('#meta_description', body).val();
                if (tinyMCE.editors.length > 0) {
                    var seoText = tinyMCE.editors[0].getContent();
                } else {
                    var seoText = $('#seoText1', body).html();
                }
                if ($('#parentCategory', body).val() == '') {
                    var newParentId = 0;
                } else {
                    var newParentId = $('#parentCategoryId', body).val();
                }

                if (newParentId === false) {
                    newParentId = parentId;
                }
                if (isSeoActive) {
                    var aliasPattern = /^[^!#$%^&*+ =~`'";:.>,<|/?\\{}\[\]]+$/i;
                    if (! aliasPattern.test(alias) && alias !== '') {
                        showError(trans.get('Alias_is_invalid'));
                        return false;
                    }
                }
                var iconimageurl = $('#newUrlPicture', body).val();
                var categoryIconClass = $('.form-category-icon__input').val();

                if (! categoryName.length) {
                    showError(trans.get('Enter_category_name_promt'));
                    return false;
                }
                if (! checkPredefinedParams()) {
                    return false;
                }
                if (categoryId.length) {
                    var params = {
                        "id" : li.attr("id"),
                        "newName" : categoryName,
                        "categoryId": categoryId,
                        "parentId": newParentId,
                        "approxweight": approxWeight,
                        "alias": alias,
                        "meta_pagetitle": meta_pagetitle,
                        "meta_title": meta_title,
                        "meta_keywords": meta_keywords,
                        "meta_description": meta_description,
                        "seoText": seoText,
                        "predefinedParams" : dataToSavePredefinedParams,
                        "oldParentId" : parentId,
                        "iconimageurl" : iconimageurl,
                        "categoryIconClass": categoryIconClass
                    };
                    if (oldAlias != alias) {
                        $.post(
                            "?cmd=Categories&do=checkCategoryAlias",
                            {
                                "name" : categoryName,
                                "alias": alias
                            },
                            function (data) {
                                if (data.error) {
                                    showError(data);
                                } else {
                                    self.sendUpdateCategoryRequest(params, li);
                                }
                            }, 'json'
                        );
                    } else {
                        self.sendUpdateCategoryRequest(params, li);
                        return true;
                    }
                } else {
                    showError(trans.get('Enter_category_name_promt'));
                    return false;
                }
                return false;
            };

            var onShowCallback = function(body){
                globalDialogBody = body;
                self.initAliasGenerator(body);
                var alias = $('#alias', body).val();
                $('#alias', body).attr('original-value', alias);
                initPopoverInsideDialog(body);
                setTimeout(function(){
                    $.ajax({
                        async : true,
                        type: 'POST',
                        dataType: 'json',
                        url: "?cmd=Categories&do=getCategoryData",
                        data : {
                            "categoryId" : categoryId,
                            "externalId" : externalId,
                            "regionId" : regionId
                        },
                        success : function (data) {
                            if (data.error) {
                                showError(data);
                                $(globalDialogBody).find('.preDefinedParams').html('');
                            } else {
                                $('.level-1 .modal-body #seoText1').html(data.seoText);
                                initTinyMCE();
                                $('.level-1 .modal-body .editableform-loading').remove();
                                $('.level-1 .modal-body #search_filters').html(data.filters);
                                if (data.totalCount) {
                                    $('.level-1 .modal-body #totalCountOfItems').find('span').html(data.totalCount);
                                    $('.level-1 .modal-body #totalCountOfItems').show();
                                }
                                $('.level-1 .modal-body #search_filters .ot_inline_editable').editable().on('shown', function() {
                                    var a = $(this).closest('a');
                                    var form = ('.editableform',a);
                                    var submitFunction = function(event) {
                                        form.submit();
                                        event.stopPropagation();
                                    };
                                    $('.btn-primary',form).unbind('click').unbind('submit');
                                    $('.btn-primary',form).click(submitFunction);
                                });
                                if (data.categoryWasDeleted) {
                                    showError(trans.get('Caregory_was_deleted_from_provider'));
                                    delete dataToSavePredefinedParams.category;
                                    delete dataToSavePredefinedParams.provider;
                                    dataToSavePredefinedParams.preDefineMode = 'virtual';
                                }
                                if (typeof dataToSavePredefinedParams.region !== 'undefined') {
                                    dataToSavePredefinedParams.region.name = data.regionName;
                                }
                                if (typeof dataToSavePredefinedParams.category !== 'undefined') {
                                    dataToSavePredefinedParams.category.name = data.externalCategory.name;
                                }
                                if (typeof dataToSavePredefinedParams.Configurators !== 'undefined') {
                                    $.each(dataToSavePredefinedParams.Configurators, function(s, conf) {
                                        if (typeof data.Configurators[conf.pid] !== 'undefined') {
                                            conf.name = data.Configurators[conf.pid].name;
                                            if (typeof data.Configurators[conf.pid].values[conf.vid] !== 'undefined') {
                                                conf.valueName = data.Configurators[conf.pid].values[conf.vid].name;
                                            } else {
                                                conf.valueName = trans.get('Undefined') + ' ID-' + conf.vid;
                                            }
                                        }
                                    });
                                }
                                asignProvidersActions(globalDialogBody);
                            }
                        }
                    });
                }, 1000);
            };

            modalDialog(trans.get('Edit_category'), content, onConfirmCallback, {confirm: trans.get('Save'), cancel: trans.get('Cancel') }, onShowCallback);
            var regionId = '';
            if (typeof dataToSavePredefinedParams.region !== 'undefined') {
                regionId = dataToSavePredefinedParams.region.RegionId;
            }
            $(globalDialogBody).find('.preDefinedParams').html('<div class="controls"><div class="ot-preloader-mini"></div></div>');

        });
        $('.ot_cat_actions .delete_category_button', item).click(function() {
            var li = $(this).closest('li');
            var isInternal = $(li).attr('isinternal');
            if (isInternal == 'false') {
            	showError(trans.get('External_category_cannot_be_deleted'));
            	return false;
            }

            var contentModalDialog = trans.get('Really_delete_this_category');
            // если для категории есть подборка
            if (self.issetRatingListForCategory(item)) {
                contentModalDialog += ' <span style="color: red;">'+trans.get('Sets_items_will_be_deleted')+'!</span>';
            }

            modalDialog(trans.get('Confirm_needed'), contentModalDialog, function() {
                $("#jstree").jstree("remove", li);
            });
        });
        $('.ot_cat_actions .move_category_button', item).mousedown(function(e) {
            var li = $(this).closest('li');
            var a = $('a',li);
            $("#jstree").jstree("start_drag", a, e);
            return false;
        });
        $('.ot_cat_actions .show_category_button', item).mousedown(function(e) {
            var li = $(this).closest('li');
            $(this).hide();
            $('.ot_cat_actions .procces_category_button', item).show();
            $.post(
                "?cmd=Categories&do=visibleCategory",
                {
                    "categoryId": li.attr('id'),
                    "visible" : 'true'
                },
                function (data) {
                    showMessage(trans.get('Notify_success'));
                    li.attr('ishidden', 'false');
                    li.attr('IsHiddenUI', 'false');
                    $('.ot_cat_actions .procces_category_button', item).hide();
                    $('.ot_cat_actions .hide_category_button', item).show();
                    $('a:first', item).css('color','');
                }, 'json'
            );

            return false;
        });
        $('.ot_cat_actions .hide_category_button', item).mousedown(function(e) {
            var li = $(this).closest('li');
            $(this).hide();
            $('.ot_cat_actions .procces_category_button', item).show();
            $.post(
                "?cmd=Categories&do=visibleCategory",
                {
                    "categoryId": li.attr('id'),
                    "visible" : 'false'
                },
                function (data) {
                    showMessage(trans.get('Notify_success'));
                    li.attr('IsHiddenUI', 'true');
                    li.attr('ishidden', 'true');
                    $('a:first', item).css('color', 'gray');
                    $('.ot_cat_actions .procces_category_button', item).hide();
                    $('.ot_cat_actions .show_category_button', item).show();
                }, 'json'
            );
            return false;
        });
        $('.ot_cat_actions .open_category_button', item).mousedown(function(e) {
            var li = $(this).closest('li');
            var id = $(li).attr('id');
            var url = '/index.php?p=search&cid=' + id;
            window.open(url);
            return false;
        });
        $('.ot_cat_actions .import_button', item).click(function() {
            var li = $(this).closest('li');
            var id = $(li).attr('id');
            $('#importСategotyId').val(id);
            return true;
        });
        $('.ot_cat_actions .export_button', item).click(function() {
            var li = $(this).closest('li');
            var id = $(li).attr('id');
            var url = '/admin/index.php?cmd=categories&do=exportXml&categoryId=' + id;
            window.open(url);
        });
        $('.ot_cat_actions .move_down_button', item).click(function() {
            var li = $(this).closest('li');
            var nextLi = $(li).next();
            var categoryId = li.attr('id');
            var ul = li.parent();
            var i = 1;
            $('li', ul).each(function() {
                var id = $(this).attr('id');
                if (id == categoryId) {
                    return false;
                }
                i++;
            });
            if (i == $('li', ul).length) {
                return;
            }
            $.post(
                "?cmd=Categories&do=orderCategory",
                {
                    "categoryId": li.attr('id'),
                    "i" : i+1
                },
                function (data) {
                    nextLi.insertBefore(li);
                }, 'json'
            );
        });
        $('.ot_cat_actions .move_up_button', item).click(function() {
            var li = $(this).closest('li');
            var prevLi = $(li).prev();
            var categoryId = li.attr('id');
            var ul = li.parent();
            var i = 1;
            $('li', ul).each(function() {
                var id = $(this).attr('id');
                if (id == categoryId) {
                    return false;
                }
                i++;
            });
            if (i == 0) {
                return;
            }
            $.post(
                "?cmd=Categories&do=orderCategory",
                {
                    "categoryId": li.attr('id'),
                    "i" : i - 1
                },
                function (data) {
                    prevLi.insertAfter(li);
                }, 'json'
            );
        });


        $('.ot_cat_actions .copy_button', item).click(function() {
            var li = $(this).closest('li');
            var categoryName = $(li).attr('name');
            var externalId = $(li).attr('externalid');
            var categoryId = li.attr('id');
            var parentId = li.attr('parentid');
            $('.categories-wrapper #clipboard_category_external_id').val(externalId);
            $('.categories-wrapper #clipboard_category_id').val(categoryId);
            $('.categories-wrapper #clipboard_category_name').val(categoryName);
            $('.categories-wrapper #clipboard_op').val('copy');
            $('.categories-wrapper #clipboard_category_parent_id').val(parentId);
            showMessage(trans.get('Notify_success'));
        });

        $('.ot_cat_actions .cut_button', item).click(function() {
            var li = $(this).closest('li');
            var categoryName = $(li).attr('name');
            var externalId = $(li).attr('externalid');
            var categoryId = li.attr('id');
            $('.categories-wrapper #clipboard_category_external_id').val(externalId);
            $('.categories-wrapper #clipboard_category_id').val(categoryId);
            $('.categories-wrapper #clipboard_category_name').val(categoryName);
            $('.categories-wrapper #clipboard_op').val('cut');
            showMessage(trans.get('Notify_success'));
        });

        $('.ot_cat_actions .paste_button', item).click(function() {
            var li = $(this).closest('li');

            var srcExternalId = $('.categories-wrapper #clipboard_category_external_id').val();
            var srcCategoryId = $('.categories-wrapper #clipboard_category_id').val();
            var srcCategoryName = $('.categories-wrapper #clipboard_category_name').val();
            var parentId = $('.categories-wrapper #clipboard_category_parent_id').val();
            srcCategoryName = srcCategoryName.trim();
            var op = $('.categories-wrapper #clipboard_op').val();

            if (typeof(op) !== 'undefined' && 'cut' == op && typeof(srcCategoryId) !== 'undefined') {
                $('.ot_cat_actions .paste_button .icon-paste:first',item).hide();
                $('.ot_cat_actions .paste_button .ot-preloader-micro:first',item).show();
                var srcLi = $('#jstree li[id="' + srcCategoryId + '"]');
                $('#jstree').jstree('move_node',srcLi, li);
                $('.ot_cat_actions .paste_button .icon-paste:first',item).show();
                $('.ot_cat_actions .paste_button .ot-preloader-micro:first',item).hide();

            } else if (typeof(op) !== 'undefined' && 'copy' == op  && typeof(srcCategoryId) !== 'undefined') {
                // need copy
                $('.ot_cat_actions .paste_button .icon-paste:first',item).hide();
                $('.ot_cat_actions .paste_button .ot-preloader-micro:first',item).show();
                $.ajax({
                    async : true,
                    type: 'POST',
                    dataType: 'json',
                    url: "?cmd=Categories&do=copyPaste",
                    data : {
                        "copiedId" : srcCategoryId,
                        "targetId" : $(li).attr('id'),
                        "copiedName" : srcCategoryName,
                        "copiedExternalId" : srcExternalId,
                        "parentId": parentId
                    },
                    success : function (data) {
                        if (data.error) {
                            showError(data);
                        } else {
                            //ok
                            showMessage(trans.get('Notify_success'));
                            $('#jstree').jstree('close_node', li);
                            $(li).addClass('jstree-closed');
                            $(li).removeClass('jstree-leaf');
                            setTimeout(function(){
                                $('#jstree').jstree('open_node', li);
                            }, 1000);
                        }
                        $('.ot_cat_actions .paste_button .icon-paste:first', item).show();
                        $('.ot_cat_actions .paste_button .ot-preloader-micro:first', item).hide();
                    }
                });


/*                $("#jstree").jstree("create", li, 'last', srcCategoryName, function(node) {
                    $(node).attr('externalid', srcExternalId);
                }, true);

                if (undefined != srcCategoryId && '' != srcCategoryId) {
                    var copiedLi = $('#'+srcCategoryId);
                    if (copiedLi.length>0) {
                        $("#jstree").jstree("remove", copiedLi);
                    }
                }*/
            }
            $('.categories-wrapper #clipboard_category_internal_id').val('');
            $('.categories-wrapper #clipboard_category_id').val('');
            $('.categories-wrapper #clipboard_category_name').val('');
        });

        $('.ot_cat_actions .open_sets', item).click(function(e) {
        	var url = $(e.currentTarget).attr('sets-url');
        	window.open(url);
        });
    },

    sendUpdateCategoryRequest: function (params, obj, parent) {
        var dataToSavePredefinedParams = params.predefinedParams;


        var isChangeParent, needAdditionalUpdate;
        if (typeof params.parentId !== 'undefined' && params.oldParentId != params.parentId) {
            isChangeParent = true;
            /**
             * если новый родитель загружен на странице,
             * выполнить запрос на смену родителя отдельным запросом
             */
            if ($('#jstree li[id="' + params.parentId + '"]').length || params.parentId === 0) {
                var updateCategoryParams = $.extend(true, {}, params, {parentId:''});
                needAdditionalUpdate = true;
            } else {
                var updateCategoryParams = params;
                needAdditionalUpdate = false;
            }
        } else {
            var updateCategoryParams = params;
            isChangeParent = false;
        }

        $.post(
            "?cmd=Categories&do=updateCategory", updateCategoryParams,
            function (data) {
                if (data.error) {
                    showError(data);
                    return false;
                } else {
                    if ((typeof dataToSavePredefinedParams.provider === 'undefined') || (dataToSavePredefinedParams.provider == '')) {
                        providerType = ' ';
                    } else {
                        providerType = ' [' + dataToSavePredefinedParams.provider + '] ';
                    }

                    if (!isMetrologistActive || params.approxweight == '') {
                        titleApproxWeight = ' ';
                    } else {
                        titleApproxWeight = ' (' + params.approxweight + ' ' + trans.get('kg') + ')';
                    }
                    var title = $.trim(params.newName + titleApproxWeight + providerType);
                    $(obj).attr('alias', params.alias);
                    $(obj).attr('name', params.newName);

                    $(obj).attr("isparent", data.isParent);
                    if (data.isParent) {
                        $(obj).removeClass('jstree-leaf').addClass('jstree-closed');
                    }
                    $(obj).attr('approxweight', params.approxweight);
                    $(obj).attr('seo_pagetitle', params.meta_pagetitle);
                    $(obj).attr('seo_title', params.meta_title);
                    $(obj).attr('seo_keywords', params.meta_keywords);
                    $(obj).attr('seo_description', params.meta_description);
                    var externalId = (typeof dataToSavePredefinedParams.category !== 'undefined') ? dataToSavePredefinedParams.category.id : '';

                    $(obj).attr('externalid', externalId);
                    $(obj).attr('alias', params.alias);
                    $(obj).attr("predifenedparams", JSON.stringify(dataToSavePredefinedParams));
                    $("#jstree").jstree("rename_node", obj, title);
                    $(obj).attr('parentId', params.oldParentId);
                    if (params.iconimageurl) {
                        $(obj).attr('iconimageurl', params.iconimageurl);
                    } else {
                        $(obj).attr('iconimageurl', '');
                    }
                    if (params.categoryIconClass) {
                        $(obj).attr('categoryIconClass', params.categoryIconClass);
                    } else {
                        $(obj).attr('categoryIconClass', '');
                    }

                    if (isChangeParent) {
                        if (needAdditionalUpdate) {
                            if (params.parentId === 0) {
                                // новый родитель - корень
                                var parentLi = -1;
                            } else {
                                // новый родитель - категория params.parentId
                                var parentLi = $('#jstree li[id="' + params.parentId + '"]');
                            }
                            $('#jstree').jstree('move_node',obj, parentLi);
                        } else {
                            $(obj).remove();
                        }
                    }

                    showMessage(trans.get('Notify_success'));

                    $('.confirmDialog .close').trigger('click');
                }
            }, 'json'
        );
    },
    addJSTreeItemTaskBar: function(item) {
    	var id = $(item).attr('id');
        var html = '<span class="ot_cat_actions">'+
        '<button class="btn btn-tiny offset-right1 move_category_button" title="' + trans.get("Move_category") + '"><i class="icon-move"></i></button>'+
        '<span class="btn-group">'+
        '    <button class="btn btn-tiny rename_category_button" title="' + trans.get("Edit_category") + '"><i class="icon-pencil"></i></button>' +
        '    <button class="btn btn-tiny ot_show_add_cat_item_window add_category_button" title="' + trans.get("Add_category") + '"><i class="icon-plus"></i></button>' ;
        if ( $(item).attr('IsHiddenUI')=='true') {
            $('a:first',item).css('color','gray');
            html += '    <button class="btn btn-tiny show_category_button" title="' + trans.get("Show_category") + '"><i class="icon-eye-open"></i></button>' +
            '    <button class="btn btn-tiny hide_category_button hide" title="' + trans.get("Hide_category") + '"><i class="icon-eye-close"></i></button>';
        }
        else {
            html += '    <button class="btn btn-tiny show_category_button hide" title="' + trans.get("Show_category") + '"><i class="icon-eye-open"></i></button>' +
            '    <button class="btn btn-tiny hide_category_button" title="' + trans.get("Hide_category") + '"><i class="icon-eye-close"></i></button>';
        }

        html += '    <button class="btn btn-tiny procces_category_button hide" title="' + trans.get("Show_category") + '"><i class="ot-preloader-micro"></i></button>' +
        '    <button class="btn btn-tiny open_category_button" title="' + trans.get("Open_category") + '"><i class="icon-search"></i></button>';

        // если включена фича RatingListForCategory и категория виртуальная
        if (isRatingListForCategoryActive && $(item).attr('isvirtual') === 'true') {
            if (this.issetRatingListForCategory(item)) {
                html += '    <button sets-url="/admin/?cmd=sets&do=items&type=Category&cid=' + id + '" class="btn btn-tiny open_sets" title="' + trans.get("Sets_products") + '"><i class="icon-tags"></i></button>';
            } else {
                html += '    <button sets-url="/admin/?cmd=sets&do=items&type=Category&cid=' + id + '" class="btn btn-tiny open_sets" title="' + trans.get("Sets_products") + '"><i class="icon-tag"></i></button>';
            }
        }

        html += '    <button class="btn btn-tiny ot_show_deletion_dialog_modal delete_category_button" title="'+trans.get("Delete_category")+'"><i class="icon-remove"></i></button>' +
        '</span>' +
        '<span class="offset-left1"><span class="btn-group">' +
        '    <button class="btn btn-tiny export_button" title="' + trans.get("Export_category") + '"><i class="icon-download-alt"></i></button>' +
        '    <button class="btn btn-tiny import_button dropdown-toggle" data-dropdown="#ot_import_cat" data-toggle="dropdown" title="' + trans.get("Import_category") + '"><i class="icon-upload-alt"></i></button>' +
        '    <button class="btn btn-tiny move_up_button" title="' + trans.get("Move_up_category") + '"><i class="icon-level-up"></i></button>' +
        '    <button class="btn btn-tiny move_down_button" title="' + trans.get("Move_down_category") + '"><i class="icon-level-down"></i></button>' +
        '    <button class="btn btn-tiny copy_button" title="' + trans.get("Copy") + '"><i class="icon-copy"></i></button>' +
        '    <button class="btn btn-tiny cut_button" title="' + trans.get("Cut") + '"><i class="icon-cut"></i></button>' +
        '     <button class="btn btn-tiny paste_button" title="' + trans.get("Paste") + '"><i class="icon-paste"></i><i class="ot-preloader-micro" style="display: none;"></i></button>' +
        '</span></span>'+

        '</span>';

        if ($('span.ot_cat_actions', item).length == 0) {
            if (id != 0) {
                $(item).append(html);

                this.assignItemHandlers(item);

                $('a',item).hover(function() {
                    $('.ot_cat_actions').hide();
                    $(this).parent('li').mouseenter();
                    return false;
                });

                $(item).hover(function() {
                    $('.ot_cat_actions').hide();
                    $('.ot_cat_actions:first', this).show();
                    return false;
                },function() {
                    $('.ot_cat_actions').hide();
                    return false;
                });
            }

        }
    },

});

$(function() {
    var U = new CategoriesPage();

});
//Функции по привязке - в бэкбон не засунуть START
//================================================
//================================================
//================================================
//================================================

    function asignProvidersActions(body) {
        $('#isProvider', body).hide();

        if (typeof dataToSavePredefinedParams.preDefineMode === 'undefined') {
            $('#preDefineMode :nth-child(1)', body).attr("selected", "selected");
            $('#provider', body).each(function() {
                $(this).prop("checked",false);
            });
        } else {
            $("#preDefineMode option[value='" + dataToSavePredefinedParams.preDefineMode + "']", body).attr("selected", "selected");
            if (dataToSavePredefinedParams.preDefineMode == 'category') {
                $('#provider', body).each(function() {
                    if (dataToSavePredefinedParams.provider == $(this).val()) {
                        $(this).prop("checked", true);
                    }
                });
                $('#isProvider', body).show();
            }
            if (dataToSavePredefinedParams.preDefineMode == 'search') {
                $('#provider', body).each(function() {
                    if (dataToSavePredefinedParams.provider == $(this).val()) {
                        $(this).prop("checked", true);
                    }
                });
                $('#isProvider', body).show();
            }
        }
        $('#preDefineMode', body).prop("disabled", false);
        $('#preDefineMode', body).change(function() {
            if (dataToSavePredefinedParams.preDefineMode != $(this).val()) {
                dataToSavePredefinedParams = {};
                showPredefinedParams(body);
            }
            $('#provider', body).each(function() {
                $(this).prop("checked", false);
            });
            if (($(this).val() == 'category') || ($(this).val() == 'search')) {
                $('#isProvider', body).show();
            } else {
                $('#isProvider', body).hide();
            }
            $.extend(dataToSavePredefinedParams, {"preDefineMode" : $(this).val()});
        });

        $('#provider', body).click(function() {
            showEditForms(body);
            if (typeof dataToSavePredefinedParams.provider === 'undefined') {
                $.extend(dataToSavePredefinedParams, {"provider" : $(this).val()});
            } else {
            	if (dataToSavePredefinedParams.provider != $(this).val()) {
            		dataToSavePredefinedParams.provider = $(this).val();
            		dataToSavePredefinedParams.category = {};
            		dataToSavePredefinedParams.category.id = 0;
            		dataToSavePredefinedParams.searchMethod = undefined;
            		dataToSavePredefinedParams.searchMethodName = undefined;
            		dataToSavePredefinedParams.availableSorts = undefined;
            		dataToSavePredefinedParams.availableSortsName = undefined;
            		showPredefinedParams(body);
            	} else {
            		dataToSavePredefinedParams.provider = $(this).val();
            	}
            }
        });

        // parent category typeahead
        var timeout;
        $('#parentCategory', body).typeahead({
            source: function (query, process)
            {
                if (timeout) {
                    clearTimeout(timeout);
                }
                timeout = setTimeout(function() {
                    $.get('?cmd=categories&do=getHint&name='+query, {}, function (response) {
                        var data = new Array();
                        $('#parentCategoryId', body).val(query);
                        $.each(response, function(i, item)
                        {
                            if(typeof(item.id) !== 'undefined' && typeof(item.label) !== 'undefined') {
                                data.push(item.id+'|'+item.label);
                            }

                        });
                        process(data);
                    }, 'json');
                }, 600);
            },
            //output in list
            highlighter: function(item)
            {
                var parts = item.split('|');
                return parts[1];
            },
            //select in list
            updater: function(item)
            {
                var parts = item.split('|');
                if (parts[0] != null) {
                    $('#parentCategoryId', body).val(parts[0]);
                    return parts[1];
                }
            },
        });

        $('.mceEditor', body).show();
        showPredefinedParams(body);

        $('.weight').livequery(function (element) {
            $(element).numeric({allow:".,"});
        });
        $('.price').livequery(function (element) {
            $(element).numeric({allow:"."});
        });
        $('.numeric').livequery(function (element) {
            $(element).numeric();
        });

        $('.modal-add-image', body).click(function() {
            $('.ot_crud_custom_picture_window .modal-body #dataId', body).attr('value', '');
            var content = $('.ot_crud_custom_picture_window .modal-body', body).html();
            var modal = modalDialog(trans.get('Logo'), content, function(bodySmall) {
                var newImageUrl = $('#dataId', bodySmall).val();
                console.log(newImageUrl);
                $('#newUrlPicture', body).val(newImageUrl);
                $('.image-preview', body).attr('src', newImageUrl);
                modal.toggle();
                return false;
              }, {confirm: trans.get('Add'), cancel: trans.get('Cancel') }, initPopoverInsideDialog);
        });
        $('.modal-check-category', body).click(function() {
            showCheckCategoryIcon();
        });
        var onSaveCategoryCallBack = function(data){
            if (data && 'undefined' !== typeof data.url) {
                $('#newUrlPicture', body).val(data.url);
                $('.image-preview', body).attr('src', data.url);
            }
        };

        $('.ot-uploader', body).otUploader({'afterSaveCallBack': onSaveCategoryCallBack});
    }

    function showEditForms(body) {
        $('#provider', body).each(function() {
            $('#' + $(this).val(), body).removeClass('ot-preloader-micro');
        });
        if ($('#preDefineMode', body).val() == 'category') {
            var prevBody = body;
            var searchProvider = $('#provider:checked', body).val();
            var categoryRoot = $('#provider:checked', body).attr('categoryRoot');
            var canSearchRootCategory = $('#provider:checked', body).attr('canSearchRootCategory');
            //В привязке к категории поиск в руте разрешен
            //независимо от флага canSearchRootCategory
            canSearchRootCategory = 'true';
            var categories = [];
            $('#' + searchProvider, body).addClass('ot-preloader-micro');
            if (typeof(xhr) !== 'undefined') {
                xhr.abort();
            }
            xhr = $.post(
                "?cmd=Categories&do=getCategoriesByProvider",
                {
                    "categoryRoot": categoryRoot,
                    "canSearchRootCategory": canSearchRootCategory
                },
                function (data) {
                    if (! data.error) {
                        categoriesByRoot = data.categories;
                        $('#' + searchProvider, body).removeClass('ot-preloader-micro');
                        modalDialog(trans.get('Categories'), '<div id="jstree-categoryRoot"></div>', function(body){
                            initPopoverInsideDialog(body);
                            if (tmpSelectedSearchCategory.length != 0) {
                                $.extend(dataToSavePredefinedParams, {"category" : tmpSelectedSearchCategory});
                                showPredefinedParams(prevBody)
                                return true;
                            } else {
                                showError(trans.get('Check_category'));
                                return false;
                            }
                        }, {confirm: trans.get('Save'), cancel: trans.get('Cancel')}, getCategoriesByProvider, 2);
                    } else {
                        showError(data);
                        $('#' + searchProvider, body).removeClass('ot-preloader-micro');
                        return false;
                    }
            }, 'json');
        }
        if ($('#preDefineMode', body).val() == 'search') {
            var prevBody = body;
            var searchProvider = $('#provider:checked', body).val();
            $('#' + searchProvider, body).addClass('ot-preloader-micro');
            if (typeof(xhr) !== 'undefined') {
                xhr.abort();
            }
            xhr = $.post(
                "?cmd=Categories&do=getSearchParamsForm",
                {
                    "searchProvider": searchProvider
                },
                function (data) {
                    if (! data.error) {
                        searchMethods = $.parseJSON(data.searchMethods);
                        $('#' + searchProvider, body).removeClass('ot-preloader-micro');
                        modalDialog(trans.get('Category_settings'), data.form, function(body) {
                            $.extend(dataToSavePredefinedParams, {"provider" : $('#provider:checked').val()});
                            $.extend(dataToSavePredefinedParams, {"searchUrl" : $('#serachUrl', body).val()});
                            $.extend(dataToSavePredefinedParams, {"searchWord" : $('#searchWord', body).val()});
                            if ($('#languageOfQuery', body).val() != "") {
                                $.extend(dataToSavePredefinedParams, {"languageOfQuery" : $('#languageOfQuery', body).val()});
                            }
                            $.extend(dataToSavePredefinedParams, {"searchMethod" : $('#searchMethod', body).val()});
                            $.extend(dataToSavePredefinedParams, {"searchMethodName" : $('#searchMethod :selected', body).text()});
                            $.extend(dataToSavePredefinedParams, {"vendor" : $('#vendor', body).val()});
                            $.extend(dataToSavePredefinedParams, {"minPrice" : $('#minPrice', body).val()});
                            $.extend(dataToSavePredefinedParams, {"maxPrice" : $('#maxPrice', body).val()});
                            $.extend(dataToSavePredefinedParams, {"brand" : $('#brand', body).val()});
                            $.extend(dataToSavePredefinedParams, {"stuffStatus" : $('#stuffStatus', body).val()});
                            $.extend(dataToSavePredefinedParams, {"featureDiscount" : $('#featureDiscount', body).val()});
                            $.extend(dataToSavePredefinedParams, {"featureAuction" : $('#featureAuction', body).val()});
                            $.extend(dataToSavePredefinedParams, {"availableSorts" : $('#availableSorts', body).val()});
                            $.extend(dataToSavePredefinedParams, {"availableSortsName" : $('#availableSorts :selected', body).text()});
                            if ($('input[id="categoryFiltersByChecks"]').length > 0) {
                                $.extend(dataToSavePredefinedParams, {"Configurators" : {}});
                                $.each($('input[id="categoryFiltersByChecks"]'), function() {
                                    if ($(this).prop("checked") == true) {
                                        tmpFilter = jQuery.parseJSON($(this).val());
                                        $.extend(dataToSavePredefinedParams.Configurators, jQuery.parseJSON($(this).val()));
                                    }
                                });
                            }
                            if (! checkPredefinedParams()) {
                                return false;
                            }
                            showPredefinedParams(prevBody);
                            return true;
                        }, {confirm: trans.get('Save'), cancel: trans.get('Cancel')}, asignSearchFormActions, 2);
                    } else {
                        showError(data);
                        $('#' + searchProvider, body).removeClass('ot-preloader-micro');
                        return false;
                    }
                }, 'json'
            );
        }
        $('.weight').livequery(function (element) {
            $(element).numeric({allow:".,"});
        });
        $('.price').livequery(function (element) {
            $(element).numeric({allow:"."});
        });
        $('.numeric').livequery(function (element) {
            $(element).numeric();
        });
    }


    function showSearchParamsByMethod(method, body) {
        var current = '';
        $('.isVendor', body).hide();
        $('.isVendorLocation', body).hide();
        $('.isPriceRange', body).hide();
        $('.isBrand', body).hide();
        $('.isStuffStatus', body).hide();
        $('.isConfigurators', body).hide();
        $('.isFeatureDiscount', body).hide();
        $('.isFeatureAuction', body).hide();
        $('.isAvailableSorts', body).hide();
        if (method != '') {
            $.each(searchMethods, function(i, item) {
                if (item.method == method) {
                    current = item;
                }
            });
            if (current == '') {
                showError(trans.get('Chosen_method_not_exist_imposible'));
                return;
            }
            $.each(current, function(i, item) {
                if (item == true) {
                    $('.is' + i, body).show();
                }
            });
            if (current.AvailableSorts != false) {
                $('#availableSorts', body).html('');
                $('.isAvailableSorts', body).show();
                $('#availableSorts', body)
                        .append($("<option></option>")
                        .attr("value","")
                        .text(trans.get('Not_seted')));
                $.each(current.AvailableSorts, function(i, item) {
                    $('#availableSorts', body)
                        .append($("<option></option>")
                        .attr("value",i)
                        .text(item));
                });
            }
        } else {
            //TODO
            //Показываем общие - надо упростить - обобщить...
            //Вероятно что не выйдет
            var can;
            can = true;
            $.each(searchMethods, function(i, item) {
                if (item.Vendor != true) {
                    can = false;
                }
            });
            if (can == true) {
                $('.isVendor', body).show();
            }

            can = true;
            $.each(searchMethods, function(i, item) {
                if (item.VendorLocation != true) {
                    can = false;
                }
            });
            if (can == true) {
                $('.isVendorLocation', body).show();
            }

            can = true;
            $.each(searchMethods, function(i, item) {
                if (item.PriceRange != true) {
                    can = false;
                }
            });
            if (can == true) {
                $('.isPriceRange', body).show();
            }

            can = true;
            $.each(searchMethods, function(i, item) {
                if (item.Brand != true) {
                    can = false;
                }
            });
            if (can == true) {
                $('.isBrand', body).show();
            }

            can = true;
            $.each(searchMethods, function(i, item) {
                if (item.StuffStatus != true) {
                    can = false;
                }
            });
            if (can == true) {
                $('.isStuffStatus', body).show();
            }

            can = true;
            $.each(searchMethods, function(i, item) {
                if (item.Configurators != true) {
                    can = false;
                }
            });
            if (can == true) {
                $('.isConfigurators', body).show();
            }

            can = true;
            $.each(searchMethods, function(i, item) {
                if (item.FeatureDiscount != true) {
                    can = false;
                }
            });
            if (can == true) {
                $('.isFeatureDiscount', body).show();
            }

            can = true;
            $.each(searchMethods, function(i, item) {
                if (item.FeatureAuction != true) {
                    can = false;
                }
            });
            if (can == true) {
                $('.isFeatureAuction', body).show();
            }
        }
    }

    function asignSearchFormActions(body) {
        initPopoverInsideDialog(body);
        if (typeof dataToSavePredefinedParams.searchWord !== 'undefined') {
            $('#searchWord', body).val(dataToSavePredefinedParams.searchWord);
        }
        if (typeof dataToSavePredefinedParams.languageOfQuery !== 'undefined') {
            $("#languageOfQuery option[value='" + dataToSavePredefinedParams.languageOfQuery + "']", body).attr("selected", "selected");
        }
        if (typeof dataToSavePredefinedParams.category !== 'undefined') {
            $('.chooseSearchCategory', body).html(trans.get('Change'));
            $('#selectedSearchCategory', body).html(dataToSavePredefinedParams.category.name);
        }
        if (typeof dataToSavePredefinedParams.searchMethod !== 'undefined') {
            $("#searchMethod option[value='" + dataToSavePredefinedParams.searchMethod + "']", body).attr("selected", "selected");
        }
        if (typeof dataToSavePredefinedParams.vendor !== 'undefined') {
            $('#vendor', body).val(dataToSavePredefinedParams.vendor);
        }
        if (typeof dataToSavePredefinedParams.minPrice !== 'undefined') {
            $('#minPrice', body).val(dataToSavePredefinedParams.minPrice);
        }
        if (typeof dataToSavePredefinedParams.maxPrice !== 'undefined') {
            $('#maxPrice', body).val(dataToSavePredefinedParams.maxPrice);
        }
        if (typeof dataToSavePredefinedParams.brand !== 'undefined') {
            $('#brand', body).val(dataToSavePredefinedParams.brand);
        }
        if (typeof dataToSavePredefinedParams.stuffStatus !== 'undefined') {
            $('#stuffStatus', body).val(dataToSavePredefinedParams.stuffStatus);
        }
        if (typeof dataToSavePredefinedParams.region !== 'undefined') {
            $('#selectedVendorLocation', body).html(dataToSavePredefinedParams.region.RegionId + ' ' + dataToSavePredefinedParams.region.name);
        }
        if (typeof dataToSavePredefinedParams.featureDiscount !== 'undefined') {
            $("#featureDiscount option[value='" + dataToSavePredefinedParams.featureDiscount + "']", body).attr("selected", "selected");
        }
        if (typeof dataToSavePredefinedParams.featureAuction !== 'undefined') {
            $("#featureAuction option[value='" + dataToSavePredefinedParams.featureAuction + "']", body).attr("selected", "selected");
        }
        if (typeof dataToSavePredefinedParams.Configurators !== 'undefined') {
            getCategoryFilters(dataToSavePredefinedParams.category.id, dataToSavePredefinedParams.Configurators);
        } else {
            if (typeof dataToSavePredefinedParams.category !== 'undefined') {
                getCategoryFilters(dataToSavePredefinedParams.category.id, null);
            }
        }
        showSearchParamsByMethod($('#searchMethod', body).val(), body);
        if (typeof dataToSavePredefinedParams.availableSorts !== 'undefined') {
            $("#availableSorts option[value='" + dataToSavePredefinedParams.availableSorts + "']", body).attr("selected", "selected");
        }
        $('#searchMethod', body).change(function() {
            showSearchParamsByMethod($('#searchMethod', body).val(), body);
        });

        $('.chooseSearchCategory', body).click(function() {
            var categoryRoot = $('#provider:checked').attr('categoryRoot');
            var canSearchRootCategory = $('#provider:checked', body).attr('canSearchRootCategory');
            var categories = [];
            $('#searchCategoryPreloader').addClass('ot-preloader-micro');
            $.post(
                "?cmd=Categories&do=getCategoriesByProvider",
                {
                    "categoryRoot": categoryRoot,
                    "canSearchRootCategory" : canSearchRootCategory
                },
                function (data) {
                    if (! data.error) {
                        categoriesByRoot = data.categories;
                        $('#searchCategoryPreloader').removeClass('ot-preloader-micro');
                        modalDialog(trans.get('Categories'), '<div id="jstree-categoryRoot"></div>', function(body){
                            if (tmpSelectedSearchCategory.length != 0) {
                                $.extend(dataToSavePredefinedParams, {"category" : tmpSelectedSearchCategory});
                                $('#selectedSearchCategory').html(dataToSavePredefinedParams.category.name);
                                $('.chooseSearchCategory').html(trans.get('Change'));
                                getCategoryFilters(dataToSavePredefinedParams.category.id, null);
                                return true;
                            } else {
                                showError(trans.get('Check_category'));
                                return false;
                            }
                        }, {confirm: trans.get('Save'), cancel: trans.get('Cancel')}, getCategoriesByProvider, 3);
                    } else {
                        showError(data);
                        return false;
                    }
                }, 'json'
            );

        });

        $('.chooseVendorLocation', body).click(function() {
            var nModal = modalDialog(trans.get('choice_of_delivery_region'), '<div class="ot-preloader-mini"></div>', function(body){},
            {confirm: false, cancel: trans.get('Cancel')},
            function(body) {
                $(body).append('<ul id="regions"></ul>');
                $("#regions", body).treeview({
                    url: "?cmd=shipment&do=regions",
                    onLoad: function() {
                        $('.ot-preloader-mini', body).hide();
                        $('.region-select-link', body).click(function() {
                            $.extend(dataToSavePredefinedParams, {"region" : {"RegionId" : $(this).attr('regid'), "name" : $(this).attr('regname')}});
                            $('#selectedVendorLocation').html($(this).attr('regid')+' '+$(this).attr('regname'));
                            nModal.find('.close').trigger('click');
                            return true;
                        });
                    }
                });
            }, 3);
        });
        //Мой колапс - колапс бустрапа закрывает модальное окно при сворачивании
        $('.collapseSearchForm', body).click(function() {
            if (! $('.collapseSearchForm', body).hasClass('disabled')) {
                if ($('.collapsedSearchForm', body).hasClass('hidden-element')) {
                    $('.collapsedSearchForm', body).removeClass('hidden-element').addClass('visible-element');
                    console.log('switch - auto');
                } else {
                    $('.collapsedSearchForm', body).removeClass('visible-element').addClass('hidden-element');
                    console.log('switch - none');
                }
            }
        });
        $('#serachUrl', body).keyup(function() {
            if ($('#serachUrl', body).val() != '') {
                $('.collapseSearchForm', body).addClass('disabled');
                $('.collapsedSearchForm', body).removeClass('visible-element').addClass('hidden-element');
            } else {
                $('.collapseSearchForm', body).removeClass('disabled');
            }
        });

        $('.weight').livequery(function (element) {
            $(element).numeric({allow:".,"});
        });
        $('.price').livequery(function (element) {
            $(element).numeric({allow:"."});
        });
        $('.numeric').livequery(function (element) {
            $(element).numeric();
        });

    }

    function getCategoriesByProvider(body) {
        //TODO
        //Дерево вносит рутовую категорию не в начало
        //(если ее можно выбрать - флаг canSearchRootCategory)
        $("#jstree-categoryRoot", body)
        .jstree({
            "plugins" : ["themes","json_data","ui", "types"], //,'contextmenu',"dnd"
            "themes" : {
                            "theme" : "classic",
                            "dots" : true,
                            "icons" : true
                        },
            "json_data" : {
                "data" : getPreparedCategories(categoriesByRoot, true),
                'correct_state': true,
                'progressive_render': true,
                'progressive_unload': true,
                "ajax" : {
                    "url" : '?cmd=Categories&do=getCategoriesByProvider',
                    // the `data` function is executed in the instance's scope
                    // the parameter is the node being loaded
                    // (may be -1, 0, or undefined when loading the root nodes)
                    "data" : function (node) {
                        // the result is fed to the AJAX request `data` option
                        return {
                            "categoryRoot" : node.attr ? node.attr("id") : 0
                        };
                    },
                    "success" : function (data) {
                        if (data.categories) {
                            if (data.categories.length) {
                                return getPreparedCategories(data.categories, true);
                            } else if (lastLoadedNodeCategoryRoot) {
                                lastLoadedNodeCategoryRoot.removeClass('jstree-open').addClass('jstree-leaf');
                            }
                        } else {
                            showError(data);
                        }
                    }
                }
            }
        })
        .one("reopen.jstree", function (event, data) {
        })
        .one("reselect.jstree", function (event, data) {
        })
        .bind("select_node.jstree", function (event, data) {
            var id = data.rslt.obj.attr('id');
            var externalId = data.rslt.obj.attr('externalid');
            var name = data.rslt.obj.attr('name');
            if (! id) {
                id = $('#provider:checked').attr('categoryRoot');
            }
            tmpSelectedSearchCategory = {};
            $.extend(tmpSelectedSearchCategory, {"name" : name, "id" : externalId}); //id, "externalId": externalId
        })
        .bind("loaded.jstree", function (event, data) {
        	var rootNode = $('li[id="0"]', event.target);
        	$(event.target).jstree('open_node', rootNode);
        })
        .bind("load_node.jstree", function (event, data) {
            lastLoadedNodeCategoryRoot = data.rslt.obj;
        });

    }

    function checkPredefinedParams() {
        var can = false;
        var tmpName = '';

        if (dataToSavePredefinedParams.preDefineMode == '') {
            showError(trans.get('Set_category_mode'));
            return false;
        }
        if (dataToSavePredefinedParams.preDefineMode == 'virtual') {
            return true;
        }
        $.each(dataToSavePredefinedParams, function(i, item) {
            if ((i != 'preDefineMode') && (i != 'provider')) {
                if (typeof item == 'object') {
                    if (! $.isEmptyObject(item)) {
                        tmpName = 'object';
                    }
                } else {
                    if (item != '') {
                        tmpName = item;
                    }
                }
                if (tmpName != '') {
                    can = true;
                }
            }
        });
        if (! can) {
            showError(trans.get('Must_set_at_minimum_one_category_setting'));
        }
        return can;
    }

    function showPredefinedParams(body) {
        var tmpName;
        $(body).find('.preDefinedParams').html('');

        $.each(dataToSavePredefinedParams, function(i, item) {
            tmpName = '';
            if (i == 'category') {
                if (typeof item.name === 'undefined') {
                    tmpName = 'Root';
                } else {
                    tmpName = item.name;
                }
            } else if (i == 'searchMethod') {
            	if (dataToSavePredefinedParams.searchMethodName) {
            		tmpName = dataToSavePredefinedParams.searchMethodName;
            	} else {
            		tmpName = trans.get('No_selection');
            	}
            }  else if (i == 'searchMethodName') {

            } else if (i == 'availableSorts') {
                tmpName = dataToSavePredefinedParams.availableSortsName;
            }  else if (i == 'availableSortsName') {

            } else {
                if (typeof item == 'object') {
                    if (typeof item.name === 'undefined') {
                        $.each(item, function(s, conf) {
                            if ((typeof conf !== 'undefined') && (typeof conf.name !== 'undefined') && (conf.name !== '')) {
                                tmpName = tmpName + conf.name + ':' + conf.valueName + ', ';
                            } else {
                                tmpName = tmpName + conf.pid + ':' + conf.vid;
                            }
                        });
                    } else {
                        tmpName = item.name;
                    }
                } else {
                    tmpName = item;
                }
            }
            if (tmpName != '' && typeof tmpName !== 'undefined') {
                $(body).find('.preDefinedParams').append('<div class="controls">' + trans.get('search_category_param_' + i) + ' - ' + trans.get(tmpName) + '</div>');
            }
        });
        if (dataToSavePredefinedParams.preDefineMode != 'virtual') {
            if ($(body).find('.preDefinedParams').html() != '') {
                $(body).find('.preDefinedParams').append('<div class="controls"><span class="blink blink-iconed changePreDefinedParams">' + trans.get('Edit') + '</span></div>');
                $('.changePreDefinedParams', body).click(function() {
                    showEditForms(body);
                });
            }
        }
    }

    function getPreparedCategories(categories, showPreviewHref) {
        var preparedCategories = [];
        var providerType;
        var approxWeight;

        _.each(categories, function (item) {
            var category = item.attributes ? item.attributes : item;
            if ((typeof category.ProviderType === 'undefined') || (category.ProviderType == '')) {
                providerType = ' ';
            } else {
                providerType = ' [' + category.ProviderType + '] ';
            }
            if (typeof showPreviewHref === 'undefined') {
                categoryHref = '';
            } else {
                categoryHref = "<a href='//demo.commerce.com/?p=subcategory&cid=" + category.id + "' target='_blank'>"+trans.get('open')+"</a>";
            }
            if (!isMetrologistActive || category.ApproxWeight == '') {
                approxWeight = ' ';
            } else {
                approxWeight = ' (' + category.ApproxWeight + ' ' + trans.get('kg') + ')';
            }
            var prepared = {
                "data" : {
                    "title" : $.trim(category.Name + approxWeight + providerType),
                    "metadata" : '<div class="actions"></div>',
                    "icon" : categoryHref
                },
                "attr" : category
            };
            if (category.IsParent == 'true') {
                prepared.icon = 'folder';
                if (category.children && category.children.length > 0) {
                	prepared.children = getPreparedCategories(category.children, showPreviewHref);
                	prepared.state = {'opened': true};
                } else {
                	prepared.children = [];
                }
            } else {
                prepared.icon = 'folder';
            }
            if (category.DeleteStatusUI == 'true') {
                prepared.data.icon = 'folderr';
            }

            preparedCategories.push(prepared);
        });
        return preparedCategories;
    }

    function getCategoryFilters(categoryId, predefined) {
        $('.isConfigurators').find('.controls').html('<div class="ot-preloader-mini"></div>');
        $.post(
            "?cmd=Categories&do=getCategoryFiltersData",
            {
                "categoryId": categoryId,
                "configurators": dataToSavePredefinedParams.Configurators
            },
            function (data) {
                if (! data.error) {
                    $('.isConfigurators').find('.controls').html(data.filters);
                    //Устанаваливаем филтьры по predefined если он есть
                    if ((predefined != null) && (typeof predefined.name === 'undefined')) {
                        $.each(dataToSavePredefinedParams.Configurators, function(s, conf) {
                            pidVidKey = conf.pid + "::" + conf.vid;
                            $("#categoryFiltersByChecks[pid-vid='" + pidVidKey + "']").attr('checked', true);
                        });
                    }
                } else {
                    showError(data);
                    return false;
                }
            }, 'json'
        );
    }

    function showCheckCategoryIcon() {
        $.get(
            '../css/vendor/font-icomoon-categories.css', [],
            function (data) {
                var categoryIconClasses = parseCssIcons(data);
                var container = $('.category-icons-wrapper').html('');

                categoryIconClasses.forEach(function (categoryIconClass) {
                    var html = $('<div class="category-icon" onClick="checkCategoryIcon(this)"><span></span></div>');
                    html.find('span').addClass(categoryIconClass);
                    container.append(html);
                });

                var activeCategoryIconClass = $('.form-category-icon__input').val();
                if (activeCategoryIconClass.length) {
                    container.find('.' + activeCategoryIconClass).closest('.category-icon').addClass('active');
                }

                modalDialog(
                    trans.get('Check_category'),
                    $('.check-category-icon-form .modal-body').html(),
                    function () {
                        confirmCheckCategoryIcon();
                    },
                    {
                        confirm: trans.get('Check'),
                        cancel: trans.get('Cancel')
                    },
                    function () {
                        console.log('open');
                    }
                );
            }, 'text'
        );
    }

    function confirmCheckCategoryIcon() {
        var selectedIcon = $('.modal .category-icons-wrapper').find('.active span');
        if (selectedIcon.length) {
            var categoryIcon = selectedIcon.attr('class');
            var checkIconForm = $('.form-category-icon');
            checkIconForm.find('.form-category-icon__selected span').attr('class', '').addClass(categoryIcon);
            checkIconForm.find('input').val(categoryIcon);
        }
    }

    function checkCategoryIcon(icon) {
        var catIcon = $(icon);
        catIcon.closest('.category-icons-wrapper').children('.category-icon').removeClass('active');
        catIcon.addClass('active');
    }

    function parseCssIcons(cssStr) {
        var rawClasses = cssStr.matchAll(/^\.(.+):.+{$/mg);
        return Array.from(rawClasses, function (cssClass) {
            return cssClass[1];
        });
    }

//Функции по привязке END======================

function categories_filter_changed(scope, params)
{
    $('form#filter #' + scope.name + '_value').val(scope.value);
    $('form#filter').submit();
}

function removeTinyMCE(){
    if (tinyMCE.editors.length > 0) {
        var count = tinyMCE.editors.length;
        for ( var int = count-1; int >= 0; int--) {
            tinyMCE.remove(tinyMCE.editors[int]);
        }
    }
}

function initTinyMCE()
{
    var width = jQuery(window).width();
    var left = (width-690) / 2;
    if (tinyMCE.editors.length > 0) {
        return false;
    }
    initializeTinyMCE('#seoText1');
}