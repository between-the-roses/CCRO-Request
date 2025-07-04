<!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Iligan City Civil Registry Online Appointment System</title>

      <!-- Bootstrap & Icons -->
      <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
      />
      <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css"
      />
      <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&display=swap"
        rel="stylesheet"
      />

      <style>
        body {
          font-family: "Merriweather", serif;
          background: url("images/background.png") no-repeat center center fixed;
          background-size: cover;
        }
        .hero-section h1 {
          font-family: 'Merriweather', serif;
          font-weight: 900;
          letter-spacing: -2px;
          font-size: 3.5rem;
          color: #173167;
        }
        .hero-section h2 {
          font-family: 'Inter', sans-serif;
          font-weight: 700;
          color: #086ad8;
          font-size: 2.5rem;
          margin-bottom: 10px;
        }
        .hero-section p {
          font-size: 1.2rem;
          color: #1e2435;
          line-height: 1.7;
          text-align: justify; /* Added for justified text */
        }
        .hero-section .btn-primary {
          background: linear-gradient(90deg,#0ea5e9 60%,#38bdf8 100%);
          color: #fff;
          padding: 0.92rem 2.2rem;
          border: none;
          font-size: 1.15rem;
          font-family: 'Inter', sans-serif;
          border-radius: 30px;
          margin-top: 1.2rem;
          box-shadow: 0 4px 16px rgba(51,178,255,0.12);
          font-weight: 700;
          transition: background 0.18s, box-shadow 0.18s;
        }
        .modal-custom {
          background-color: rgba(0, 0, 0, 0.5);
          backdrop-filter: blur(4px);
        }
        
        .modal-header .bx {
          font-size: 2rem;
          color: #f44336;
          margin-right: 10px;
        }

        .modal-footer .btn-primary {
          padding: 10px 25px;
          font-weight: 600;
        }
      </style>
    </head>

    <body>
      <?php include "UserDashboard/includes/navbar.php"; ?>

      <!-- Hero Section -->
      <section class="hero-section container py-5 text-center text-md-start">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h1 class="display-4 fw-bold">Iligan City Civil Registry</h1>
            <h2 class="text-primary fw-semibold">Online Appointment System</h2>
            <p class="mt-4">
              Schedule and manage your civil registry appointments with ease.<br />
              Stay updated on your requests while ensuring faster, more
              convenient, and hassle-free service — all from the comfort of your home.
            </p>
            <a href="#" class="btn btn-primary mt-3" id="startButton">Request Now</a>
          </div>
          <div class="col-md-6 text-center">
            <img
              src="images/pic.png"
              alt="Registry Illustration"
              class="img-fluid rounded-3"
            />
          </div>
        </div>
      </section>

      <!-- Info Section -->
      <section id="about" class="bg-white py-5">
        <div class="container">
          <h2 class="fw-bold mb-4">
            About LGU Iligan – City Civil Registrar's Office
          </h2>
          <p>
            Civil Registration is a very important aspect of our daily lives. It
            is the fundamental right of every individual to be registered and
            counted.
          </p>
          <p>
            The City Civil Registrar’s Office is mandated to register the birth of
            every citizen so that we will have a name and identity...
          </p>
          <p>
            We register the marriage of a man and a woman... We register the fact
            of death of a person...
          </p>
        </div>
      </section>

      <!-- Contact Section -->
      <section id="contact" class="bg-light py-5">
        <div class="container d-md-flex align-items-center gap-5">
          <div class="col-md-6 mb-4 mb-md-0">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15663.801351747147!2d124.245431!3d8.229861!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32557487e9b77cbd%3A0x54f7c5fdba614f27!2sIligan%20City%20Hall!5e0!3m2!1sen!2sph!4v1712730415455!5m2!1sen!2sph"
              width="100%"
              height="350"
              style="border: 0"
              allowfullscreen=""
              loading="lazy"
              class="rounded shadow"
            ></iframe>
          </div>
          <div class="col-md-6">
            <h2>Contact Us</h2>
            <p>
              <i class="bx bxs-map"></i> Iligan City Hall, Quezon Avenue
              Extension, Palao, Buhanginan Hills, Iligan City, 9200, Lanao del
              Norte
            </p>
            <p><i class="bx bxs-phone"></i> 0927 074 6624</p>
            <p><i class="bx bxs-envelope"></i> civilregistrar.iligan@gmail.com</p>
          </div>
        </div>
      </section>

      <!-- Modal (Inserted) -->
      <div
      class="modal fade"
      id="reminderModal"
      tabindex="-1"
      aria-labelledby="reminderModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <i class="bx bxs-error-circle text-danger"></i>
            <h5 class="modal-title fw-bold" id="reminderModalLabel">
              Important Reminders
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <h5 class="fw-bold">Welcome to the Iligan City Civil Registrar Online Document Request System!</h5>
            <p>The City Civil Registrar’s Office (CCRO) of Iligan City respects your privacy. When using this system, your personal and sensitive information—such as your name, contact number, and civil registry details—is collected only for the purpose of processing your request for:</p>
            <ul>
              <li>Birth Certificate</li>
              <li>Marriage Certificate</li>
              <li>Death Certificate</li>
            </ul>

            <p><strong>🔍 Important Reminders</strong></p>
      <ul>
        <li>Minors are not allowed to book an appointment.</li>
        <li>If you are an authorized representative, you must upload:
          <ul>
            <li>A copy of the Authorization Letter signed by the document owner</li>
            <li>A copy of your valid ID</li>
            <li>A copy of the document owner's valid ID</li>
          </ul>
        </li>
        <li>Please ensure all information provided is complete and accurate to avoid delays.</li>
        <li>Appointment slots are limited and are processed on a first-come, first-served basis.</li>
      </ul>

      <p><strong>👥 Who Can Request Documents?</strong></p>
      <ul>
        <li>The document owner</li>
        <li>Spouse of the document owner</li>
        <li>Daughter or Son</li>
        <li>An Authorized Representative</li>
      </ul>

      <p><strong>🔐 Why We Collect Your Information</strong></p>
      <ul>
        <li>Verify the records in our office</li>
        <li>Confirm your identity as the requester</li>
        <li>Process and deliver your requested document</li>
        <li>Generate non-personal statistical reports to improve our services</li>
      </ul>

      <p><strong>🛡 How We Protect Your Information</strong></p>
      <p>Your data is handled confidentially and is only accessible by trained and authorized CCRO staff. We implement security measures in compliance with the Data Privacy Act of 2012 (RA 10173).</p>

      <p><strong>📄 Your Rights</strong></p>
      <ul>
        <li>Be informed about how your data is handled</li>
        <li>Access or request correction of your personal data</li>
        <li>Withdraw your consent at any time</li>
        <li>File a complaint in case of misuse or mishandling</li>
      </ul>

      <p>For concerns or questions, you may contact our Data Privacy Officer:<br>
      📧 Email: <a href="mailto:ccro@iligan.gov.ph">ccro@iligan.gov.ph</a><br>
      📞 Contact: (063) 221-4308 / 0956-932-2306</p>

      <div class="form-check mt-3">
        <input class="form-check-input" type="checkbox" id="agreeCheckbox" />
        <label class="form-check-label" for="agreeCheckbox">
          By clicking “I Agree”, you confirm that you have read and understood this notice, and consent to the collection and use of your information as stated.
        </label>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" id="nextButton" class="btn btn-primary" disabled>
        Proceed
      </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Modal Script (Inserted) -->
    <script>
      const startButton = document.getElementById("startButton");
      const nextButton = document.getElementById("nextButton");
      const agreeCheckbox = document.getElementById("agreeCheckbox");
      const reminderModal = new bootstrap.Modal(document.getElementById("reminderModal"));

      startButton.addEventListener("click", (e) => {
        e.preventDefault();
        reminderModal.show();
      });

      agreeCheckbox.addEventListener("change", () => {
        nextButton.disabled = !agreeCheckbox.checked;
      });

      nextButton.addEventListener("click", () => {
        reminderModal.hide();
        window.location.href = "UserDashboard/certificatetype.php";
      });
    </script>
    </body>
  </html>