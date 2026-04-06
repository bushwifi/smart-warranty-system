<?php
// includes/functions.php

function generateWarrantyNumber() {
    return 'WRN-' . strtoupper(uniqid()) . '-' . date('Ymd');
}

function generateClaimNumber() {
    return 'CLM-' . strtoupper(uniqid()) . '-' . date('Ymd');
}

function logActivity($user_id, $action) {
    try {
        $pdo = Database::getInstance()->getConnection();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $action, $ip, $user_agent]);
    } catch (Exception $e) {
        // Silently handle logging errors
    }
}

function createNotification($user_id, $title, $message, $type = 'info') {
    try {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $message, $type]);
    } catch (Exception $e) {
        // Silently handle notification errors
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . SITE_URL . "login.php");
        exit();
    }
}

function checkUserType($allowed_types) {
    if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], $allowed_types)) {
        header("Location: " . SITE_URL . "login.php");
        exit();
    }
}

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}
function validateWarranty($warranty_number) {
    try {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT wr.*, p.product_name, p.warranty_period_months 
            FROM warranty_registrations wr
            JOIN products p ON wr.product_id = p.id
            WHERE wr.warranty_number = ?
        ");
        $stmt->execute([$warranty_number]);
        $warranty = $stmt->fetch();

        if (!$warranty) {
            return ['valid' => false, 'error' => 'Warranty number not found.'];
        }

        $expiry = new DateTime($warranty['warranty_end_date']);
        $now = new DateTime();

        if ($warranty['status'] == 'voided') {
            return ['valid' => false, 'error' => 'Warranty has been voided.', 'details' => $warranty];
        }

        if ($now > $expiry) {
            return ['valid' => false, 'error' => 'Warranty has expired.', 'details' => $warranty];
        }

        if ($warranty['status'] != 'active') {
             return ['valid' => false, 'error' => 'Warranty is currently ' . $warranty['status'] . '.', 'details' => $warranty];
        }

        return ['valid' => true, 'data' => $warranty];
    } catch (Exception $e) {
        return ['valid' => false, 'error' => 'System error during validation.'];
    }
}

function getClaimStatusBadge($status) {
    $colors = [
        'pending' => 'bg-warning',
        'under_review' => 'bg-info',
        'approved' => 'bg-success',
        'rejected' => 'bg-danger',
        'in_progress' => 'bg-primary',
        'completed' => 'bg-success',
        'replaced' => 'bg-purple',
        'pending_refund' => 'bg-warning',
        'refund_denied' => 'bg-danger'
    ];
    $labels = [
        'pending' => 'Pending',
        'under_review' => 'Under Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'in_progress' => 'In Repair',
        'completed' => 'Completed',
        'replaced' => 'Replaced',
        'pending_refund' => 'Refund Approval Required',
        'refund_denied' => 'Refund Denied'
    ];
    $color = $colors[$status] ?? 'bg-secondary';
    $label = $labels[$status] ?? ucfirst($status);
    return "<span class='badge {$color}'>{$label}</span>";
}

/**
 * Fraud Detection Algorithm
 * Analyzes a claim based on historical data and patterns to flag potential technician/claimant fraud.
 */
function analyzeClaimForFraud($claim_id) {
    try {
        $pdo = Database::getInstance()->getConnection();
        $risk_score = 0;
        $red_flags = [];

        // 1. Fetch current claim and product details
        $stmt = $pdo->prepare("
            SELECT c.*, wr.serial_number, wr.user_id as owner_id, cv.technician_id
            FROM claims c
            JOIN warranty_registrations wr ON c.warranty_id = wr.id
            LEFT JOIN claim_verification cv ON c.id = cv.claim_id
            WHERE c.id = ?
        ");
        $stmt->execute([$claim_id]);
        $claim = $stmt->fetch();

        if (!$claim) return ['score' => 0, 'flags' => []];

        // 2. Check for Serial Number Reuse (Same serial, multiple claims in short time)
        $stmtSerial = $pdo->prepare("
            SELECT COUNT(*) FROM claims c
            JOIN warranty_registrations wr ON c.warranty_id = wr.id
            WHERE wr.serial_number = ? AND c.id != ? AND c.created_at > DATE_SUB(NOW(), INTERVAL 6 MONTH)
        ");
        $stmtSerial->execute([$claim['serial_number'], $claim_id]);
        $serialClaims = $stmtSerial->fetchColumn();
        if ($serialClaims > 0) {
            $risk_score += 30 * $serialClaims;
            $red_flags[] = "Serial reuse: {$serialClaims} other claims in 6 months.";
        }

        // 3. Check Technician Approval Bias
        if (isset($claim['technician_id'])) {
            $stmtTech = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN is_valid = 1 THEN 1 ELSE 0 END) as approved FROM claim_verification WHERE technician_id = ?");
            $stmtTech->execute([$claim['technician_id']]);
            $techStats = $stmtTech->fetch();
            if ($techStats['total'] > 10) {
                $rate = ($techStats['approved'] / $techStats['total']) * 100;
                if ($rate > 90) { $risk_score += 20; $red_flags[] = "Tech Approval Rate > 90%"; }
            }
        }

        // 4. Check for Multiple Claims by Same User
        $stmtUser = $pdo->prepare("SELECT COUNT(*) FROM claims WHERE user_id = ? AND id != ? AND created_at > DATE_SUB(NOW(), INTERVAL 3 MONTH)");
        $stmtUser->execute([$claim['owner_id'], $claim_id]);
        $userClaims = $stmtUser->fetchColumn();
        if ($userClaims > 2) {
            $risk_score += 15;
            $red_flags[] = "Customer filed {$userClaims} claims in 3 months.";
        }

        return ['score' => min(100, $risk_score), 'flags' => $red_flags];
    } catch (Exception $e) {
        return ['score' => 0, 'flags' => []];
    }
}

function getFraudRiskBadge($score) {
    if ($score < 30) return '<span class="badge bg-success">Low Risk (' . $score . ')</span>';
    if ($score < 70) return '<span class="badge bg-warning">Medium Risk (' . $score . ')</span>';
    return '<span class="badge bg-danger">High Risk (' . $score . ')</span>';
}
