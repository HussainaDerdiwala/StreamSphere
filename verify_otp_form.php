<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Verify OTP</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #e0f7fa, #fff);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .otp-container {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      text-align: center;
      width: 90%;
      max-width: 400px;
    }

    .otp-container h2 {
      margin-bottom: 20px;
      color: #333;
    }

    .otp-inputs {
      display: flex;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 20px;
    }

    .otp-inputs input {
      width: 40px;
      height: 50px;
      font-size: 24px;
      text-align: center;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    .otp-inputs input:focus {
      outline: none;
      border-color: #007BFF;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
    }

    button {
      background: #007BFF;
      color: white;
      border: none;
      padding: 10px 20px;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
    }

    button:hover {
      background: #0056b3;
    }

    .message {
      color: red;
      margin-top: 10px;
    }
  </style>
</head>
<body>

<div class="otp-container">
  <h2>
    <?php
      if ($_SESSION['otp_action'] === 'signup') echo "Confirm Your Email";
      elseif ($_SESSION['otp_action'] === 'login') echo "Verify Login";
      elseif ($_SESSION['otp_action'] === 'reset') echo "Reset Password";
      else echo "Verify OTP";
    ?>
  </h2>

  <?php if ($error): ?>
    <div class="message"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" id="otpForm" action="verify_otp.php">
    <div class="otp-inputs">
      <input type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" required />
      <input type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" required />
      <input type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" required />
      <input type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" required />
      <input type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" required />
      <input type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" required />
    </div>
    <input type="hidden" name="otp" id="otp_hidden">
    <button type="submit">Verify OTP</button>
  </form>
</div>

<script>
const inputs = document.querySelectorAll('.otp-inputs input');
const hiddenInput = document.getElementById('otp_hidden');
const form = document.getElementById('otpForm');

inputs[0].focus();

inputs.forEach((input, index) => {
  input.addEventListener('input', () => {
    if (input.value.length === 1 && index < inputs.length - 1) {
      inputs[index + 1].focus();
    }
  });

  input.addEventListener('keydown', (e) => {
    if (e.key === 'Backspace' && input.value === '' && index > 0) {
      inputs[index - 1].focus();
    }
  });
});

form.addEventListener('submit', function (e) {
  e.preventDefault();
  const otp = Array.from(inputs).map(input => input.value.trim()).join('');
  hiddenInput.value = otp;

  if (otp.length === 6) {
    setTimeout(() => form.submit(), 50);
  } else {
    alert("Please enter all 6 digits of the OTP.");
  }
});
</script>

</body>
</html>
