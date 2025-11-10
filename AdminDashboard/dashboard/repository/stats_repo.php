<?php
// AdminDashboard/dashboard/repository/stats_repo.php

/**
 * PostgreSQL-specific repository for dashboard statistics
 */

function certCounts(PDO $conn): array {
    try {
        $qTotalBirth = "
            SELECT COUNT(*)::int AS total
            FROM customer c
            WHERE LOWER(c.certificate_type::text) LIKE '%birth%' 
               OR LOWER(c.certificate_type::text) LIKE '%livebirth%'
        ";
        $qBirthDone = "
            SELECT COUNT(*)::int AS completed
            FROM customer c
            LEFT JOIN transaction t ON c.customer_id = t.customer_id
            WHERE (LOWER(c.certificate_type::text) LIKE '%birth%' 
                OR LOWER(c.certificate_type::text) LIKE '%livebirth%')
              AND t.status = 'confirmed'
        ";

        $qTotalMarriage = "
            SELECT COUNT(*)::int AS total
            FROM customer c
            WHERE LOWER(c.certificate_type::text) LIKE '%marriage%'
        ";
        $qMarriageDone = "
            SELECT COUNT(*)::int AS completed
            FROM customer c
            LEFT JOIN transaction t ON c.customer_id = t.customer_id
            WHERE LOWER(c.certificate_type::text) LIKE '%marriage%'
              AND t.status = 'confirmed'
        ";

        $qTotalDeath = "
            SELECT COUNT(*)::int AS total
            FROM customer c
            WHERE LOWER(c.certificate_type::text) LIKE '%death%'
        ";
        $qDeathDone = "
            SELECT COUNT(*)::int AS completed
            FROM customer c
            LEFT JOIN transaction t ON c.customer_id = t.customer_id
            WHERE LOWER(c.certificate_type::text) LIKE '%death%'
              AND t.status = 'confirmed'
        ";

        $birthCount       = (int)$conn->query($qTotalBirth)->fetchColumn();
        $birthCompleted   = (int)$conn->query($qBirthDone)->fetchColumn();
        $marriageCount    = (int)$conn->query($qTotalMarriage)->fetchColumn();
        $marriageCompleted= (int)$conn->query($qMarriageDone)->fetchColumn();
        $deathCount       = (int)$conn->query($qTotalDeath)->fetchColumn();
        $deathCompleted   = (int)$conn->query($qDeathDone)->fetchColumn();

        return compact('birthCount','birthCompleted','marriageCount','marriageCompleted','deathCount','deathCompleted');
    } catch (PDOException $e) {
        error_log("Error in certCounts: " . $e->getMessage());
        return [
            'birthCount' => 0, 'birthCompleted' => 0,
            'marriageCount' => 0, 'marriageCompleted' => 0,
            'deathCount' => 0, 'deathCompleted' => 0
        ];
    }
}

function statusCounts(PDO $conn): array {
    try {
        $sql = "
            SELECT COALESCE(t.status, 'pending') AS status, COUNT(*)::int AS count
            FROM customer c
            LEFT JOIN transaction t ON c.customer_id = t.customer_id
            GROUP BY COALESCE(t.status, 'pending')
        ";
        $rows = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $pending = $confirmed = $cancelled = $processing = 0;
        foreach ($rows as $r) {
            switch ($r['status']) {
                case 'pending':    $pending    = (int)$r['count']; break;
                case 'confirmed':  $confirmed  = (int)$r['count']; break;
                case 'cancelled':  $cancelled  = (int)$r['count']; break;
                case 'processing': $processing = (int)$r['count']; break;
            }
        }
        $total = (int)$conn->query("SELECT COUNT(*) FROM customer")->fetchColumn();

        return [
            'totalRequests'    => $total,
            'pendingRequests'  => $pending,
            'confirmedRequests'=> $confirmed,
            'cancelledRequests'=> $cancelled,
            'processingRequests'=> $processing,
        ];
    } catch (PDOException $e) {
        error_log("Error in statusCounts: " . $e->getMessage());
        return [
            'totalRequests' => 0, 'pendingRequests' => 0,
            'confirmedRequests' => 0, 'cancelledRequests' => 0,
            'processingRequests' => 0
        ];
    }
}

