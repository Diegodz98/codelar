(function ($) {
  Drupal.behaviors.myModuleCopyButton = {
    attach: function (context, settings) {
      const copyButtons = document.querySelectorAll('.copy-button');
      copyButtons.forEach(function (button) {
        button.addEventListener('click', function (e) {
          const textToCopy = this.getAttribute('data-clipboard-text');

          const tempInput = document.createElement('input');
          tempInput.setAttribute('value', textToCopy);
          document.body.appendChild(tempInput);
          tempInput.select();
          document.execCommand('copy');
          document.body.removeChild(tempInput);
          e.preventDefault();

        });
      });
    },
  };

  $(document).ready(function () {




    // Buscamos todos los botones "Favorito" y agregamos un evento de clic a cada uno
    const favoriteButtons = document.querySelectorAll('.favorite-button');
    favoriteButtons.forEach((button) => {
      button.addEventListener('click', (event) => {
        const productId = event.target.dataset.productId; // Obtenemos el ID del producto desde el botón
        const favorites = JSON.parse(localStorage.getItem('favoriteProducts') || '[]'); // Obtenemos la lista de favoritos desde el almacenamiento local o creamos una nueva si no existe

        // Si el producto ya está marcado como favorito, lo quitamos de la lista
        if (favorites.includes(productId)) {
          const index = favorites.indexOf(productId);
          favorites.splice(index, 1);
          event.target.classList.remove('favorite'); // Quitamos la clase "favorite" del botón
        }
        // Si el producto no está marcado como favorito, lo agregamos a la lista
        else {
          favorites.push(productId);
          event.target.classList.add('favorite'); // Agregamos la clase "favorite" al botón
        }

        localStorage.setItem('favoriteProducts', JSON.stringify(favorites)); // Guardamos la lista de favoritos en el almacenamiento local
      });
    });

    // Cuando se carga la página, revisamos la lista de favoritos y marcamos los productos correspondientes
    const favorites = JSON.parse(localStorage.getItem('favoriteProducts') || '[]');
    favorites.forEach((productId) => {
      const button = document.querySelector(`.favorite-button[data-product-id="${productId}"]`);
      if (button) {
        button.classList.add('favorite');
      }
    });


    // Selecciona el botón
    const favoritesButton = document.querySelector('#favorites-button');

    // Agrega un evento de clic
    favoritesButton.addEventListener('click', function () {
      // Obtener los productos favoritos almacenados en el navegador
      const favorites = JSON.parse(localStorage.getItem('favoriteProducts')) || [];

      // Selecciona todos los productos
      const products = document.querySelectorAll('.product');

      if (favoritesButton.textContent === 'Show only favorites') {
        // Oculta todos los productos
        products.forEach(function (product) {
          product.style.display = 'none';
        });

        // Muestra solo los productos favoritos
        favorites.forEach(function (favorite) {
          const product = document.querySelector(`[data-product-id="${favorite}"]`);
          if (product) {
            product.style.display = 'block';
          }
        });

        // Cambia el texto del botón
        favoritesButton.textContent = 'Show all products';
      } else {
        // Muestra todos los productos
        products.forEach(function (product) {
          product.style.display = 'block';
        });

        // Cambia el texto del botón
        favoritesButton.textContent = 'Show only favorites';
      }
    });




  });
})(jQuery);









