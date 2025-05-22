document.addEventListener('DOMContentLoaded', function(){
    const moins = document.querySelector('.moins');
    const plus = document.querySelector('.plus');
    const quantityInput = document.getElementById('quantityInput');

    moins.addEventListener('click', function(){
        console.log('1')
        let actualValue = parseInt(quantityInput.value);
        if(actualValue>0){
            quantityInput.value = actualValue - 1
        }
    })
    plus.addEventListener('click', function () {
        console.log('2')
        let actualValue = parseInt(quantityInput.value);
        quantityInput.value = actualValue + 1;
    });

})


