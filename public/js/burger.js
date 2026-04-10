const burger = document.getElementById('burger');
const nav = document.getElementById('main-nav');

if (burger && nav) {
  burger.addEventListener('click', () => {
    const isExpanded = burger.getAttribute('aria-expanded') === 'true';
    burger.setAttribute('aria-expanded', String(!isExpanded));
    burger.classList.toggle('is-open', !isExpanded);
    nav.classList.toggle('is-open', !isExpanded);
  });
}
