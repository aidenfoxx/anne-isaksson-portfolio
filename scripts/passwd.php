<?php
  if (isset($_POST['password'])) {
    echo '<p>Output: ' . password_hash(trim($_POST['password']), PASSWORD_DEFAULT) . '</p>';
  }
?>
<form method="POST">
  <label>
    Password:
    <input type="password" name="password" />
  </label>
  <button>Generate</button>
</form>
