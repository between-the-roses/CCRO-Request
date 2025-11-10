<?php
?>
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

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
    background: #f5f5f5;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: none;
    border: none;
    padding: 60px 20px;
  }

  .captcha-container {
    background: transparent;
    max-width: 700px;
    width: 100%;
    margin: 0 auto;
    text-align: center;
  }

  /* Header Section */
  .verification-header {
    margin-bottom: 60px;
  }

  .verification-header h1 {
    font-size: 32px;
    font-weight: 600;
    color: #202124;
    margin-bottom: 8px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  }

  .verification-header p {
    font-size: 15px;
    color: #80868b;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  }

  /* Main Content Section */
  .verification-content {
    margin: 40px 0;
  }

  /* Light Captcha Box */
  .captcha-box-light {
    border: 1px solid #dadce0;
    border-radius: 8px;
    padding: 20px 24px;
    background: #fff;
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
  }

  /* Dark Captcha Box */
  .captcha-box-dark {
    background: #2d2d2d;
    border-radius: 8px;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 40px;
  }

  .captcha-checkbox {
    width: 24px;
    height: 24px;
    min-width: 24px;
    border: 2px solid #dadce0;
    border-radius: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    background: #fff;
    transition: all 0.2s ease;
  }

  .captcha-checkbox:hover {
    border-color: #5f6368;
  }

  .captcha-checkbox.verified {
    background: #1f73e7;
    border-color: #1f73e7;
  }

  .captcha-checkbox.verified::after {
    content: "✓";
    color: white;
    font-size: 14px;
    font-weight: bold;
  }

  .captcha-text {
    text-align: left;
    flex: 1;
  }

  .captcha-text h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 500;
    color: #202124;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  }

  .captcha-text p {
    margin: 2px 0 0 0;
    font-size: 13px;
    color: #80868b;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  }

  /* Dark box text */
  .captcha-box-dark .captcha-text h3 {
    color: #fff;
  }

  .captcha-box-dark .captcha-text p {
    color: #ccc;
  }

  .captcha-box-dark .captcha-checkbox {
    background: #404040;
    border-color: #606060;
  }

  .captcha-logo-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-left: auto;
    flex-shrink: 0;
  }

  .cloudflare-logo {
    width: 28px;
    height: 28px;
    background: #f4a823;
    border-radius: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: bold;
    color: #fff;
  }

  .logo-text {
    text-align: right;
  }

  .logo-text p {
    margin: 0;
    font-size: 11px;
    color: #80868b;
    line-height: 1.3;
  }

  /* Turnstile Container */
  #recaptcha-container {
    background: transparent;
    border: none;
    padding: 0;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: auto;
  }

  .cf-turnstile {
    display: flex;
    justify-content: center;
  }

  /* Message Section */
  .verification-message {
    font-size: 14px;
    color: #5f6368;
    margin-top: 30px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    line-height: 1.6;
  }

  .verification-message strong {
    color: #202124;
    font-weight: 600;
  }

  /* Error/Success Messages */
  #recaptcha-error {
    color: #d32f2f;
    font-weight: 500;
    background: #ffebee;
    border-radius: 4px;
    padding: 12px;
    margin-bottom: 20px;
    display: none;
    font-size: 13px;
    border-left: 4px solid #d32f2f;
  }

  #recaptcha-success {
    color: #0d652d;
    font-weight: 500;
    background: #e6f4ea;
    border-radius: 4px;
    padding: 12px;
    margin-bottom: 20px;
    display: none;
    font-size: 13px;
    border-left: 4px solid #0d652d;
  }

  /* Footer Section */
  .captcha-footer {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 40px;
    padding-top: 24px;
    border-top: 1px solid #dadce0;
    font-size: 12px;
    color: #80868b;
    text-align: center;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  }

  .footer-links {
    display: flex;
    gap: 4px;
    justify-content: center;
  }

  .footer-links a {
    color: #1f73e7;
    text-decoration: none;
    font-size: 12px;
    transition: all 0.2s ease;
  }

  .footer-links a:hover {
    text-decoration: underline;
  }

  .footer-branding {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    font-size: 12px;
    color: #80868b;
  }

  /* Loading Spinner */
  .captcha-spinner {
    display: none;
    width: 20px;
    height: 20px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #1f73e7;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  .captcha-spinner.active {
    display: block;
  }

  /* Success Container */
  .success-container {
    display: none;
    text-align: center;
    padding: 40px 20px;
    animation: fadeIn 0.5s ease;
  }

  .success-icon {
    width: 80px;
    height: 80px;
    background: #0d652d;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    animation: scaleIn 0.5s ease-out;
  }

  .success-icon::after {
    content: "✓";
    color: white;
    font-size: 44px;
    font-weight: bold;
  }

  @keyframes scaleIn {
    0% { transform: scale(0); }
    100% { transform: scale(1); }
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .success-container h2 {
    color: #0d652d;
    font-size: 24px;
    font-weight: 600;
    margin: 0 0 8px 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  }

  .success-container p {
    color: #5f6368;
    font-size: 14px;
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  }

  /* Responsive Design */
  @media (max-width: 600px) {
    #recaptchaModal .modal-content {
      padding: 30px 16px;
    }

    .captcha-container {
      max-width: 100%;
    }

    .verification-header h1 {
      font-size: 24px;
    }

    .verification-header p {
      font-size: 13px;
    }

    .captcha-box-light,
    .captcha-box-dark {
      flex-direction: column;
      align-items: flex-start;
      gap: 12px;
    }

    .captcha-logo-badge {
      margin-left: 0;
      align-self: flex-start;
    }

    .verification-message {
      font-size: 13px;
    }
  }
