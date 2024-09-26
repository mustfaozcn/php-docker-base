<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var csrfToken = "<?php echo $csrfToken; ?>";
    </script>
</head>
<body>
<div class="container w-50 ">
    <main class="form-signin w-100 m-auto text-center">
        <form id="loginForm">
            <h1 class="h3 mb-3 fw-normal">CRUD Login</h1>

            <div class="form-floating">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                <label for="username">Username</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password"
                       required>
                <label for="password">Password</label>
            </div>

            <button class="w-100 mt-2 btn btn-lg btn-primary" type="submit">Sign in</button>
        </form>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
<script>
    document.getElementById('loginForm').addEventListener('submit', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Giriş yapılıyor...',
            text: 'Lütfen bekleyin',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData(this);
        console.log('Form data:', Object.fromEntries(formData));

        fetch('/ajax-login', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Server responded with status: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Giriş başarılı, yönlendiriliyorsunuz...'
                    }).then(() => {
                        window.location.href = data.redirect; // Sunucudan gelen yönlendirme URL'sini kullan
                    });
                } else {
                    throw new Error(data.message || 'Bilinmeyen bir hata oluştu');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: error.message || 'Bir hata oluştu, lütfen tekrar deneyin.'
                });
            });
    });
</script>
</body>
</html>