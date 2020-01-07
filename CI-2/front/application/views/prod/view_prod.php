	<!-- contents -->
	<main class="sub">
		<section class="sub-product">
			<div class="prod-slider">
				<div class="swiper-wrapper">
					<div class="swiper-slide original"><a href="#original">
						<dfn>KITKAT <span>4 FINGER</span> <i>킷캣 4 핑거</i></dfn>
						<div class="img-prod">
							<img src="/images/product/four_finger.png" alt="KITKAT 4 FINGER">
						</div>
					</a></div>
					<div class="swiper-slide new3"><a href="#new3">
						<span class="tag"><img src="/images/common/new.png" alt="NEW"></span>
						<dfn>KITKAT <span>2 FINGER</span> <i>킷캣 2 핑거</i></dfn>
						<div class="img-prod">
							<img src="/images/product/two_finger.png" alt="KITKAT 2 FINGER">
						</div>
					</a></div>					
					<div class="swiper-slide chunky"><a href="#chunky">
						<dfn>KITKAT <span>CHUNKY</span> <i>킷캣 청키</i></dfn>
						<div class="img-prod">
							<img src="/images/product/chunky.png" alt="KITKAT CHUNKY">
						</div>
					</a></div>
				</div>
				<div class="btn-control">
					<button type="button" class="prod-prev">이전 제품</button>
					<button type="button" class="prod-next">다음 제품</button>
				</div>
				<div class="prod-paging"></div>
			</div>
		</section>
	</main>
	<!-- //contents -->

	<!-- product detail -->
	<div class="modal modals <?=$no>0 && $code!="" ? 'active' : ''?>">

	</div>
	<!-- //product detail -->
	<script>
		$(function(){
			//alert('<?=$code?>');
			$(document).on('click','.prod-slider .swiper-slide a',function(e){
				e.preventDefault();
				var code=$(this).attr('href').replace('#','');
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
			});		
			function getProd(code){
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
			}
			if("<?=$no?>">0 && <?=$no?>!=""){
				prodSwiper.slideTo(<?=$no?>, 0);
				getProd('<?=$code?>');
			}
		});
	</script>