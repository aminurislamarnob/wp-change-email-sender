<?php
/**
 * Test email HTML template.
 *
 * @package WpChangeEmailSender
 *
 * @var string $heading         Email heading text.
 * @var string $intro           Introductory paragraph text.
 * @var string $custom_message  Optional custom message from the user.
 * @var string $from_name_label Label for the from name row.
 * @var string $from_name       Configured sender name.
 * @var string $from_email_label Label for the from email row.
 * @var string $from_email      Configured sender email.
 * @var string $sent_to_label   Label for the sent-to row.
 * @var string $recipient       Recipient email address.
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f4f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f7;padding:40px 0;">
<tr><td align="center">
<table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;">
	<tr><td style="background:#5539FD;padding:24px 32px;">
		<h1 style="margin:0;color:#ffffff;font-size:20px;font-weight:600;"><?php echo esc_html( $heading ); ?></h1>
	</td></tr>
	<tr><td style="padding:32px;">
		<p style="margin:0 0 24px;color:#51545e;font-size:15px;line-height:1.6;"><?php echo esc_html( $intro ); ?></p>
		<?php if ( ! empty( $custom_message ) ) : ?>
		<p style="margin:0 0 24px;color:#1f2937;font-size:15px;line-height:1.6;background:#f9fafb;padding:16px;border-radius:6px;"><?php echo nl2br( esc_html( $custom_message ) ); ?></p>
		<?php endif; ?>
		<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:6px;overflow:hidden;">
			<tr style="background:#f9fafb;">
				<td style="padding:10px 16px;font-size:13px;color:#6b7280;border-bottom:1px solid #e5e7eb;width:120px;"><?php echo esc_html( $from_name_label ); ?></td>
				<td style="padding:10px 16px;font-size:14px;color:#1f2937;border-bottom:1px solid #e5e7eb;"><?php echo esc_html( $from_name ); ?></td>
			</tr>
			<tr>
				<td style="padding:10px 16px;font-size:13px;color:#6b7280;border-bottom:1px solid #e5e7eb;"><?php echo esc_html( $from_email_label ); ?></td>
				<td style="padding:10px 16px;font-size:14px;color:#1f2937;border-bottom:1px solid #e5e7eb;"><?php echo esc_html( $from_email ); ?></td>
			</tr>
			<tr style="background:#f9fafb;">
				<td style="padding:10px 16px;font-size:13px;color:#6b7280;"><?php echo esc_html( $sent_to_label ); ?></td>
				<td style="padding:10px 16px;font-size:14px;color:#1f2937;"><?php echo esc_html( $recipient ); ?></td>
			</tr>
		</table>
	</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
