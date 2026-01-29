// JavaScript Document
	$(document).ready(function(e) {
        $('.folder-btn').click(function(){
			if($(this).parent('li').children('ul').css('display') == 'none'){
				$(this).children('i').attr('class','folder-on')
				$(this).parent('li').children('ul').css('display','block')
			}else{
				$(this).children('i').attr('class','folder-off')				
				$(this).parent('li').children('ul').css('display','none')				
			}
		})
    });
	function empty(val,str){
		if(val == ''){
			alert('請填寫' + str )
			return true
		}else{
			return false
		}
 	}
	function Notnum(val,str,t){
		var re = /^[0-9]+$/;
		switch(t){
			case '1':
				if (!re.test(val)){
					alert(str + ' 請輸入數字');
					return true;			
				}else{
					return false;
				}
			break;
			case '2':
				if (!re.test(val) & val != ''){
					alert(str + ' 請輸入數字');
					return true;			
				}else{
					return false;
				}			
			break;			
		}	
	}