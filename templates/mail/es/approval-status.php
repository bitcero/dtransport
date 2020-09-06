<p>Hola <strong><?php echo $userName; ?></strong>:</p>

<p>Tu descarga <strong><?php echo $download?></strong> ha sido <span style="font-size: 16px; font-weight: 700; color: #ad0000;"><?php echo $status?></span>.</p>

<?php if('' != $message): ?>
<p>
    <strong>Mensaje del administrador:</strong><br><br>
    <?php echo $message; ?>
</p>
<?php endif; ?>

<p>
    Administrar: <?php echo $urlManage; ?><br>
    Ver: <?php echo $urlView; ?>
</p>

<hr>

<p>Si crees que este mensaje ha sido enviado por error, por favor contacta al administrador.</p>

<small>
    <a href="<?php echo $siteUrl; ?>"><?php echo $siteName; ?></a>
</small>


