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
								<img src="<?=$this->createAbsoluteUrl('/images/email/logo.gif')?>" width="250" height="39" alt="GigaScience's logo" border="0">
							</td>
						</tr>
						<tr>
							<td width="600" bgcolor="#ffffff" style="padding-left:40px;padding-right:40px;line-height:20px;padding-bottom:10px;">
								<h2 style="font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;color:#626262; font-size:24px; font-weight:200;padding-top:10px;">Password reset</h2>
								<p style="color:#626262; font-size:15px; font-weight:200;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;">We have reset your password to <b><? if (isset($password_unhashed)) echo $password_unhashed; else  echo 'PASSWORD_IS_EMPTY'; ?></b>.
								<br/>Please login and change your password <a href="http://www.gigadb.org/site/login" target="_blank" style="color:#006633;">here</a>.</p>
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
