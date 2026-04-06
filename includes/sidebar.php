<?php
// includes/sidebar.php
$user_type = $_SESSION['user_type'] ?? '';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <?php if ($user_type === 'admin'): ?>
        <a href="<?php echo SITE_URL; ?>admin/dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a>
        <a href="<?php echo SITE_URL; ?>admin/claims.php" class="<?php echo $current_page == 'claims.php' ? 'active' : ''; ?>"><i class="fas fa-exclamation-triangle"></i> All Claims Audit</a>
        <a href="<?php echo SITE_URL; ?>admin/settings.php" class="<?php echo $current_page == 'settings.php' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> System Settings</a>

    <?php elseif ($user_type === 'client'): ?>
        <a href="<?php echo SITE_URL; ?>client/dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a>
        <a href="<?php echo SITE_URL; ?>client/warranties.php" class="<?php echo $current_page == 'warranties.php' ? 'active' : ''; ?>"><i class="fas fa-file-contract"></i> My Warranties</a>
        <a href="<?php echo SITE_URL; ?>client/register_warranty.php" class="<?php echo $current_page == 'register_warranty.php' ? 'active' : ''; ?>"><i class="fas fa-plus-circle"></i> Register Warranty</a>
        <a href="<?php echo SITE_URL; ?>client/file_claim.php" class="<?php echo $current_page == 'file_claim.php' ? 'active' : ''; ?>"><i class="fas fa-exclamation-triangle"></i> File Claim</a>
        <a href="<?php echo SITE_URL; ?>client/claims.php" class="<?php echo $current_page == 'claims.php' ? 'active' : ''; ?>"><i class="fas fa-list"></i> My Claims</a>
        <a href="<?php echo SITE_URL; ?>client/profile.php" class="<?php echo $current_page == 'profile.php' ? 'active' : ''; ?>"><i class="fas fa-user-cog"></i> Profile</a>
    
    <?php elseif ($user_type === 'technician'): ?>
        <a href="<?php echo SITE_URL; ?>technician/dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Dashboard</a>
        <a href="<?php echo SITE_URL; ?>technician/pending_claims.php" class="<?php echo $current_page == 'pending_claims.php' ? 'active' : ''; ?>"><i class="fas fa-clock"></i> Pending Claims</a>
        <a href="<?php echo SITE_URL; ?>technician/verified_claims.php" class="<?php echo $current_page == 'verified_claims.php' ? 'active' : ''; ?>"><i class="fas fa-check-circle"></i> Verified Claims</a>
        <a href="<?php echo SITE_URL; ?>technician/reports.php" class="<?php echo $current_page == 'reports.php' ? 'active' : ''; ?>"><i class="fas fa-chart-bar"></i> Reports</a>
    
    <?php elseif ($user_type === 'owner'): ?>
        <div style="padding: 10px; margin: 0 15px 15px; font-size: 10px; color: var(--primary); font-weight: 800; background: rgba(99,102,241,0.1); border-radius: 6px; text-align: center; letter-spacing: 1px;">BUSINESS OWNER</div>
        <a href="<?php echo SITE_URL; ?>owner/dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="<?php echo SITE_URL; ?>owner/manage_sold_items.php" class="<?php echo $current_page == 'manage_sold_items.php' ? 'active' : ''; ?>"><i class="fas fa-tags"></i> Sold Items Inventory</a>
        <a href="<?php echo SITE_URL; ?>owner/assign_technician.php" class="<?php echo $current_page == 'assign_technician.php' ? 'active' : ''; ?>"><i class="fas fa-user-plus"></i> Assign Technicians</a>
        <a href="<?php echo SITE_URL; ?>owner/resolution_approvals.php" class="<?php echo $current_page == 'resolution_approvals.php' ? 'active' : ''; ?>"><i class="fas fa-check-double"></i> Resolution Approvals</a>
        <a href="<?php echo SITE_URL; ?>owner/resolved_claims.php" class="<?php echo $current_page == 'resolved_claims.php' ? 'active' : ''; ?>"><i class="fas fa-history"></i> Resolution History</a>
        <a href="<?php echo SITE_URL; ?>owner/reports.php" class="<?php echo $current_page == 'reports.php' ? 'active' : ''; ?>"><i class="fas fa-file-invoice-dollar"></i> Financial Reports</a>
        <hr style="margin: 10px 1.5rem; border: 0; border-top: 1px solid #eee;">
        <a href="<?php echo SITE_URL; ?>owner/products.php" class="<?php echo $current_page == 'products.php' ? 'active' : ''; ?>"><i class="fas fa-box"></i> Product Catalog</a>
        <a href="<?php echo SITE_URL; ?>owner/users.php" class="<?php echo $current_page == 'users.php' ? 'active' : ''; ?>"><i class="fas fa-users-cog"></i> Manage Staff</a>
        <a href="<?php echo SITE_URL; ?>owner/claims_analytics.php" class="<?php echo $current_page == 'claims_analytics.php' ? 'active' : ''; ?>"><i class="fas fa-chart-pie"></i> Claims Analytics</a>
    <?php endif; ?>
</div>
