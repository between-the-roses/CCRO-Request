// AdminDashboard/transactions/assets/transactions.js
document.addEventListener('DOMContentLoaded', function () {
  // Search
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      const term = this.value.toLowerCase();
      document.querySelectorAll('.custom-table tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
      });
    });
  }

  // Sidebar toggle (if your navbar/sidebars add the toggle button)
  const sidebarToggle = document.querySelector('.sidebar-toggle');
  const sidebar = document.querySelector('.sidebar');
  const mainContent = document.querySelector('.main-content');
  if (sidebarToggle && sidebar && mainContent) {
    sidebarToggle.addEventListener('click', function (e) {
      e.preventDefault();
      sidebar.classList.toggle('collapsed');
      mainContent.classList.toggle('expanded');
      if (sidebar.classList.contains('collapsed')) localStorage.setItem('sidebarCollapsed', 'true');
      else localStorage.removeItem('sidebarCollapsed');
    });
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
      sidebar.classList.add('collapsed'); mainContent.classList.add('expanded');
    }
  }

  // Transaction modal
  const transactionModal = document.getElementById('transactionModal');
  const transactionIdElements = document.querySelectorAll('.transaction-id');

  transactionIdElements.forEach(el => {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      const data = this.getAttribute('data-transaction');
      if (!data) return;
      try {
        const trx = JSON.parse(data);
        loadTransactionDetails(trx);
        const modal = new bootstrap.Modal(transactionModal);
        modal.show();
      } catch (err) {
        console.error('Parse error', err);
        alert('Error loading transaction details.');
      }
    });
  });

  // Certificate type change listeners
  document.querySelectorAll('input[name="certificate_type"]').forEach(r => {
    r.addEventListener('change', function () {
      // future: persist/change display, etc.
      console.log('Certificate type:', this.value);
    });
  });

  // Buttons: confirm / cancel
  const confirmBtn = document.getElementById('confirmTransaction');
  const cancelBtn  = document.getElementById('cancelTransaction');

  if (confirmBtn) {
    confirmBtn.addEventListener('click', function () {
      const transactionId = document.getElementById('transactionId').value;
      const remarks = document.getElementById('transactionRemarks').value;
      const selectedCert = document.querySelector('input[name="certificate_type"]:checked');
      if (confirm('Confirm this transaction?')) {
        postAction('confirm', transactionId, remarks, selectedCert ? selectedCert.value : '');
      }
    });
  }
  if (cancelBtn) {
    cancelBtn.addEventListener('click', function () {
      const transactionId = document.getElementById('transactionId').value;
      const remarks = document.getElementById('transactionRemarks').value;
      if (confirm('Cancel this transaction?')) {
        postAction('cancel', transactionId, remarks, '');
      }
    });
  }

  // Export buttons
  document.getElementById('exportExcel')?.addEventListener('click', () => console.log('Export Excel'));
  document.getElementById('exportPDF')?.addEventListener('click', () => console.log('Export PDF'));
  document.getElementById('printData')?.addEventListener('click', () => window.print());
});

function postAction(action, id, remarks, certificateType) {
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = `${window.TRANS_BASE}/actions.php`;
  form.innerHTML = `
    <input type="hidden" name="action" value="${action}">
    <input type="hidden" name="transaction_id" value="${id}">
    <input type="hidden" name="remarks" value="${escapeHtml(remarks)}">
    <input type="hidden" name="certificate_type" value="${escapeHtml(certificateType)}">
  `;
  document.body.appendChild(form);
  form.submit();
}

