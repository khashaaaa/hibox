
var insertli = false;
var level = 1;
var HcategoryCount = 0;
	
	
function OpenHierarchy(){ 	
    //alert('Open her');
	var HactiveCategory =  activeCategory.replace(/\ /g, "_");
	    HactiveCategory =  HactiveCategory.replace(/\&/g, "-and-");
		
	$.each(hierarchy, function(mas,Mass){
		var ClassNameH =  Mass[0].replace(/\ /g, "_");
		    ClassNameH =  ClassNameH.replace(/\&/g, "-and-");
			
		var ClassNameP =  Mass[1].replace(/\ /g, "_");		
		    ClassNameP =  ClassNameP.replace(/\&/g, "-and-");
					
		if (ClassNameP==HactiveCategory) {
			$('.'+ClassNameH).toggle();
						
		}
    });	
	//Показываем всех родителей
	Pclass = $('.'+HactiveCategory).attr('idparent');
	//alert('IDparent - ' +Pclass);
	if (Pclass!=undefined) {
	 while (Pclass!='none') {			
	 	$('.'+Pclass).show();			
	 	Pclass = $('.'+Pclass).attr('idparent');
	 } 	
	}
	$('.'+HactiveCategory).show();
	$('[categoryId="'+activeCategory+'"]').css({'font-weight': 'bold'});
}


function FillHierarchy(){ 
	
	insertli = false;
	$.each(hierarchy, function(mas,Mass){
			var ClassNameH =  Mass[0].replace(/\ /g, "_");
			    ClassNameH =  ClassNameH.replace(/\&/g, "-and-");
				
			var ClassNameP =  Mass[1].replace(/\ /g, "_");
			    ClassNameP =  ClassNameP.replace(/\&/g, "-and-");		
					
        	var categoryTitle = Mass[0];
			var levelTxt = "";			
			HcategoryCount = 0;	
			
			if(Mass[1] != 'none') {
				
				//alert(ClassNameH+ ' - ' +ClassNameP);
				if (($('.'+ClassNameP).length) && ($('.'+ClassNameH).length==0)) {				
  					// Если родитель есть, а самого элемента нет
					$.each(categories, function(categoryName, categoryCount){
						if (categoryName==Mass[0]) 
							HcategoryCount = categoryCount;
					});
					
					levelTxt = $('.'+ClassNameP).attr('hierarchy');
					levelTxt = levelTxt + "-";
					$('.'+ClassNameP).after(
            		$('<li></li>')
                	.addClass('ui-state-default')
                	.addClass('mb5')
					.addClass(ClassNameH)
					.attr({ 
							hierarchy:levelTxt ,
					 		idparent : ClassNameP,
							idcat:ClassNameH,
							CatCount:HcategoryCount
					 })										
                	.append(
                	$('<div></div>')
                     	.html(levelTxt + '>  ' + categoryTitle + ' ('+ HcategoryCount +')')
                     	.attr({
                            categoryId: Mass[0],
                            'class':    'apply-category'
                     	})
					
            		));
					//Делаем скрытым  &raquo; this					
			 		$('.'+ClassNameH).toggle();		 			
					insertli = true;	
														
				} 
				    		
			}
        	
			
    });		
	
	if (insertli==true)  {	
		FillHierarchy(); //Вставили новый эллемент запускаем дальше, не вставили значит все элементы уже прошли
	} else {
		
		OpenHierarchy();
	}	
	
}




$(function(){		   
    $('#set-categories').empty();
	if (hierarchy.length>0) {		
		//Если есть иерархия - рекурсия
		//Вначале выводим родительские	
					   
		$.each(hierarchy, function(mas,Mass){
			
			var ClassNameH =  Mass[0].replace(/\ /g, "_"); 
			    ClassNameH =  ClassNameH.replace(/\&/g, "-and-");				
        	var categoryTitle = Mass[0];
        	if(categoryTitle == '0')
            	categoryTitle = trans.home;
			HcategoryCount = 0;
			if(Mass[1] == 'none') {
				//alert($.inArray(Mass[0], categories));
				$.each(categories, function(categoryName, categoryCount){					
					if (categoryName==Mass[0]) {
						HcategoryCount = categoryCount;
						
					}
						
				});					
        		$('#set-categories').append(
            		$('<li></li>')
                	.addClass('ui-state-default')
                	.addClass('mb5')
					.addClass(ClassNameH)
					.attr({
						hierarchy: '' ,					    
						idcat : ClassNameH,
						idparent : 'none',
						CatCount:HcategoryCount
					})																
                	.append(
                	$('<div></div>')
                     	.html(categoryTitle + ' ('+ HcategoryCount +')')
                     	.attr({
                            categoryId: Mass[0],
                            'class':    'apply-category'
                     	})					
					
            		));
				
			}
			
        	
    	});
		//Понеслась рекурсия
		
		FillHierarchy();
		
		
	} else {
		//Нету иерархия		
    	$.each(categories, function(categoryName, categoryCount){
        	var categoryTitle = categoryName;
        	if(categoryTitle == '0')
            	categoryTitle = trans.home;
        		$('#set-categories').append(
            		$('<li></li>')
                	.addClass('ui-state-default')
                	.addClass('mb5')
                	.append(
                    $('<div></div>')
                        .html(categoryTitle + ' ('+ categoryCount +')')
                        .attr({
                            categoryId: categoryName,
                            'class':    'apply-category'
                     })
            	));
        	$('[categoryId="'+activeCategory+'"]').css({'font-weight': 'bold'});
    	});
	}
	
	
});