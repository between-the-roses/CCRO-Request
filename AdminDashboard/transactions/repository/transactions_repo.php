<?php
// AdminDashboard/transactions/repository/transactions_repo.php

function trx_total_records(PDO $conn): int {
    $sql = 'SELECT COUNT(*)::int AS total
            FROM customer c
            LEFT JOIN "transaction" t ON c.customer_id = t.customer_id';
    return (int)$conn->query($sql)->fetchColumn();
}

function trx_fetch_page(PDO $conn, int $limit, int $offset): array {
    $sql = '
      SELECT
        c.customer_id AS id,
        (\'TXN-\' || c.customer_id::text) AS transaction_no,
        c.fullname AS requesting_party,
        c.contactno AS contact_number,
        c.relationship,
        c.address,
        c.certificate_type::text AS document_type,
        \'Cash\' AS payment_mode,
        c.purpose,
        COALESCE(t.status, \'pending\') AS status,
        COALESCE(t.created_at, c.createdat) AS date_created
      FROM customer c
      LEFT JOIN "transaction" t ON c.customer_id = t.customer_id
      ORDER BY c.createdat DESC
      LIMIT :limit OFFSET :offset';
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function trx_fetch_detail(PDO $conn, int $customerId): ?array {
    $sql = '
      SELECT
        c.*,
        (\'TXN-\' || c.customer_id::text) AS transaction_no,
        COALESCE(t.status, \'pending\') AS status,
        \'Cash\' AS payment_mode,
        COALESCE(t.created_at, c.createdat) AS transaction_date
      FROM customer c
      LEFT JOIN "transaction" t ON c.customer_id = t.customer_id
      WHERE c.customer_id = :id
      LIMIT 1';
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $customerId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * Update/Insert status for a customer’s transaction.
 * Uses UPDATE first; if 0 rows, INSERT (no need to rely on ON CONFLICT target).
 */
function trx_set_status(PDO $conn, int $customerId, string $status): bool {
    $status = strtolower($status);
    if (!in_array($status, ['pending','confirmed','cancelled','processing'], true)) {
        $status = 'pending';
    }

    // Try UPDATE
    $upd = $conn->prepare('
        UPDATE "transaction"
        SET status = :status, updated_at = NOW()
        WHERE customer_id = :id
    ');
    $upd->execute([':status' => $status, ':id' => $customerId]);

    if ($upd->rowCount() > 0) return true;

    // If no row updated, INSERT
    $ins = $conn->prepare('
        INSERT INTO "transaction" (customer_id, status, created_at, updated_at)
        VALUES (:id, :status, NOW(), NOW())
    ');
    return $ins->execute([':id' => $customerId, ':status' => $status]);
}
