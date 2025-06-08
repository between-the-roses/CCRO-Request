<?php
// Include any necessary configuration files
// include 'config.php';

// Sample team data - in a real implementation, this would come from a database
$teams = [
    [
        'name' => 'Raj Chauhan',
        'email' => 'raj@example.com',
        'role' => 'Department Head',
        'last_activity' => 'Today'
    ],
    [
        'name' => 'Sandeep Chauhan',
        'email' => 'sandeep@example.com',
        'role' => 'Founders',
        'last_activity' => 'Today'
    ],
    [
        'name' => 'Aravind Deswal',
        'email' => 'aravind@example.com',
        'role' => 'Admin',
        'last_activity' => 'Today'
    ],
    [
        'name' => 'Jai Chauhan',
        'email' => 'jai@example.com',
        'role' => 'Staff',
        'last_activity' => 'Yesterday'
    ],
    [
        'name' => 'Patricia Maria',
        'email' => 'patricia@example.com',
        'role' => 'Staff',
        'last_activity' => '3 May, 2023'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Settings – CCRO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f9;
    }
    
    /* Main Content Styles */
    .main {
        margin-left: 220px; /* Default margin when sidebar is expanded */
        margin-top: 20px; /* Matches the navbar height */
        padding: 20px;
        background-color: #f8f9fa;
        min-height: calc(100vh - 60px); /* Ensures the content spans the full height */
        transition: margin-left 0.3s ease; /* Smooth transition for content adjustment */
    }

    .sidebar.collapsed ~ .main {
        margin-left: 0; /* Reset margin when sidebar is collapsed */
    }
    
    .user {
      font-size: 16px;
      font-weight: 500;
    }
    
    .content {
      padding: 20px;
    }
    
    .settings-container {
      max-width: 100%;
      margin: 0 auto;
    }
    
    .breadcrumb {
      margin-bottom: 20px;
    }
    
    .settings-card {
      background: white;
      border-radius: 6px;
      padding: 20px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
    
    .settings-header {
      margin-bottom: 20px;
      font-weight: 500;
    }
    
    .team-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background-color: #e9ecef;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-weight: 500;
      margin-right: 10px;
    }
    
    .team-item {
      display: flex;
      align-items: center;
    }
    
    .team-name {
      font-weight: 500;
      margin-bottom: 0;
    }
    
    .team-email {
      font-size: 12px;
      color: #6c757d;
      margin-bottom: 0;
    }
    
    .update-form label {
      font-weight: 500;
      margin-bottom: 5px;
    }
    
    .update-form .form-control {
      margin-bottom: 15px;
    }
    
    .btn-save {
      background-color: #28a745;
      color: white;
      padding: 6px 20px;
    }
    
    /* Custom avatar colors */
    .avatar-1 { background-color: #FF725E; }
    .avatar-2 { background-color: #6E8CD5; }
    .avatar-3 { background-color: #FFA726; }
    .avatar-4 { background-color: #66BB6A; }
    .avatar-5 { background-color: #AB47BC; }
    
    /* Table styles */
    .table th {
      font-weight: 500;
      color: #495057;
      border-top: none;
      font-size: 14px;
    }
    
    .table td {
      vertical-align: middle;
      font-size: 14px;
    }
  </style>
</head>
<body>

<?php include './includes/sidebar.php'; ?>
<?php include './includes/navbar.php'; ?>

<div class="main">
  <div class="content">
    <div class="settings-container">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="settings-header mb-0">Settings</h4>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="includes/home.php" style="text-decoration: none; color: #007bff;">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Settings</li>
          </ol>
        </nav>
      </div>
      
      <!-- Teams Section -->
      <div class="settings-card">
        <h6 class="mb-3">Teams</h6>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Last Activity</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($teams as $index => $team): ?>
                <tr>
                  <td>
                    <div class="team-item">
                      <div class="team-avatar avatar-<?= $index + 1 ?>">
                        <?= strtoupper(substr($team['name'], 0, 1)) ?>
                      </div>
                      <div>
                        <p class="team-name"><?= $team['name'] ?></p>
                        <p class="team-email"><?= $team['email'] ?></p>
                      </div>
                    </div>
                  </td>
                  <td><?= $team['role'] ?></td>
                  <td><?= $team['last_activity'] ?></td>
                  <td class="text-center">
                    <button class="btn btn-sm btn-light">
                      <i class='bx bx-dots-vertical-rounded'></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Update Information Form -->
      <div class="settings-card">
        <h6 class="mb-3">UPDATE INFORMATION</h6>
        <form class="update-form">
          <div class="mb-3">
            <label for="verified-by" class="form-label">Verified by:</label>
            <input type="text" class="form-control" id="verified-by" placeholder="Full Name">
          </div>
          <div class="mb-3">
            <label for="approved-by" class="form-label">Approved by:</label>
            <input type="text" class="form-control" id="approved-by" placeholder="Full Name">
          </div>
          <div class="d-flex justify-content-start">
            <button type="submit" class="btn btn-save">Save</button>
          </div>
        </form>
      </div>
      
    </div>
  </div>
</div>

<script>
  // Team member search functionality
  document.addEventListener('DOMContentLoaded', function() {
    // Add search input
    const teamCard = document.querySelector('.settings-card');
    const searchDiv = document.createElement('div');
    searchDiv.className = 'mb-3';
    searchDiv.innerHTML = `
      <div class="input-group mb-3">
        <span class="input-group-text"><i class='bx bx-search'></i></span>
        <input type="text" class="form-control" id="team-search" placeholder="Search team members...">
      </div>
    `;
    teamCard.insertBefore(searchDiv, teamCard.querySelector('.table-responsive'));
    
    // Search functionality
    const searchInput = document.getElementById('team-search');
    searchInput.addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();
      const teamRows = document.querySelectorAll('.table tbody tr');
      
      teamRows.forEach(row => {
        const name = row.querySelector('.team-name').textContent.toLowerCase();
        const email = row.querySelector('.team-email').textContent.toLowerCase();
        const role = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        
        if (name.includes(searchTerm) || email.includes(searchTerm) || role.includes(searchTerm)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
    
    // Add edit functionality
    const editButtons = document.querySelectorAll('.table .btn-light');
    editButtons.forEach(button => {
      button.setAttribute('title', 'Edit Member');
      button.addEventListener('click', function() {
        const row = this.closest('tr');
        const name = row.querySelector('.team-name').textContent;
        const email = row.querySelector('.team-email').textContent;
        const role = row.querySelector('td:nth-child(2)').textContent;
        
        // Create modal for editing
        const modalHtml = `
          <div class="modal fade" id="editMemberModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Team Member</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form id="edit-member-form">
                    <div class="mb-3">
                      <label for="edit-name" class="form-label">Name</label>
                      <input type="text" class="form-control" id="edit-name" value="${name}">
                    </div>
                    <div class="mb-3">
                      <label for="edit-email" class="form-label">Email</label>
                      <input type="email" class="form-control" id="edit-email" value="${email}">
                    </div>
                    <div class="mb-3">
                      <label for="edit-role" class="form-label">Role</label>
                      <select class="form-select" id="edit-role">
                        <option value="Department Head" ${role === 'Department Head' ? 'selected' : ''}>Department Head</option>
                        <option value="Founders" ${role === 'Founders' ? 'selected' : ''}>Founders</option>
                        <option value="Admin" ${role === 'Admin' ? 'selected' : ''}>Admin</option>
                        <option value="Staff" ${role === 'Staff' ? 'selected' : ''}>Staff</option>
                      </select>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary" id="save-member-btn">Save changes</button>
                </div>
              </div>
            </div>
          </div>
        `;
        
        // Check if modal already exists and remove it
        const existingModal = document.getElementById('editMemberModal');
        if (existingModal) {
          existingModal.remove();
        }
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Initialize and show modal
        const modal = new bootstrap.Modal(document.getElementById('editMemberModal'));
        modal.show();
        
        // Handle save button
        document.getElementById('save-member-btn').addEventListener('click', function() {
          const newName = document.getElementById('edit-name').value;
          const newEmail = document.getElementById('edit-email').value;
          const newRole = document.getElementById('edit-role').value;
          
          // Update the row (in a real app, this would also update the database)
          row.querySelector('.team-name').textContent = newName;
          row.querySelector('.team-email').textContent = newEmail;
          row.querySelector('td:nth-child(2)').textContent = newRole;
          row.querySelector('.team-avatar').textContent = newName.charAt(0).toUpperCase();
          
          // Close modal
          modal.hide();
          
          // Show success message
          showToast('Team member updated successfully!');
        });
      });
    });
    
    // Form validation for Update Information
    const updateForm = document.querySelector('.update-form');
    updateForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const verifiedBy = document.getElementById('verified-by').value;
      const approvedBy = document.getElementById('approved-by').value;
      
      if (!verifiedBy || !approvedBy) {
        showToast('Please fill in all fields', 'danger');
        return;
      }
      
      // In a real app, this would submit the data to the server
      showToast('Information updated successfully!');
      
      // Clear form
      this.reset();
    });
  });
  
  // Toast notification function
  function showToast(message, type = 'success') {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
      toastContainer = document.createElement('div');
      toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
      document.body.appendChild(toastContainer);
    }
    
    // Create toast
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.id = toastId;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">
          ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Initialize and show toast
    const bsToast = new bootstrap.Toast(toast, {
      autohide: true,
      delay: 3000
    });
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
      toast.remove();
    });
  }

  // Add Bootstrap JS dependency
  if (!document.getElementById('bootstrap-js')) {
    const script = document.createElement('script');
    script.id = 'bootstrap-js';
    script.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js';
    document.body.appendChild(script);
  }
</script>
</body>
</html>