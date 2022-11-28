			</div>
			<div id="footer">
				<p>
					Tots els drets dels textos, imatges i obres esmentades pertanyen a llurs propietaris.<br />
					Codi i disseny © <?php echo date('Y')>2020 ? '2020-'.date('Y') : date('Y'); ?> Fansubs.cat. <a href="https://github.com/fansubscat/Fansubs.cat" target="_blank">És codi obert</a>. <a class="contact-link">Contacta amb nosaltres</a>. Segueix-nos <a rel="me" href="https://mastodont.cat/@fansubscat" target="_blank">al Mastodon</a> o <a rel="me" href="https://twitter.com/fansubscat" target="_blank">al Twitter</a>.
				</p>
			</div>
		</div>
	</body>
</html>
<?php
ob_flush();
mysqli_close($db_connection);
?>
