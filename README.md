# Smart Product Approval Manager 

This plugin creates a simple admin dashboard for approving/rejecting products via REST API.


## Installation
1. Upload the `smart-product-approval-manager` folder to `wp-content/plugins/`.
2. Activate the plugin in WordPress admin.
3. Go to Admin -> Approvals to open the dashboard.

## Notes
- If WooCommerce is installed, the plugin will try to fetch real pending products.
- If WooCommerce is not present, the plugin returns sample data for demo purposes.
- Approval/reject actions will log entries into `{$wpdb->prefix}spam_logs` (created on activation).
- The admin UI uses a minimal JavaScript bundle included in `build/bundle.js`.

## Files
- `includes/` — PHP service classes and REST controller.
- `build/` — frontend assets (JS + CSS).
- `smart-product-approval-manager.php` — plugin bootstrap.

## For developers
- You can extend the REST routes in `includes/class-rest-controller.php`.
- The approval logic is in `includes/class-approval-service.php`.
