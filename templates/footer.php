			</div>
			<footer>
				<hr>
				<a href="#" class="pull-right">&uarr; Topo</a>
				
				<h3><?= ORG_NAME ?></h3>
				<p>Encontrou um problema ou tem uma sugestão? Forneça <a title="Ajude a melhorar a Central de solicitações" href="<?= SITE_BASE ?>feedback">feedback</a>!<br>
				<small><?= ORG_LEGAL ?></small>
				</p>
			</footer>

			<script type="text/javascript" src="<?= SITE_BASE ?>res/js/bootstrap.min.js"></script>
			<script type="text/javascript">
				$("input[data-type='date']").live("focus",function(){$(this).mask("99/99/9999");});
				$("input[type='date']").live("focus",function(){$(this).mask("99/99/9999");});
				$("input[data-type='datetime']").live("focus",function(){$(this).mask("99/99/9999? 99:99");});
				$("input[type='datetime']").live("focus",function(){$(this).mask("99/99/9999? 99:99");});
				$("input[data-type='money']").live("focus",function(){$(this).priceFormat({prefix:'R$ ',centsSeparator:',',thousandsSeparator:'.',allowNegative:true});});
				<?php if($_SESSION['IS_ERROR']){ ?>
					setTimeout(function(){
						$('body').addClass('error');
					},3000);
				<?php } ?>
				$(window).bind("resize",function(e){
					var height = $(window).height();
					if(height <= 480){
						$("#application-body").find(".btn").addClass("btn-block");
					}else{
						$("#application-body").find(".btn").removeClass("btn-block");
					}
				});
				<?php if(isset($_SESSION['flash'])){ ?>
					setTimeout(function(){
						$("#flash").fadeIn();
					},500);
				<?php unset($_SESSION['flash']);} ?>
				/* EFEITO FADE-IN/FADE-OUT incompatível com Internet Explorer 7
				setTimeout(function(){
					$("#application-body").fadeIn();
				},100);
				$(window).bind("beforeunload",function(e){
					$("#application-body").fadeOut();
				});*/
				$("a").tooltip();
			</script>
		</div>
	</body>
</html>
