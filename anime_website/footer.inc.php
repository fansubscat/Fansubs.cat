			</div>
			<div id="footer">
				<p>
					Tots els drets dels textos, imatges i obres esmentades pertanyen a llurs propietaris.<br />
					Codi i disseny © <?php echo date('Y')>2020 ? '2020-'.date('Y') : date('Y'); ?> Fansubs.cat. <a href="https://github.com/Ereza/Fansubs.cat">És codi obert</a>. <a id="contact-link">Contacta'ns</a>.
				</p>
			</div>
		</div>
	</body>
</html>
<?php
ob_flush();
mysqli_close($db_connection);
?>
