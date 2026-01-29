// JavaScript Document
var BodyHH = $(window).height();
var BodyWW = $(window).width();
	
	$(function(){
		$('#frame').change(function(){
			//AdjustIframeHeightOnLoad();
		})
		
		if($('#iframe-content').attr('src') == ""){
			$('#iframe-content').attr('src','/Maintain/frame/Dashbord.php')
		}
	});
	/*功能 : 框架Menu*/
	function MenuSet(obj,Url){
		//alert(Url)
		//移除所有的active
		/*
		$('.active').each(function(index, element) {
			$(this).removeClass('active')
		});
		$(obj).attr('class','active')
		*/
		$('#iframe-content').prop('src',Url)
	}
	/*
	function AdjustIframeHeightOnLoad() { 
		$('#frame').css('width', (BodyWW - 170) + 'px');
		$('#frame').css('height', (BodyHH) + 'px');
	}
	*/
	
	function SearchPage(obj){
		switch ($(obj).val()){
			case 'M':
				window.location = 'main4.php';
				break;
			case 'T':
				window.location = 'main4_summary.php';
				break;
			case 'F':
				window.location = 'main4_fire.php';
				break;
			case 'L':
				window.location = 'main4_learn.php';
				break;
		}
	}