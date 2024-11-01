<?php

/**
 * Enqueue admin scripts (css+js)
 * @since 0.1 
 * @since 0.5 Now it actually enqueue scripts only if in the admin page of the plugin 
 * @see @http://wordpress.stackexchange.com/questions/41207/how-do-i-enqueue-styles-scripts-on-certain-wp-admin-pages
 * @since 2016-10-13-21-16-10 UTC fixed issue #9 @see https://gitlab.com/paolofalomo/wp-flickity/issues/9
 */
function wp_flickity_enqueue_admin_scripts() {
	/**
	 * ENQUEUE WORDPRESS SCRIPTS
	 */
	wp_enqueue_script('media-upload');
	wp_enqueue_media();
	/**
	 * REGISTERING PLUGIN SCRIPTS
	 */
    wp_register_style( WP_FLICKITY_UNIQUE_IDENTIFIER.'admin_css', WP_FLICKITY_PLUGIN_URL . 'assets/wp-flickity-admin-ui.css', false, WP_FLICKITY_VERSION );
    wp_register_script( WP_FLICKITY_UNIQUE_IDENTIFIER.'admin_js', WP_FLICKITY_PLUGIN_URL . 'assets/wp-flickity-admin-ui.js', false, WP_FLICKITY_VERSION, true );
    /**
     * REGISTERING EXTERNAL SCRIPTS
     * Deprecated soon
     */
    //wp_register_script( WP_FLICKITY_UNIQUE_IDENTIFIER.'bootbox', 'https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js', false, WP_FLICKITY_VERSION, false );
    //wp_register_script( WP_FLICKITY_UNIQUE_IDENTIFIER.'bootstrap-modal-js', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/js/bootstrap-modal.min.js', false, WP_FLICKITY_VERSION, false );
    //wp_register_script( WP_FLICKITY_UNIQUE_IDENTIFIER.'bootstrap-modalmanager-js', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/js/bootstrap-modalmanager.min.js', false, WP_FLICKITY_VERSION, false );
    //wp_register_style( WP_FLICKITY_UNIQUE_IDENTIFIER.'bootstrap-modal-css', WP_FLICKITY_PLUGIN_URL . 'assets/bootstrap-modals.css', false, WP_FLICKITY_VERSION );


    //ORDER AND ENQUEUE CSS + JS
    //wp_enqueue_style( WP_FLICKITY_UNIQUE_IDENTIFIER.'bootstrap-modal-css' );
    //wp_enqueue_script( WP_FLICKITY_UNIQUE_IDENTIFIER.'bootstrap-modal-js' );
    //wp_enqueue_script( WP_FLICKITY_UNIQUE_IDENTIFIER.'bootstrap-modalmanager-js' );
    //wp_enqueue_script( WP_FLICKITY_UNIQUE_IDENTIFIER.'bootbox' );
    wp_enqueue_script( WP_FLICKITY_UNIQUE_IDENTIFIER.'admin_js' );
    wp_enqueue_style( WP_FLICKITY_UNIQUE_IDENTIFIER.'admin_css' );
}


/**
 * This function is a trigger to load the admin ui correctly
 * @since 0.5 
 */
function wp_flickity_load_ui(){
    // Unfortunately we can't just enqueue our scripts here - it's too early. So register against the proper action hook to do it
    add_action( 'admin_enqueue_scripts', 'wp_flickity_enqueue_admin_scripts' );
}


/**
 * PLUGIN INITIALIZER + ADMIN MENU + ADMIN UI
 * @since  	0.1		Added an Admin Menu Page
 * @since 	0.5		Now it enqueue the flickity admin ui just if is the plugin page
 */
function wp_flickity_menu() {
	//MENU PAGE
	$wp_flickity_admin_page = add_menu_page( WP_FLICKITY_NAME, WP_FLICKITY_NAME, 'upload_files', WP_FLICKITY_DOMAIN, 'wp_flickity_html_admin', 'dashicons-images-alt', WP_FLICKITY_MENUPOSITION );
	add_action('load-'.$wp_flickity_admin_page,'wp_flickity_load_ui');
}
add_action('admin_menu', 'wp_flickity_menu');


/**
 * ADMIN PAGE HTML
 * @return echo/html/text
 * @uses  definitions,wordpress,html
 * @since 0.1 Initial Admin
 */
