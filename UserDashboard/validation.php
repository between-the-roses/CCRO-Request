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
  }
</style>

<div class="modal fade" id="recaptchaModal" tabindex="-1" aria-labelledby="recaptchaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <h2 class="fw-bold mb-4" id="recaptchaLabel">Verify you are human</h2>
      <div class="d-flex justify-content-center mb-3">
        <div id="recaptcha-container">
          <div class="cf-turnstile" data-sitekey="0x4AAAAAABecoQ9SovGYRIe8"></div>
        </div>
      </div>
      <div id="recaptcha-error" class="text-danger mb-2" style="display:none;">Please complete the CAPTCHA.</div>
      <button type="button" class="btn btn-success px-4" id="recaptchaContinueBtn">Continue</button>
    </div>
  </div>
</div>

<!-- Bootstrap JS & Turnstile API -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script>
  // Wait for DOM and Turnstile to be ready
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('recaptchaContinueBtn').addEventListener('click', function() {
      // Cloudflare Turnstile sets the response in this hidden input
      const response = document.querySelector('input[name="cf-turnstile-response"]')?.value;
      if (!response) {
        document.getElementById('recaptcha-error').style.display = 'block';
        return;
      }
      document.getElementById('recaptcha-error').style.display = 'none';
      window.location.href = "../auth/verify.php?type=" + encodeURIComponent(activeType) + "&self=yes";
    });
  });
</script>