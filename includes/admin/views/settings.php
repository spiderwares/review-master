<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif;
?>
<div class="wrap spiderwares_settings_page rmpro-settings">
    <div class="spiderwares_settings_page_nav">
        <h1><?php esc_html_e( 'Review Master Settings', 'review-master' ); ?></h1>
        <h2 class="nav-tab-wrapper">
            <?php foreach ( $tabs as $tab_key => $tab ) : ?>
                <a href="?page=rmpro-settings&tab=<?php echo esc_attr($tab_key); ?>" class="nav-tab <?php echo esc_html( $current_tab === $tab_key ? 'nav-tab-active' : '' ); ?>">
                    <?php echo esc_html( $tab['label'] ); ?>
                </a>
            <?php endforeach; ?>
        </h2>
        <div class="spiderwares_settings_page_content">
            <?php do_action( 'rmpro_settings_before_tab_content', $current_tab ); ?>
            <form method="post" action="options.php">
                <?php 
                    settings_fields( 'rmpro_' . $current_tab . '_group' ); // Option group
                    
                    do_settings_sections( 'rmpro_' . $current_tab );
                ?>
                <div class="rmpro-wrap">
                    <?php 
                        do_action( 'rmpro_settings_tab_content_' . $current_tab, $current_tab );
                        
                        submit_button(); 
                    ?>
                </div>
            </form>
            <?php do_action( 'rmpro_settings_after_tab_content', $current_tab ); ?>
        </div>
    </div>
</div>
