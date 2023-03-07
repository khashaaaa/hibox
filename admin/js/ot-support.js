var SupportPage = Backbone.View.extend({
    "el": ".ot_support",
    "events": {
        "click #collapse-messages .collapse" : "collapseMessage",         
        "change #ot-perpage-change"  : "changePerPage", 
        "click .ot-show-chat"  : "showChat",
        "click .ot-send-message"  : "sendMessage",
        "click .ot-close-ticket"  : "closeTicket"
    },
    render: function()
    {
        this.$('.ot_support_view_topic').portamento({
            wrapper: $('.ot_support'), gap: 50
        });        
        return this;
    },
    initialize: function(){        
        this.render();
        var self = this;    
        $(window).scroll(function(){            
            self.messagesResize();
        });
        $(window).resize(function(){              
            self.messagesResize();
        });
        
    },
    collapseMessage: function(ev) {
        ev.preventDefault();
        var target = this.$(ev.target).parent();        
        var messageIds = target.attr('orderId');            
        if (target.parent().hasClass('order_expanded')) {
            target.parent().removeClass('order_expanded');
            target.find('i:first').removeClass('icon-collapse-alt').addClass('icon-expand-alt');            
            this.$('.' + messageIds).each(function() {
                $(this).hide();                
            });
        } else {
            target.parent().addClass('order_expanded');
            target.find('i:first').removeClass('icon-expand-alt').addClass('icon-collapse-alt');
            this.$('.' + messageIds).each(function() {
                $(this).show();
            });
        }        
        
    },
    changePerPage: function(ev) {
        this.$('input[name=perpage]').val(this.$(ev.target).find('option:selected').val());
        this.$('#filters').submit();        
    },
    showChat: function(ev) {
        var self = this;
        var target = this.$(ev.target);
        var ticketId = target.attr('ticketId');
        var userId = target.attr('userId');
        this.$(".ot_support_view_topic .well").append('<i class="ot-preloader-medium preloader-centered"></i>');        
        $.post(
            '?cmd=support&do=getChat',
            {
                'ticketId': ticketId,
                'userId': userId
            },
            function (data) {                
                if (! data.error) {                    
                    content = renderTemplate('support/js_tpl/support-chat',{ticketData: data});
                    $(".ot_support_view_topic .well").html(content);
                    self.messagesResize();
                    if ($(target).hasClass('strong')) {
                        $(target).removeClass('strong');
                        $(".countNewTickets").html(parseInt($(".countNewTickets").html()) - 1);
                    }
                } else {
                    showError(data.message ? data.message : trans.get('Notify_error'));
                    $('.ot-preloader-medium').hide();
                }
            }, 'json'
        );
    },
    sendMessage: function(ev) {
        ev.preventDefault();
        var self = this;
        var message = this.$("#ot-support-message").val();
        var userMail = this.$("#userMail").val();
        var ticketId = this.$("#ticketId").val();
        var ticketMain = $("span[ticketId='" + ticketId.replace("Ticket-","") + "']");        
        var $button = this.$(ev.target).button('loading');
        
		if (message.length == 0) {
            showError(trans.get('Message_can_not_be_empty'));
            $button.button('reset');
            return;
        }		
        $.post(
            '?cmd=support&do=sendMessage',
            {
                'ticketId' : ticketId,
                'message'  : message,
                'userMail' : userMail
            },
            function (data) {                
                if (! data.error) {
                    if (data.message) {
                        message = data.message;
                    }
                    content = renderTemplate('support-chat-message',{date: self.getFullJSDate(data.time), message : message});
                    $(".ot_support_view_topic .well .chat-messages .message-box:first").prepend(content);                    
                    $("#ot-support-message").val('');
                    $button.button('reset');
                    if (ticketMain.prev().hasClass('icon-flag')) {
                        ticketMain.prev().remove();
                        var orderId = ticketMain.parent().parent().attr("ticketorderid");
                        if ($("tr[ticketorderid='" + orderId + "'] i.icon-flag").length == 0) {
                            ticketMain.parent().parent().parent().find("td[orderid='" + orderId + "']").find('.icon-flag').remove();
                            ticketMain.parent().parent().parent().find("td[orderid='" + orderId + "']").find('.comma').remove();
                        }
                    }
                    if (ticketMain.parent().prev().find('i').hasClass('icon-flag')) {
                        ticketMain.parent().prev().find('i').remove();
                        var orderId = ticketMain.parent().parent().parent().attr("ticketorderid");
                        if ($("tr[ticketorderid='" + orderId + "'] i.icon-flag").length == 0) {
                            ticketMain.parent().parent().parent().parent().find("td[orderid='" + orderId + "']").find('.icon-flag').remove();
                            ticketMain.parent().parent().parent().parent().find("td[orderid='" + orderId + "']").find('.comma').remove();
                        }
                    }
                    $(".countNotAnswerTickets").html(parseInt($(".countNotAnswerTickets").html()) - 1);
                } else {
                    showError(data.message ? data.message : trans.get('Notify_error'));
                    $button.button('reset');                    
                }
            }, 'json'
        );
    },
    closeTicket: function(ev) {
        ev.preventDefault();
        var ticketId = this.$("#ticketId").val();        
        $.post(
            '?cmd=support&do=closeTicket',
            {
                'ticketId' : ticketId
            },
            function (data) {                
                if (! data.error) {                    
                    $("#ot-status-ticket").html('<p>' + trans.get('Ticket_closed') + '</p>');
                } else {
                    showError(data.message ? data.message : trans.get('Notify_error'));                                    
                }
            }, 'json'
        );
        
		
    },
    getFullJSDate: function(time) {
    	var date = new Date();
        var dateString;
        if (time) {
        	date = new Date(time);
        }
        
        var dd = date.getDate();
        if (dd < 10) {
            dd = '0' + dd;
        }
        var mm = date.getMonth() + 1;  
        if (mm < 10) {
            mm = '0' + mm;
        }
        var hh = date.getHours();  
        if (hh < 10) {
            hh = '0' + hh;
        }
        var min = date.getMinutes();  
        if (min < 10) {
            min = '0' + min;
        }
        dateString = dd + '.' + mm + '.' + date.getFullYear() + ' ' + hh + ':' + min;
        return  dateString; 
    },
    messagesResize: function() {
        var windowHeight = $(window).height();
        var delHeight = 150;
        var messagesBlock = $(".chat-messages");
        var wrapperHeight = $(".ot_support").height();

        if (messagesBlock.height() + delHeight >= windowHeight) {
            messagesBlock.css('height', windowHeight - delHeight);
        }
        if (messagesBlock.height() + delHeight >= wrapperHeight) {
            messagesBlock.css('height', wrapperHeight - delHeight);
        }
    }
    
});

$(function(){
    var S = new SupportPage();
});
