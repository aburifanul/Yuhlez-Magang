document.addEventListener('DOMContentLoaded', function () {

```
const button = document.getElementById('dashboardMenuButton');
const menu = document.getElementById('dashboardMobileMenu');

if (!button || !menu) {
    return;
}

button.addEventListener('click', function () {

    menu.classList.toggle('show');

    const icon = button.querySelector('i');

    if (!icon) {
        return;
    }

    if (menu.classList.contains('show')) {

        icon.classList.remove('bi-list');
        icon.classList.add('bi-x');

    } else {

        icon.classList.remove('bi-x');
        icon.classList.add('bi-list');

    }

});
```

});
