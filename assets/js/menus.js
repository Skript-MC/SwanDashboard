
const modals = document.querySelectorAll('[data-swan-modal]');
modals.forEach((t) => {
  t.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById(t.dataset.swanModal);
    modal.classList.remove('hidden');
    const exits = modal.querySelectorAll('.modal-exit');
    exits.forEach((e2) => {
      e2.addEventListener('click', (e3) => {
        e3.preventDefault();
        modal.classList.add('hidden');
      });
    });
  });
});
