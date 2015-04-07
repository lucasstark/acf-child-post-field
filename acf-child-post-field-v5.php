<?php

class ACF_Child_Post_Field_V5 extends acf_field {

	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new ACF_Child_Post_Field_V5();
		}
	}

	function __construct() {

		// vars
		$this->name = 'childbuilder';
		$this->label = __( "Child Post", 'acf_child_post_field' );
		$this->category = 'layout';
		$this->defaults = array(
		    'post_type' => 'post',
		    'fieldgroups' => array(),
		    'sub_fields' => array(),
		    'min' => 0,
		    'max' => 0,
		    'layout' => 'block',
		    'button_label' => __( "Add Child", 'acf_child_post_field' ),
		    'include_title_editor' => 1,
		    'include_content_editor' => 0,
		    'include_excerpt_editor' => 0,
		    'include_featured_image_editor' => 0,
		);
		$this->l10n = array(
		    'min' => __( "Minimum children reached ({min} rows)", 'acf_child_post_field' ),
		    'max' => __( "Maximum children reached ({max} rows)", 'acf_child_post_field' ),
		);


		// do not delete!
		parent::__construct();
	}

	/*
	 *  render_field_settings()
	 *
	 *  Create extra settings for your field. These are visible when editing a field
	 *
	 *  @type	action
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	$field (array) the $field being edited
	 *  @return	n/a
	 */

	function render_field_settings( $field ) {

		$post_types = get_post_types();
		$post_type_array = array();
		foreach ( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );
			$post_type_array[$post_type] = $post_type_object->labels->name;
		}

		acf_render_field_setting( $field, array(
		    'label' => __( 'Post Type', 'acf_child_post_field' ),
		    'instructions' => __( 'The childbuilder post type to manage', 'acf_child_post_field' ),
		    'class' => 'acf-childbuilder-field-post-type',
		    'type' => 'select',
		    'name' => 'post_type',
		    'choices' => $post_type_array
		) );


		$field_groups = acf_get_field_groups();
		$field_groups_array = array();

		foreach ( $field_groups as $field_group ) {
			$field_groups_array[$field_group['ID']] = $field_group['title'];
		}

		acf_render_field_setting( $field, array(
		    'label' => __( 'Include Title Field', 'acf_child_post_field' ),
		    'instructions' => '',
		    'class' => 'acf-childbuilder-field-include',
		    'type' => 'radio',
		    'layout' => 'horizontal',
		    'name' => 'include_title_editor',
		    'std' => 1,
		    'choices' => array(1 => 'Yes', 0 => 'No')
		) );

		acf_render_field_setting( $field, array(
		    'label' => __( 'Include Content WYSIWYG', 'acf_child_post_field' ),
		    'instructions' => '',
		    'class' => 'acf-childbuilder-field-include',
		    'type' => 'radio',
		    'layout' => 'horizontal',
		    'name' => 'include_content_editor',
		    'std' => 0,
		    'choices' => array(1 => 'Yes', 0 => 'No')
		) );

		acf_render_field_setting( $field, array(
		    'label' => __( 'Include Excerpt Field', 'acf_child_post_field' ),
		    'instructions' => '',
		    'class' => 'acf-childbuilder-field-include',
		    'type' => 'radio',
		    'layout' => 'horizontal',
		    'name' => 'include_excerpt_editor',
		    'std' => 0,
		    'choices' => array(1 => 'Yes', 0 => 'No')
		) );

		acf_render_field_setting( $field, array(
		    'label' => __( 'Include Featured Image Field', 'acf_child_post_field' ),
		    'instructions' => '',
		    'class' => 'acf-childbuilder-field-include',
		    'type' => 'radio',
		    'layout' => 'horizontal',
		    'name' => 'include_featured_image_editor',
		    'std' => 0,
		    'choices' => array(1 => 'Yes', 0 => 'No')
		) );

		acf_render_field_setting( $field, array(
		    'label' => __( 'Field Groups', 'acf_child_post_field' ),
		    'instructions' => __( 'Field Groups to Load', 'acf_child_post_field' ),
		    'class' => 'acf-childbuilder-field-groups',
		    'type' => 'checkbox',
		    'name' => 'fieldgroups',
		    'choices' => $field_groups_array
		) );

		// rows
		$field['min'] = empty( $field['min'] ) ? '' : $field['min'];
		$field['max'] = empty( $field['max'] ) ? '' : $field['max'];



		// min
		acf_render_field_setting( $field, array(
		    'label' => __( 'Minimum Rows', 'acf_child_post_field' ),
		    'instructions' => '',
		    'type' => 'number',
		    'name' => 'min',
		    'placeholder' => '0',
		) );


		// max
		acf_render_field_setting( $field, array(
		    'label' => __( 'Maximum Rows', 'acf_child_post_field' ),
		    'instructions' => '',
		    'type' => 'number',
		    'name' => 'max',
		    'placeholder' => '0',
		) );


		// layout
		acf_render_field_setting( $field, array(
		    'label' => __( 'Layout', 'acf_child_post_field' ),
		    'instructions' => '',
		    'class' => 'acf-childbuilder-field-layout',
		    'type' => 'radio',
		    'name' => 'layout',
		    'layout' => 'horizontal',
		    'choices' => array(
			'block' => __( 'Block', 'acf_child_post_field' ),
		    )
		) );


		// button_label
		acf_render_field_setting( $field, array(
		    'label' => __( 'Button Label', 'acf_child_post_field' ),
		    'instructions' => '',
		    'type' => 'text',
		    'name' => 'button_label',
		) );
	}

	/*
	 *  render_field()
	 *
	 *  Create the HTML interface for your field
	 *
	 *  @param	$field (array) the $field being rendered
	 *
	 *  @type	action
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	$field (array) the $field being edited
	 *  @return	n/a
	 */

	function render_field( $field ) {

		// ensure value is an array
		if ( empty( $field['value'] ) ) {

			$field['value'] = array();
		}


		// rows
		$field['min'] = empty( $field['min'] ) ? 0 : $field['min'];
		$field['max'] = empty( $field['max'] ) ? 0 : $field['max'];


		// populate the empty row data (used for acfcloneindex and min setting)
		$empty_row = array();

		foreach ( $field['sub_fields'] as $f ) {
			$empty_row[$f['key']] = isset( $f['default_value'] ) ? $f['default_value'] : false;
		}

		foreach ( $field['acf_child_field_fields'] as $f ) {
			$empty_row[$f['key']] = isset( $f['default_value'] ) ? $f['default_value'] : false;
		}


		// If there are less values than min, populate the extra values
		if ( $field['min'] ) {

			for ( $i = 0; $i < $field['min']; $i++ ) {

				// continue if already have a value
				if ( array_key_exists( $i, $field['value'] ) ) {

					continue;
				}


				// populate values
				$field['value'][$i] = $empty_row;
			}
		}


		// If there are more values than man, remove some values
		if ( $field['max'] ) {

			for ( $i = 0; $i < count( $field['value'] ); $i++ ) {

				if ( $i >= $field['max'] ) {

					unset( $field['value'][$i] );
				}
			}
		}


		// setup values for row clone
		$field['value']['acfcloneindex'] = $empty_row;


		// show columns
		$show_order = true;
		$show_add = true;
		$show_remove = true;


		if ( $field['max'] ) {

			if ( $field['max'] == 1 ) {

				$show_order = false;
			}

			if ( $field['max'] <= $field['min'] ) {

				$show_remove = false;
				$show_add = false;
			}
		}


		// field wrap
		$el = 'td';
		$before_fields = '';
		$after_fields = '';

		if ( $field['layout'] == 'row' ) {

			$el = 'tr';
			$before_fields = '<td class="acf-table-wrap"><table class="acf-table">';
			$after_fields = '</table></td>';
		} elseif ( $field['layout'] == 'block' ) {

			$el = 'div';

			$before_fields = '<td class="acf-fields">';
			$after_fields = '</td>';
		}


		// hidden input
		acf_hidden_input( array(
		    'type' => 'hidden',
		    'name' => $field['name'],
		) );
		?>
		<div <?php acf_esc_attr_e( array('class' => 'acf-childbuilder', 'data-min' => $field['min'], 'data-max' => $field['max']) ); ?>>
			<table <?php acf_esc_attr_e( array('class' => "acf-table acf-input-table {$field['layout']}-layout") ); ?>>

				<tbody>
					<?php foreach ( $field['value'] as $i => $row ): ?>
						<tr class="acf-row<?php echo ($i === 'acfcloneindex') ? ' acf-clone' : ''; ?>">

							<?php if ( $show_order ): ?>
								<td class="order" title="<?php _e( 'Drag to reorder', 'acf_child_post_field' ); ?>"><?php echo intval( $i ) + 1; ?></td>
							<?php endif; ?>

							<?php echo $before_fields; ?>

							<?php
							// prevent childbuilder field from creating multiple conditional logic items for each row
							$sub_field = $field['sub_fields'][0];
							$sub_field['conditional_logic'] = 0;

							$acf_child_field_post_id = '';
							// add value
							if ( isset( $row[$sub_field['key']] ) ) {
								// this is a normal value
								$acf_child_field_post_id = $row[$sub_field['key']];
							} elseif ( isset( $sub_field['default_value'] ) ) {
								// no value, but this sub field has a default value
								$acf_child_field_post_id = $sub_field['default_value'];
							}

							$sub_field['value'] = $acf_child_field_post_id;
							// update prefix to allow for nested values
							$sub_field['prefix'] = "{$field['name']}[{$i}]";

							// render input
							acf_render_field_wrap( $sub_field, $el );


							$post = get_post( $acf_child_field_post_id );

							if ( $field['include_title_editor'] ) {
								acf_render_field_wrap( acf_get_valid_field( array(
								    'name' => "{$field['name']}[{$i}][post_data][post_title]",
								    'label' => 'Title',
								    'type' => 'text',
								    'value' => $post->post_title,
								    'required' => true
									) ), $el );
							}

							if ( $field['include_content_editor'] ) {
								acf_render_field_wrap( acf_get_valid_field( array(
								    'name' => "{$field['name']}[{$i}][post_data][post_content]",
								    'label' => __( 'Post Content', 'acf_child_post_field' ),
								    'type' => 'wysiwyg',
								    'value' => $post->post_content,
								    'required' => false
									) ), $el );
							}

							if ( $field['include_excerpt_editor'] ) {
								acf_render_field_wrap( acf_get_valid_field( array(
								    'name' => "{$field['name']}[{$i}][post_data][post_excerpt]",
								    'label' => __( 'Excerpt', 'acf_child_post_field' ),
								    'type' => 'textarea',
								    'value' => $post->post_excerpt,
								    'required' => false
									) ), $el );
							}

							if ( $field['include_featured_image_editor'] ) {
								acf_render_field_wrap( acf_get_valid_field( array(
								    'name' => "{$field['name']}[{$i}][post_data][featured_image]",
								    'label' => __( 'Featured Image', 'acf_child_post_field' ),
								    'type' => 'image',
								    'value' => get_post_thumbnail_id( $post->ID ),
								    'required' => false
									) ), $el );
							}

							foreach ( $field['acf_child_field_fields'] as $child_field ):

								// prevent childbuilder field from creating multiple conditional logic items for each row
								if ( $i !== 'acfcloneindex' ) {
									$child_field['conditional_logic'] = 0;
								}


								// add value
								if ( isset( $row['acf_child_field_values'][$child_field['key']] ) ) {
									// this is a normal value
									$child_field['value'] = $row['acf_child_field_values'][$child_field['key']];
								} elseif ( isset( $child_field['default_value'] ) ) {

									// no value, but this sub field has a default value
									$child_field['value'] = $child_field['default_value'];
								}


								// update prefix to allow for nested values
								$child_field['prefix'] = "{$field['name']}[{$i}]";


								// render input
								acf_render_field_wrap( $child_field, $el );
								?>

							<?php endforeach; ?>

							<?php echo $after_fields; ?>

							<?php if ( $show_remove ): ?>
								<td class="remove">
									<a class="acf-icon small acf-childbuilder-add-row" href="#" data-before="1" title="<?php _e( 'Add row', 'acf_child_post_field' ); ?>"><i class="acf-sprite-add"></i></a>
									<a class="acf-icon small acf-childbuilder-remove-row" href="#" title="<?php _e( 'Remove row', 'acf_child_post_field' ); ?>"><i class="acf-sprite-remove"></i></a>
								</td>
							<?php endif; ?>

						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php if ( $show_add ): ?>

				<ul class="acf-hl acf-clearfix">
					<li class="acf-fr">
						<a href="#" class="acf-button blue acf-childbuilder-add-row"><?php echo $field['button_label']; ?></a>
					</li>
				</ul>

			<?php endif; ?>
		</div>
		<?php
	}

	function input_admin_enqueue_scripts() {

		$dir = plugin_dir_url( __FILE__ );

		// register & include JS
		wp_register_script( 'acf-input-childbuilder', "{$dir}assets/js/acf-childbuilder-field.js" );
		wp_enqueue_script( 'acf-input-childbuilder' );


		// register & include CSS
		wp_register_style( 'acf-input-childbuilder', "{$dir}assets/css/acf-childbuilder-field.css" );
		wp_enqueue_style( 'acf-input-childbuilder' );
	}

	/*
	 *  input_admin_head()
	 *
	 *  This action is called in the admin_head action on the edit screen where your field is created.
	 *  Use this action to add CSS and JavaScript to assist your render_field() action.
	 *
	 *  @type	action (admin_head)
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	n/a
	 *  @return	n/a
	 */
	/*

	  function input_admin_head() {



	  }

	 */


	/*
	 *  input_form_data()
	 *
	 *  This function is called once on the 'input' page between the head and footer
	 *  There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and 
	 *  'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
	 *  seen on comments / user edit forms on the front end. This function will always be called, and includes
	 *  $args that related to the current screen such as $args['post_id']
	 *
	 *  @type	function
	 *  @date	6/03/2014
	 *  @since	5.0.0
	 *
	 *  @param	$args (array)
	 *  @return	n/a
	 */

	/*

	  function input_form_data( $args ) {



	  }

	 */


	/*
	 *  input_admin_footer()
	 *
	 *  This action is called in the admin_footer action on the edit screen where your field is created.
	 *  Use this action to add CSS and JavaScript to assist your render_field() action.
	 *
	 *  @type	action (admin_footer)
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	n/a
	 *  @return	n/a
	 */
	/*

	  function input_admin_footer() {



	  }

	 */


	/*
	 *  field_group_admin_enqueue_scripts()
	 *
	 *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	 *  Use this action to add CSS + JavaScript to assist your render_field_options() action.
	 *
	 *  @type	action (admin_enqueue_scripts)
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	n/a
	 *  @return	n/a
	 */
	/*

	  function field_group_admin_enqueue_scripts() {

	  }

	 */

	/*
	 *  field_group_admin_head()
	 *
	 *  This action is called in the admin_head action on the edit screen where your field is edited.
	 *  Use this action to add CSS and JavaScript to assist your render_field_options() action.
	 *
	 *  @type	action (admin_head)
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	n/a
	 *  @return	n/a
	 */
	/*

	  function field_group_admin_head() {

	  }

	  /*
	 *  load_value()
	 *
	 *  This filter is applied to the $value after it is loaded from the db
	 *
	 *  @type	filter
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	$value (mixed) the value found in the database
	 *  @param	$post_id (mixed) the $post_id from which the value was loaded
	 *  @param	$field (array) the field array holding all the field options
	 *  @return	$value
	 */

	function load_value( $value, $post_id, $field ) {

		// bail early if no value
		if ( empty( $value ) || empty( $field['sub_fields'] ) ) {
			return $value;
		}


		// convert to int
		$value = intval( $value );


		// vars
		$rows = array();


		// check number of rows
		if ( $value > 0 ) {

			// loop through rows
			for ( $i = 0; $i < $value; $i++ ) {

				// create empty array
				$rows[$i] = array();


				// get sub field
				$sub_field = $field['sub_fields'][0];


				// update $sub_field name
				$sub_field['name'] = "{$field['name']}_{$i}_acf_child_field_post_id";


				// get value
				$acf_child_field_post_id = acf_get_value( $post_id, $sub_field );





				// add value
				$rows[$i][$sub_field['key']] = $acf_child_field_post_id;

				$rows[$i]['acf_child_field_values'] = array();
				foreach ( array_keys( $field['acf_child_field_fields'] ) as $j ) {
					// get sub field
					$child_field = $field['acf_child_field_fields'][$j];

					// update $sub_field name
					//$sub_field['name'] = "{$field['name']}_{$i}_{$sub_field['name']}";
					// get value
					$child_value = acf_get_value( $acf_child_field_post_id, $child_field );


					// add value
					$rows[$i]['acf_child_field_values'][$child_field['key']] = $child_value;
				}
			}
			// for
		}
		// if
		// return
		return $rows;
	}

	/*
	 *  update_value()
	 *
	 *  This filter is appied to the $value before it is updated in the db
	 *
	 *  @type	filter
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	$value - the value which will be saved in the database
	 *  @param	$field - the field array holding all the field options
	 *  @param	$post_id - the $post_id of which the value will be saved
	 *
	 *  @return	$value - the modified value
	 */

	function update_value( $value, $post_id, $field ) {

		// remove acfcloneindex
		if ( isset( $value['acfcloneindex'] ) ) {

			unset( $value['acfcloneindex'] );
		}


		// vars
		$total = 0;


		// update sub fields
		if ( !empty( $value ) ) {

			// $i
			$i = -1;


			// loop through rows
			foreach ( $value as $row ) {

				$sub_field = $field['sub_fields'][0];

				// $i
				$i++;


				// increase total
				$total++;

				$child_post_id = 0;
				if ( isset( $row['_acf_child_field_post_id'] ) && !empty( $row['_acf_child_field_post_id'] ) ) {
					$child_post_id = $row['_acf_child_field_post_id'];
				}

				$post_data = array(
				    'post_type' => $field['post_type'],
				    'post_status' => 'publish',
				    'post_author' => get_current_user_id(),
				    'post_title' => isset( $row['post_data']['post_title'] ) ? $row['post_data']['post_title'] : '',
				    'post_content' => isset( $row['post_data']['post_content'] ) ? $row['post_data']['post_content'] : '',
				    'post_excerpt' => isset( $row['post_data']['post_excerpt'] ) ? $row['post_data']['post_excerpt'] : '',
				);

				if ( empty( $child_post_id ) ) {
					$result = wp_insert_post( $post_data );
					if ( $result && !is_wp_error( $result ) ) {
						$child_post_id = $result;
					}
				} else {

					$the_child_post = get_post( $child_post_id );
					$post_data['ID'] = $child_post_id;

					//Reset the fields if we haven't configured them to be editable. 
					if ( !$field['include_title_editor'] ) {
						$post_data['post_title'] = $the_child_post->post_title;
					}

					if ( !$field['include_content_editor'] ) {
						$post_data['post_title'] = $the_child_post->post_content;
					}

					if ( !$field['include_excerpt_editor'] ) {
						$post_data['post_title'] = $the_child_post->post_excerpt;
					}

					wp_update_post( $post_data );

					if ( $field['include_featured_image_editor'] ) {
						$image_id = isset( $row['post_data']['featured_image'] ) ? $row['post_data']['featured_image'] : 0;
						update_post_meta($child_post_id, '_thumbnail_id', $image_id);	
					}
				}

				
				// modify name for save
				$sub_field['name'] = "{$field['name']}_{$i}_acf_child_field_post_id";
				// update field

				acf_update_value( $child_post_id, $post_id, $sub_field );

				// loop through sub fields
				if ( !empty( $field['acf_child_field_fields'] ) ) {

					foreach ( $field['acf_child_field_fields'] as $child_field ) {
						$child_value = false;

						// key (backend)
						if ( isset( $row[$child_field['key']] ) ) {
							$child_value = $row[$child_field['key']];
						} elseif ( isset( $row[$child_field['name']] ) ) {

							$child_value = $row[$child_field['name']];
						} else {
							// input is not set (hidden by conditioanl logic)
							continue;
						}

						// update field
						acf_update_value( $child_value, $child_post_id, $child_field );
					}

					// foreach
				}
				// if
			}
			// foreach
		}
		// if
		// get old value (db only)
		$old_total = intval( acf_get_value( $post_id, $field, true ) );

		if ( $old_total > $total ) {

			for ( $i = $total; $i < $old_total; $i++ ) {

				foreach ( $field['sub_fields'] as $sub_field ) {

					acf_delete_value( $post_id, "{$field['name']}_{$i}_{$sub_field['name']}" );
				}
				// foreach
			}
			// for
		}

		// if
		// update $value and return to allow for the normal save function to run
		$value = $total;

		// return
		return $value;
	}

	/*
	 *  format_value()
	 *
	 *  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	 *
	 *  @type	filter
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	$value (mixed) the value which was loaded from the database
	 *  @param	$post_id (mixed) the $post_id from which the value was loaded
	 *  @param	$field (array) the field array holding all the field options
	 *
	 *  @return	$value (mixed) the modified value
	 */

	/*

	  function format_value( $value, $post_id, $field ) {

	  // bail early if no value
	  if( empty($value) ) {

	  return $value;

	  }


	  // apply setting
	  if( $field['font_size'] > 12 ) {

	  // format the value
	  // $value = 'something';

	  }


	  // return
	  return $value;
	  }

	 */


	/*
	 *  validate_value()
	 *
	 *  This filter is used to perform validation on the value prior to saving.
	 *  All values are validated regardless of the field's required setting. This allows you to validate and return
	 *  messages to the user if the value is not correct
	 *
	 *  @type	filter
	 *  @date	11/02/2014
	 *  @since	5.0.0
	 *
	 *  @param	$valid (boolean) validation status based on the value and the field's required setting
	 *  @param	$value (mixed) the $_POST value
	 *  @param	$field (array) the field array holding all the field options
	 *  @param	$input (string) the corresponding input name for $_POST value
	 *  @return	$valid
	 */

	/*

	  function validate_value( $valid, $value, $field, $input ){

	  // Basic usage
	  if( $value < $field['custom_minimum_setting'] )
	  {
	  $valid = false;
	  }


	  // Advanced usage
	  if( $value < $field['custom_minimum_setting'] )
	  {
	  $valid = __('The value is too little!','acf-FIELD_NAME'),
	  }


	  // return
	  return $valid;

	  }

	 */


	/*
	 *  delete_value()
	 *
	 *  This action is fired after a value has been deleted from the db.
	 *  Please note that saving a blank value is treated as an update, not a delete
	 *
	 *  @type	action
	 *  @date	6/03/2014
	 *  @since	5.0.0
	 *
	 *  @param	$post_id (mixed) the $post_id from which the value was deleted
	 *  @param	$key (string) the $meta_key which the value was deleted
	 *  @return	n/a
	 */

	/*

	  function delete_value( $post_id, $key ) {



	  }

	 */


	/*
	 *  load_field()
	 *
	 *  This filter is applied to the $field after it is loaded from the database
	 *
	 *  @type	filter
	 *  @date	23/01/2013
	 *  @since	3.6.0	
	 *
	 *  @param	$field (array) the field array holding all the field options
	 *  @return	$field
	 */

	function load_field( $field ) {
		$field['acf_child_field_fields'] = array();

		if ( !empty( $field['fieldgroups'] ) ) {
			foreach ( $field['fieldgroups'] as $id ) {
				$field['acf_child_field_fields'] += acf_get_fields_by_id( $id );
			}
		}

		$field['sub_fields'] = array();

		$field['sub_fields'][] = acf_get_valid_field(
			array(
			    'ID' => 0,
			    'name' => '_acf_child_field_post_id',
			    'key' => '_acf_child_field_post_id',
			    'type' => 'text',
			    'label' => 'Child Post ID'
			)
		);

		return $field;
	}

	/*
	 *  update_field()
	 *
	 *  This filter is appied to the $field before it is saved to the database
	 *
	 *  @type	filter
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	$field - the field array holding all the field options
	 *  @param	$post_id - the field group ID (post_type = acf)
	 *
	 *  @return	$field - the modified field
	 */

	function update_field( $field ) {
		// remove sub fields
		unset( $field['sub_fields'] );
		unset( $field['acf_child_field_fields'] );

		// return		
		return $field;
	}

	/*
	 *  delete_field()
	 *
	 *  This action is fired after a field is deleted from the database
	 *
	 *  @type	action
	 *  @date	11/02/2014
	 *  @since	5.0.0
	 *
	 *  @param	$field (array) the field array holding all the field options
	 *  @return	n/a
	 */

	/*

	  function delete_field( $field ) {



	  }

	 */
}
