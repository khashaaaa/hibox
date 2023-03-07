var Promo = new Backbone.Collection();
var PromoPage = Backbone.View.extend({
    "el": ".well",
    "events": {
        "click #updateSiteMap" : "updateSiteMapAction",
    },
    render: function()
    {        
        return this;
        
    },
    initialize: function(){
        this.render();               
    },

    updateSiteMapAction: function(ev)
    {
        var updateBtn = this.$('#updateSiteMap');
        var updatingBtn = this.$('#updatingSiteMap');
        updatingBtn.removeClass('disabled');
        updatingBtn.show();
        updateBtn.hide();
        jQuery.ajax($(updateBtn).attr('action'),{
            type    :   'POST',
            dataType:   'json',
            success :   function(data) {
            	updatingBtn.hide();
                updateBtn.show();
                if (typeof data.error !=='undefined' && data.error == '1') {
                    showError(trans.get('Sitemap_updating_error'));
                } else {
                	showMessage(trans.get('Sitemap_updated_successfully'));
                }
             },
             error: function(){
                 showError(trans.get('Sitemap_updating_error'));
                 updatingBtn.hide();
                 updateBtn.show();
             }
        });
        return false;
    }
});

$(function(){
    var promoPage = new PromoPage();
});
