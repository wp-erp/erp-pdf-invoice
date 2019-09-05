<?php
// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;
/**
 * Check if update is required.
 *
 * @return bool
 */
function erp_pdf_invoice_need_update() {

    if ( version_compare( get_option( 'wp_erp_version' ), '1.5.0', '>=' ) ) {
        return true;
    }
    return false;
}
