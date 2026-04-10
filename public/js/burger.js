const header = document.getElementById('siteHeader');
const burger = document.getElementById('burger');

if (header && burger) {
  burger.addEventListener('click', () => {
    const isOpen = header.classList.toggle('menu-open');
    burger.setAttribute('aria-expanded', String(isOpen));
    burger.setAttribute('aria-label', isOpen ? 'Fermer le menu' : 'Ouvrir le menu');
  });

  document.addEventListener('click', (event) => {
    if (!header.contains(event.target) && header.classList.contains('menu-open')) {
      header.classList.remove('menu-open');
      burger.setAttribute('aria-expanded', 'false');
      burger.setAttribute('aria-label', 'Ouvrir le menu');
    }
  });
}
