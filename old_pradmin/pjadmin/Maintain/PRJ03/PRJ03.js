// JavaScript Document
	$(function() {
		$(".datepicker").datepicker({
			dateFormat: 'yymmdd'
		});
		
		$(".chosen-select").chosen({
			no_results_text: "Oops, nothing found!",
			search_contains:true
		}); 
	 });
	function ChkForm(form){
		if(empty(form.comm01_no.value,'客戶')){
		}else if(Notnum(form.inm01_subtotal.value,'金額',1)){
		}else{
			form.submit()
		}
	}
	function prjfromcom(Key){
		$.post(
			'PRJ03_PrjFromCom.php',
			{DataKey:Key},
			function(xml){
				$('#prjm01_no').val("")
				$('#prjm01_no').find('option').remove();
				if($('resu',xml).text() == '1'){					
					$('#prjm01_no').append($('<option>', { value : '' }).text(' --- '));
					
					$('Row',xml).each(function(){
						$('#prjm01_no')
							.append($('<option>', { value : $('no',this).text() ,t02:$('t02nm',this).text()})
							.text($('nm',this).text()));						
					})
					$(".chosen-select").trigger("chosen:updated");
				}
			}
		);
	}
	/* 自動帶入統編 */
	function quidfromcom(obj){
		$("#comm01_quid").val($(obj).find("option:selected").attr("quid"))
	}
	/* 帶入內容*/
	function content(){	
		var T1 = $("#comm01_no").find("option:selected");
		var T2 = $("#prjm01_no").find("option:selected");
		var content
		
		if (T1.val() != "" & T2.val() != ""){
			content = T1.text() + " - " + T2.text()
		}else if(T1.val() != ""){
			content = T1.text()
		}else if(T1.val() != ""){
			content = T2.text()			
		}
		$("#inm01_content").val(content)
	}
	/* 自動計算稅額*/
	function GetTotal(){
		var subTotal = $("#inm01_subtotal").val()
		var hasTax = $("#inm01_hastax").find("option:selected").val()
		var TaxPer = $("#inm01_taxper").val()

		switch(hasTax){
			case 'Y':
				tax = Math.round(subTotal * (TaxPer/100))
				$("#inm01_tax").val(tax)
				$("#hasTaxTotal").text(parseInt(tax) + parseInt(subTotal))
				break;
			case 'N':
				$("#inm01_tax").val(0)
				$("#hasTaxTotal").text(subTotal)				
				break;				
		}
	}
	/* 作廢*/
	function InInvalid(DataKey){
		if(confirm("確定要刪除?")){
			window.location='PRJ03_Invalid.php?DataKey=' + DataKey + '&PageCon=' + $("#PageCon").val() + '&Source=' + $("#Source").val()
		}
	}