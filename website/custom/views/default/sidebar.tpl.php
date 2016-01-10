			<div id="sidebar" class="aside">
				<div class="section">
					<h2>Fansubs actius</h2>
					<ul>
						<li>
							<img src="custom/img/favicon-www-catsub-net.png" alt="" height="14" width="14" />
							<a href="http://www.catsub.net/">CatSub</a>
						</li>
						<li>
							<img src="custom/img/favicon-ippantekina-blogspot-com.png" alt="" height="14" width="14" />
							<a href="http://ippantekina.blogspot.com/">Ippantekina jimaku</a>
						</li>
						<li>
							<img src="custom/img/favicon-llunaplenanofansub-blogspot-com-es.png" alt="" height="14" width="14" />
							<a href="http://llunaplenanofansub.blogspot.com.es/">Lluna Plena no Fansub</a>
						</li>
						<li>
							<img src="custom/img/favicon-seireiteinofansub-blogspot-com-es.png" alt="" height="14" width="14" />
							<a href="http://seireiteinofansub.blogspot.com.es/">Seireitei no Fansub</a>
						</li>
						<li>
							<img src="custom/img/favicon-yoshiwaranofansub-wordpress-com.png" alt="" height="14" width="14" />
							<a href="https://yoshiwaranofansub.wordpress.com/">Yoshiwara no Fansub</a>
						</li>
					</ul>
				</div>

				<div class="section">
					<h2>Fansubs inactius</h2>
					<ul>
						<li>
							<img src="custom/img/favicon-hist-anicat.png" alt="" height="14" width="14" />
							<span>AniCat</span>
						</li>
						<li>
							<img src="custom/img/favicon-hist-animelliure.png" alt="" height="14" width="14" />
							<span>Animelliure Fansub</span>
						</li>
						<li>
							<img src="custom/img/favicon-hist-dengekidaisycat.png" alt="" height="14" width="14" />
							<a href="https://dengekidaisycat.wordpress.com/">Dengeki Daisy Cat</a>
						</li>
						<li>
							<img src="custom/img/favicon-hist-dragon.png" alt="" height="14" width="14" />
							<a href="http://dragonnofansub.new-forum.net/">Dragon no Fansub</a>
						</li>
						<li>
							<img src="custom/img/favicon-hist-gacelapunch.png" alt="" height="14" width="14" />
							<span>GacelaPunch no Fansub</span>
						</li>
						<li>
							<img src="custom/img/favicon-hist-tintaxina.png" alt="" height="14" width="14" />
							<span>TintaXina.net</span>
						</li>
						<li>
							<img src="custom/img/favicon-hist-xop.png" alt="" height="14" width="14" />
							<a href="https://xopfansub.wordpress.com/">XOP Fansub</a>
						</li>
					</ul>
				</div>

				<div class="section">
<?php
if (isset($_GET['fansub']) && $_GET['fansub']!=''){
?>
					<h2>Totes les notícies</h2>
					<ul>
						<li><a href="/">Torna a la pàgina inicial</a></li>
					</ul>
<?php
}
else{
?>
					<h2>Arxiu</h2>
					<ul>
						<li><a href="/arxiu">Mostra tot l'històric</a></li>
					</ul>
<?php
}
?>
				</div>
			</div>
