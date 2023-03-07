
var insertli = false;
var level = 1;

	
	
function OpenHierarchy(){ 	
	var HactiveCategory =  activeCategory.replace(/\ /g, "_");
	var HactiveCategory =  HactiveCategory.replace(/\&/g, "-and-");
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
	Pclass = $('.'+HactiveCategory).attr('idparent');;
	while (Pclass!='none') {			
		$('.'+Pclass).show();			
		Pclass = $('.'+Pclass).attr('idparent');
	} 	
	$('.'+HactiveCategory).show();
}


function FillHierarchy(){ 
	
	insertli = false;
	$.each(hierarchy, function(mas,Mass){
			var ClassNameH =  Mass[0].replace(/\ /g, "_");
			    ClassNameH =  ClassNameH.replace(/\&/g, "-and-");
			var ClassNameP =  Mass[1].replace(/\ /g, "_");
			    ClassNameP =  ClassNameP.replace(/\&/g, "-and-");
			   
			var CatTitleUrl =  Mass[0].replace(/\&/g, "-and-");
			    CatTitleUrl =  CatTitleUrl.replace(/\ /g, "_");
        	var categoryTitle = Mass[0];
			var levelTxt = "";						
			if(Mass[1] != 'none') {
				
				if (($('.'+ClassNameP).length) && ($('.'+ClassNameH).length==0)) {				
  					// Если родитель есть, а самого элемента нет					
					levelTxt = $('.'+ClassNameP).attr('hierarchy');
					levelTxt = levelTxt + "-";
					$('.'+ClassNameP).after(
            		$('<li></li>')
                	.addClass('titlecat bbn')
					.addClass(ClassNameH)
					.attr({ 
							hierarchy:levelTxt ,
					 		idparent : ClassNameP,
							idcat:ClassNameH							
					 })										
                	.append(
                	$('<a></a>')
                     	.html(levelTxt + '>  ' + categoryTitle)
                     	.attr({
							href:'recommendations&cid='+CatTitleUrl,
                            categoryId: ClassNameH
                     	})
					
            		));
					//Делаем скрытым  &raquo; this					
			 		$('.'+ClassNameH).toggle();		 			
					insertli = true;	
														
				} 
				    		
			}
        	
			
    });	
	HactiveCategory =  activeCategory.replace(/\ /g, "_");
	HactiveCategory =  HactiveCategory.replace(/\&/g, "-and-");	
	$('[categoryId="'+HactiveCategory+'"]').css({'color': '#ed1c24'});
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
			var CatTitleUrl =  Mass[0].replace(/\&/g, "-and-");
			    CatTitleUrl =  CatTitleUrl.replace(/\ /g, "_");
        	var categoryTitle = Mass[0];
        	if(categoryTitle != '0') {            	
			if(Mass[1] == 'none') {
				//alert($.inArray(Mass[0], categories));				
        		$('#set-categories').append(
            		$('<li></li>')
                	.addClass('titlecat bbn')
					.addClass(ClassNameH)
					.attr({
						hierarchy: '' ,					    
						idcat : ClassNameH,
						idparent : 'none'						
					})																
                	.append(
                	$('<a></a>')
                     	.html(categoryTitle)
                     	.attr({
							href:'recommendations&cid='+CatTitleUrl,
                            categoryId: ClassNameH
                     	})					
					
            		));
				
			}
			}
    	});
		//Понеслась рекурсия
		
		FillHierarchy();
		
		
	}	
	
});