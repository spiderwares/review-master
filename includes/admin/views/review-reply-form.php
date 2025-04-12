<form method="get">
    <table style="display:none;">
        <tbody id="review-reply">
            <tr id="respondreview" class="rmpro-respond inline-edit-row inline-edit-row-post respond-row respond-row-post inline-edit-site-review inline-editor">
                <td colspan="7" class="colspanchange">
                    <div class="inline-edit-wrapper" role="region" aria-labelledby="respond-legend">
                        <legend class="inline-edit-legend"><?php esc_html_e( 'Respond to the review', 'review-master' ); ?></legend>
                        <div class="flex-row">
                            <fieldset class="rmpro-inline-edit-col-left">
                                <div class="inline-edit-col">
                                    <label>
                                        <span class=""><?php esc_html_e( 'Review', 'review-master' ); ?></span>
                                        <textarea cols="22" rows="1" data-name="_rmpro_review" readonly=""></textarea>
                                    </label>
                                </div>
                            </fieldset>
                            <fieldset class="rmpro-inline-edit-col-right">
                                <div class="inline-edit-col">
                                    <label>
                                        <span class=""><?php esc_html_e( 'Response', 'review-master' ); ?></span>
                                        <textarea cols="22" rows="1" name="_response"></textarea>
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                        <div class="submit inline-edit-save">
                            <input type="hidden" name="review_id" value="">
                            <button type="button" class="button cancel alignleft"><?php esc_html_e( 'Cancel', 'review-master' ); ?></button>
                            <button type="button" class="button button-primary save alignright"><?php esc_html_e( 'Respond', 'review-master' ); ?></button>
                            <span class="spinner"></span>
                            <br class="clear">
                            <div class="notice notice-error notice-alt inline hidden">
                                <p class="error"></p>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</form>