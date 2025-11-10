<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="transactionModalLabel">Transaction Details</h5>
        <span id="transactionStatus" class="status-pending ms-auto">Pending</span>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="row">
          <!-- Left Column -->
          <div class="col-lg-8">
            <div class="transaction-details">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="transactionId">Transaction ID</label>
                  <input type="text" class="form-control" id="transactionId" readonly>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="transactionNo">Transaction No.</label>
                  <input type="text" class="form-control" id="transactionNo" readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="documentType">Document Type</label>
                  <input type="text" class="form-control" id="documentType" readonly>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="paymentMode">Mode of Payment</label>
                  <input type="text" class="form-control" id="paymentMode" readonly>
                </div>
              </div>

              <div class="section-title">Personal Information</div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="requestingParty">Requesting Party</label>
                  <input type="text" class="form-control" id="requestingParty" readonly>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="contactNumber">Contact Number</label>
                  <input type="text" class="form-control" id="contactNumber" readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="relationship">Relationship</label>
                  <input type="text" class="form-control" id="relationship" readonly>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="address">Address</label>
                  <input type="text" class="form-control" id="address" readonly>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="purpose">Purpose</label>
                  <input type="text" class="form-control" id="purpose" readonly>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="dateCreated">Date Created</label>
                  <input type="text" class="form-control" id="dateCreated" readonly>
                </div>
              </div>

              <!-- <div class="section-title">Actions</div>
              <div class="action-buttons-left">
                <button class="btn-confirm" id="confirmTransaction">
                  <i class='bx bx-check'></i> Confirm Transaction
                </button>
                <button class="btn-cancel" id="cancelTransaction">
                  <i class='bx bx-x'></i> Cancel Transaction
                </button>
              </div> -->
            </div>
          </div>

          <!-- Right Column -->
          <div class="col-lg-4">
            <div class="right-panel">
              <div class="certificate-checklist">
                <div class="certificate-checklist-title">
                  <i class='bx bx-list-check'></i> Certificate Types Available
                </div>
                <div id="certificateTypes">
                  <div class="certificate-category" id="birthCertificates" style="display:none;">
                    <div class="certificate-category-title"><i class='bx bx-child'></i> Birth Certificate Options:</div>
                    <div class="certificate-item">
                      <input type="radio" id="birth-photocopy" name="certificate_type" value="Certified Photocopy - Birth">
                      <label for="birth-photocopy">Certified Photocopy</label>
                    </div>
                    <div class="certificate-item">
                      <input type="radio" id="birth-form1a" name="certificate_type" value="Civil Registry Form No. 1A (Birth Available)">
                      <label for="birth-form1a">Civil Registry Form No. 1A (Birth Available)</label>
                    </div>
                    <div class="certificate-item">
                      <input type="radio" id="birth-form1b" name="certificate_type" value="Civil Registry Form No. 1B (Birth - Not Available)">
                      <label for="birth-form1b">Civil Registry Form No. 1B (Birth - Not Available)</label>
                    </div>
                    <div class="certificate-item">
                      <input type="radio" id="birth-form1c" name="certificate_type" value="Civil Registry Form No. 1C (Birth Destroyed)">
                      <label for="birth-form1c">Civil Registry Form No. 1C (Birth Destroyed)</label>
                    </div>
                  </div>

                  <div class="certificate-category" id="marriageCertificates" style="display:none;">
                    <div class="certificate-category-title"><i class='bx bx-heart'></i> Marriage Certificate Options:</div>
                    <div class="certificate-item">
                      <input type="radio" id="marriage-photocopy" name="certificate_type" value="Certified Photocopy - Marriage">
                      <label for="marriage-photocopy">Certified Photocopy</label>
                    </div>
                    <div class="certificate-item">
                      <input type="radio" id="marriage-form2a" name="certificate_type" value="Civil Registry Form No. 2A (Marriage Available)">
                      <label for="marriage-form2a">Civil Registry Form No. 2A (Marriage Available)</label>
                    </div>
                    <div class="certificate-item">
                      <input type="radio" id="marriage-form2b" name="certificate_type" value="Civil Registry Form No. 2B (Marriage - Not Available)">
                      <label for="marriage-form2b">Civil Registry Form No. 2B (Marriage - Not Available)</label>
                    </div>
                    <div class="certificate-item">
                      <input type="radio" id="marriage-form2c" name="certificate_type" value="Civil Registry Form No. 2C (Marriage Destroyed)">
                      <label for="marriage-form2c">Civil Registry Form No. 2C (Marriage Destroyed)</label>
                    </div>
                  </div>

                  <div class="certificate-category" id="deathCertificates" style="display:none;">
                    <div class="certificate-category-title"><i class='bx bx-cross'></i> Death Certificate Options:</div>
                    <div class="certificate-item">
                      <input type="radio" id="death-photocopy" name="certificate_type" value="Certified Photocopy - Death">
                      <label for="death-photocopy">Certified Photocopy</label>
                    </div>
                    <div class="certificate-item">
                      <input type="radio" id="death-form3a" name="certificate_type" value="Civil Registry Form No. 3A (Death Available)">
                      <label for="death-form3a">Civil Registry Form No. 3A (Death Available)</label>
                    </div>
                    <div class="certificate-item">
                      <input type="radio" id="death-form3b" name="certificate_type" value="Civil Registry Form No. 3B (Death - Not Available)">
                      <label for="death-form3b">Civil Registry Form No. 3B (Death - Not Available)</label>
                    </div>
                    <div class="certificate-item">
                      <input type="radio" id="death-form3c" name="certificate_type" value="Civil Registry Form No. 3C (Death Destroyed)">
                      <label for="death-form3c">Civil Registry Form No. 3C (Death Destroyed)</label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- <div class="remarks-section">
                <label for="transactionRemarks">Remarks</label>
                <textarea id="transactionRemarks" class="form-control" rows="3"></textarea>
              </div> -->

              <div class="transaction-history">
                <div class="transaction-history-title"><i class='bx bx-history'></i> Transaction History</div>
                <div id="transactionHistory"></div>
              </div>
            </div>
          </div>
          <!-- /Right Column -->
        </div>
      </div>
    </div>
  </div>
</div>
