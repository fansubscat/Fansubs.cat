<?php
$style_type='text';
require_once("../common.fansubs.cat/header.inc.php");

if (!empty($user)){
?>
Sessió iniciada com a <?php echo $user['username']; ?>.
<?php
} else {
?>
Sessió NO iniciada.
<?php
}
require_once("../common.fansubs.cat/footer.inc.php");
?>
