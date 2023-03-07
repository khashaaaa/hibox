var WarehouseForm = new Backbone.Collection();
var WarehouseFormPage = Backbone.View.extend({
    "el": ".warehouse-form-wrapper",
    "events": {

    },
    render: function()
    {
        return this;
    },
    initialize: function(){
        this.render();
    }
});

$(function(){
    var UF = new WarehouseFormPage();
});
