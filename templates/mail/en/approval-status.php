<p>Hello <strong><?php echo $userName; ?></strong>:</p>

<p>Your download item <strong><?php echo $download?></strong> has been <span style="font-size: 16px; font-weight: 700; color: #ad0000;"><?php echo $status?></span>.</p>

<?php if('' != $message): ?>
<p>
    <strong>Message from administrator:</strong><br><br>
    <?php echo $message; ?>
</p>
<?php endif; ?>

<p>
    Manage: <?php echo $urlManage; ?><br>
    View: <?php echo $urlView; ?>
</p>

<hr>

<p>If you think that this is an error please contact the administrator.</p>

<small>
    <a href="<?php echo $siteUrl; ?>"><?php echo $siteName; ?></a>
</small>


