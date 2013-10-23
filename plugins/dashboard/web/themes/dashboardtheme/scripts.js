/*Ready document*////////////////////////////////////////////////////////////////////
$(document).ready(function() {
	
	$(':header:empty, p:empty, ul.number li:empty, .tabs ul li a:empty, .tabs>div:empty').hide();
	$('div.gradient > h2, div.pattern > h2').append('<span class="minimize icomoon-minus-2 icon16"></span>');
	
	$( ".tabs" ).tabs();
	$('div.tabs.tabs-left > div.ui-widget-content').css('min-height', $('div.tabs.tabs-left > ul.ui-tabs-nav').height());
	
// Tooltips //
	$('body').on('mouseover', '.tooltip, .checkbox-wrap:has(.tooltip), .radio-wrap:has(.tooltip), .btn-filter:has(.tooltip)', function(){
		var $this = $(this);
		if(!$(this).hasClass('tooltip')){
			$this = $('.tooltip', this);
		}
		$this.after('<p class="tooltip-show"><span class="title-tooltip">'+	$this.data('title') +'</span></p>');
		var pos = $this.offset(), posTop = pos.top, posLeft = pos.left;
		var docScroll = $(document).scrollTop();
		var heightTooltip = $('.tooltip-show').outerHeight(true);
		var widthTooltip = $('.tooltip-show').outerWidth(true);
		var thisHeight = $this.is(':checkbox') ? 16 : $this.outerHeight();
		var thisWidth = $this.is(':checkbox') ? 16 : $this.outerWidth();
		
		//tooltip position
		if($this.hasClass('tooltip-left')){				
			$('.tooltip-show').addClass('tooltip-left').css({
				top:posTop - docScroll + (thisHeight/2-heightTooltip/2)+'px',
				left:posLeft-widthTooltip-10+'px'
			});				
		}else if($this.hasClass('tooltip-bottom')){
			$('.tooltip-show').addClass('tooltip-bottom').css({
				top:posTop-docScroll+thisHeight+10+'px',
				left:posLeft+(thisWidth/2-widthTooltip/2)+'px'
			});
		}else if($this.hasClass('tooltip-right')){
			$('.tooltip-show').addClass('tooltip-right').css({
				top:posTop-docScroll+(thisHeight/2-heightTooltip/2)+'px',
				left:posLeft+thisWidth+10+'px'
			});
		}else if($this.hasClass('tooltip-top')){
			$('.tooltip-show').addClass('tooltip-top').css({
				top:posTop-docScroll-heightTooltip-10+'px',
				left:posLeft+(thisWidth/2-widthTooltip/2)+'px'
			});
		}else{
			$this.mousemove(function(e){
				$('.tooltip-show').addClass('tooltip-default').css({
					top:e.clientY+15+'px',
					left:e.clientX+15+'px'
				});
			});
		}
		
		//tooltip color
		if($this.hasClass('tooltip-orange')){
			$('.tooltip-show').addClass('orange orange-bg');
		}else if($this.hasClass('tooltip-red')){
			$('.tooltip-show').addClass('red red-bg');
		}else if($this.hasClass('tooltip-blue')){
			$('.tooltip-show').addClass('blue blue-bg');
		}else if($this.hasClass('tooltip-green')){
			$('.tooltip-show').addClass('green green-bg');
		}else if($this.hasClass('tooltip-gray')){
			$('.tooltip-show').addClass('gray gray-bg');
		}else{				
			$('.tooltip-show').addClass('black black-bg');				
		}
	});	
	$('body').on('mouseout', '.tooltip, .checkbox-wrap:has(.tooltip), .radio-wrap:has(.tooltip), .btn-filter:has(.tooltip)', function(){
		$('.tooltip-show').remove();
	});//end tooltips

// Minimize //
	$('.minimize').click(function (){
		$(this).toggleClass('icomoon-minus-2 icomoon-plus-2').parent().nextAll().slideToggle();
	});
	$('.left-box-button').click(function (){
		$(this).toggleClass('active');
		$('#left').slideToggle();
	});//end minimize
	
// Accordion //
	$('.accordion>li:first-child').addClass('active').next('ul').slideToggle();
	$('.accordion>li').click(function (event){
		if(event.target.tagName != 'A'){
			if($(this).hasClass('active')){
				$(this).removeClass('active').next('ul').slideUp();
			}else{
				$('.seotoaster-dash .accordion>li.active').removeClass('active').next('ul').slideUp();
				$(this).toggleClass('active').next('ul').slideToggle();
			}
		}
	});//end accordion
	
// Animate count //
	var agencyCount = $('.agencyCount').text()*1;
	var clentCount = $('.clentCount').text()*1;
	var websiteCount = $('.websiteCount').text()*1;
	var iA = 0, iC = 0, iW = 0;
	$('.agencyCount, .clentCount, .websiteCount').each(function() {
		if($(this).length == 4){
			$(this).css({'font-size':'26px', 'margin-left':'-5px'})
		}
		$(this).text('0');
	});;
	countA(), countC(), countW();
	function countA(){
		if(iA < agencyCount){
			$('.agencyCount').text(iA+1); ++iA; setTimeout(countA, 70);
		}	
	}
	function countC(){
		if(iC < clentCount){
			$('.clentCount').text(iC+1); ++iC; setTimeout(countC, 70);
		}	
	}
	function countW(){
		if(iW < websiteCount){
			$('.websiteCount').text(iW+1); ++iW; setTimeout(countW, 70);
		}	
	}//end animate count
	
// Step block //
	if($('.step-box').length){
		var i = 0;
		var stepUl = $('.step-box>ul');
		var stepLi = $('.step-box>ul>li');
		var stepContent = $('.step-box .step-content > div');
		var stepContentWidth = $('.step-box .step-content > div').width();
		var stepButton = $('.step-box .step-button a');
		$(".step-box>ul>li:first-child, .step-box .step-content > div:first-child").addClass('active'); // add class "active" for first step
		$(stepContent).width(stepContentWidth).css('float','left'); // width for div in step content
		$('.step-box .step-content').width((stepContentWidth+20) * $(stepContent).length); // width step content
		$(stepButton).click(function(){ // action for step button
			if($(this).hasClass('next') && i < $(stepContent).length-1){ // action for step button next
				$('.step-box>ul>li.active').removeClass('active').addClass('done').next().addClass('active');
				$('.step-box .step-content').animate({'marginLeft':'-'+ (stepContentWidth+20)*(i+=1) +'px'},500);
				$('.step-box>ul>li.active').index()+1 == $(stepContent).length ? $('.step-box .step-button a.next').addClass('disable') && $('.step-box .step-button a.finish').removeClass('disable') : $('.step-box .step-button a.next, .step-box .step-button a.prev').removeClass('disable');
			}else if($(this).hasClass('prev') && i >= 1){ // action for step button prev
				$('.step-box>ul>li.active').removeClass('active').addClass('done').prev().addClass('active');
				$('.step-box .step-content').animate({'marginLeft':'-'+ (stepContentWidth+20)*(i-=1) +'px'},500);
				$('.step-box>ul>li.active').index() == 0 ? $('.step-box .step-button a.prev').addClass('disable') : $('.step-box .step-button a.next, .step-box .step-button a.prev').removeClass('disable');
			}else if($(this).hasClass('finish') && i == $(stepContent).length-1){ // action for step button finish
				$('.step-box>ul>li.active').addClass('done');
			}
			$('.step-box>ul>li.done span.step-number').text('').attr('class', 'step-number icomoon-checkmark icon24');
		});
		$('body').on('click', '.step-box ul li.done', function(){
			$('.step-box .step-content').animate({'marginLeft':'-'+ (stepContentWidth+20)*$(this).index() +'px'},500);
		});
	}//end step block
	
// Checkbox & Radio button //
	replaceStyleButtons();
	$('body').on('click', '.checkbox-wrap', checkedBox);
	$('body').on('click', '.radio-wrap', checkedRadio);
	$('body').on('click', '.btn-filter', function(){
		var nameRadio = $('[type="radio"]', this).attr('name');
		var groupRadio = $('.btn-filter input[name="'+nameRadio+'"]');
		$(groupRadio).each(function(){
			if($(this).is(':checked')){
				$(this).parent().addClass('disable');
			}else{
				$(this).parent().removeClass('disable');
			}
		}); 
	});
//end Checkbox & Radio button
	
// Replacement for the standard scroll //
	function changeScroll(){
	}
//end replacement for the standard scroll
	
});


