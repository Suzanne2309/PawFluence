(() => {
  const $ = (s, p = document) => p.querySelector(s);
  const $$ = (s, p = document) => [...p.querySelectorAll(s)];

  const navToggle = $('[data-mobile-toggle]');
  const nav = $('[data-mobile-nav]');
  navToggle?.addEventListener('click', () => nav?.classList.toggle('open'));

  const search = $('[data-post-filter]');
  if (search) {
    search.addEventListener('input', () => {
      const term = search.value.trim().toLowerCase();
      $$('[data-post-card]').forEach((card) => {
        const text = card.textContent.toLowerCase();
        card.classList.toggle('hidden', !text.includes(term));
      });
    });
  }

  $$('textarea').forEach((ta) => {
    const fit = () => { ta.style.height = 'auto'; ta.style.height = `${ta.scrollHeight}px`; };
    ta.addEventListener('input', fit);
    fit();
  });

  const imgInput = $('[data-image-input]');
  const preview = $('[data-image-preview]');
  imgInput?.addEventListener('input', () => {
    if (!preview) return;
    const value = imgInput.value.trim();
    if (!value) return preview.classList.add('hidden');
    preview.src = value;
    preview.classList.remove('hidden');
  });

  const toast = $('.toast');
  const showToast = (message) => {
    if (!toast) return;
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 1200);
  };

  $$('[data-like-btn]').forEach((btn) => btn.addEventListener('click', () => {
    const liked = btn.dataset.state === 'on';
    btn.dataset.state = liked ? 'off' : 'on';
    btn.textContent = liked ? '♡ J’aime' : '♥ J’aimé';
    showToast(liked ? 'Like retiré' : 'Post liké');
  }));

  $$('[data-save-btn]').forEach((btn) => btn.addEventListener('click', () => {
    const saved = btn.dataset.state === 'on';
    btn.dataset.state = saved ? 'off' : 'on';
    btn.textContent = saved ? '⤴ Sauvegarder' : '✔ Sauvegardé';
    showToast(saved ? 'Retiré des favoris' : 'Ajouté aux favoris');
  }));

  const backTop = $('[data-back-top]');
  const onScroll = () => backTop?.classList.toggle('show', window.scrollY > 350);
  window.addEventListener('scroll', onScroll);
  onScroll();
  backTop?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
})();
