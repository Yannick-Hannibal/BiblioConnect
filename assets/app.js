import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

let notation = () => {
    document.addEventListener('DOMContentLoaded', function () {
        if (document.querySelectorAll('.rating-container .star')) {
            const stars = document.querySelectorAll('.rating-container .star');

            function highlightStars(index) {
                stars.forEach((star, i) => {
                    if (i <= index) {
                        star.style.color = 'gold';
                    } else {
                        star.style.color = '#ccc';
                    }
                });
            }

            stars.forEach((star, index) => {
                star.addEventListener('mouseover', () => highlightStars(index));

                star.addEventListener('mouseout', () => {
                    const checkedInput = document.querySelector('.rating-container input[type="radio"]:checked');
                    if (checkedInput) {
                        highlightStars(checkedInput.value - 1);
                    } else {
                        highlightStars(-1);
                    }
                });

                star.addEventListener('click', () => {
                    stars[index].previousElementSibling.checked = true;
                    highlightStars(index);
                });
            });
        }
    });
}

notation();