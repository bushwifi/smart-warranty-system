<?php
// owner/claims_analytics.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'owner') {
    header("Location: ../index.php");
    exit();
}

// Issue categories distribution
$categories = $pdo->query("
    SELECT issue_category, COUNT(*) as volume 
    FROM claims 
    GROUP BY issue_category 
    ORDER BY volume DESC
")->fetchAll();

$page_title = "Claims Analytics";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>
<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-chart-pie"></i> Defect Categorization</h2>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Reported Issue Category</th>
                        <th>Total Claims Filed</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $total = array_sum(array_column($categories, 'volume'));
                        foreach($categories as $cat): 
                            $pct = $total > 0 ? round(($cat['volume'] / $total) * 100, 1) : 0;
                    ?>
                        <tr>
                            <td><strong><?php echo ucfirst(htmlspecialchars($cat['issue_category'])); ?></strong></td>
                            <td><?php echo htmlspecialchars($cat['volume']); ?></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div style="width:100px; background:#e0e0e0; height:10px; border-radius:5px;">
                                        <div style="width:<?php echo $pct; ?>%; background:#667eea; height:100%; border-radius:5px;"></div>
                                    </div>
                                    <span><?php echo $pct; ?>%</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