function wp_flickity_html_admin(){
	global $wpdb,$wp_flickity_table_name;
	$wpflickity_subpage = isset($_GET['wp-flickity-page'])? $_GET['wp-flickity-page'] : "main";
	$current_page_class = 'current-flickity-page';

	/**
	 * DELETE FUNCTIONALITY
	 * @since 0.4.1 Tested
	 */
	if( isset($_GET['delete-flickity']) ){
		$fkIDToRemove = intval($_GET['delete-flickity']);
		$wpdb->update( 
			$wp_flickity_table_name, 
			array( 
				'status' => 'trashed'
			), 
			array( 'id' => $fkIDToRemove )
		);
		?>
		<div class="notice notice-success is-dismissible">
	        <p><?php _e( 'Flickity Deleted!', 'wp-flickity' ); ?></p>
	    </div>
	<?php }
	/**
	 * Post enabler functionality
	 * @since  0.5.0 2016-10-13-22-48-56 UTC
	 */	
	if( isset($_GET['post_enable']) and isset($_GET['flickity-id'])){
		$flktID = $_GET['flickity-id'];
		$flickity_mtd = $wpdb->get_var( "SELECT flickity_metadata FROM $wp_flickity_table_name WHERE id='$flktID'" );
		$flickity_mtd = unserialize($flickity_mtd);
		$post_enabler = (bool) intval($_GET['post_enable']);
		
		$flickity_mtd['post_enabled'] = $post_enabler;
		$wpdb->update( 
			$wp_flickity_table_name, 
			array( 
				'flickity_metadata' => serialize($flickity_mtd)
			), 
			array( 'id' => intval($_GET['flickity-id']) )
		);
		?>
	<?php }	?>
	<div class="wrap">
		
		<div class="wp-flickity-wrapper">
			<div class="wp-flickity-menu">
				<ul class="<?php echo ($wpflickity_subpage=="edit")?'editing':''?>">
					<li>
						<a class="<?php echo ($wpflickity_subpage=="main")?$current_page_class:''?>" href="<?php echo admin_url( 'admin.php?page=wp-flickity') ?>">Flickity</a>
					</li>
					<li>
						<a class="<?php echo ($wpflickity_subpage=="create-new")?$current_page_class:''?>" href="<?php echo admin_url( 'admin.php?page=wp-flickity&wp-flickity-page=create-new') ?>">Add New<span class="dashicons dashicons-plus"></span></a>
					</li>
					<li>
						<a id="wp-flickity-help-button" href="#"><span class="dashicons dashicons-flag"></span></a>
					</li>
				</ul>
				<?php
				if($wpflickity_subpage=="edit"){
					$flickity_id = intval($_GET['flickity-id']);
					$slider_title = $wpdb->get_var("SELECT name FROM $wp_flickity_table_name WHERE id='".$flickity_id."'");
					?>
					<h2>Editing Slider: <em><?php echo $slider_title?></em></h2>
					<h3>[wp_flickity id="<?php echo $flickity_id?>"]</h3>
				<?php } ?>
			</div>
			<!-- PLUGIN CONTENT -->
			<div class="wp-flickity-pagecontent">
				<?php if($wpflickity_subpage=="main"){
					$flickity_count = intval($wpdb->get_var( "SELECT COUNT(*) FROM $wp_flickity_table_name" ));
					if($flickity_count==0){ ?>
						<div class="wp-flickity-initial-message">
							<p>
								No Flickities found.<br>
								Let's start <a href="<?php echo admin_url( 'admin.php?page=wp-flickity&wp-flickity-page=create-new')?>">adding one</a>!
							</p>
						</div>
					<?php } else {
						$myflickities = $wpdb->get_results( "SELECT id, name FROM $wp_flickity_table_name WHERE status='publish'" ); ?>
						<table class="table-of-flickities">
							<thead>
								<tr>
									<th width="15px">#</th>
									<th>Name</th>
									<th>Actions</th>
									<th>Shortcode</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($myflickities as $row => $flickity_data){ ?>
									<tr>
										<td><?php echo $flickity_data->id?></td>
										<td><?php echo $flickity_data->name?></td>
										<td>
											<a class="flickity-edit-link" href="<?php echo wp_flickity_edit_link($flickity_data->id)?>">Edit</a>
											<a class="flickity-remove-link" href="<?php echo admin_url('admin.php?page=wp-flickity&delete-flickity='.$flickity_data->id)?>">Delete</a>
										</td>
										<td class="shortcode-flickity-col">
											<pre>[wp_flickity id="<?php echo $flickity_data->id?>"]</pre>
										</td>
									</tr>
								<?php } ?>								
							</tbody>
						</table>
					<?php }
				}elseif($wpflickity_subpage == "create-new"){
					if(isset($_POST['wp_flickity_slider_name']) and $_POST['wp_flickity_slider_name']!=""){
						$wpdb->insert(
							$wp_flickity_table_name,
							array(
								'name' => $_POST['wp_flickity_slider_name'],
							)
						); ?>
						<div class="notice notice-success is-dismissible">
					        <p><?php _e( 'Flickity Created! <a href="'.wp_flickity_edit_link($wpdb->insert_id).'">Edit it!</a>', 'wp-flickity' ); ?></p>
					    </div>
						<?php } ?>
				 	<form method="post">
						<table class="form-table">
						  <tr valign="top">
						  <th scope="row">Flickity Slider Name</th>
						  <td><input type="text" name="wp_flickity_slider_name"/></td>
						  </tr>
						</table>
						<?php submit_button(); ?>
				  	</form>
				<?php }elseif($wpflickity_subpage=="edit"){			
					if(isset($_POST['flickity_metadata'])){
						if(!isset($_POST['flickity_metadata']['settings']['prevNextButtons'])){
							$_POST['flickity_metadata']['settings']['prevNextButtons'] = 0;
						}else{
							$_POST['flickity_metadata']['settings']['prevNextButtons'] = 1;
						}
						if(!isset($_POST['flickity_metadata']['settings']['pageDots'])){
							$_POST['flickity_metadata']['settings']['pageDots'] = 0;
						}else{
							$_POST['flickity_metadata']['settings']['pageDots'] = 1;
						}
						if(isset($_POST['flickity_metadata']['settings']['wrapAround'])){
							$_POST['flickity_metadata']['settings']['wrapAround'] = intval($_POST['flickity_metadata']['settings']['wrapAround']);
						}
						if(isset($_POST['flickity_metadata']['settings']['freeScroll'])){
							$_POST['flickity_metadata']['settings']['freeScroll'] = intval($_POST['flickity_metadata']['settings']['freeScroll']);
						}
						$wpdb->update( 
							$wp_flickity_table_name, 
							array( 
								'flickity_metadata' => serialize($_POST['flickity_metadata'])
							), 
							array( 'id' => $flickity_id )
						);
					}
					//get the flickity metadata
					$flickity_metadata = $wpdb->get_var( "SELECT flickity_metadata FROM $wp_flickity_table_name WHERE id='$flickity_id'" );
					$flickity_metadata = unserialize($flickity_metadata);
					$flickity_post_enabled = (isset($flickity_metadata['post_enabled']) and ((bool)$flickity_metadata['post_enabled'])==true)?true:false;
					?>
					<div id="flickity_slider_configurator">
						<div class="flickity-slides" <?=$flickity_post_enabled?'style="display:none"':''?>>
							<div id="flickity-images-wrapper">	</div>
							<button class="upload-custom-img">ADD SLIDE/IMAGES</button>
						</div>
						<div class="flickity-post-configurator">
							<a href="
							<?=wp_flickity_edit_link($flickity_id).
							'&post_enable='.
							(($flickity_post_enabled)?'0':'1')?>" class="wp-flickity-posts-btn <?=$flickity_post_enabled?'is-on':'is-off'?>" >
								<?php if($flickity_post_enabled){
									echo 'Use Images as Slides';
								}else{
									echo 'Use Posts as Slides';
								}?>
							</a>
							<?php
							if(!isset($flickity_metadata['posts']['query'])){
								$flickityQueryPost = WP_FLICKITY_DEFAULT_QUERY_POST;
							}else{
								$flickityQueryPost = $flickity_metadata['posts']['query'];	
							}								
							?>
							<div class="wp-flickity-posts-fields-container" <?=(!$flickity_post_enabled)?'style="display:none"':''?>>
								<div class="wp-flickity-posts-field-block" wp-flickity-field-id="query">
									<label for="wp-flickity-query-posts">Query: </label>
									<textarea wp-flickity-sync-on='#wp-flickity-post-query-value' name="wp-flickity-query-posts" class="wp-flickity-posts-input" type="text" id="flickity-query-posts"><?=trim($flickityQueryPost)?></textarea>	
								</div>
								<div class="wp-flickity-posts-fields-block" wp-flickity-field-id="flickity-querycomposer">
									<button id="flickity-querycomposer-button" class="wp-flickity-button" sync-query-to='[name="wp-flickity-query-posts"]'>Select Posts</button>
									<div class="querycomposer" style="display:none;">
										<h3 class="query-preview"></h3>
										<table class="form-table">
											<tr valign="top">
											<th scope="row">How many posts want you show max?</th>
											<td><input type="number" name="posts_per_page" value="" min="1"/></td>
											</tr>
											
											<tr valign="top">
											<th scope="row">Post types<br><small>Multiple select allowed (with Ctrl/Cmd)</small></th>
											<td><select multiple name="post_type">
												<?php
												foreach ( get_post_types( array('public'=>true), 'names' ) as $post_type ) {
													echo '<option value="' . $post_type . '">' . $post_type . '</p>';
												}
												?>
												</select></td>
											</tr>
											
											<tr valign="top">
											<th scope="row">Advanced query</th>
											<td><input type="text" name="advanced_query" value="" placeholder="cat=4,6,23&category__not_in=3,2"/></td>
											</tr>

											<small><em>To generate a complex querystring params see the <a href="https://codex.wordpress.org/Class_Reference/WP_Query" target="_blank">codex</a></em></small>
										</table>
										<button class="wp-flickity-button" id="save-querycomposer">Save</button>
									</div>
								</div>
							</div>	

						</div>
					</div>
					<form method="post">
						<div class="flickity-single-slide-config">
							<iframe scrolling="no" id="wp-flickity-previewer" src="<?=wp_flickity_preview_url($flickity_id)?>">								
							</iframe>
						</div>
						<div class="flickity-slider-settings">
							<?php
								//inherith settings from #Flickity
								$wrapAround = isset($flickity_metadata['settings']['wrapAround'])?true:false;
								$freeScroll = isset($flickity_metadata['settings']['freeScroll'])?true:false;
								//
								$prevNextButtons = isset($flickity_metadata['settings']['prevNextButtons'])?intval($flickity_metadata['settings']['prevNextButtons']):true;
								$pageDots = isset($flickity_metadata['settings']['pageDots'])?intval($flickity_metadata['settings']['pageDots']):true;

								$autoPlay = isset($flickity_metadata['settings']['autoPlay'])?intval($flickity_metadata['settings']['autoPlay']):false;

								//CUSTOM OF WP-FLICKITY								
								$forceFullWidth = isset($flickity_metadata['settings']['forceFullWidth'])?true:false;
								$customSliderClass = isset($flickity_metadata['settings']['customSliderClass'])?trim($flickity_metadata['settings']['customSliderClass']):false;
							?>
							<p>
								<input type="hidden" name="flickity_metadata[posts][query]"
								id="wp-flickity-post-query-value">
								<input type="hidden" name="flickity_metadata[post_enabled]"
								id="wp-flickity-post-query-value" value="<?=(int)$flickity_post_enabled?>">
								
								<span class="flickity-setting">
									<input type="checkbox" name="flickity_metadata[settings][wrapAround]" <?php echo ($wrapAround)?'checked':''?> value="1"/>
									<label for="flickity_metadata[settings][wrapAround]" class="prfx-row-title">Wrap Around
										<a  class="flickity-question-info" title="What is that?" href="http://flickity.metafizzy.co/#wraparound" target="_blank">	<span class="dashicons dashicons-info"></span>
										</a>
									</label>
								</span>
								<span class="flickity-setting">
									<input type="checkbox" name="flickity_metadata[settings][freeScroll]" <?php echo ($freeScroll)?'checked':''?> value="1"/>
									<label for="flickity_metadata[settings][freeScroll]" class="prfx-row-title">Free Scroll
										<a  class="flickity-question-info" title="What is that?" href="http://flickity.metafizzy.co/#freescroll" target="_blank">	<span class="dashicons dashicons-info"></span>
										</a>
									</label>
								</span>
								<span class="flickity-setting">
									<input type="checkbox" name="flickity_metadata[settings][prevNextButtons]" <?php echo ($prevNextButtons)?'checked':''?> value="1"/>
									<label for="flickity_metadata[settings][prevNextButtons]" class="prfx-row-title">Navigation Buttons (prev/next)
										<a  class="flickity-question-info" title="What is that?" href="http://flickity.metafizzy.co/options.html#prevnextbuttons" target="_blank">	<span class="dashicons dashicons-info"></span>
										</a>
									</label>
								</span>
								<span class="flickity-setting">
									<input type="checkbox" name="flickity_metadata[settings][pageDots]" <?php echo ($pageDots)?'checked':''?> value="1"/>
									<label for="flickity_metadata[settings][pageDots]" class="prfx-row-title">Navigation Dots 
										<a  class="flickity-question-info" title="What is that?" href="http://flickity.metafizzy.co/options.html#pagedots" target="_blank">	<span class="dashicons dashicons-info"></span>
										</a>
									</label>
								</span>	
								<span class="flickity-setting">
									<input type="number" name="flickity_metadata[settings][autoPlay]" <?php echo ($autoPlay)?'checked':''?>  min="0" max="30000" step="100" value="<?php echo ($autoPlay)?$autoPlay:'0'?>"/>
									<label for="flickity_metadata[settings][autoPlay]" class="prfx-row-title">Auto Play 
										<a  class="flickity-question-info" title="What is that?" href="http://flickity.metafizzy.co/#autoplay" target="_blank">	<span class="dashicons dashicons-info"></span>
										</a>
									</label>
									<small class="flickity-helper-msg"> Enter here value in milliseconds. (1000 = 1 Second). <strong>Put 0 to disable autoplay.</strong></small>
								</span>	

								<span class="flickity-settings-separator"></span>

								<span class="flickity-setting">
									<input type="checkbox" name="flickity_metadata[settings][forceFullWidth]" <?php echo ($forceFullWidth)?'checked':''?> value="1"/>
									<label for="flickity_metadata[settings][forceFullWidth]" class="prfx-row-title">Force Full Width Slides
									</label>
									<small class="flickity-helper-msg"> This will force each slide to not exit and not being lower the slider viewport.<em> (Experimental)</em></small>
								</span>	
								<span class="flickity-setting">
									<input type="text" name="flickity_metadata[settings][customSliderClass]"  value="<?php echo ($customSliderClass)?$customSliderClass:''?>"/>
									<label for="flickity_metadata[settings][customSliderClass]" class="prfx-row-title">Custom Class
									</label>
									<small class="flickity-helper-msg">You may want to add custom css classes to your slider.</em></small>
								</span>	
								<span class="flickity-setting">
									<select type="text" name="flickity_metadata[settings][thumbSize]"  value="<?php echo ($customSliderClass)?$customSliderClass:''?>">
										<option <?php echo (isset($flickity_metadata['settings']['thumbSize']) and !empty($flickity_metadata['settings']['thumbSize']))?'selected="selected"':''?> value="">Auto</option>
										<?php foreach (get_intermediate_image_sizes() as $thumbSizeKey => $thumbSizeString): ?>
											<option <?php if(isset($flickity_metadata['settings']['thumbSize']) and $flickity_metadata['settings']['thumbSize'] == $thumbSizeString)echo 'selected="selected"';?> value="<?php echo $thumbSizeString?>">
												<?php echo $thumbSizeString?>
												<?php if(intval( get_option( "{$thumbSizeString}_size_w") )) echo intval( get_option( "{$thumbSizeString}_size_w") ).'x'.intval( get_option( "{$thumbSizeString}_size_h") );?>
											</option>
										<?php endforeach; ?>
									</select>
									<label for="flickity_metadata[settings][thumbSize]" class="prfx-row-title">Image Size
									</label>
									<small class="flickity-helper-msg">Choose the resolution to use for images</em></small>
								</span>						        						        
						    </p>
						</div>
						<input type="hidden" id="flickities-images" name="flickity_metadata[images_ids]" value="<?php echo $flickity_metadata['images_ids']?>"/>
						<?php submit_button(); ?>
				  	</form>
				<?php } ?>
			</div>
		</div>
		<div class="wp-flickity-help-wrapper" style="display:none;">
			<div class="wp-flickity-help-content">
				<span id="wp-flickity-help-closer" class="dashicons dashicons-no"></span>
				<div class="wp-flickity-main-help-description">
					<p>This plugin is powered by <a href="http://www.paolofalomo.com/" target="_blank">Paolo Falomo Web Designer</a>.<br> If you want to <a href="https://gitlab.com/paolofalomo/wp-flickity/" target="_blank">contribute</a> or <a href="https://www.paypal.me/PaoloFalomo" target="_blank">make a donation</a> you are free to do it :)<br><br><em>Open Source! Yeah!!</em></p>
				</div>
				<h2>Any questions?</h2>
				<a href="mailto:info@paolofalomo.it">Ask me anything! I answer in seconds</a>
				<h2>Something Buggy?</h2>
				<a href="https://gitlab.com/paolofalomo/wp-flickity/issues/new"  target="_blank">Submit an issue on Gitlab.com </a> or <a href="https://wordpress.org/support/plugin/wp-flickity"  target="_blank">Request support on Wordpress.org </a>
				<hr>
				<small id="wp-flickity-credits">
					<strong>Credits</strong><br><br>All JS/CSS Frameworks credits goes to Dave De Sandro from <a href="http://metafizzy.co/" target="_blank">Metafizzy</a>.<br>Documentation of external libraries can be found <a href="http://flickity.metafizzy.co/" target="_blank">here</a>
				</small>
			</div>
		</div>
	</div>
	<?php
}
//