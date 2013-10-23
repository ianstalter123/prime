$(document).ready(function(){
	//  tabs 
	$('.tabs-container').on('click', '.tabs-nav li', function(){
	    var indx = $(this).index();
	    $('.tabs-nav li').removeClass('active');
	    $(this).addClass('active');
	    $('.tabs-box li').removeClass('active').eq(indx).addClass('active');
	});
	//  Menu
	//$('li.cat-1 a').text('').addClass('icon-home');
	//	Search
	$('.searchParams').change(function(){
        $(this).next('.searchButton').click();
    });

    //
    if($('.toaster-cart .cart-items-count').text() != 0){
		$('.toaster-cart-content').show();
	}
	//	product list
	$('.old-price').each(function(){
		if($(this).text().length != 0){
		    $(this).show().prev('.price').addClass('new-price');
		}
	});

	if($('#left .product-item').length > 0){
		$('#left .page-title').before('<div class="list-style">\
                    <span class="link_grid icon-grid"></span>\
                    <span class="link_list icon-list"></span>\
        </div>');

		if($('#left .product-item').is('.box')){
			$(".link_grid").addClass("activelink");
		} else {
			$(".link_list").addClass("activelink");
		}


	    $(".link_list").click(function(){
	        $(".link_grid").removeClass("activelink");
	        $(".link_list").addClass("activelink");
	        $("#left .product-list .product-item").removeClass("box").addClass("list");
	    });
	    $(".link_grid").click(function(){
	        $(".link_list").removeClass("activelink");
	        $(".link_grid").addClass("activelink");
	        $("#left .product-list .product-item").removeClass("list").addClass("box");
	    });
	}

	/*$('#left').find('.product-list').each(function(index, element) {
		$('h1.page-title').prepend(
			'<div class="list-style">'+
				'<span class="icon-list"></span>'+
				'<span class="icon-grid active"></span>'+
			'</div>'
		)
	});
	$('.product-list .list-style .icon-list, .product-list .list-style .icon-grid').on('click', function(){
		$(this).parent().find('span').removeClass('active');
		$(this).addClass('active');
		if($(this).hasClass('icon-list')){
			$(this).closest('.product-list').find('.product-item').removeClass('box').addClass('list');
		}else{
			$(this).closest('.product-list').find('.product-item').removeClass('list').addClass('box');
		}
	});*/

	// Slider for footer news
	$('ul#footer-news').cycle({ 
	    fx:      'scrollHorz',  
	    timeout:  6000, 
	    speed:    1000,
	});



});