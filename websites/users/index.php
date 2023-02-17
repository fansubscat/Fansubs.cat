<?php
require_once('header.inc.php');

if (!empty($user)){
?>
Sessió iniciada com a <?php echo $user['username']; ?>.
<?php
} else {
?>
Sessió NO iniciada.
<?php
}

require_once('footer.inc.php');
?>
