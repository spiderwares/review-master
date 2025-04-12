<?php if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;
?>
<form  action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="rmpro_review" method="post" id="post">
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Edit Review', 'review-master' ); ?></h1>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content" class="edit-form-section edit-review-section">
                    <div id="titlediv">
                        <div id="titlewrap">
                            <input type="text" name="rmpro_title" id="title" spellcheck="true" autocomplete="off" value="<?php echo esc_attr( isset( $review['title'] ) ? $review['title'] : '' ); ?>" />
                        </div>
                    </div>

                    <div class="postarea rmpro-post-content">
                        <div id="wp-content-wrap" class="wp-core-ui wp-editor-wrap html-active" style="padding-top: 0px;">
							<?php 
								wp_editor( 
									wp_strip_all_tags( isset( $review['your_review'] ) ? $review['your_review'] : '', true ), 
									'rmpro_review_content', 
									array(
										'textarea_name' => 'rmpro_review_content',
										'media_buttons' => false,  // Removes "Add Media" button
										'tinymce' => false,        // Disables TinyMCE (Visual Editor)
										'quicktags' => false       // Disables Quicktags (HTML mode)
									)
								); 
							?>
                        </div>
                    </div>

					<div class="postbox">
						<div class="postbox-header">
							<h2 class="edit-review-author"><?php esc_html_e( 'Overview', 'review-master' ); ?></h2>
						</div>
						<div class="inside">
							<fieldset>
								<legend class="screen-reader-text">
								<?php esc_html_e( 'Review Author', 'review-master' ); ?></legend>
								<table class="form-table editreview" role="presentation">
									<tbody>
										<tr>
											<th class="first">
                                                <label for="avatar">
                                                    <b><?php esc_html_e( 'Avatar', 'review-master' ); ?></b>
                                                </label>
                                            </td>
											<td>
												<?php echo get_avatar( isset( $review['email'] ) ? $review['email'] : '', 92 ); ?>
											</td>
										</tr>
										<?php if( !empty( $reviews_by_cat ) ): ?>
										<tr>
											<th class="first">
												<label for="ratings">
													<b><?php esc_html_e( 'Ratings', 'review-master' ); ?></b>
												</label>
											</th>
											<td>
												<table class="rmpro-subtable category-rating-table">
													<thead>
														<tr>
															<th><?php esc_html_e( 'Category Name', 'review-master' ); ?></th>
															<th><?php esc_html_e( 'Rating', 'review-master' ); ?></th>
														</tr>
													</thead>
													<tbody>
														<?php foreach ( $reviews_by_cat as $key => $category ) : ?>
															<tr>
																<td>
																	<b><?php echo esc_html( $category['label'] ); ?></b>
																	<input type="hidden" name="rating_category_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $category['label'] ); ?>">
																</td>
																<td>
																	<div class="rmpro-stars">
																		<?php for ( $index = 5; $index >= 1; $index-- ) : ?>
																			<input 
																				type="radio" 
																				id="<?php echo esc_attr( 'star' . $index . '_' . $key ); ?>" 
																				name="<?php echo esc_attr( "ratings[{$category['label']}]" ); ?>" 
																				value="<?php echo esc_attr( $index ); ?>" 
																				<?php checked( $category['rating'], $index ); ?>
																			/>
																			<label for="<?php echo esc_attr( 'star' . $index . '_' . $key ); ?>">
																				<img src="data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cpath fill='transparent' stroke='%23FFBD13' stroke-width='38' d='M259.216 29.942L330.27 173.92l158.89 23.087L374.185 309.08l27.145 158.23-142.114-74.698-142.112 74.698 27.146-158.23L29.274 197.007l158.89-23.088z' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E" alt="star">
																			</label>
																		<?php endfor; ?>
																	</div>
																</td>
															</tr>
														<?php endforeach; ?>
													</tbody>
												</table>
												<p class="rmpro-note">
													<?php esc_html_e( 'Note: Click on the stars to update your rating and adjust your selection as needed.', 'review-master' ) ?>
												</p>
											</td>
										</tr>
										
										<tr>
											<th class="first">
                                                <label for="average-rating">
                                                    <b><?php esc_html_e( 'Average Rating', 'review-master' ); ?></b>
                                                </label>
                                            </td>
											<td>
												<?php 
													printf(
														'<div class="rmpro-rating-container">
															<div class="rmpro-rating-stars overall-rating" style="width: %d%%;"></div>
														</div>', 
														esc_attr( RMPRO_Reviews_List_Table::calculate_star_width( floatval( isset( $review['rating'] ) ? $review['rating'] : 0 ) ) ) 
													); 
												?>
												<input type="hidden" name="avg_rating" value="<?php echo esc_attr( isset( $review['rating'] ) ? $review['rating'] : '' ); ?>">
												<p class="rmpro-note">
													<?php esc_html_e( 'Note: You cannot change these values directly as they are calculated based on the category average.', 'review-master' ) ?>
												</p>
											</td>
										</tr>
										<?php else: ?>
											<tr>
											<th class="first">
                                                <label for="average-rating">
                                                    <b><?php esc_html_e( 'Rating', 'review-master' ); ?></b>
                                                </label>
                                            </td>
											<td>
												<div class="rmpro-stars">
													<?php 
													$rating_value = isset( $review['rating'] ) ? round( $review['rating'] ) : 0;
													for ( $index = 5; $index >= 1; $index-- ) : ?>
														<input 
															type="radio" 
															id="<?php echo esc_attr( "star-$index" ); ?>" 
															name="avg_rating" 
															value="<?php echo esc_attr( $index ); ?>" 
															<?php checked( $rating_value, $index ); ?>
														/>
														<label for="<?php echo esc_attr( "star-$index" ); ?>">
															<img src="data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'%3E%3Cpath fill='transparent' stroke='%23FFBD13' stroke-width='38' d='M259.216 29.942L330.27 173.92l158.89 23.087L374.185 309.08l27.145 158.23-142.114-74.698-142.112 74.698 27.146-158.23L29.274 197.007l158.89-23.088z' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E" alt="star">
														</label>
													<?php endfor; ?>
												</div>
											</td>

										</tr>
										<?php endif; ?>

                                        <tr>
											<th class="first">
                                                <label for="name">
                                                    <b><?php esc_html_e( 'Name', 'review-master' ); ?></b>
                                                </label>
                                            </td>
											<td><input type="text" name="rmpro_name" size="30" value="<?php echo esc_attr( isset( $review['name'] ) ? $review['name'] : '' ); ?>" id="name"></td>
										</tr>
										<tr>
											<th class="first">
                                                <label for="email">
                                                    <b><?php esc_html_e( 'Email', 'review-master' ); ?></b>
                                                </label>
                                            </td>
											<td>
												<input type="text" name="rmpro_email" size="30" value="<?php echo esc_attr( isset( $review['email'] ) ? $review['email'] : '' ); ?>" id="email">
											</td>
										</tr>
                                        <tr>
											<th class="first">
                                                <label for="ip_address">
                                                    <b><?php esc_html_e( 'IP Address', 'review-master' ); ?></b>
                                                </label>
                                            </td>
											<td>
												<input type="text" name="rmpro_ip_address" value="<?php echo esc_attr( isset( $review['ip_address'] ) ? $review['ip_address'] : '' ); ?>" id="ip_address">
											</td>
										</tr>
									</tbody>
								</table>
							</fieldset>
						</div>
					</div>

					<div class="postbox">
						<div class="postbox-header">
							<h2 class="edit-review-author"><?php esc_html_e( 'Review Respond', 'review-master' ); ?></h2>
						</div>
						<div class="inside">
						<?php 
							wp_editor( 
								wp_strip_all_tags( isset( $review['id'] ) ? get_metadata( 'rmpro_reviews', $review['id'], '_response', true ) : '', true ), 
								'rmpro_review_respond', 
								array(
									'textarea_rows' => 5, 
									'textarea_name' => 'rmpro_review_respond',
									'media_buttons' => false,  // Removes "Add Media" button
									'tinymce' => false,        // Disables TinyMCE (Visual Editor)
									'quicktags' => false       // Disables Quicktags (HTML mode)
								)
							); 
						?>
						</div>
					</div>
				</div><!-- /post-body-content -->


				<div id="postbox-container-1" class="postbox-container">
					<div id="submitdiv" class="stuffbox">
						<h2><?php esc_html_e( 'Save', 'review-master' ); ?></h2>
						<div class="inside">
							<div class="submitbox" id="submitreview">
								<div id="minor-publishing">

									<div id="misc-publishing-actions">

										<div class="misc-pub-section misc-pub-review-status" id="review-status">
											<?php esc_html_e( 'Status:', 'review-master' ); ?>
											<fieldset id="review-status-radio">
												<legend class="screen-reader-text">
												<?php esc_html_e( 'review status', 'review-master' ); ?></legend>
												<label>
													<input type="radio" name="review_status"
														value="approve" 
														<?php checked( isset( $review['status'] ) ? $review['status'] : '', 'approve' ); ?> ><?php esc_html_e( 'Approved', 'review-master' ); ?>
												</label><br>
												<label>
													<input type="radio" name="review_status"
														value="unapprove" 
														<?php checked( isset( $review['status'] ) ? $review['status'] : '', 'unapprove' ); ?> ><?php esc_html_e( 'Unapproved', 'review-master' ); ?>
												</label><br>
												<label>
													<input type="radio" name="review_status"
														value="spam" 
														<?php checked( isset( $review['status'] ) ? $review['status'] : '', 'spam' ); ?> ><?php esc_html_e( 'Spam', 'review-master' ); ?>
												</label><br>
												<label>
													<input type="radio" name="review_status"
														value="trash" 
														<?php checked( isset( $review['status'] ) ? $review['status'] : '', 'trash' ); ?> ><?php esc_html_e( 'Trash', 'review-master' ); ?>
												</label>
											</fieldset>
										</div><!-- .misc-pub-section -->

										<div class="misc-pub-section curtime misc-pub-curtime">
											<span id="timestamp">
												<?php esc_html_e( 'Submitted on:', 'review-master' ); ?>
												<?php if( isset( $review['created_at'] ) ) : ?>
													<b><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $review['created_at'] ) ) ); 
													?></b>
												<?php endif; ?>
											</span>
										</div>
										<?php if( isset( $module_link ) ) : ?>
											<div class="misc-pub-section misc-pub-response-to">
												<?php esc_html_e( 'In response to: ', 'review-master' ); ?>
												<b><?php echo wp_kses_post( rmpro_get_module_html_link( $review ) ); ?></b>
											</div>
										<?php endif; ?>

									</div> <!-- misc actions -->
									<div class="clear"></div>
								</div>

								<div id="major-publishing-actions">
									<div id="publishing-action">
										<?php if ( isset( $_GET['review_id'] ) ) : ?>
											<input type="hidden" name="review_id" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET['review_id'] )) ); ?>">
										<?php endif; ?>
										<input type="hidden" name="associate_id" value="<?php echo intval( isset( $review['associate_id'] ) ? $review['associate_id'] : '' ); ?>">
										<input type="hidden" name="module_type" value="<?php echo esc_attr( isset( $review['module_type'] ) ? $review['module_type'] : '' ); ?>">
										<input type="hidden" name="action" value="rmpro_save_review" />
										<input type="hidden" name="module" value="<?php echo esc_attr( isset( $review['module'] ) ? $review['module'] : '' ); ?>">
										<?php wp_nonce_field( 'rmpro_save_review_action', 'rmpro_save_review' ); ?>
										<input type="submit" name="save"
											class="button button-primary button-large" value="<?php esc_html_e( 'Update', 'review-master' ); ?>">
									</div>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					</div><!-- /submitdiv -->
				</div>

			</div><!-- /post-body -->
		</div>
	</div>
</form>