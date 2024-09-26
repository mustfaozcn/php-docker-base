<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Yönetimi</title>
    <link rel="stylesheet" href="/css/style.css">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrfToken ?? ''); ?>">
</head>
<body>
<div class="container">
    <h1>Kullanıcı Yönetimi</h1>

    <div id="userForm">
        <h2>Kullanıcı Ekle/Düzenle</h2>
        <form id="addEditUserForm">
            <input type="hidden" id="userId">
            <input type="text" id="username" placeholder="Kullanıcı Adı" required>
            <input type="email" id="email" placeholder="E-posta" required>
            <input type="password" id="password" placeholder="Şifre">
            <button type="submit" id="submitBtn">Ekle</button>
            <button type="button" id="cancelBtn" style="display:none;">İptal</button>
        </form>
    </div>

    <div id="userList">
        <h2>Kullanıcı Listesi</h2>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Kullanıcı Adı</th>
                <th>E-posta</th>
                <th>İşlemler</th>
            </tr>
            </thead>
            <tbody id="userTableBody">
            <!-- Kullanıcılar buraya dinamik olarak eklenecek -->
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/js/script.js"></script>
</body>
</html>