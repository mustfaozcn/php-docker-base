<!DOCTYPE html>
<html>
<head>
    <title>Ana Sayfa</title>
</head>
<body>
<h1>Hoş Geldiniz, <?php echo $this->session->get('user'); ?>!</h1>
<a href="/logout">Çıkış Yap</a>
<h2>Kullanıcı Listesi</h2>
<ul>
    <?php foreach ($users as $user): ?>
        <li><?php echo $user->getUsername(); ?></li>
    <?php endforeach; ?>
</ul>
</body>
</html>