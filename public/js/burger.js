const burger = document.getElementById('burger');
const menu = document.getElementById('principal');

if (burger && menu) {
  burger.addEventListener('click', () => {
    const expanded = burger.getAttribute('aria-expanded') === 'true';
    burger.setAttribute('aria-expanded', String(!expanded));
    burger.classList.toggle('is-open', !expanded);
    menu.classList.toggle('is-open', !expanded);
  });
}
