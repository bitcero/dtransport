<?php if($method!=1): ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- Facebook sharing information tags -->
        <meta property="og:title" content="Download Deletion">
        <title><?php echo sprintf('%s Eliminado!',$download); ?></title>
        <style type="text/css">
            body{
                width:100% !important;
                -webkit-text-size-adjust:none;
                margin:0;
                padding:0;
                font-family: Helvetica Neue, Arial, Helvetica, Geneva, sans-serif;
            }
            img{
                border:0;
                height:auto;
                line-height:100%;
                outline:none;
                text-decoration:none;
            }
            table td{
                border-collapse:collapse;
            }
            #backgroundTable{
                height:100% !important;
                margin:0;
                padding:0;
                width:100% !important;
            }
            body,#backgroundTable{
                background-color:#FAFAFA;
            }
            #templateContainer{
                border:1px solid #DDDDDD;
            }
            h1,.h1{
                color:#B0B0B0;
                display:block;
                font-family:Arial;
                font-size:34px;
                font-weight:bold;
                line-height:100%;
                margin-top:0;
                margin-right:0;
                margin-bottom:10px;
                margin-left:0;
            }
            h2,.h2{
                color:#B0B0B0;
                display:block;
                font-family:Arial;
                font-size:30px;
                font-weight:bold;
                line-height:100%;
                margin-top:0;
                margin-right:0;
                margin-bottom:10px;
                margin-left:0;
            }
            h3,.h3{
                color:#B0B0B0;
                display:block;
                font-family:Arial;
                font-size:26px;
                font-weight:bold;
                line-height:100%;
                margin-top:0;
                margin-right:0;
                margin-bottom:10px;
                margin-left:0;
            }
            h4,.h4{
                color:#B0B0B0;
                display:block;
                font-family:Arial;
                font-size:22px;
                font-weight:bold;
                line-height:100%;
                margin-top:0;
                margin-right:0;
                margin-bottom:10px;
                margin-left:0;
            }
        </style>
    </head>

    <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="-webkit-text-size-adjust: none;margin: 20px;padding: 0;background-color: #DEDEDE;width: 100% !important;font-family: Helvetica Neue, Arial, Helvetica, Geneva, sans-serif; font-size: 12px;color:#444444;">
        <table width="600" border="0" align="center" cellpadding="20" cellspacing="0" style="border-radius: 5px; -webkit-border-radius: 5px; -moz-border-radius: 5px;background-color: #FFF;">
          <tr style="background-color: #2a2a2a;">
            <td width="350" valign="middle" align="left" style="padding: 10px; color: #ededed;font-weight: bold;border-radius: 5px 5px 0 0; -webkit-border-radius: 5px 5px 0 0; -moz-border-radius: 5px 5px 0 0;">
                <a href="<?php echo $siteurl; ?>" style="color: #ededed;"><?php echo $sitename; ?></a> |
                <a href="<?php echo $dturl; ?>" style="color: #ededed;"><?php echo $dtname; ?></a>
            </td>
          </tr>
          <tr>
            <td align="center"><h2 style="color:#444444;">Aviso de Eliminación de Descarga</h2></td>
          </tr>
          <tr>
            <td>
                <h4 style="padding: 0; margin-top: 0; margin-left: 0; margin-right: 0; margin-bottom: 10px;"><?php echo sprintf('Hola %s:', '<span style="font-weight: bold;">'.$uname.'</span>'); ?></h4>
                <p>
                    La descarga llamada "<?php echo $download; ?>", creada por ti ha sido removida de nuestra
                    base de datos, y ya no estará disponible en nuestro sitio.
                    Si necesitas revisar los cambios hechos en tu cuenta por favor
                    <a href="<?php echo $siteurl?>/user.php">ingresa</a> y ve a tu <a href="<?php echo $downcp; ?>">panel de control
                    de descargas</a>.
                </p>
                <p>
                    Si crees que este es un error, por favor contáctanos para obtener mas información.
                </p>
                <p>
                    <strong><?php echo $sitename; ?></strong>
                </p>
            </td>
          </tr>
          <tr>
            <td style="background-color: #2a2a2a; color: #ededed;" align="center">
                Has recibido este mensaje porque estas registrado con este email
                <em>(<?php echo $email; ?>)</em> en nuestro sitio web.</td>
          </tr>
        </table>
    </body>
</html>
<?php else: ?>
Aviso de eliminación de descarga.

Hola <?php echo $uname; ?>:

Please note that the download called "<?php echo $download; ?>", created by you has been removed from our database, and will not be available.
If you need to review the changes made on your account please [url=<?php echo $siteurl; ?>/user.php]login[/url] and go to your [url=<?php echo $downcp; ?>]downloads control panel[/url].

La descarga llamada "<?php echo $download; ?>", creada por ti ha sido removida de nuestra
base de datos, y ya no estará disponible en nuestro sitio.
Si necesitas revisar los cambios hechos en tu cuenta por favor
[url=<?php echo $siteurl; ?>/user.php]ingresa[/url] y ve a tu
[url=<?php echo $downcp; ?>]panel de control de descargas[/url].

Si crees que este es un error, por favor contáctanos para obtener mas información.

[b]<?php echo $sitename; ?>[/b]

---------------------------------------------------------
Has recibido este mensaje porque estas registrado con este email (<?php echo $email; ?>) en nuestro sitio web.

<?php endif; ?>