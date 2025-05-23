document.addEventListener('DOMContentLoaded', function () {
    const selectors = document.querySelectorAll('.quantitySelector');

    selectors.forEach((selector) => {
        const moins = selector.querySelector('.moins');
        const plus = selector.querySelector('.plus');
        const quantityInput = selector.querySelector('.quantityInput');

        moins.addEventListener('click', function () {
            let actualValue = parseInt(quantityInput.value);
            if (actualValue > 1) {
                quantityInput.value = actualValue - 1;
            }
        });

        plus.addEventListener('click', function () {
            let actualValue = parseInt(quantityInput.value);
            quantityInput.value = actualValue + 1;
        });
    });
});