/*Full load window*//////////////////////////////////////////////////////////////////
$(window).load(function(){
});


/*All function*//////////////////////////////////////////////////////////////////

// Table - header position fixed //
var tableIndex = 0;
function tableFixed(table, height){
	$(table+' thead').insertBefore(table).wrap('<table class="'+$(table).attr('class')+' thead-fixed" data-tableId="'+tableIndex+'"></table>');
	var tableHeader = $(table).prev('.thead-fixed');
	$(table).addClass('thead-fixed-body').attr('data-tableId', tableIndex).wrap('<div class="scroll"></div>').parent().css('height',height);
	resizeWidth(table);
	$(window).resize(resizeWidth(table));
	tableIndex +=1;
};
function resizeWidth(table) {
	$(table).each(function() {
		var tableHeader = $(this).parent().prev('.thead-fixed');
		var tableId = $(this).attr('data-tableId');
		console.log(tableId);
		$(this).css('width', '100%')
		$(tableHeader).width($(this).width()+1);	
		$(this).width($(this).width()+1);	
		for(i=0; i<$('.thead-fixed-body[data-tableId="'+tableId+'"] tr:first-child td').length; i+=1){
			$('.thead-fixed[data-tableId="'+tableId+'"] tr th:eq('+i+'), .thead-fixed-body[data-tableId="'+tableId+'"] tr td:eq('+i+')').width($('.thead-fixed-body[data-tableId="'+tableId+'"] tr:first-child td:eq('+i+')').width());
		};			
	});
};//end table - header position fixed //

