(function ($) {

	acf.fields.childbuilderbuilder = acf.field.extend({
		type: 'childbuilder',
		$el: null,
		$input: null,
		$field_list: null,
		$clone: null,
		actions: {
			'ready': 'initialize',
			'append': 'initialize',
			'show': 'show'
		},
		events: {
			'click .acf-childbuilder-add-row': 'add',
			'click .acf-childbuilder-remove-row': 'remove',
		},
		focus: function () {

			this.$el = this.$field.find('.acf-childbuilder:first');
			this.$input = this.$field.find('input:first');
			this.$field_list = this.$el.find('div.acf-field-list');
			this.$clone = this.$el.find('.acf-field-object.acf-clone').eq(0);
			this.o = acf.get_data(this.$el);

		},
		initialize: function () {
			// field events
			this.$field.on('click', '.edit-field', function (e) {
				e.preventDefault();
				self.edit_field( $(this).closest('div.acf-field-object') );
			});

			// CSS fix
			/*
			 this.$tbody.on('mouseenter', 'tr.acf-row', function( e ){
			 
			 // vars
			 var $tr = $(this),
			 $td = $tr.children('.remove'),
			 $a = $td.find('.acf-childbuilder-add-row'),
			 margin = ( $td.height() / 2 ) + 9; // 9 = padding + border
			 
			 
			 // css
			 $a.css('margin-top', '-' + margin + 'px' );
			 
			 });
			 */

			// sortable
			if (this.o.max != 1) {

				// reference
				var self = this, $field_list = this.$field_list, $field = this.$field;


				//this.$el.one('mouseenter', 'td.order', function( e ){

				$field_list.unbind('sortable').sortable({
					handle: '.acf-icon',
					forceHelperSize: true,
					forcePlaceholderSize: true,
					scroll: true,
					start: function (event, ui) {

						// focus
						self.doFocus($field);

						acf.do_action('sortstart', ui.item, ui.placeholder);

					},
					stop: function (event, ui) {

						// render
						self.render();

						acf.do_action('sortstop', ui.item, ui.placeholder);

					},
					update: function (event, ui) {

						// trigger change
						self.$input.trigger('change');

					}

				});

				//});

			}


			// set column widths
			// no longer needed due to refresh action in acf.pro model
			//acf.pro.render_table( this.$el.children('table') );


			// disable clone inputs
			this.$clone.find('[name]').attr('disabled', 'disabled');


			// render
			this.render();

		},
		show: function () {

			this.$el.find('.acf-field:visible').each(function () {

				acf.do_action('show_field', $(this));

			});

		},
		count: function () {

			return this.$field_list.length - 1;

		},
		render: function () {

			// loop over fields
			this.$field_list.children().each(function (i) {
				console.log(i + 1);
				// update meta
				//self.update_field_meta( $(this), 'menu_order', i );


				// update icon number
				$(this).children('.handle').find('.acf-icon').html(i + 1);
			});


			// empty?
			if (this.count() == 0) {

				this.$el.addClass('empty');

			} else {

				this.$el.removeClass('empty');

			}


			// row limit reached
			if (this.o.max > 0 && this.count() >= this.o.max) {

				this.$el.addClass('disabled');
				this.$el.find('> .acf-hl .acf-button').addClass('disabled');

			} else {

				this.$el.removeClass('disabled');
				this.$el.find('> .acf-hl .acf-button').removeClass('disabled');

			}

		},
		add: function (e) {
			// clone tr
			
			// validate
			if (this.o.max > 0 && this.count() >= this.o.max) {

				alert(acf._e('childbuilder', 'max').replace('{max}', this.o.max));
				return false;

			}
			
			
			// create and add the new field
			var new_id = acf.get_uniqid();
			var html = this.$clone.outerHTML();
			

			// replace acfcloneindex
			var html = html.replace(/(="[\w-\[\]]+?)(acfcloneindex)/g, '$1' + new_id);
			var $html = $(html);


			// remove clone class
			$html.removeClass('acf-clone');
			
			// enable inputs
			$html.find('[name]').removeAttr('disabled');
			
			// show
			$html.show();
			this.$clone.before($html);


			// trigger mouseenter on parent childbuilder to work out css margin on add-row button
			//this.$field.parents('.acf-row').trigger('mouseenter');


			// update order
			this.render();
			this.open_field( $html.first('div.acf-field-object') );

			// validation
			//acf.validation.remove_error(this.$field);


			// setup fields
			acf.do_action('append', $html);
			

			// return
			return $html;
		},
		remove: function (e) {

			// reference
			var self = this,
				$field = this.$field;


			// vars
			var $tr = e.$el.closest('.acf-row'),
				$table = $tr.closest('table');


			// validate
			if (this.count() <= this.o.min) {

				alert(acf._e('childbuilder', 'min').replace('{min}', this.o.min));
				return false;
			}


			// trigger change to allow attachmetn save
			this.$input.trigger('change');


			// animate out tr
			acf.remove_tr($tr, function () {

				// render
				self.doFocus($field).render();


				// trigger mouseenter on parent childbuilder to work out css margin on add-row button
				$field.closest('.acf-row').trigger('mouseenter');


				// trigger conditional logic render
				// when removing a row, there may not be a need for some appear-empty cells
				if ($table.hasClass('table-layout')) {

					acf.conditional_logic.render($table);

				}


			});

		},
		/*
		 *  edit_field
		 *
		 *  This function is triggered when clicking on a field. It will open / close a fields settings
		 *
		 *  @type	function
		 *  @date	8/04/2014
		 *  @since	5.0.0
		 *
		 *  @param	$el
		 *  @return	n/a
		 */

		edit_field: function ($el) {

			if ($el.hasClass('open')) {

				this.close_field($el);

			} else {

				this.open_field($el);

			}
	},
		/*
		 *  open_field
		 *
		 *  This function will open a fields settings
		 *
		 *  @type	function
		 *  @date	8/04/2014
		 *  @since	5.0.0
		 *
		 *  @param	$el
		 *  @return	n/a
		 */

		open_field: function ($el) {

			// bail early if already open
			if ($el.hasClass('open')) {

				return false;

			}


			// add class
			$el.addClass('open');


			// action for 3rd party customization
			//acf.do_action('open_field', $el);


			// animate toggle
			$el.children('.settings').animate({'height': 'toggle'}, 250);
	},
		/*
		 *  close_field
		 *
		 *  This function will open a fields settings
		 *
		 *  @type	function
		 *  @date	8/04/2014
		 *  @since	5.0.0
		 *
		 *  @param	$el
		 *  @return	n/a
		 */

		close_field: function ($el) {

			// bail early if already closed
			if (!$el.hasClass('open')) {

				return false;

			}


			// remove class
			$el.removeClass('open');


			// action for 3rd party customization
			//acf.do_action('close_field', $el);


			// animate toggle
			$el.children('.settings').animate({'height': 'toggle'}, 250);		},
	});

})(jQuery);