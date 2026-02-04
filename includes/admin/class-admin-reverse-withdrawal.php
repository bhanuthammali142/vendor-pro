<?php
/**
 * Admin Reverse Withdrawal Page
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Admin_Reverse_Withdrawal
{
    /**
     * Render page
     */
    public static function render_page()
    {
        // Filter transactions where type is 'reverse_withdrawal' or logic implies it (balance updates)
        // Actually, we can assume 'unpaid' commissions for COD orders fall here OR negative balance logic.
        // For simplicity, let's list the vendors who have a NEGATIVE balance exceeding threshold.

        $db = VendorPro_Database::instance();

        $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 20;
        $offset = ($paged - 1) * $per_page;

        // Custom Query to get vendors with negative balance
        // We will misuse existing get_vendors but filter in PHP or create new method. 
        // For efficiency, let's just show ALL vendors with their balance, sorted by balance ASC (Lowest/Most Negative first).

        $args = array(
            'limit' => $per_page,
            'offset' => $offset,
            // 'orderby' => 'balance', // Need to support sorting by balance joined table? Complex.
            // Let's stick to simple list for now.
        );

        $vendors = $db->get_vendors(array('limit' => -1)); // Get all to filter in PHP for this view (not optimal for thousands, but works for V1)

        $reverse_vendors = array();
        foreach ($vendors as $vendor) {
            $balance = $db->get_vendor_balance($vendor->id);
            if ($balance < 0) {
                $vendor->balance = $balance;
                $reverse_vendors[] = $vendor;
            }
        }

        $total_items = count($reverse_vendors);
        $reverse_vendors = array_slice($reverse_vendors, $offset, $per_page);

        include VENDORPRO_TEMPLATES_DIR . 'admin/reverse-withdrawal.php';
    }
}
