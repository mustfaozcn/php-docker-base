<!DOCTYPE html>
<html>
<head>
    <title>Giriş</title>
</head>
<body>
<h1>Giriş Yap</h1>
<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>
<form method="post" action="/login">
    <label for="username">Kullanıcı Adı:</label>
    <input type="text" id="username" name="username" required><br><br>
    <label for="password">Şifre:</label>
    <input type="password" id="password" name="password" required><br><br>
    <input type="submit" value="Giriş Yap">
</form>
</body>
</html>