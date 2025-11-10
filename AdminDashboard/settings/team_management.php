<?php
// Include database connection - Fixed path
require_once __DIR__ . '/../../backend/db.php';

// Start session if not already started - Fixed to avoid headers already sent error
if (session_status() == PHP_SESSION_NONE) {
    // Check if headers have already been sent
    if (!headers_sent()) {
        session_start();
    }
}

// Get logged in user info
$loggedInRole = $_SESSION['role'] ?? 'Staff';
$loggedInusername = $_SESSION['username'] ?? '';
$loggedInAdminId = $_SESSION['admin_id'] ?? 0;

/**
 * Fetch all admins from database with debug information
 */
function fetchAllAdmins($conn) {
    try {
        // First, let's check if we have any admins at all
        if ($conn instanceof PDO) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM admin");
            $stmt->execute();
            $adminCount = $stmt->fetchColumn();
            error_log("Total admins in database: " . $adminCount);
            
            $stmt = $conn->prepare("SELECT COUNT(*) FROM authentication");
            $stmt->execute();
            $authCount = $stmt->fetchColumn();
            error_log("Total authentication records: " . $authCount);
            
            // Check the data structure
            $stmt = $conn->prepare("SELECT admin_id, auth_id, role, fullname FROM admin LIMIT 5");
            $stmt->execute();
            $adminSample = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Admin sample: " . json_encode($adminSample));
            
            $stmt = $conn->prepare("SELECT auth_id, username FROM authentication LIMIT 5");
            $stmt->execute();
            $authSample = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Auth sample: " . json_encode($authSample));
            
            // Try the original query with debug
            $stmt = $conn->prepare("
                SELECT 
                    a.admin_id, 
                    a.auth_id as admin_auth_id,
                    auth.auth_id as auth_auth_id,
                    auth.username, 
                    a.role, 
                    a.fullname,
                    a.created_at,
                    a.updated_at
                FROM admin a 
                LEFT JOIN authentication auth ON a.auth_id = auth.auth_id
                WHERE auth.username IS NOT NULL
                ORDER BY a.created_at DESC
            ");
            $stmt->execute();
            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("JOIN query result: " . json_encode($admins));
            
            // If no results, try without WHERE clause
            if (empty($admins)) {
                error_log("No results with WHERE clause, trying without...");
                $stmt = $conn->prepare("
                    SELECT 
                        a.admin_id, 
                        a.auth_id as admin_auth_id,
                        auth.auth_id as auth_auth_id,
                        auth.username, 
                        a.role, 
                        a.fullname,
                        a.created_at,
                        a.updated_at
                    FROM admin a 
                    LEFT JOIN authentication auth ON a.auth_id = auth.auth_id
                    ORDER BY a.created_at DESC
                ");
                $stmt->execute();
                $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
                error_log("JOIN query without WHERE: " . json_encode($admins));
            }
            
            // If still no results, try simple admin query
            if (empty($admins)) {
                error_log("No results from JOIN, trying simple admin query...");
                $stmt = $conn->prepare("
                    SELECT 
                        admin_id, 
                        'No username' as username, 
                        role, 
                        fullname,
                        created_at,
                        updated_at
                    FROM admin 
                    ORDER BY created_at DESC
                ");
                $stmt->execute();
                $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
                error_log("Simple admin query: " . json_encode($admins));
            }
            
        } else {
            // MySQLi version with debug
            $result = mysqli_query($conn, "SELECT COUNT(*) FROM admin");
            $adminCount = mysqli_fetch_row($result)[0];
            error_log("Total admins in database: " . $adminCount);
            
            $result = mysqli_query($conn, "SELECT COUNT(*) FROM authentication");
            $authCount = mysqli_fetch_row($result)[0];
            error_log("Total authentication records: " . $authCount);
            
            // Try the original query
            $result = mysqli_query($conn, "
                SELECT 
                    a.admin_id, 
                    a.auth_id as admin_auth_id,
                    auth.auth_id as auth_auth_id,
                    auth.username, 
                    a.role, 
                    a.fullname,
                    a.created_at,
                    a.updated_at
                FROM admin a 
                LEFT JOIN authentication auth ON a.auth_id = auth.auth_id
                WHERE auth.username IS NOT NULL
                ORDER BY a.created_at DESC
            ");
            $admins = mysqli_fetch_all($result, MYSQLI_ASSOC);
            error_log("JOIN query result: " . json_encode($admins));
            
            // If no results, try without WHERE clause
            if (empty($admins)) {
                error_log("No results with WHERE clause, trying without...");
                $result = mysqli_query($conn, "
                    SELECT 
                        a.admin_id, 
                        a.auth_id as admin_auth_id,
                        auth.auth_id as auth_auth_id,
                        auth.username, 
                        a.role, 
                        a.fullname,
                        a.created_at,
                        a.updated_at
                    FROM admin a 
                    LEFT JOIN authentication auth ON a.auth_id = auth.auth_id
                    ORDER BY a.created_at DESC
                ");
                $admins = mysqli_fetch_all($result, MYSQLI_ASSOC);
                error_log("JOIN query without WHERE: " . json_encode($admins));
            }
            
            // If still no results, try simple admin query
            if (empty($admins)) {
                error_log("No results from JOIN, trying simple admin query...");
                $result = mysqli_query($conn, "
                    SELECT 
                        admin_id, 
                        'No username' as username, 
                        role, 
                        fullname,
                        created_at,
                        updated_at
                    FROM admin 
                    ORDER BY created_at DESC
                ");
                $admins = mysqli_fetch_all($result, MYSQLI_ASSOC);
                error_log("Simple admin query: " . json_encode($admins));
            }
        }
        
        // Add activity data for each admin (only if we have valid admin_id)
        if (!empty($admins)) {
            foreach ($admins as &$admin) {
                if (isset($admin['admin_id'])) {
                    $admin['activity_count'] = getAdminActivityCount($conn, $admin['admin_id']);
                    $admin['last_activity'] = getAdminLastActivity($conn, $admin['admin_id']);
                } else {
                    $admin['activity_count'] = 0;
                    $admin['last_activity'] = 'No activity';
                }
            }
        }
        
        error_log("Final admins array: " . json_encode($admins));
        return $admins;
        
    } catch (Exception $e) {
        error_log("Failed to fetch admins: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        return [];
    }
}

/**
 * Get admin activity count
 */
function getAdminActivityCount($conn, $adminId) {
    try {
        if ($conn instanceof PDO) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM admin_activity_log WHERE admin_id = ?");
            $stmt->execute([$adminId]);
            return $stmt->fetchColumn();
        } else {
            $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM admin_activity_log WHERE admin_id = $adminId");
            $row = mysqli_fetch_assoc($result);
            return $row ? $row['count'] : 0;
        }
    } catch (Exception $e) {
        error_log("Error getting activity count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get admin last activity
 */
function getAdminLastActivity($conn, $adminId) {
    try {
        if ($conn instanceof PDO) {
            $stmt = $conn->prepare("SELECT MAX(created_at) FROM admin_activity_log WHERE admin_id = ?");
            $stmt->execute([$adminId]);
            $lastActivity = $stmt->fetchColumn();
        } else {
            $result = mysqli_query($conn, "SELECT MAX(created_at) as last_activity FROM admin_activity_log WHERE admin_id = $adminId");
            $row = mysqli_fetch_assoc($result);
            $lastActivity = $row ? $row['last_activity'] : null;
        }
        
        if ($lastActivity) {
            $activityTime = new DateTime($lastActivity);
            $now = new DateTime();
            $diff = $now->diff($activityTime);
            
            if ($diff->days == 0) {
                return 'Today';
            } elseif ($diff->days == 1) {
                return 'Yesterday';
            } else {
                return $diff->days . ' days ago';
            }
        }
        
        return 'No activity';
    } catch (Exception $e) {
        error_log("Error getting last activity: " . $e->getMessage());
        return 'Unknown';
    }
}

/**
 * Create new admin
 */
function createAdmin($conn, $username, $password, $role, $fullname) {
    try {
        if ($conn instanceof PDO) {
            $conn->beginTransaction();
        } else {
            mysqli_autocommit($conn, false);
        }
        
        // Check if username already exists
        if ($conn instanceof PDO) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM authentication WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetchColumn() > 0) {
                $conn->rollback();
                return ['success' => false, 'message' => 'username already exists'];
            }
        } else {
            $stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM authentication WHERE username = ?");
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_fetch_row($result)[0] > 0) {
                mysqli_rollback($conn);
                return ['success' => false, 'message' => 'username already exists'];
            }
        }
        
        // Create admin record
        if ($conn instanceof PDO) {
            $stmt = $conn->prepare("INSERT INTO admin (role, fullname, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $stmt->execute([$role, $fullname]);
            $adminId = $conn->lastInsertId();
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO admin (role, fullname, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            mysqli_stmt_bind_param($stmt, "ss", $role, $fullname);
            mysqli_stmt_execute($stmt);
            $adminId = mysqli_insert_id($conn);
        }
        
        if (!$adminId) {
            if ($conn instanceof PDO) {
                $conn->rollback();
            } else {
                mysqli_rollback($conn);
            }
            return ['success' => false, 'message' => 'Failed to create admin record'];
        }
        
        // Create authentication record
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        if ($conn instanceof PDO) {
            $stmt = $conn->prepare("INSERT INTO authentication (auth_id, username, password, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$adminId, $username, $hashedPassword]);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO authentication (auth_id, username, password, created_at) VALUES (?, ?, ?, NOW())");
            mysqli_stmt_bind_param($stmt, "iss", $adminId, $username, $hashedPassword);
            mysqli_stmt_execute($stmt);
        }
        
        // Update admin with auth_id reference
        if ($conn instanceof PDO) {
            $stmt = $conn->prepare("UPDATE admin SET auth_id = ? WHERE admin_id = ?");
            $stmt->execute([$adminId, $adminId]);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE admin SET auth_id = ? WHERE admin_id = ?");
            mysqli_stmt_bind_param($stmt, "ii", $adminId, $adminId);
            mysqli_stmt_execute($stmt);
        }
        
        if ($conn instanceof PDO) {
            $conn->commit();
        } else {
            mysqli_commit($conn);
        }
        
        return ['success' => true, 'message' => 'Admin created successfully'];
        
    } catch (Exception $e) {
        if ($conn instanceof PDO) {
            $conn->rollback();
        } else {
            mysqli_rollback($conn);
        }
        error_log("Error creating admin: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to create admin: ' . $e->getMessage()];
    }
}

/**
 * Update admin
 */
function updateAdmin($conn, $adminId, $username, $role, $fullname) {
    try {
        if ($conn instanceof PDO) {
            $conn->beginTransaction();
        } else {
            mysqli_autocommit($conn, false);
        }
        
        // Update admin record
        if ($conn instanceof PDO) {
            $stmt = $conn->prepare("UPDATE admin SET role = ?, fullname = ?, updated_at = NOW() WHERE admin_id = ?");
            $stmt->execute([$role, $fullname, $adminId]);
            
            // Update authentication username
            $stmt = $conn->prepare("UPDATE authentication SET username = ? WHERE auth_id = ?");
            $stmt->execute([$username, $adminId]);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE admin SET role = ?, fullname = ?, updated_at = NOW() WHERE admin_id = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $role, $fullname, $adminId);
            mysqli_stmt_execute($stmt);
            
            $stmt = mysqli_prepare($conn, "UPDATE authentication SET username = ? WHERE auth_id = ?");
            mysqli_stmt_bind_param($stmt, "si", $username, $adminId);
            mysqli_stmt_execute($stmt);
        }
        
        if ($conn instanceof PDO) {
            $conn->commit();
        } else {
            mysqli_commit($conn);
        }
        
        return ['success' => true, 'message' => 'Admin updated successfully'];
        
    } catch (Exception $e) {
        if ($conn instanceof PDO) {
            $conn->rollback();
        } else {
            mysqli_rollback($conn);
        }
        error_log("Error updating admin: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to update admin: ' . $e->getMessage()];
    }
}

/**
 * Delete admin
 */
function deleteAdmin($conn, $adminId) {
    try {
        if ($conn instanceof PDO) {
            $conn->beginTransaction();
        } else {
            mysqli_autocommit($conn, false);
        }
        
        // Delete authentication record first
        if ($conn instanceof PDO) {
            $stmt = $conn->prepare("DELETE FROM authentication WHERE auth_id = ?");
            $stmt->execute([$adminId]);
        } else {
            $stmt = mysqli_prepare($conn, "DELETE FROM authentication WHERE auth_id = ?");
            mysqli_stmt_bind_param($stmt, "i", $adminId);
            mysqli_stmt_execute($stmt);
        }
        
        // Delete admin record
        if ($conn instanceof PDO) {
            $stmt = $conn->prepare("DELETE FROM admin WHERE admin_id = ?");
            $stmt->execute([$adminId]);
        } else {
            $stmt = mysqli_prepare($conn, "DELETE FROM admin WHERE admin_id = ?");
            mysqli_stmt_bind_param($stmt, "i", $adminId);
            mysqli_stmt_execute($stmt);
        }
        
        if ($conn instanceof PDO) {
            $conn->commit();
        } else {
            mysqli_commit($conn);
        }
        
        return ['success' => true, 'message' => 'Admin deleted successfully'];
        
    } catch (Exception $e) {
        if ($conn instanceof PDO) {
            $conn->rollback();
        } else {
            mysqli_rollback($conn);
        }
        error_log("Error deleting admin: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to delete admin: ' . $e->getMessage()];
    }
}

/**
 * Get role badge class
 */
function getRoleBadgeClass($role) {
    switch (strtolower($role)) {
        case 'admin':
        case 'system admin':
            return 'role-admin';
        case 'staff':
            return 'role-staff';
        case 'department head':
            return 'role-department-head';
        case 'founders':
            return 'role-founders';
        default:
            return 'role-staff';
    }
}

/**
 * Handle AJAX requests
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'create_admin':
            if ($loggedInRole !== 'Admin' && $loggedInRole !== 'System Admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'Staff';
            $fullname = $_POST['fullname'] ?? '';
            
            if ($username && $password && $fullname) {
                $result = createAdmin($conn, $username, $password, $role, $fullname);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
            }
            exit;
            
        case 'update_admin':
            if ($loggedInRole !== 'Admin' && $loggedInRole !== 'System Admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            
            $adminId = $_POST['admin_id'] ?? 0;
            $username = $_POST['username'] ?? '';
            $role = $_POST['role'] ?? 'Staff';
            $fullname = $_POST['fullname'] ?? '';
            
            if ($adminId && $username && $fullname) {
                $result = updateAdmin($conn, $adminId, $username, $role, $fullname);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
            }
            exit;
            
        case 'delete_admin':
            if ($loggedInRole !== 'Admin' && $loggedInRole !== 'System Admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            
            $adminId = $_POST['admin_id'] ?? 0;
            
            if ($adminId && $adminId != $loggedInAdminId) {
                $result = deleteAdmin($conn, $adminId);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cannot delete your own account']);
            }
            exit;
    }
}

// Check if connection exists
if (!$conn) {
    error_log("Database connection not available");
    $admins = [];
} else {
    // Fetch all admins for display
    $admins = fetchAllAdmins($conn);
}

// If this is an AJAX request, don't output HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    exit;
}

// Debug output for browser
echo "<!-- Debug: Found " . count($admins) . " admins -->";
echo "<!-- Debug: Connection type: " . (is_object($conn) ? get_class($conn) : 'Not an object') . " -->";
?>

<link rel="stylesheet" href="settings.css">

<div class="settings-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="settings-header">
            <i class='bx bx-users'></i>
            Team Management
        </h6>
        <?php if ($loggedInRole === 'Admin' || $loggedInRole === 'System Admin'): ?>
        <button class="btn-action btn-add" onclick="showAddAdminModal()">
            <i class='bx bx-plus'></i>
            Add Admin
        </button>
        <?php endif; ?>
    </div>
    
    <div class="table-responsive">
        <table class="team-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Last Activity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($admins)): ?>
                    <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td>
                            <div class="team-item">
                                <div class="team-avatar">
                                    <?= strtoupper(substr($admin['fullname'] ?? $admin['username'] ?? 'A', 0, 1)) ?>
                                </div>
                                <div class="team-info">
                                    <p class="team-name"><?= htmlspecialchars($admin['fullname'] ?? 'Unknown') ?></p>
                                    <p class="team-username"><?= htmlspecialchars($admin['username'] ?? 'No username') ?></p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="team-role <?= getRoleBadgeClass($admin['role']) ?>">
                                <?= htmlspecialchars($admin['role']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="activity-status">
                                <span class="activity-dot"></span>
                                <?= htmlspecialchars($admin['last_activity']) ?>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($loggedInRole === 'Admin' || $loggedInRole === 'System Admin'): ?>
                                    <button class="btn-action btn-edit" onclick="showEditAdminModal(<?= $admin['admin_id'] ?>, '<?= htmlspecialchars($admin['username'] ?? '') ?>', '<?= htmlspecialchars($admin['role']) ?>', '<?= htmlspecialchars($admin['fullname'] ?? '') ?>')">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <?php if ($admin['admin_id'] != $loggedInAdminId): ?>
                                    <button class="btn-action btn-delete" onclick="deleteAdmin(<?= $admin['admin_id'] ?>)">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">
                            <div class="empty-state">
                                <i class='bx bx-users'></i>
                                <p>No admins found</p>
                                <small>Check the browser console and server logs for debug information</small>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAdminModalLabel">Add New Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addAdminForm">
                    <div class="form-group">
                        <label for="addFullname">Full Name</label>
                        <input type="text" class="form-control" id="addFullname" name="fullname" required>
                    </div>
                    <div class="form-group">
                        <label for="addusername">username</label>
                        <input type="username" class="form-control" id="addusername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="addPassword">Password</label>
                        <input type="password" class="form-control" id="addPassword" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="addRole">Role</label>
                        <select class="form-control" id="addRole" name="role" required>
                            <option value="Staff">Staff</option>
                            <option value="Admin">Admin</option>
                            <option value="Department Head">Department Head</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-action btn-add" onclick="addAdmin()">Add Admin</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Admin Modal -->
<div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAdminModalLabel">Edit Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editAdminForm">
                    <input type="hidden" id="editAdminId" name="admin_id">
                    <div class="form-group">
                        <label for="editFullname">Full Name</label>
                        <input type="text" class="form-control" id="editFullname" name="fullname" required>
                    </div>
                    <div class="form-group">
                        <label for="editusername">username</label>
                        <input type="username" class="form-control" id="editusername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="editRole">Role</label>
                        <select class="form-control" id="editRole" name="role" required>
                            <option value="Staff">Staff</option>
                            <option value="Admin">Admin</option>
                            <option value="Department Head">Department Head</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-action btn-edit" onclick="updateAdmin()">Update Admin</button>
            </div>
        </div>
    </div>
</div>

<script>
// Show Add Admin Modal
function showAddAdminModal() {
    document.getElementById('addAdminForm').reset();
    var modal = new bootstrap.Modal(document.getElementById('addAdminModal'));
    modal.show();
}

// Show Edit Admin Modal
function showEditAdminModal(adminId, username, role, fullname) {
    document.getElementById('editAdminId').value = adminId;
    document.getElementById('editFullname').value = fullname;
    document.getElementById('editusername').value = username;
    document.getElementById('editRole').value = role;
    
    var modal = new bootstrap.Modal(document.getElementById('editAdminModal'));
    modal.show();
}

// Add Admin
function addAdmin() {
    const formData = new FormData(document.getElementById('addAdminForm'));
    formData.append('action', 'create_admin');
    
    fetch('settings/team_management.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Admin added successfully');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the admin');
    });
}

// Update Admin
function updateAdmin() {
    const formData = new FormData(document.getElementById('editAdminForm'));
    formData.append('action', 'update_admin');
    
    fetch('settings/team_management.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Admin updated successfully');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the admin');
    });
}

// Delete Admin
function deleteAdmin(adminId) {
    if (confirm('Are you sure you want to delete this admin?')) {
        const formData = new FormData();
        formData.append('action', 'delete_admin');
        formData.append('admin_id', adminId);
        
        fetch('settings/team_management.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Admin deleted successfully');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the admin');
        });
    }
}

// Debug function to check what's happening
console.log('Team management loaded');
console.log('Admin count from PHP:', <?= count($admins) ?>);
</script>