function timeCounts(PDO $conn): array {
    try {
        // today
        $today = (int)$conn->query("
            SELECT COUNT(*) FROM customer 
            WHERE createdat::date = CURRENT_DATE
        ")->fetchColumn();

        // current week (ISO week starts Monday)
        $week = (int)$conn->query("
            SELECT COUNT(*) FROM customer
            WHERE date_trunc('week', createdat) = date_trunc('week', CURRENT_DATE)
        ")->fetchColumn();

        // current month
        $month = (int)$conn->query("
            SELECT COUNT(*) FROM customer
            WHERE date_trunc('month', createdat) = date_trunc('month', CURRENT_DATE)
        ")->fetchColumn();

        // current year
        $year = (int)$conn->query("
            SELECT COUNT(*) FROM customer
            WHERE date_part('year', createdat) = date_part('year', CURRENT_DATE)
        ")->fetchColumn();

        return [
            'todayRequests' => $today,
            'weekRequests'  => $week,
            'monthRequests' => $month,
            'yearRequests'  => $year,
        ];
    } catch (PDOException $e) {
        error_log("Error in timeCounts: " . $e->getMessage());
        return [
            'todayRequests' => 0, 'weekRequests' => 0,
            'monthRequests' => 0, 'yearRequests' => 0
        ];
    }
}

function avgProcessingHours(PDO $conn): float {
    try {
        $sql = "
            SELECT AVG(EXTRACT(EPOCH FROM (t.updated_at - c.createdat))/3600.0) AS hrs
            FROM customer c
            JOIN transaction t ON c.customer_id = t.customer_id
            WHERE t.status = 'confirmed' AND t.updated_at IS NOT NULL
        ";
        $row = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
        return round((float)($row['hrs'] ?? 0), 1);
    } catch (PDOException $e) {
        error_log("Error in avgProcessingHours: " . $e->getMessage());
        return 0.0;
    }
}

function recentRequests(PDO $conn, int $limit = 10): array {
    try {
        $sql = "
            SELECT 
                c.customer_id,
                c.fullname,
                c.certificate_type::text AS certificate_type,
                c.createdat,
                COALESCE(t.status, 'pending') AS status,
                t.updated_at
            FROM customer c
            LEFT JOIN transaction t ON c.customer_id = t.customer_id
            ORDER BY c.createdat DESC
            LIMIT :lim
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error in recentRequests: " . $e->getMessage());
        return [];
    }
}

function monthlyStats(PDO $conn): array {
    try {
        $sql = "
            SELECT 
                to_char(createdat, 'YYYY-MM') AS month,
                COUNT(*)::int AS total_requests,
                SUM(CASE WHEN LOWER(certificate_type::text) LIKE '%birth%' 
                          OR LOWER(certificate_type::text) LIKE '%livebirth%' THEN 1 ELSE 0 END)::int AS birth_count,
                SUM(CASE WHEN LOWER(certificate_type::text) LIKE '%marriage%' THEN 1 ELSE 0 END)::int AS marriage_count,
                SUM(CASE WHEN LOWER(certificate_type::text) LIKE '%death%' THEN 1 ELSE 0 END)::int AS death_count
            FROM customer
            WHERE createdat >= (CURRENT_DATE - INTERVAL '12 months')
            GROUP BY month
            ORDER BY month DESC
        ";
        return $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error in monthlyStats: " . $e->getMessage());
        return [];
    }
}

function dailyStats(PDO $conn): array {
    try {
        $sql = "
            SELECT 
                createdat::date AS date,
                COUNT(*)::int AS total_requests,
                SUM(CASE WHEN LOWER(certificate_type::text) LIKE '%birth%' 
                          OR LOWER(certificate_type::text) LIKE '%livebirth%' THEN 1 ELSE 0 END)::int AS birth_count,
                SUM(CASE WHEN LOWER(certificate_type::text) LIKE '%marriage%' THEN 1 ELSE 0 END)::int AS marriage_count,
                SUM(CASE WHEN LOWER(certificate_type::text) LIKE '%death%' THEN 1 ELSE 0 END)::int AS death_count
            FROM customer
            WHERE createdat >= (CURRENT_DATE - INTERVAL '30 days')
            GROUP BY date
            ORDER BY date DESC
        ";
        return $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error in dailyStats: " . $e->getMessage());
        return [];
    }
}

function calendarEvents(PDO $conn): array {
    try {
        $sql = "
            SELECT
                createdat::date AS date,
                COUNT(*)::int AS count,
                string_agg(DISTINCT certificate_type::text, ', ') AS types
            FROM customer
            WHERE date_trunc('month', createdat) = date_trunc('month', CURRENT_DATE)
            GROUP BY date
            ORDER BY date
        ";
        $rows = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $map = [];
        foreach ($rows as $r) {
            $map[$r['date']] = ['count' => (int)$r['count'], 'types' => $r['types']];
        }
        return $map;
    } catch (PDOException $e) {
        error_log("Error in calendarEvents: " . $e->getMessage());
        return [];
    }
}