function escapeHtml(s) {
  return (s || '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#039;"}[m]));
}

function loadTransactionDetails(t) {
  document.getElementById('transactionId').value   = t.id || '';
  document.getElementById('transactionNo').value   = t.transaction_no || '';
  document.getElementById('requestingParty').value = t.requesting_party || '';
  document.getElementById('contactNumber').value   = t.contact_number || '';
  document.getElementById('relationship').value    = t.relationship || '';
  document.getElementById('address').value         = t.address || '';
  document.getElementById('documentType').value    = t.document_type || '';
  document.getElementById('paymentMode').value     = t.payment_mode || 'Cash';
  document.getElementById('purpose').value         = t.purpose || '';
  document.getElementById('dateCreated').value     = formatDateTime(t.date_created || new Date());

  const statusEl = document.getElementById('transactionStatus');
  const status = (t.status || 'pending').toLowerCase();
  statusEl.textContent = status.charAt(0).toUpperCase() + status.slice(1);
  statusEl.className = `status-${status}`;

  showCertificateTypes(t.document_type || '');
  loadTransactionHistory(createTransactionHistory(t));
}

function showCertificateTypes(documentType) {
  document.querySelectorAll('.certificate-category').forEach(el => el.style.display = 'none');
  document.querySelectorAll('#certificateTypes input[type="radio"]').forEach(r => r.checked = false);
  const doc = (documentType || '').toLowerCase();
  if (doc.includes('birth') || doc.includes('livebirth')) {
    document.getElementById('birthCertificates').style.display = 'block';
    document.getElementById('birth-photocopy')?.setAttribute('checked', 'checked');
  } else if (doc.includes('marriage')) {
    document.getElementById('marriageCertificates').style.display = 'block';
    document.getElementById('marriage-photocopy')?.setAttribute('checked', 'checked');
  } else if (doc.includes('death')) {
    document.getElementById('deathCertificates').style.display = 'block';
    document.getElementById('death-photocopy')?.setAttribute('checked', 'checked');
  }
}

function createTransactionHistory(t) {
  const arr = [{
    action: 'Transaction Created',
    timestamp: t.date_created || new Date(),
    staff: 'System',
    notes: 'Initial transaction created'
  }];
  const s = (t.status || 'pending').toLowerCase();
  if (s === 'confirmed') {
    arr.push({ action: 'Transaction Confirmed', timestamp: t.date_created || new Date(), staff: 'System Admin', notes: 'Transaction confirmed and processed' });
  } else if (s === 'cancelled') {
    arr.push({ action: 'Transaction Cancelled', timestamp: t.date_created || new Date(), staff: 'System Admin', notes: 'Transaction cancelled by administrator' });
  }
  return arr;
}

function loadTransactionHistory(history) {
  const c = document.getElementById('transactionHistory');
  if (!c) return;
  c.innerHTML = '';
  if (!history.length) {
    c.innerHTML = '<div style="text-align:center;color:#6c757d;font-size:13px;padding:10px;">No history available</div>';
    return;
  }
  history.forEach(h => {
    const div = document.createElement('div');
    div.className = 'history-item';
    const icon = getHistoryIcon(h.action);
    div.innerHTML = `
      <i class='bx ${icon}'></i>
      <div class="history-item-content">
        <div style="font-size:13px;font-weight:600;color:#2c3e50;">${h.action}</div>
        <div style="font-size:12px;color:#6c757d;margin-top:2px;">by <span class="staff-name">${h.staff || 'System'}</span></div>
        <div class="history-timestamp">${formatDateTime(h.timestamp)}</div>
        ${h.notes ? `<div style="font-size:11px;color:#6c757d;margin-top:3px;font-style:italic;">${h.notes}</div>` : ''}
      </div>`;
    c.appendChild(div);
  });
}

function getHistoryIcon(action) {
  switch (action) {
    case 'Transaction Created':   return 'bx-plus-circle';
    case 'Transaction Confirmed': return 'bx-check-circle';
    case 'Transaction Cancelled': return 'bx-x-circle';
    case 'Transaction Updated':   return 'bx-edit-alt';
    default: return 'bx-info-circle';
  }
}

function formatDateTime(dt) {
  const d = new Date(dt);
  if (isNaN(d.getTime())) return '';
  return d.toLocaleString('en-US', { year:'numeric', month:'short', day:'numeric', hour:'2-digit', minute:'2-digit', hour12:true });
}