</style>

<div class="modal fade" id="recaptchaModal" tabindex="-1" aria-labelledby="recaptchaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <div class="captcha-container">
        
        <!-- Verification Header -->
        <div class="verification-header">
          <h1 id="site-name">Transaction Request</h1>
          <p>Checking if the site connection is secure</p>
        </div>

        <!-- Error/Success Messages -->
        <div id="recaptcha-error"></div>
        <div id="recaptcha-success"></div>

        <!-- Verification Form -->
        <div id="verification-form">
          
          <!-- Main Content -->
          <div class="verification-content">
            
            <!-- Light Captcha Box -->
            <div class="captcha-box-light">
              <div class="captcha-checkbox" id="captchaCheckbox"></div>
              <div class="captcha-text">
                <h3>I'm not a robot</h3>
                <p>Powered by Turnstile</p>
              </div>
              <div class="captcha-logo-badge">
                <div class="cloudflare-logo">☁</div>
                <div class="logo-text">
                  <p>Privacy - Terms</p>
                </div>
              </div>
            </div>

            <!-- Dark Captcha Box -->
            <div class="captcha-box-dark">
              <div class="captcha-text">
                <h3>Verify you are human</h3>
              </div>
              <div class="captcha-logo-badge">
                <div class="cloudflare-logo">☁</div>
              </div>
            </div>

            <!-- Turnstile Container -->
            <div id="recaptcha-container">
              <div class="cf-turnstile" 
                   data-sitekey="0x4AAAAAABecoQ9SovGYRIe8"
                   data-callback="onTurnstileCallback"
                   data-error-callback="onTurnstileError">
              </div>
            </div>

            <!-- Loading Spinner -->
            <div class="captcha-spinner" id="captchaSpinner"></div>

          </div>

          <!-- Verification Message -->
          <div class="verification-message">
            <strong id="site-name-msg">CCRO-Request</strong> needs to review the security of your connection before proceeding.
          </div>

          <!-- Footer -->
          <div class="captcha-footer">
            <div class="footer-links">
              <a href="#privacy">Privacy</a>
              <span>-</span>
              <a href="#terms">Terms</a>
            </div>
            <div class="footer-branding">
              <span>Performance & security by</span>
              <strong>Cloudflare</strong>
            </div>
          </div>

        </div>

        <!-- Success Message -->
        <div class="success-container" id="success-container">
          <div class="success-icon"></div>
          <h2>Verification Successful!</h2>
          <p>Redirecting to your request form...</p>
        </div>

      </div>

    </div>
  </div>
</div>

<!-- Bootstrap JS & Turnstile API -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<script>
  let currentCertificateType = 'marriage';
  let turnstileVerified = false;
  let turnstileResponse = null;
  let isReturningUser = false; // Track if user is returning

  function getCertificateType() {
    if (typeof window.activeType !== 'undefined' && window.activeType) {
      return window.activeType;
    }
    if (typeof parent.activeType !== 'undefined' && parent.activeType) {
      return parent.activeType;
    }
    return 'marriage';
  }

  function onTurnstileCallback(token) {
    console.log('✅ Verification successful');
    
    turnstileVerified = true;
    turnstileResponse = token;
    
    document.getElementById('captchaCheckbox').classList.add('verified');
    
    setTimeout(() => {
      showSuccessMessage();
    }, 800);
  }

  function onTurnstileError(error) {
    console.error('❌ Verification error:', error);
    
    turnstileVerified = false;
    turnstileResponse = null;
    
    const errorDiv = document.getElementById('recaptcha-error');
    errorDiv.innerHTML = '❌ Verification failed. Please try again.';
    errorDiv.style.display = 'block';
    
    document.getElementById('captchaCheckbox').classList.remove('verified');
  }

  function showSuccessMessage() {
    document.getElementById('verification-form').style.display = 'none';
    document.getElementById('success-container').style.display = 'block';
    
    setTimeout(() => {
      redirectUser();
    }, 2000);
  }

  function redirectUser() {
    currentCertificateType = getCertificateType();
    
    if (!turnstileVerified) {
      document.getElementById('recaptcha-error').innerHTML = 'Please complete the verification.';
      document.getElementById('recaptcha-error').style.display = 'block';
      return;
    }
    
    let redirectUrl;
    
    // Check if user is returning or new
    if (isReturningUser) {
      // Redirect returning users to authentication.php first
      redirectUrl = `profile/authentication.php?type=${encodeURIComponent(currentCertificateType)}`;
      console.log('🔐 Returning user redirecting to authentication:', redirectUrl);
    } else {
      // Redirect new users to customer.php
      redirectUrl = `customer.php?type=${encodeURIComponent(currentCertificateType)}`;
      console.log('🎯 New user redirecting to:', redirectUrl);
    }
    
    window.location.href = redirectUrl;
  }

  document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Verification modal loaded');
    
    currentCertificateType = getCertificateType();
    const siteName = currentCertificateType.charAt(0).toUpperCase() + currentCertificateType.slice(1) + ' Request';
    
    document.getElementById('site-name').textContent = siteName;
    document.getElementById('site-name-msg').textContent = siteName;
  });

  window.setCertificateType = function(type) {
    window.activeType = type;
    currentCertificateType = type;
  };

  // Function to mark user as returning
  window.setReturningUser = function() {
    isReturningUser = true;
    console.log('👤 Marked as RETURNING user');
  };

  // Function to mark user as new
  window.setNewUser = function() {
    isReturningUser = false;
    console.log('👤 Marked as NEW user');
  };
</script>