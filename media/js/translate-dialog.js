var translateDialog = {
	
	lang_data: null,
	
	create: function(){
		
		this.setupForm();
		
		this.initEvents()
		
	},
	
	initEvents: function(){
		
		$("#dialog-translate-submit").on('click', function(){
												
			translateDialog.saveTranslate();
			
			return false;
			
		});
			
	},
	
	saveTranslate: function(){
			
		var $button = $("#dialog-translate-form .button");		
			
		$button.text('loading...');	
			
		tidioAutomationOptions.apiUpdateSettings(function(){
			
			$button.text('save changes');
			
		});
			
	},
	
	exportData: function(){
		
		var lang_data = {};
		
		//
		
		$("#dialog-translate-form .translate-input").each(function(){
			
			var this_id = this.getAttribute('data-id'),
				this_value = this.value;
				
			lang_data[this_id] = this_value;
			
		});
		
		return lang_data;
		
	},
	
	//
	
	setupForm: function(){
		
		var translate_form = '';
		
		for(i in this.lang_data){
			
			translate_form +=
			'<div class="e clearfix">' +
				'<label for="translate-dialog-' + i + '">' + this.lang_data[i] + '</label>' + 
				'<input id="translate-dialog-' + i + '" value="' + this.lang_data[i] + '" class="translate-input" data-id="' + i + '" />' +
			'</div>';
			
		}
		
		//
		
		
		$("#dialog-translate-form").prepend(translate_form);
		
	},
	
	//
	
	showDialog: function(){
				
		tidioDialog.show('#dialog-translate');
		
	},
	
	//
	
	importTranslate: function(lang_data){
		
		this.lang_data = lang_data;
		
	}
	
};

//

translateDialog.lang_data = {
    "automationOfflineHeader": "Automation / leave a message",
    "automationOfflineDefaultMessage": "Sorry, we aren't online at the moment. <strong>Leave a message</strong> and we'll get back to you.",
    "automationOfflineFormEmail": "Enter your email address...",
    "automationOfflineFormMessage": "Enter your message...",
    "automationOfflineFormSubmit": "Send",
    "automationOnlineFormMessage": "Type your message...",
    "automationOnlineSoundOn": "turn sound on",
    "automationOnlineSoundOff": "turn sound off",
    "automationOnlineSoundClearMessages": "clear message"
};