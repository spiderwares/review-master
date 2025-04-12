<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif; ?>

<div id="review-categories" class="rmpro-tab-content">

    <!---------------------------- Post Type Setting ---------------------------->
    <div class="rmpro-title"><h2><?php esc_html_e( 'Post Type Setting', 'review-master' ); ?></h2></div>
    <div class="rmpro-grid">
        <?php foreach ( $post_types as $post_type ) : ?>
            <div class="rmpro-grid-item">
                <div class="rmpro-grid-item-header">
                    <label><?php echo esc_html( $post_type->labels->name ); ?></label>
                </div>
                <div class="rmpro-flex">
                    <div class="rmpro-label"><b><?php esc_html_e( 'Replace with comment form', 'review-master' ) ?></b></div>
                    <div class="rmpro-switch">
                        <input type="checkbox" 
                            class="rmpro-toggle-category" 
                            id="<?php echo esc_attr( "switch-" . $post_type->name ); ?>"
                            data-module-type="<?php echo esc_attr( $post_type->name ); ?>" 
                            <?php checked( isset( $this->settings[ $post_type->name ]['enable'] ) && $this->settings[ $post_type->name ]['enable'] ); ?> 
                            name="rmpro_<?php echo esc_attr( $current_tab ); ?>[<?php echo esc_attr( $post_type->name ); ?>][enable]" 
                        />
                        <label for="<?php echo esc_attr( "switch-" . $post_type->name ); ?>" class="rmpro-switch-label"></label>
                    </div>
                </div>
                
                <!-- Enable Summary switch -->
                <div class="rmpro-flex">
                    <div class="rmpro-label"><b><?php esc_html_e( 'Enable Summary', 'review-master' ) ?></b></div>
                    <div class="rmpro-switch">
                        <input type="checkbox" 
                            class="rmpro-toggle-summary" 
                            id="<?php echo esc_attr( "summary-switch-" . $post_type->name ); ?>"
                            data-module-type="<?php echo esc_attr( $post_type->name ); ?>" 
                            <?php checked( isset( $this->settings[ $post_type->name ]['enable_summary'] ) && $this->settings[ $post_type->name ]['enable_summary'] ); ?> 
                            name="rmpro_<?php echo esc_attr( $current_tab ); ?>[<?php echo esc_attr( $post_type->name ); ?>][enable_summary]" 
                        />
                        <label for="<?php echo esc_attr( "summary-switch-" . $post_type->name ); ?>" class="rmpro-switch-label"></label>
                    </div>
                </div>

                <!-- Per Page Field -->
                <div class="rmpro-flex rmpro-per-page">
                    <label for="<?php echo esc_attr( "per-page-" . $post_type->name ); ?>" class="rmpro-label">
                        <b><?php esc_html_e( 'Per Page', 'review-master' ); ?></b>
                    </label>
                    <input type="number" id="<?php echo esc_attr( "per-page-" . $post_type->name ); ?>" 
                        name="rmpro_<?php echo esc_attr( $current_tab ); ?>[<?php echo esc_attr( $post_type->name ); ?>][per_page]" 
                        value="<?php echo esc_attr( isset( $this->settings[ $post_type->name ]['per_page'] ) ? $this->settings[ $post_type->name ]['per_page'] : 5 ); ?>" 
                        min="1" class="rmpro-per-page-input">
                </div>

                <p class="description"><?php esc_html_e( 'Enable rating feature category wise?', 'review-master' ); ?></p>
                
                <div class="rmpro-category-inputs">
                    <ul class="rmpro-category-list">
                        <?php if ( isset( $this->settings[ $post_type->name ]['category'] ) ) :
                            foreach ( $this->settings[ $post_type->name ]['category'] as $category ) : ?>
                                <li>
                                    <span class="rmpro-category-icon">&#9776;</span>
                                    <span class="rmpro-category-name"><?php echo esc_html( $category ); ?></span>
                                    <a href="#" data-category="<?php echo esc_attr( $category ); ?>" class="rmpro-remove-category"><?php esc_html_e( 'Remove', 'review-master' ); ?></a>
                                    <input type="hidden" name="rmpro_<?php echo esc_attr( $current_tab ); ?>[<?php echo esc_attr( $post_type->name ); ?>][category][]" value="<?php echo esc_attr( $category ); ?>">
                                </li>
                            <?php endforeach;
                        endif; ?>
                    </ul>
                    <div class="rmpro-category-add">
                        <input type="text" class="rmpro-category-input" placeholder="<?php esc_html_e( 'Add new category', 'review-master' ); ?>" />
                        <button data-module-type="<?php echo esc_attr( $post_type->name ); ?>" type="button" class="rmpro-add-category">
                            <?php esc_html_e( '+', 'review-master' ); ?>
                        </button>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

    <div class="rmpro-notice">
        <p>Integrate with any custom module, WordPress custom table, or theme. <a target="_blank" href="https://spiderwares.com/documentation/rmpro/">Click Here</a> for guidence</p>
    </div>
</div>