// Checkbox & Radio button //
function replaceStyleButtons(){
	$('[type="checkbox"]').each(function(){
		if(!$(this).parent('.checkbox-wrap').length && !$(this).hasClass('not-wrap')){
			if($(this).is(':checked')){
				$(this).wrap('<label class="checkbox-wrap icomoon-checkbox-partial icon16"></label>');
			}else{
				$(this).wrap('<label class="checkbox-wrap icomoon-checkbox-unchecked icon16"></label>');
			}
		}
	});
	$('[type="radio"]').each(function(){	
		if(!$(this).parent('.radio-wrap').length && !$(this).hasClass('not-wrap')){
			if($(this).is(':checked')){
				$(this).wrap('<label class="radio-wrap icomoon-radio-checked icon16"></label>');
			}else{
				$(this).wrap('<label class="radio-wrap icomoon-radio-unchecked icon16"></label>');
			}	
		}	
	});
};
function checkedBox(){
	if(!$('[type="checkbox"]', this).attr('disabled')){
		$('[type="checkbox"]:not(:disabled)').each(function(){
			if($(this).is(':checked')){
				$(this).parent().removeClass('icomoon-checkbox-unchecked').addClass('icomoon-checkbox-partial');
			}else{
				$(this).parent().removeClass('icomoon-checkbox-partial').addClass('icomoon-checkbox-unchecked');
			}
		});
	}
};
function checkedRadio(){
	if(!$('[type="radio"]', this).attr('disabled')){
		var attrName = $('[type="radio"]', this).attr('name');
		$(':radio[name="'+attrName+'"]:not(:disabled)').each(function(){
			if($(this).is(':checked')){
				$(this).parent().removeClass('icomoon-radio-unchecked').addClass('icomoon-radio-checked');
			}else{
				$(this).parent().removeClass('icomoon-radio-checked').addClass('icomoon-radio-unchecked');
			}
		});
	}
};
// end Checkbox & Radio button
