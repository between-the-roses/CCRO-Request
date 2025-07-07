<?php
?>
<style>
  #recaptchaModal .modal-dialog {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    max-width: 100vw;
  }
  #recaptchaModal .modal-content {
    width: 100%;
    min-height: 100vh;
    border-radius: 0;
    background: linear-gradient(120deg, #0ea5e9 60%, #38bdf8 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: none;
    border: none;
  }
  #recaptchaModal h2 {
    color: #fff;
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 2rem;
    letter-spacing: 1px;
  }
  #recaptcha-container {
    background: #fff;
    border-radius: 16px;
    padding: 32px 24px;
    box-shadow: 0 8px 32px rgba(14, 55, 54, 0.15);
    display: inline-block;
    margin-bottom: 1.5rem;
  }
  #recaptcha-error {
    color: #dc3545;
    font-weight: 500;
    background: #fff3f3;
    border-radius: 8px;
    padding: 8px 16px;
    margin-bottom: 1.5rem;
    display: none;
  }
  #recaptcha-success {
    color: #28a745;
    font-weight: 500;
    background: #f0fff4;
    border-radius: 8px;
    padding: 8px 16px;
    margin-bottom: 1.5rem;
    display: none;
  }
  #recaptchaModal .btn-success {
    font-size: 1.2rem;
    font-weight: 600;
    padding: 12px 48px;
    border-radius: 30px;
    background: linear-gradient(90deg, #22d3ee 60%, #0ea5e9 100%);
    border: none;
    color: #fff;
    box-shadow: 0 4px 16px rgba(14, 55, 54, 0.10);
    transition: background 0.2s;
    z-index: 2;
    position: relative;
  }
  #recaptchaModal .btn-success:hover {
    background: linear-gradient(90deg, #0ea5e9 60%, #38bdf8 100%);
    color: #fff;
  }
  #recaptchaModal .btn-success:disabled {
    background: #6c757d;
    opacity: 0.6;
    cursor: not-allowed;
  }
  .debug-info {
    background: rgba(255, 255, 255, 0.9);
    color: #333;
    padding: 10px;
    border-radius: 8px;
    font-family: monospace;
    font-size: 12px;
    margin-bottom: 1rem;
    text-align: left;
  }
  @media (max-width: 600px) {
    #recaptchaModal .modal-content {
      padding: 1rem !important;
    }
    #recaptcha-container {
      padding: 16px 4px;
    }
    #recaptchaModal h2 {
      font-size: 1.3rem;
    }
    #recaptchaModal .btn-success {
      font-size: 1rem;
      padding: 10px 24px;
    }
    .debug-info {
      font-size: 10px;
    }
  }
</style>

<div class="modal fade" id="recaptchaModal" tabindex="-1" aria-labelledby="recaptchaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <h2 class="fw-bold mb-4" id="recaptchaLabel">Verify you are human</h2>
      
      <!-- Debug Information -->
      <div class="debug-info">
        <strong>🔍 Debug Info:</strong><br>
        Certificate Type: <span id="debug-cert-type">Loading...</span><br>
        Turnstile Status: <span id="debug-turnstile-status">Not loaded</span><br>
        Will redirect to: <span id="debug-redirect-url">Calculating...</span>
      </div>
      
      <div class="d-flex justify-content-center mb-3">
        <div id="recaptcha-container">
          <div class="cf-turnstile" 
               data-sitekey="0x4AAAAAABecoQ9SovGYRIe8"
               data-callback="onTurnstileCallback"
               data-error-callback="onTurnstileError">
          </div>
        </div>
      </div>
      
      <div id="recaptcha-error" class="text-danger mb-2" style="display:none;">
        Please complete the CAPTCHA verification.
      </div>
      
      <div id="recaptcha-success" class="text-success mb-2" style="display:none;">
        ✅ Verification successful! Ready to continue.
      </div>
      
      <button type="button" class="btn btn-success px-4" id="recaptchaContinueBtn" disabled>
        Continue
      </button>
    </div>
  </div>
</div>

<!-- Bootstrap JS & Turnstile API -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

