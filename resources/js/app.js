import './bootstrap';
import 'flowbite';
import 'sweetalert2/src/sweetalert2.scss';
import Swal from 'sweetalert2';

$(function () {
    window.Swal = Swal;
    const darkModeButton = $('#darkModeButton');
    darkModeButton.on('click', function () {
        if (
            localStorage.getItem('theme') === 'light' ||
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: light)').matches)
        ) {
            localStorage.setItem('theme', 'dark');
            document.documentElement.classList.add('dark');
        } else {
            localStorage.setItem('theme', 'light');
            document.documentElement.classList.remove('dark');
        }
    });
});
