var ActivitiesList = Backbone.View.extend({
    "el": ".plugins-wrapper",
    "events": {
    	 "click .openActivityBtn": "openActivity",
    },

    openActivity: function(ev)
    {
    	var target = this.$(ev.target);
    	var tr = $(target).closest('tr');
    	var id = $(tr).attr('activity-id'); 
    	var type = $(tr).attr('activity-type');
    	var finished = $(tr).attr('activity-finished');

    	openActivity(id, type, null, finished);
    },
    render: function()
    {
        return this;
    },
    initialize: function() 
    {
    	var self = this;
        this.render();
        
        
        $(document).ready(function(){
        	var options = {};
        	if(currentAdminLang != 'en') {
                options.language = {
                    url: '/admin/js/vendor/DataTables/js/i18n/' + currentAdminLang + '.lang.json'
                }
                options.columns = [{"sType": "date"}, null, null, null, { "orderable": false }];
                options.order = [[ 0, "desc" ]];
            }

            $('#activitiesList').dataTable(options);
        });
    }
});

$(function(){
    var activitiesList = new ActivitiesList();
});
