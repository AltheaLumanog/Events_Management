// script.js - client side code

// 1. Sign up form: check the passwords before submitting
var registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
        var pass = document.getElementById('password').value;
        var confirm = document.getElementById('confirm').value;
        var msg = document.getElementById('errorMsg');

        if (pass.length < 6) {
            msg.textContent = 'Password must be at least 6 characters.';
            e.preventDefault();
        } else if (pass !== confirm) {
            msg.textContent = 'Passwords do not match.';
            e.preventDefault();
        }
    });
}

// 2. Home page: live search (hides event cards that do not match)
var searchBox = document.getElementById('searchBox');
if (searchBox) {
    searchBox.addEventListener('input', function () {
        var text = searchBox.value.toLowerCase();
        var cards = document.querySelectorAll('.event-item');

        cards.forEach(function (card) {
            if (card.textContent.toLowerCase().indexOf(text) > -1) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

// 3. Event page: Register button (no page reload)
var registerBtn = document.getElementById('registerBtn');
if (registerBtn) {
    registerBtn.addEventListener('click', function () {
        var data = new FormData();
        data.append('event_id', registerBtn.dataset.id);

        registerBtn.disabled = true;

        fetch('register_event.php', { method: 'POST', body: data })
            .then(function (res) { return res.json(); })
            .then(function (result) {
                var box = document.getElementById('message');

                if (result.ok) {
                    box.innerHTML = '<div class="alert alert-success">' + result.message + '</div>';
                    document.getElementById('seatsLeft').textContent = result.seats_left;
                    document.getElementById('seatBar').style.width = result.percent + '%';
                    registerBtn.textContent = 'You are registered';
                    registerBtn.className = 'btn btn-success w-100';
                } else {
                    box.innerHTML = '<div class="alert alert-danger">' + result.message + '</div>';
                    registerBtn.disabled = false;
                }
            })
            .catch(function () {
                alert('Something went wrong. Please try again.');
                registerBtn.disabled = false;
            });
    });
}

// 4. Attendees page: tick "Present" without reloading
var boxes = document.querySelectorAll('.attend-box');
boxes.forEach(function (box) {
    box.addEventListener('change', function () {
        var data = new FormData();
        data.append('id', box.dataset.id);
        data.append('attended', box.checked ? '1' : '0');

        fetch('toggle_attendance.php', { method: 'POST', body: data })
            .then(function (res) { return res.text(); })
            .then(function (text) {
                if (text !== 'ok') {
                    box.checked = !box.checked;
                    alert('Could not save. Please try again.');
                }
            });
    });
});