<script>
  // Global variables
  let currentCertificateType = 'marriage'; // Default
  let turnstileVerified = false;
  let turnstileResponse = null;

  // Get certificate type from parent window's global variable
  function getCertificateType() {
    // Try multiple ways to get the certificate type
    if (typeof window.activeType !== 'undefined' && window.activeType) {
      return window.activeType;
    }
    if (typeof parent.activeType !== 'undefined' && parent.activeType) {
      return parent.activeType;
    }
    if (typeof activeType !== 'undefined' && activeType) {
      return activeType;
    }
    return 'marriage'; // fallback
  }

  // Update debug information
  function updateDebugInfo() {
    currentCertificateType = getCertificateType();
    document.getElementById('debug-cert-type').textContent = currentCertificateType;
    document.getElementById('debug-turnstile-status').textContent = turnstileVerified ? 'Verified ✅' : 'Pending ⏳';
    document.getElementById('debug-redirect-url').textContent = `customer.php?type=${currentCertificateType}`;
  }

  // Turnstile success callback
  function onTurnstileCallback(token) {
    console.log('✅ Turnstile verification successful');
    console.log('🎯 Certificate type:', getCertificateType());
    
    turnstileVerified = true;
    turnstileResponse = token;
    
    // Enable continue button
    const continueBtn = document.getElementById('recaptchaContinueBtn');
    continueBtn.disabled = false;
    continueBtn.textContent = 'Continue ✓';
    
    // Show success message
    document.getElementById('recaptcha-success').style.display = 'block';
    document.getElementById('recaptcha-error').style.display = 'none';
    
    updateDebugInfo();
  }

  // Turnstile error callback
  function onTurnstileError(error) {
    console.error('❌ Turnstile error:', error);
    
    turnstileVerified = false;
    turnstileResponse = null;
    
    document.getElementById('recaptcha-error').innerHTML = 'Verification failed. Please try again.';
    document.getElementById('recaptcha-error').style.display = 'block';
    document.getElementById('recaptcha-success').style.display = 'none';
    
    const continueBtn = document.getElementById('recaptchaContinueBtn');
    continueBtn.disabled = true;
    continueBtn.textContent = 'Continue';
    
    updateDebugInfo();
  }

  // DOM ready
  document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Validation modal loaded');
    
    // Initial debug update
    updateDebugInfo();
    
    // Update debug info every 2 seconds in case certificate type changes
    setInterval(updateDebugInfo, 2000);
    
    // Continue button click handler
    document.getElementById('recaptchaContinueBtn').addEventListener('click', function() {
      currentCertificateType = getCertificateType();
      
      console.log('🔍 Continue button clicked');
      console.log('🎯 Final certificate type:', currentCertificateType);
      console.log('✅ Turnstile verified:', turnstileVerified);
      
      if (!turnstileVerified) {
        // Try to get the response from the hidden input as fallback
        const hiddenResponse = document.querySelector('input[name="cf-turnstile-response"]')?.value;
        if (hiddenResponse) {
          turnstileResponse = hiddenResponse;
          turnstileVerified = true;
          console.log('📋 Got turnstile response from hidden input');
        } else {
          document.getElementById('recaptcha-error').style.display = 'block';
          document.getElementById('recaptcha-error').innerHTML = 'Please complete the CAPTCHA verification.';
          return;
        }
      }
      
      document.getElementById('recaptcha-error').style.display = 'none';
      
      // Show loading state
      const continueBtn = document.getElementById('recaptchaContinueBtn');
      continueBtn.disabled = true;
      continueBtn.textContent = 'Redirecting...';
      
      // Hide modal
      const modal = bootstrap.Modal.getInstance(document.getElementById('recaptchaModal'));
      if (modal) {
        modal.hide();
      }
      
      // Redirect directly to customer.php with certificate type
      const redirectUrl = `customer.php?type=${encodeURIComponent(currentCertificateType)}`;
      console.log('🎯 Redirecting to:', redirectUrl);
      
      // Small delay to ensure modal is hidden
      setTimeout(() => {
        window.location.href = redirectUrl;
      }, 500);
    });
  });

  // Function that can be called from parent window to set certificate type
  function setCertificateType(type) {
    window.activeType = type;
    currentCertificateType = type;
    updateDebugInfo();
    console.log('📝 Certificate type updated to:', type);
  }

  // Make function available globally
  window.setCertificateType = setCertificateType;
  
  // Also make it available to parent window
  if (window.parent !== window) {
    window.parent.setCertificateType = setCertificateType;
  }
</script>