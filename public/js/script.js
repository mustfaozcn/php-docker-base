$(document).ready(function () {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    console.log('CSRF Token:', csrfToken);

    // Kullanıcıları listele
    function loadUsers() {
        $.ajax({
            url: '/users/list',
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function (data) {
                console.log('Received data:', data);
                $('#userTableBody').empty();
                if (Array.isArray(data)) {
                    data.forEach(function (user) {
                        $('#userTableBody').append(`
                        <tr>
                            <td>${user.id}</td>
                            <td>${user.username}</td>
                            <td>${user.email}</td>
                            <td>
                                <button class="edit-btn" data-id="${user.id}" data-username="${user.username}" data-email="${user.email}">Düzenle</button>
                                <button class="delete-btn" data-id="${user.id}">Sil</button>
                            </td>
                        </tr>
                    `);
                    });
                } else {
                    console.error('Received data is not an array:', data);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error loading users:', status, error);
                console.error('Response:', xhr.responseText);
                alert('Kullanıcılar yüklenirken hata oluştu: ' + error);
            }
        });
    }

    // Sayfa yüklendiğinde kullanıcıları listele
    loadUsers();

    // Kullanıcı ekleme/düzenleme formu gönderildiğinde
    $('#addEditUserForm').submit(function (e) {
        e.preventDefault();
        var userId = $('#userId').val();
        var userData = {
            username: $('#username').val(),
            email: $('#email').val(),
            password: $('#password').val()
        };

        if (userId) {
            // Kullanıcı düzenleme
            $.ajax({
                url: '/users/' + userId,
                type: 'PUT',
                data: JSON.stringify(userData),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (response) {
                    alert('Kullanıcı güncellendi!');
                    resetForm();
                    loadUsers();
                },
                error: function (xhr) {
                    alert('Hata: ' + xhr.responseJSON.error);
                }
            });
        } else {
            // Yeni kullanıcı ekleme
            $.ajax({
                url: '/users/create',
                type: 'POST',
                data: JSON.stringify(userData),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (response) {
                    alert('Kullanıcı eklendi!');
                    resetForm();
                    loadUsers();
                },
                error: function (xhr) {
                    alert('Hata: ' + xhr.responseJSON.error);
                }
            });
        }
    });

    // Düzenle butonuna tıklandığında
    $(document).on('click', '.edit-btn', function () {
        var userId = $(this).data('id');
        var username = $(this).data('username');
        var email = $(this).data('email');

        $('#userId').val(userId);
        $('#username').val(username);
        $('#email').val(email);
        $('#password').val(''); // Güvenlik nedeniyle şifreyi boş bırak
        $('#submitBtn').text('Güncelle');
        $('#cancelBtn').show();
    });

    // Sil butonuna tıklandığında
    $(document).on('click', '.delete-btn', function () {
        var userId = $(this).data('id');
        if (confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')) {
            $.ajax({
                url: '/users/' + userId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (response) {
                    alert('Kullanıcı silindi!');
                    loadUsers();
                },
                error: function (xhr) {
                    alert('Hata: ' + xhr.responseJSON.error);
                }
            });
        }
    });

    // İptal butonuna tıklandığında
    $('#cancelBtn').click(function () {
        resetForm();
    });

    // Formu sıfırla
    function resetForm() {
        $('#userId').val('');
        $('#username').val('');
        $('#email').val('');
        $('#password').val('');
        $('#submitBtn').text('Ekle');
        $('#cancelBtn').hide();
    }
});