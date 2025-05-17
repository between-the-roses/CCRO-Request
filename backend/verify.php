<?php
// After successful verification, redirect:
$type = isset($_GET['type']) ? $_GET['type'] : null;
$self = isset($_GET['self']) ? $_GET['self'] : null;

if (is_string($type) && is_string($self)) {
    // Map to the correct page
    $redirects = [
        'livebirth' => [
            'yes' => '../UserDashboard/Birth/livebirthyes.php',
            'no'  => '../UserDashboard/Birth/livebirthno.php'
        ],
        'marriage' => [
            'yes' => '../UserDashboard/Marriage/marriageyes.php',
            'no'  => '../UserDashboard/Marriage/marriageno.php'
        ],
        'death' => [
            'yes' => '../UserDashboard/Death/deathyes.php',
            'no'  => '../UserDashboard/Death/deathno.php'
        ]
    ];
    if (isset($redirects[$type][$self])) {
        // Example: After verification, use this:
        // header("Location: " . $redirects[$type][$self]);
        // exit;
        echo "<p>Verification successful! Redirecting...</p>";
        $redirectUrl = htmlspecialchars($redirects[$type][$self], ENT_QUOTES, 'UTF-8');
        echo "<script>setTimeout(function(){ window.location.href = '{$redirectUrl}'; }, 2000);</script>";
        exit;
    }
}
?>
<!-- Add your Google/OTP verification UI here -->
<h2>Verify your identity</h2>
<p>(Google Sign-In or OTP form goes here)</p>