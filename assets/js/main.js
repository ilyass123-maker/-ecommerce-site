// assets/js/main.js

document.addEventListener('DOMContentLoaded', () => {
  // ----------------------------
  // 1) Seller interface: Pré-remplissage du formulaire d'édition
  // ----------------------------
  const editButtons = document.querySelectorAll('.btn-edit-product');
  if (editButtons.length) {
    editButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        // On attend que seller_interface.php ait ajouté les data-attributes correspondantes
        document.getElementById('id').value    = btn.dataset.id;
        document.getElementById('name').value  = btn.dataset.name;
        document.getElementById('price').value = btn.dataset.price;
        document.getElementById('type').value  = btn.dataset.type;
        document.getElementById('image').value = btn.dataset.image;
        // Scroll automatique vers le formulaire d'édition
        location.hash = '#form';
      });
    });
  }

  // ----------------------------
  // 2) Confirmation avant suppression (seller & cart)
  // ----------------------------
  document.querySelectorAll('a[href*="?delete="], a[href*="?remove="]').forEach(link => {
    link.addEventListener('click', event => {
      const confirmMsg = link.href.includes('?delete=')
        ? 'Voulez-vous vraiment supprimer ce produit ?'
        : 'Retirer cet article du panier ?';
      if (!confirm(confirmMsg)) {
        event.preventDefault();
      }
    });
  });

  // ----------------------------
  // 3) Navbar mobile: auto-collapse après clic
  // ----------------------------
  const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
  const navCollapse = document.getElementById('navMenu');
  navLinks.forEach(link => {
    link.addEventListener('click', () => {
      if (window.getComputedStyle(navCollapse).getPropertyValue('display') !== 'none') {
        const bsCollapse = new bootstrap.Collapse(navCollapse, { toggle: false });
        bsCollapse.hide();
      }
    });
  });
});
