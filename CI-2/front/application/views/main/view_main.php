	<!-- contents -->
	<main class="main">
		<section class="kv">
			<h2 class="blind">킷캣</h2>
			<ul class="swiper-wrapper">
				<li class="swiper-slide">
					<video autoplay loop muted playsinline preload="metadata">
						<source src="/video/20191224_kitKat_home.mp4" type="video/mp4">
						<source src="/video/20191224_kitKat_home.webm" type="video/webm">
						<source src="/video/20191224_kitKat_home.ogv" type="video/ogg">
					</video>
					<div class="slogan">
						<img src="/images/main/slogan.png" alt="Have a break, have a KitKat">
					</div>
				</li>
				<li class="swiper-slide">
					<div class="product">
						<div class="img">
							<img src="images/main/img_product.png" alt="카라멜, 헤이즐넛, 쿠키크럼블">
						</div>
						<strong>
							<span>KITKAT</span>
							<span>NEW <i>3</i></span>
							<span>FLAVOUR</span>
						</strong>
						<p class="new">새롭게 출시한 3가지 킷캣!</p>
						<p class="name">카라멜, 헤이즐넛, 쿠키크럼블</p>
					</div>
				</li>
			</ul>
			<div class="paging"></div>
			<div class="btn">
				<button type="button" class="btn-kvPrev">이전</button>
				<button type="button" class="btn-kvNext">다음</button>
			</div>
		</section>
		<section class="intro-prod">
			<div class="inner">
				<span class="since">SINCE 1935</span>
				<h2>전 세계인들의 마음을 <br class="mobile">사로잡은 킷캣!</h2>

				<ul class="tab tab-main">
					<li class="active"><a href="#original">4 FINGER</a></li>
					<li><a href="#new3">2 FINGER</a></li>
					<li><a href="#chunky">CHUNKY</a></li>
				</ul>

				<div class="img-area">
					<div class="swiper-wrapper">
						<div class="swiper-slide" id="original" style="display:block;">
							<div class="img">
								<img src="/images/main/four_finger.png" alt="">
							</div>
							<p>킷캣 4핑거 <span>밀크와 초콜릿의 바삭한 조화</span></p>
						</div>
						<div class="swiper-slide" id="new3">
							<span class="tag"><img src="/images/main/new.png" alt="NEW"></span>
							<div class="img">
								<img src="/images/main/two_finger.png" alt="">
							</div>
							<p>킷캣 2핑거 <span>킷캣의 새로운 3가지 맛</span></p>
						</div>
						<div class="swiper-slide" id="chunky">
							<div class="img">
								<img src="/images/main/chunky.png" alt="">
							</div>
							<p>킷캣 청키 <span>달콤바삭한 한 입의 즐거움</span></p>
						</div>
					</div>
				</div>
			</div>
		</section>
		<section class="sns">
			<span class="since">SOCIAL CENTER</span>
			<h2>즐거움이 가득한 킷캣소식을 <br class="mobile">SNS로 만나보세요!</h2>
			<div class="sns-slide">
				<ul class="swiper-wrapper">
					
				</ul>
			</div>
		</section>
	</main>
	<!-- //contents -->
	<!-- product detail -->
	<div class="modal modals">

	</div>
	<!-- //product detail -->
	<script>
		$(function(){
			$.ajax({
				url: "/main/getSns3",
				type: 'post',
				dataType: 'json',
				async: true,
				data: {},
				success: function(res){
					//console.log(res);
					var str="";
					for(i=0;i<res.length;i++){
						str="";
						//console.log(res[i]['id']);
						str+="<li class='swiper-slide'>";
						str+="	<div class='img-area'>";
						str+="		<img src='"+res[i]['images']+"' alt=''>";
						str+="	</div>";
						str+="	<div class='txt-area'>";
						str+="		<p>"+res[i]['text']+"</p>";
						str+="	</div>";
						str+="</li>";
						//console.log(str);
						$('.sns-slide .swiper-wrapper').append(str);
					}
				},
				beforeSend:function(){},
				complete:function(){
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
				},
				error: function(){}
			});
			$(document).on('click','.img-area .swiper-slide',function(e){
				e.preventDefault();
				var code=$(this).attr('id');
				/*
				$('.modals').attr('id',code);
				$.ajax({
					url: "/product/getProductView",
					type: 'post',
					dataType: 'html',
					async: true,
					data: {"code":code},
					success: function(dom){
						$('.modals').html(dom);
						$('.modals').addClass('active');
					},
					beforeSend:function(){},
					complete:function(){
						 
					},
					error: function(){}
				});	
				*/
				location.href="/product/prod/"+code;
			});		
			
		});
	</script>