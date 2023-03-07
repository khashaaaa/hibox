$(document).ready(function() {
        $("#createAddCat").bind("click", function() {
            $('#addcat_title').val('');
            $('#addcat_desc').val('');
            $("#popup").bPopup();
            return false
        });

        $("#editDigestCat").bind("click", function() {
            var category = [];
            var data = $('#category option:selected').val();
            if (data == '') {
                ShowError(trans.select_cat_error);
                return false
            } else {
                $.each(Digest_cats, function(i, val) {
                    if (val.id == data)
                       category = val;
                });
                $('#editcat_title').val(category.title);
                $('#editcat_desc').val(category.description);
                $('#editcategory_id').val(category.id);
                $("#popupedit").bPopup();
                return false
            }
        });

        $("#delDigestCat").bind("click", function() {
            var data = $('#category option:selected').val();
            var data_string = $('#category option:selected').attr('data-cat');
            if (data == '') {
                ShowError(trans.select_cat_error);
                return false
            } else {
                delCategory(data);
                return false
            }
        });

        $("#date").datepicker({
            dateFormat: 'dd.mm.yy'
        });

        editBlock();
    });

    function ShowError(message) {
        $('#select_cat_error').css('background', '#FF9191');
        $('#select_cat_error').html(message);
        $('#select_cat_error').show();
        $('#select_cat_error').fadeOut(10000);
    }

    function ShowMessage(message) {
        $('#select_cat_error').css('background', '#93FFAE');
        $('#select_cat_error').html(message);
        $('#select_cat_error').show();
        $('#select_cat_error').fadeOut(3000);
    }


    function loadCategoriesByLang() {
        var postLang = $('#postlang option:selected').val();
        var d = {'postLang' : postLang, 'cmd' : 'digest', 'do' : 'getCategoriesByLang'};
                $.ajax({url: '',
                    type:'GET',
                    data: d,
                    success: function(dt){
                        $("select#category").html('');
                        var list = $("select#category");
                        arr = $.parseJSON(dt);
                        Digest_cats = arr;
                        var option = new Option('Без названия', '');
                        list.append(option);
                        $.each(arr, function(index, itemData) {
                            if(itemData.title == title)
                                option = new Option(itemData.title, itemData.id, false, true);
                            else
                                option = new Option(itemData.title, itemData.id);
                            list.append(option);
                        });
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        ShowError(thrownError + '<br />' + xhr.responseText);
                    }
                });
    }


    function delCategory(id) {
        var postLang = $('#postlang option:selected').val();
        var d = {'id' : id, 'postLang' : postLang, 'cmd' : 'digest', 'do' : 'deleteCategory'};
                $.ajax({url: '',
                    type:'GET',
                    data: d,
                    success: function(dt){
                        $("select#category").html('');
                        var list = $("select#category");
                        arr = $.parseJSON(dt);
                        Digest_cats = arr;
                        var option = new Option('Без названия', '');
                        list.append(option);
                        $.each(arr, function(index, itemData){
                            if(itemData.title == title)
                                option = new Option(itemData.title, itemData.id, false, true);
                            else
                                option = new Option(itemData.title, itemData.id);
                            list.append(option);
                        });
                        ShowMessage(trans.saved);
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        ShowError(thrownError + '<br />' + xhr.responseText);

                    }
                });
    }



    function editCategory() {
        var title = $('#editcat_title').val();
                if (! title) return false;
        var desc = $('#editcat_desc').val();
        var lang = $('#editcat_lang option:selected').val();
        var id = $('#editcategory_id').val();
        var postLang = $('#postlang option:selected').val();
        var d = {'title':title, 'desc':desc,'lang':lang,'id':id, 'postLang' : postLang, 'cmd':'digest', 'do':'editCategory'};
                $.ajax({url: '',
                    type:'GET',
                    data: d,
                    success: function(dt){
                        $("#popupedit").bPopup().close();
                        $("select#category").html('');
                        var list = $("select#category");
                        arr = $.parseJSON(dt);
                        Digest_cats = arr;
                        var option = new Option('Без названия', '');
                        list.append(option);
                        $.each(arr, function(index, itemData){
                            if(itemData.title == title)
                                option = new Option(itemData.title, itemData.id, false, true);
                            else
                                option = new Option(itemData.title, itemData.id);
                            list.append(option);
                        });
                        ShowMessage(trans.saved);
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        ShowError(thrownError + '<br />' + xhr.responseText);

                    }
                });
    }

    function addCategory() {
        var title = $('#addcat_title').val();
                if (!title) return false;
        var desc = $('#addcat_desc').val();
        var lang = $('#addcat_lang option:selected').val();
        var postLang = $('#postlang option:selected').val();
        var d = {'title':title, 'desc':desc,'lang':lang, 'postLang': postLang, 'cmd':'digest', 'do':'addCategory'};
                $.ajax({url: '',
                    type:'GET',
                    data: d,
                    success: function(dt){
                        $("#popup").bPopup().close();
                        $("select#category").html('');
                        var list = $("select#category");
                        arr = $.parseJSON(dt);
                        Digest_cats = arr;
                        var option = new Option('Без названия', '');
                        list.append(option);
                        $.each(arr, function(index, itemData){
                            if(itemData.title == title)
                                option = new Option(itemData.title, itemData.id, false, true);
                            else
                                option = new Option(itemData.title, itemData.id);
                            list.append(option);
                        });
                        ShowMessage(trans.saved);
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        ShowError(thrownError + '<br />' + xhr.responseText);

                    }
                });
    }


    function showPrompt()    {
        var ids = prompt(enter_id,"");
        d = {'cmd':'digest','do':'getitems', 'ids':ids};
        if (ids != null)  {
            $("#load_ajax").css('display','block');
            $.ajax({
                url:'',
                type:'GET',
                data: d,
                success: function(data) {
                    //$('#edit_content').html($('#edit_content').html()+'\n <br>'+data);

                    tinyMCE.get('edit_content').setContent(tinyMCE.get('edit_content').getContent()+data);

                    $("#load_ajax").css('display','none');
                }
            });
        }

    }

    function CheckSubmit(data)    {
        if (data.title != null && data.title.value.length ==0 ){
            $('#title').css('border-color','red');
        return false;}
    }

    function editBlock()   {
        $('#edit_content').css('width', 700);
        $('#edit_content').css('height', 400);
        $('#edit_content').show();

        tinyMCE.init({
            mode : "exact",
            elements : "edit_content",
            theme : "advanced",
            plugins : "table,heading,advhr,advimage,advlink,paste,fullscreen,noneditable,contextmenu,inlinepopups,emotions,media,insertdatetime,nonbreaking",
            theme_advanced_buttons1_add_before : "newdocument,separator",
            theme_advanced_buttons1_add_before : "fontselect,fontsizeselect,h1,h2,h3,h4,h5,h6,separator",
            theme_advanced_buttons1_add : "fontselect,fontsizeselect",
            theme_advanced_buttons2_add : "separator,forecolor,backcolor,liststyle,separator,insertdate,inserttime",
            theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator",
            theme_advanced_buttons3_add_before : "tablecontrols,separator",
            theme_advanced_buttons3_add : "flash,advhr,emotions,media,nonbreaking,separator,fullscreen,mybutton",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            extended_valid_elements : "hr[class|width|size|noshade],ul[class=listUL],ol[class=listOL],script[language|type|src], object[width|height|classid|codebase|embed|param],param[name|value],embed[param|src|type|width|height|flashvars|wmode], iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder]",
            media_strict: false,
            file_browser_callback : "ajaxfilemanager",
            paste_use_dialog : false,
            theme_advanced_resizing : true,
            theme_advanced_resize_horizontal : true,
            apply_source_formatting : true,
            force_br_newlines : false,
            force_p_newlines : true,
            relative_urls : true,
            content_css : "css/style_editor.css", //+ new Date().getTime(),
            content_css : "css/style.css",
            init_instance_callback : "myCustomInitInstance",
            setup: function(ed){
                ed.addButton('mybutton',
                { title : 'My button',
                    'class' : 'MyCoolBtn',
                    onclick : function() {
                        ed.focus();
                        ed.selection.setContent('<h2>' + ed.selection.getContent() + '</h2>');
                    }
                }
            );
            }
        });
    }

        function checklogin()
        {
            $.ajax({
                url: "index.php?do=checklogin",
                }).done(function ( data ) {
                if (data == 'SessionExpired') location.href='index.php?expired';
            });
        }
        setInterval('checklogin();', 1000*30);





     $("#dialog-form").dialog({
        autoOpen:false,
        height:315,
        width:350,
        modal:true,
        buttons:{
            "Закрыть":function () {
                $(this).dialog("close");
            }
        },
        close:function () {
        }
    });


