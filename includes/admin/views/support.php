<?php
if ( ! defined( 'ABSPATH' ) ) :
    exit; // Exit if accessed directly
endif; ?>

<div class="wrap spiderwares_settings_page rmpro-support">
    <div class="spiderwares_settings_page_nav">
        <h1><?php esc_html_e( 'Review Master Help & Support', 'review-master' ); ?></h1>
        <h2 class="nav-tab-wrapper">
            <?php foreach ( $tabs as $tab_key => $tab ) : ?>
                <a href="?page=rmpro-support&tab=<?php echo esc_attr($tab_key); ?>" class="nav-tab <?php echo esc_html( $current_tab === $tab_key ? 'nav-tab-active' : '' ); ?>">
                    <?php echo esc_html( $tab['label'] ); ?>
                </a>
            <?php endforeach; ?>
        </h2>
        <div class="spiderwares_settings_page_content">
            <?php do_action( 'rmpro_help_support_before_tab_content', $current_tab ); ?>
                <div class="rmpro-wrap">
                    <?php do_action( 'rmpro_help_support_tab_content_' . $current_tab, $current_tab ); ?>
                </div>
            <?php do_action( 'rmpro_help_support_after_tab_content', $current_tab ); ?>
        </div>
    </div>
</div>