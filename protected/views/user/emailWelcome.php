<html>
  <body style="margin: 0; padding: 0; background: #ebf6f0;">
  	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td align="center" width="100%" bgcolor="#ebf6f0" style="margin: 0;">
			    <table cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="font-family:'Helvetica Neue', Helvetica, Arial, sans-serif; color: #6f6f6f;">
			    	<tr>
					  <td height="20" width="600"></td>
					</tr>
					<tr>
					  <td height="6" width="600">
					    <img src="<?=$this->createAbsoluteUrl('/images/email/top.gif')?>" width="600" height="50" alt="Banner" border="0" valign="bottom" style="vertical-align:bottom;">
					  </td>
					</tr>
					<tr>
					    <td align="left" width="600" bgcolor="#ffffff;" style="background-color:#ffffff;border-bottom:1px solid #D7E1EE; padding-bottom: 10px; padding-left:40px; padding-top:10px;">
					      <img src="<?=$this->createAbsoluteUrl('/images/email/logo.gif')?>" width="250" height="39" alt="GigaDB's logo" border="0">
					    </td>
					</tr>
					<tr>
					    <td width="600" bgcolor="#ffffff" style="padding-left:40px;padding-right:40px;line-height:20px;padding-bottom:10px;padding-top:10px;">
					    	<h2 style="font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#626262; font-size:24px; font-weight:200;">Welcome to GigaDB!</h2>
					      <p style="color:#626262; font-size:15px; font-weight:200;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;">Thanks for signing up!<br />Before we can give you access to all of GigaDB's features, please confirm your account by clicking the link below.</p>
							</td>
					</tr>
					<tr>
					  	<td width="600" align="center" bgcolor="#ffffff;" style="background-color:#ffffff;border-top:1px solid #D7E1EE;padding-top:15px;padding-bottom:20px;">
					    <a href="<?php if (isset($url)) echo $url; else echo 'http://cogini.com'; ?>"  target="_blank" style="font-family:'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:15px; color:#ffffff; text-decoration:none; background-image:url(/images/email/confirm-btn.gif); background-color:#6ea23a; display:block; width:250px; height:26px; padding-top:4px;">Confirm your account</a>
					  	</td>
					</tr>
					<tr>
					    <td height="6" width="600">
					        <img src="<?=$this->createAbsoluteUrl('/images/email/bottom.gif')?>" width="600" height="70" alt="Footer" border="0">
					    </td>
					</tr>
					<tr>
					  <td height="20" width="600"></td>
					</tr>
				</table>
			</td>
		</tr>
    </table>
  </body>
</html>
