<?php
session_start();

$type = isset($_GET['type']) ? $_GET['type'] : null;
$self = isset($_GET['self']) ? $_GET['self'] : null;

echo "<pre>";
echo "Certificate type: " . htmlspecialchars($type) . "\n";
echo "Requestor status: " . htmlspecialchars($self) . "\n";
echo "</pre>";

if (is_string($type) && is_string($self)) {
    $redirects = [
        'livebirth' => [
            'yes' => '../UserDashboard/forms/livebirth.php',
            'no'  => '../UserDashboard/forms/livebirth.php'
        ],
        'marriage' => [
            'yes' => '../UserDashboard/forms/marriage.php',
            'no'  => '../UserDashboard/forms/marriage.php'
        ],
        'death' => [
            'yes' => '../UserDashboard/forms/death.php',
            'no'  => '../UserDashboard/forms/death.php'
        ]
    ];
    if (isset($redirects[$type][$self])) {
        echo "<p>Verification successful! Redirecting...</p>";
        $redirectUrl = htmlspecialchars($redirects[$type][$self], ENT_QUOTES, 'UTF-8');
        echo "<script>setTimeout(function(){ window.location.href = '{$redirectUrl}'; }, 2000);</script>";
        exit;
    }
}
?>

<!-- <h2>Verify your identity</h2>
<p>Enter your phone number to receive an OTP:</p>

<input type="text" id="phone" placeholder="+63XXXXXXXXXX" />
<div id="recaptcha-container"></div>
<button onclick="sendOTP()">Send OTP</button>

<p>Enter the OTP:</p>
<input type="text" id="otp" placeholder="Enter OTP" />
<button onclick="verifyOTP()">Verify</button> -->

<!-- Firebase SDKs -->
<script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js"></script>

<!-- <script src="https://accounts.google.com/gsi/client" async defer></script>
<div id="g_id_onload"
     data-client_id="YOUR_GOOGLE_CLIENT_ID"
     data-callback="handleGoogleCallback"
     data-auto_prompt="false">
</div>
<div class="g_id_signin"
     data-type="standard"
     data-size="large"
     data-theme="outline"
     data-text="sign_in_with"
     data-shape="rectangular"
     data-logo_alignment="left">
</div>
<script>
  function handleGoogleCallback(response) {
    // Handle the Google sign-in response here
    console.log("Google sign-in response: ", response);
  }
</script> -->

<script>
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyCZD03Ub1BhAjAwbbVENLmQQi98_v_MDXA",
  authDomain: "thesis-516a1.firebaseapp.com",
  projectId: "thesis-516a1",
  storageBucket: "thesis-516a1.firebasestorage.app",
  messagingSenderId: "872820770050",
  appId: "1:872820770050:web:2243105b4ca772ea442e1b",
  measurementId: "G-VRPJ3VC6G9"
};
firebase.initializeApp(firebaseConfig);

// Initialize Firebase Authentication and get a reference to the service
const auth = firebase.auth();
// Initialize Recaptcha
const recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
  'size': 'invisible',
  'callback': function(response) {
    // reCAPTCHA solved, allow signInWithPhoneNumber.
  },
  'expired-callback': function() {
    // Response expired. Ask user to solve reCAPTCHA again.
  }
});
// // Initialize variables
// let verificationId = null;

// // Function to send OTP
// function sendOTP() {
//   const phone = document.getElementById("phone").value;
//   const appVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
//     size: 'invisible'
//   });

//   firebase.auth().signInWithPhoneNumber(phone, appVerifier)
//     .then(confirmationResult => {
//       verificationId = confirmationResult.verificationId;
//       alert("OTP sent!");
//     }).catch(error => {
//       alert("Error sending OTP: " + error.message);
//     });
// }

// function verifyOTP() {
//   const otp = document.getElementById("otp").value;
//   const credential = firebase.auth.PhoneAuthProvider.credential(verificationId, otp);

//   firebase.auth().signInWithCredential(credential)
//     .then(userCredential => {
//   // Send session flag to backend
//   fetch("verifyOTP.php", {
//     method: "POST",
//     headers: { "Content-Type": "application/json" },
//     body: JSON.stringify({ verified: true })
//   })
//   .then(() => {
//     const urlParams = new URLSearchParams(window.location.search);
//     const type = urlParams.get('type');
//     const self = urlParams.get('self');
//     window.location.href = `verify.php?type=${type}&self=${self}`;
//   });
// })

}
</script>
