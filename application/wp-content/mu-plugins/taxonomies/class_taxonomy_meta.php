<?php
/**
 * No direct access
 */
defined( 'ABSPATH' ) or die( 'Nothing here!' );

/**
 * CTax_MetaFields post type class
 *
 * Adds custom meta fields to taxonomies
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */
class CTax_MetaFields
{
	/**
	 * The constructor
	 *
	 * Initialize & hook into WP
	 *
	 * @access  public
	 * @since   1.0
	 * @return  void
 	 */
	public function __construct()
	{
		add_action( 'ctax_teamdepartment_add_form_fields', array($this,'add_new_meta_field'), 10, 2 );
		add_action( 'ctax_teamdepartment_edit_form_fields', array($this,'edit_meta_field'), 10, 2 );
		add_action( 'create_ctax_teamdepartment', array($this,'save_taxonomy_meta'), 10, 2 );
		add_action( 'edited_ctax_teamdepartment', array($this,'save_taxonomy_meta'), 10, 2 );
		add_action( 'delete_ctax_teamdepartment', array($this,'delete_taxonomy_meta'), 10, 3 );
		add_action( 'get_terms', array($this,'get_terms'), 10, 3 );
		
	}
	
	
	public function get_terms($terms, $taxonomies, $args)
	{
		#debug($terms);
		
		if( 'all' === $args['fields'] ) {
			if( is_array($terms) && count($terms) > 0 ) {
				foreach($terms as $term){
					$tt_id = $term->term_taxonomy_id;
					$term_meta = get_option( "tax_meta_$tt_id" );
					if( $term_meta ){
						foreach( $term_meta as $meta_key => $meta_value ) {
								$term->$meta_key = $meta_value;
						}						
					}					
				}
			}			
		}
		
		return $terms;
	}
	
	
	
	
	/**
	 * Saves meta field for taxonomy term
	 *
	 * @access  public
	 * @since   1.0
	 *
	 * @param int     $term         Term ID.
	 * @param int     $tt_id        Term taxonomy ID.
	 * @param mixed   $deleted_term Copy of the already-deleted term, in the form specified
	 *                              by the parent function. {@see WP_Error} otherwise.
	 */
	public function delete_taxonomy_meta( $term, $tt_id, $deleted_term )
	{
		delete_option("tax_meta_$tt_id");
	}
	
	
	/**
	 * Saves meta field for taxonomy term
	 *
	 * @access  public
	 * @since   1.0
	 *
	 * @param int $term_id Term ID.
	 * @param int $tt_id   Term taxonomy ID.
	 */
	public function save_taxonomy_meta( $term_id, $tt_id )
	{

		$meta_keys = array_keys( $_POST['term_meta'] );

		foreach ( $meta_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				if('menu_order' == $key){
					$term_meta[$key] = intval($_POST['term_meta'][$key]);
				} else {
					$term_meta[$key] = $_POST['term_meta'][$key];
				}

			}
		}

		update_option("tax_meta_$tt_id", $term_meta);

	}



	/**
	 * Adds meta field to the Add New term page.
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function add_new_meta_field()
	{
		?>
		<div class="form-field">
			<label for="term_meta[menu_order]"><?php _e( 'Menu Order', 'mcshane' ); ?></label>
			<input type="text" name="term_meta[menu_order]" id="term_meta[menu_order]" value="">
			<p class="description"><?php _e( 'This will determine the order the terms show up on an archive page.','mcshane' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Adds meta field to the Edit term page.
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function edit_meta_field($term)
	{
		
		// put the term ID into a variable
		$tt_id = $term->term_taxonomy_id;

		// retrieve the existing value(s) for this meta field. This returns an array
		$term_meta = get_option( "tax_meta_$tt_id" );

		?>

		<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[menu_order]"><?php _e( 'Menu Order', 'mcshane' ); ?></label></th>
			<td>
				<input type="text" name="term_meta[menu_order]" id="term_meta[menu_order]" value="<?php echo esc_attr( $term_meta['menu_order'] ) ? esc_attr( $term_meta['menu_order'] ) : ''; ?>">
				<p class="description"><?php _e( 'This will determine the order the terms show up on an archive page.','pippin' ); ?></p>
			</td>
		</tr>
	<?php
	}





}



new CTax_MetaFields();

