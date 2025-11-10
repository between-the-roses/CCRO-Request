<?php
include '../../backend/archiving.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../../admin.php');
    exit();
}

try {
    // Fetch records from database using PDO
    $query_livebirth = "SELECT * FROM livebirth ORDER BY lastname ASC";
    $query_marriage = "SELECT * FROM marriage ORDER BY groom_lastname ASC";
    $query_death = "SELECT * FROM death ORDER BY lastname ASC";

    $result_livebirth = $conn->query($query_livebirth)->fetchAll(PDO::FETCH_ASSOC);
    $result_marriage = $conn->query($query_marriage)->fetchAll(PDO::FETCH_ASSOC);
    $result_death = $conn->query($query_death)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $result_livebirth = [];
    $result_marriage = [];
    $result_death = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archives Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <?php include '../../includes/sidebar.php'; ?>
        <?php include '../../includes/navbar.php'; ?>

        <h2>Archives Dashboard</h2>
        
        <!-- Livebirth Records -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Livebirth Records (<?php echo count($result_livebirth); ?>)</h4>
            </div>
            <div class="card-body">
                <?php if (count($result_livebirth) > 0): ?>
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Date of Birth</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($result_livebirth as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['lastname'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['firstname'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['date_of_birth'] ?? ''); ?></td>
                                    <td>
                                        <a href="view_livebirth.php?id=<?php echo $row['id'] ?? ''; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No livebirth records found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Marriage Records -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Marriage Records (<?php echo count($result_marriage); ?>)</h4>
            </div>
            <div class="card-body">
                <?php if (count($result_marriage) > 0): ?>
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Groom's Name</th>
                                <th>Bride's Name</th>
                                <th>Date of Marriage</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($result_marriage as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(($row['groom_lastname'] ?? '') . ', ' . ($row['groom_firstname'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars(($row['bride_lastname'] ?? '') . ', ' . ($row['bride_firstname'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars($row['date_of_marriage'] ?? ''); ?></td>
                                    <td>
                                        <a href="view_marriage.php?id=<?php echo $row['id'] ?? ''; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No marriage records found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Death Records -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Death Records (<?php echo count($result_death); ?>)</h4>
            </div>
            <div class="card-body">
                <?php if (count($result_death) > 0): ?>
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Date of Death</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($result_death as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['lastname'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['firstname'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['date_of_death'] ?? ''); ?></td>
                                    <td>
                                        <a href="view_death.php?id=<?php echo $row['id'] ?? ''; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No death records found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>