var prodSwiper;
$(function(){

	//nav
	$('.btn-nav').on('click', function(){
		if($(this).hasClass('active')){
			$('body').removeClass('lock');
			// $('.header h1').removeClass('active');
			$(this).removeClass('active');
			$('nav').removeClass('open');
		} else{
			$('body').addClass('lock');
			// $('.header h1').addClass('active');
			$(this).addClass('active');
			$('nav').addClass('open');
		}
	});

	//terms
	$('.btn-terms').on('click', function(e){
		e.preventDefault();
		$('body').addClass('lock');
		$('.modal-dialog').addClass('active');
	});
	$(document).on('click','.modal-dialog .btn-close',function(e){
		e.preventDefault();
		$('body').removeClass('lock');
		$('.modal-dialog').removeClass('active');
	});

	//kv slider
	var kvSwiper = new Swiper ('.kv', {
		navigation: {
			nextEl: '.btn-kvNext',
			prevEl: '.btn-kvPrev',
		},
		pagination: {
			el: '.paging',
			clickable: true
		},
	});

	//main prod
	var introSwiper = new Swiper ('.intro-prod .img-area', {
		loop: true,
		spaceBetween: 20,
		simulateTouch: false,
		on:{
			slideChange: function(){
				var idx = introSwiper.realIndex;
				$('.intro-prod .tab li').removeClass('active').eq(idx).addClass('active');
			}
		},
		breakpoints: {
			768: {
				spaceBetween: 40
			}
		}
	});
	/*
	var snsSwiper = new Swiper ('.sns-slide', {
		autoplay: {
			delay: 5000,
		},
		loop: true,
		slidesPerView: 3,
	    centeredSlides: true,
	    breakpoints: {
			768: {
				slidesPerView: 5,
			}
		}
	});
	*/
	//prod slider
	prodSwiper = new Swiper ('.prod-slider', {
		loop: true,
		spaceBetween: 20,
		pagination: {
			el: '.prod-paging',
			type: 'fraction'
		},
		navigation: {
			nextEl: '.prod-next',
			prevEl: '.prod-prev'
		},
		breakpoints: {
			768: {
				spaceBetween: 50
			}
		}
	});
	$(document).on('click','.prod-slider .swiper-slide a',function(e){
		e.preventDefault();
		var target = $(this.hash);
		$(target).addClass('active');
	});
	$(document).on('click','.prod-intro .btn-close',function(e){
		e.preventDefault();
		$('.modal').removeClass('active');
	});

	//tab
	$(document).on('click','.tab-main a',function(e){
		e.preventDefault();
		if($('.intro-prod').length){
			var idx = $(this).parent().index() + 1;
			introSwiper.slideTo(idx);
			$(this).parent().addClass('active').siblings().removeClass('active');
		} else{
			var target = $(this.hash);
			$(this).parent().addClass('active').siblings().removeClass('active');
			$(target).fadeIn().siblings('.prod-item').fadeOut();
		}
	});
	$(document).on('click','.tabs a',function(e){
		e.preventDefault();

		var target = $(this.hash);
		$(this).parent().addClass('active').siblings().removeClass('active');
		$(target).fadeIn().siblings('.prod-item').fadeOut();
		
	});	
});