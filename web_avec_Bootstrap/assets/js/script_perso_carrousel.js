$(document).ready(function(){
    let car = $('.carrousel');
        car.slick({
            slidesToShow: 6,
            slidesToScroll: 3,
            autoplay: true,
            autoplaySpeed: 3000,
            dots : true,
            arrows: false
        });
    car.on('afterChange', function(){
        car.slick('slickPlay');
    });
    });
