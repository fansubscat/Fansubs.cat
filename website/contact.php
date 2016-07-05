<?php
require_once("db.inc.php");

$header_page_title='Fansubs.cat - Envia notícies / Contacta';
$header_current_page='contact';

require_once('header.inc.php');
?>
					<div class="page-title">
						<h2>Envia notícies / Contacta</h2>
					</div>
					<div class="article">
<?php
//We must validate everything even with HTML5 validation, we never know what evil people will do.
if ($_POST['reason']!=NULL){
	$valid = FALSE;
	if ($_POST['reason']=='add_news'){
		//Add news
		if ($_POST['name']!=NULL && strlen($_POST['name'])<=255 && $_POST['email']!=NULL && strlen($_POST['email'])<=255
			&& $_POST['add_news_title']!=NULL && $_POST['add_news_contents']!=NULL && $_POST['add_news_url']!=NULL){
			$message = "";
			$message .= "Nou correu des de Fansubs.cat - Nova notícia.\n\n";
			$message .= "Nom: {$_POST['name']}\n";
			$message .= "Correu electrònic: {$_POST['email']}\n";
			$message .= "Títol: {$_POST['add_news_title']}\n";
			$message .= "Contingut: {$_POST['add_news_contents']}\n";
			$message .= "URL de la notícia: {$_POST['add_news_url']}\n";
			$message .= "URL de la imatge: {$_POST['add_news_image_url']}\n";
			$message .= "Comentaris: {$_POST['comments']}\n";
			mail($contact_email,'Fansubs.cat - Nova notícia', $message,'','-f info@fansubs.cat -F Fansubs.cat');
			mysqli_query($db_connection, "INSERT INTO pending_news (title, contents, url, image_url, sender_name, sender_email, comments) VALUES ('".mysqli_real_escape_string($db_connection, $_POST['add_news_title'])."','".mysqli_real_escape_string($db_connection, $_POST['add_news_contents'])."','".mysqli_real_escape_string($db_connection, $_POST['add_news_url'])."',".($_POST['add_news_image_url']!=NULL ? "'".mysqli_real_escape_string($db_connection, $_POST['add_news_image_url'])."'" : '').",'".mysqli_real_escape_string($db_connection, $_POST['name'])."','".mysqli_real_escape_string($db_connection, $_POST['email'])."',".($_POST['comments']!=NULL ? "'".mysqli_real_escape_string($db_connection, $_POST['comments'])."'" : 'NULL').")") or crash(mysqli_error($db_connection));
			$valid = TRUE;
		}
	}
	else if ($_POST['reason']=='new_fansub'){
		//New fansub
		if ($_POST['name']!=NULL && strlen($_POST['name'])<=255 && $_POST['email']!=NULL && strlen($_POST['email'])<=255
			&& $_POST['new_fansub_name']!=NULL && strlen($_POST['new_fansub_name'])<=255 && $_POST['new_fansub_url']!=NULL && strlen($_POST['new_fansub_url'])<=255){
			$message = "";
			$message .= "Nou correu des de Fansubs.cat - Nou fansub.\n\n";
			$message .= "Nom: {$_POST['name']}\n";
			$message .= "Correu electrònic: {$_POST['email']}\n";
			$message .= "Nom del fansub: {$_POST['new_fansub_name']}\n";
			$message .= "URL del fansub: {$_POST['new_fansub_url']}\n";
			$message .= "Comentaris: {$_POST['comments']}\n";
			mail($contact_email,'Fansubs.cat - Nou fansub', $message,'','-f info@fansubs.cat -F Fansubs.cat');
			$valid = TRUE;
		}
	}
	else{
		//Others
		if ($_POST['name']!=NULL && strlen($_POST['name'])<=255 && $_POST['email']!=NULL && strlen($_POST['email'])<=255){
			$message = "";
			$message .= "Nou correu des de Fansubs.cat - Comentaris (altres).\n\n";
			$message .= "Nom: {$_POST['name']}\n";
			$message .= "Correu electrònic: {$_POST['email']}\n";
			$message .= "Comentaris: {$_POST['comments']}\n";
			mail($contact_email,'Fansubs.cat - Comentaris', $message,'','-f info@fansubs.cat -F Fansubs.cat');
			$valid = TRUE;
		}
	}

	if ($valid){
?>
						<div style="text-align: center; width: 100%;">
							<p>Gràcies per posar-te en contacte amb nosaltres! Mirarem de respondre't aviat!<br /><br /><strong><a href="/">Torna a la pàgina principal</a></strong></p>
						</div>
<?php
	}
	else{
?>
						<div style="text-align: center; width: 100%;">
							<p>Hi ha algun error en les dades enviades, si us plau, torna-ho a provar.<br /><br /><strong><a href="/">Torna a la pàgina principal</a></strong></p>
						</div>
<?php
	}
}
else{
?>
						<p>Si has subtitulat alguna obra al català però no estàs associat a cap fansub, pots afegir una notícia al web de Fansubs.cat per difondre-ho.<br />D'altra banda, si coneixes o pertanys a un fansub en català que no aparegui aquí, avisa'ns! Mirarem d'afegir-lo ben aviat, de manera que les properes notícies apareguin automàticament al web.</p><p>Tingues en compte que tot el que enviïs en aquest formulari serà validat manualment per un administrador, de manera que pot passar un temps fins que aparegui al web.</p>
						<form method="post" action="/envia-noticies-contacta">
							<table class="contact-us">
								<tbody>
									<tr>
										<td><strong>Motiu de contacte</strong></td>
										<td>
											<select name="reason" class="wide">
												<option value="add_news">Afegir una notícia</option>
												<option value="new_fansub">Informar d'un nou fansub</option>
												<option value="other">Altres</option>
											</select>
									</tr>
									<tr>
										<td><strong>El teu nom o pseudònim</strong></td>
										<td><input type="text" class="wide" required maxlength="255" name="name"></td>
									</tr>
									<tr>
										<td><strong>El teu correu electrònic</strong></td>
										<td><input type="email" class="wide" required maxlength="255" name="email"></td>
									</tr>
									<tr class="add_news">
										<td><strong>Títol de la notícia</strong></td>
										<td><input type="text" class="wide" required name="add_news_title"></td>
									</tr>
									<tr class="add_news">
										<td><strong>Text de la notícia</strong></td>
										<td><textarea class="tall wide" required name="add_news_contents"></textarea></td>
									</tr>
									<tr class="add_news">
										<td><strong>URL de la notícia</strong><br /><small><small>La notícia l'has de pujar en algun web, ja que a Fansubs.cat no permetem enllaços directes a descàrregues.</small></small></td>
										<td><input type="text" class="wide" required name="add_news_url"></td>
									</tr>
									<tr class="add_news">
										<td><strong>URL d'una imatge</strong><br /><small><small>Pots deixar-ho en blanc si no en tens cap.</small></small></td>
										<td><input type="text" class="wide" name="add_news_image_url"></td>
									</tr>
									<tr class="new_fansub">
										<td><strong>Nom del fansub</strong></td>
										<td><input type="text" class="wide" required maxlength="255" name="new_fansub_name"></td>
									</tr>
									<tr class="new_fansub">
										<td><strong>URL del fansub</strong></td>
										<td><input type="text" class="wide" required maxlength="255" name="new_fansub_url"></td>
									</tr>
									<tr>
										<td><strong>Comentaris</strong><br /><small><small>Comenta'ns el que vulguis, si ho creus apropiat.</small></small></td>
										<td><textarea class="notsotall wide" name="comments"></textarea></td>
									</tr>
								</tbody>
							</table>
							<div style="width: 100%; text-align: center;">
								<input type="submit" value="Envia" />
							</div>
						</form>
<?php
}
?>
					</div>
<?php
require_once('footer.inc.php');
?>
