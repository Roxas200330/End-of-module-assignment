// menu toggle and form checks, php still validates on server

// wait for the page so the elements exist
document.addEventListener('DOMContentLoaded', function () {

    // mobile menu open/close, toggles .open class from style.css
    var toggle = document.getElementById('menuToggle');
    var nav = document.getElementById('siteNav');
    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            var open = nav.classList.toggle('open');
            // aria-expanded for screen readers
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    // put error message in the form
    function fail(form, message) {
        var box = form.querySelector('#formError');
        if (box) box.textContent = message;
    }

    // register form checks, same rules as register.php
    var reg = document.getElementById('registerForm');
    if (reg) {
        reg.addEventListener('submit', function (ev) {
            var name = reg.name.value.trim();
            var email = reg.email.value.trim();
            var pw = reg.password.value;
            var confirm = reg.confirm.value;
            var msg = '';

            // basic email regex, something@something.something
            if (name.length < 2) msg = 'Name must be at least 2 characters.';
            else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) msg = 'Enter a valid email address.';
            else if (pw.length < 6) msg = 'Password must be at least 6 characters.';
            else if (pw !== confirm) msg = 'Passwords do not match.';

            // preventDefault stops the form sending
            if (msg) { ev.preventDefault(); fail(reg, msg); }
        });
    }

    // login form, just make sure both filled in
    var login = document.getElementById('loginForm');
    if (login) {
        login.addEventListener('submit', function (ev) {
            if (!login.email.value.trim() || !login.password.value) {
                ev.preventDefault();
                fail(login, 'Enter your email and password.');
            }
        });
    }

    // search form, max time must be a number if entered
    var search = document.getElementById('searchForm');
    if (search) {
        search.addEventListener('submit', function (ev) {
            var t = search.max_time.value;
            if (t !== '' && (isNaN(t) || Number(t) < 1)) {
                ev.preventDefault();
                alert('Max time must be a positive number of minutes.');
            }
        });
    }

    // rating form, star must be picked
    var rate = document.getElementById('rateForm');
    if (rate) {
        rate.addEventListener('submit', function (ev) {
            if (!rate.stars.value) {
                ev.preventDefault();
                alert('Choose a star rating first.');
            }
        });
    }
});
