(function($){
		
	acf.fields.childbuilderbuilder = acf.field.extend({
		
		type: 'childbuilder',
		$el: null,
		$input: null,
		$tbody: null,
		$clone: null,
		
		actions: {
			'ready':	'initialize',
			'append':	'initialize',
			'show':		'show'
		},
		
		events: {
			'click .acf-childbuilder-add-row': 		'add',
			'click .acf-childbuilder-remove-row': 	'remove',
		},
		
		focus: function(){
			
			this.$el = this.$field.find('.acf-childbuilder:first');
			this.$input = this.$field.find('input:first');
			this.$tbody = this.$el.find('tbody:first');
			this.$clone = this.$tbody.children('tr.acf-clone');
			
			this.o = acf.get_data( this.$el );
			
		},
		
		initialize: function(){
			
			// CSS fix
			this.$tbody.on('mouseenter', 'tr.acf-row', function( e ){
				
				// vars
				var $tr = $(this),
					$td = $tr.children('.remove'),
					$a = $td.find('.acf-childbuilder-add-row'),
					margin = ( $td.height() / 2 ) + 9; // 9 = padding + border
				
				
				// css
				$a.css('margin-top', '-' + margin + 'px' );
				
			});
			
			
			// sortable
			if( this.o.max != 1 ) {
				
				// reference
				var self = this,
					$tbody = this.$tbody,
					$field = this.$field;
					
				
				$tbody.one('mouseenter', 'td.order', function( e ){
					
					$tbody.unbind('sortable').sortable({
					
						items					: '> tr',
						handle					: '> td.order',
						forceHelperSize			: true,
						forcePlaceholderSize	: true,
						scroll					: true,
						
						start: function(event, ui) {
							
							// focus
							self.doFocus($field);
							
							acf.do_action('sortstart', ui.item, ui.placeholder);
							
			   			},
			   			
			   			stop: function(event, ui) {
							
							// render
							self.render();
							
							acf.do_action('sortstop', ui.item, ui.placeholder);
							
			   			},
			   			
			   			update: function(event, ui) {
				   			
				   			// trigger change
							self.$input.trigger('change');
							
				   		}
			   			
					});
				
				});
				
			}

			
			// set column widths
			// no longer needed due to refresh action in acf.pro model
			//acf.pro.render_table( this.$el.children('table') );
			
			
			// disable clone inputs
			this.$clone.find('[name]').attr('disabled', 'disabled');
						
			
			// render
			this.render();
			
		},
		
		show: function(){
			
			this.$tbody.find('.acf-field:visible').each(function(){
				
				acf.do_action('show_field', $(this));
				
			});
			
		},
		
		count: function(){
			
			return this.$tbody.children().length - 1;
			
		},
		
		render: function(){
			
			// update order numbers
			this.$tbody.children().each(function(i){
				
				$(this).children('td.order').html( i+1 );
				
			});
			
			
			// empty?
			if( this.count() == 0 ) {
			
				this.$el.addClass('empty');
				
			} else {
			
				this.$el.removeClass('empty');
				
			}
			
			
			// row limit reached
			if( this.o.max > 0 && this.count() >= this.o.max ) {
				
				this.$el.addClass('disabled');
				this.$el.find('> .acf-hl .acf-button').addClass('disabled');
				
			} else {
				
				this.$el.removeClass('disabled');
				this.$el.find('> .acf-hl .acf-button').removeClass('disabled');
				
			}
			
		},
		
		add: function( e ){
			
			// find $before
			var $before	= this.$clone;
			
			if( e && e.$el.is('.acf-icon') ) {
			
				$before	= e.$el.closest('.acf-row');
				
			}
			
			
			// validate
			if( this.o.max > 0 && this.count() >= this.o.max ) {
			
				alert( acf._e('childbuilder','max').replace('{max}', this.o.max) );
				return false;
				
			}
			
		
			// create and add the new field
			var new_id = acf.get_uniqid(),
				html = this.$clone.outerHTML();
				
				
			// replace acfcloneindex
			var html = html.replace(/(="[\w-\[\]]+?)(acfcloneindex)/g, '$1' + new_id),
				$html = $( html );
			
			
			// remove clone class
			$html.removeClass('acf-clone');
			
			
			// enable inputs
			$html.find('[name]').removeAttr('disabled');
			
			
			// add row
			$before.before( $html );
			
			
			// trigger mouseenter on parent childbuilder to work out css margin on add-row button
			this.$field.parents('.acf-row').trigger('mouseenter');
			
			
			// update order
			this.render();
			
			
			// validation
			acf.validation.remove_error( this.$field );
			
			
			// setup fields
			acf.do_action('append', $html);
			
			
			// return
			return $html;
		},
		
		remove : function( e ){
			
			// reference
			var self = this,
				$field = this.$field;
			
			
			// vars
			var $tr = e.$el.closest('.acf-row'),
				$table = $tr.closest('table');
			
			
			// validate
			if( this.count() <= this.o.min ) {
			
				alert( acf._e('childbuilder','min').replace('{min}', this.o.min) );
				return false;
			}
			
			
			// trigger change to allow attachmetn save
			this.$input.trigger('change');
				
				
			// animate out tr
			acf.remove_tr( $tr, function(){
				
				// render
				self.doFocus($field).render();
				
				
				// trigger mouseenter on parent childbuilder to work out css margin on add-row button
				$field.closest('.acf-row').trigger('mouseenter');
				
				
				// trigger conditional logic render
				// when removing a row, there may not be a need for some appear-empty cells
				if( $table.hasClass('table-layout') ) {
					
					acf.conditional_logic.render( $table );
					
				}
				
				
			});
			
		}
		
	});	
	
})(jQuery);