<?php
/**
 * Plugin Name: Rahbar Contact
 * Description: فرم تماس حداقلی و امن برای Rebuild راهبر حساب.
 * Version: 0.1.0
 * Requires at least: 7.0
 * Requires PHP: 8.1
 */
declare(strict_types=1);
if ( ! defined( 'ABSPATH' ) ) { exit; }

function rahbar_contact_redirect( string $status ): void {
	wp_safe_redirect( add_query_arg( 'contact', $status, home_url( '/contact/' ) ) . '#contact-form' );
	exit;
}

function rahbar_contact_form_shortcode(): string {
	$status = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '';
	ob_start(); ?>
	<div class="rahbar-contact-form-wrap" id="contact-form">
		<?php if ( 'sent' === $status ) : ?><p class="rahbar-form-message rahbar-form-message--success" role="status">پیام شما با موفقیت ثبت شد.</p>
		<?php elseif ( 'invalid' === $status ) : ?><p class="rahbar-form-message rahbar-form-message--error" role="alert">لطفاً فیلدهای الزامی را به‌درستی تکمیل کنید.</p>
		<?php elseif ( 'failed' === $status ) : ?><p class="rahbar-form-message rahbar-form-message--error" role="alert">ارسال پیام انجام نشد. لطفاً با شماره‌های مجموعه تماس بگیرید.</p>
		<?php elseif ( 'limited' === $status ) : ?><p class="rahbar-form-message rahbar-form-message--error" role="alert">تعداد درخواست‌ها بیش از حد مجاز است. کمی بعد دوباره تلاش کنید.</p><?php endif; ?>
		<form class="rahbar-contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="rahbar_contact_submit"><?php wp_nonce_field( 'rahbar_contact_submit', 'rahbar_contact_nonce' ); ?>
			<div class="rahbar-contact-honeypot" aria-hidden="true"><label>وب‌سایت<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
			<p><label for="rahbar-contact-first-name">نام <span aria-hidden="true">*</span></label><input id="rahbar-contact-first-name" name="first_name" type="text" maxlength="80" autocomplete="given-name" required></p>
			<p><label for="rahbar-contact-last-name">نام خانوادگی <span aria-hidden="true">*</span></label><input id="rahbar-contact-last-name" name="last_name" type="text" maxlength="100" autocomplete="family-name" required></p>
			<p class="rahbar-contact-form__wide"><label for="rahbar-contact-phone">تلفن <span aria-hidden="true">*</span></label><input id="rahbar-contact-phone" name="phone" type="tel" maxlength="24" inputmode="tel" autocomplete="tel" required></p>
			<p class="rahbar-contact-form__wide"><label for="rahbar-contact-message">پیام</label><textarea id="rahbar-contact-message" name="message" rows="5" maxlength="2000"></textarea></p>
			<p class="rahbar-contact-form__wide"><button type="submit">ارسال</button></p>
		</form>
	</div>
	<?php return (string) ob_get_clean();
}
add_shortcode( 'rahbar_contact_form', 'rahbar_contact_form_shortcode' );

function rahbar_contact_handle_submit(): void {
	if ( ! isset( $_POST['rahbar_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rahbar_contact_nonce'] ) ), 'rahbar_contact_submit' ) ) { rahbar_contact_redirect( 'invalid' ); }
	if ( ! empty( $_POST['website'] ) ) { rahbar_contact_redirect( 'sent' ); }
	$ip_hash = hash_hmac( 'sha256', (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ), wp_salt( 'nonce' ) );
	$key = 'rahbar_contact_' . substr( $ip_hash, 0, 24 );
	$count = (int) get_transient( $key );
	if ( $count >= 5 ) { rahbar_contact_redirect( 'limited' ); }
	set_transient( $key, $count + 1, HOUR_IN_SECONDS );
	$first = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$last = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
	$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	if ( '' === $first || '' === $last || '' === $phone || mb_strlen( $first ) > 80 || mb_strlen( $last ) > 100 || mb_strlen( $phone ) > 24 || mb_strlen( $message ) > 2000 ) { rahbar_contact_redirect( 'invalid' ); }
	$recipient = sanitize_email( (string) get_option( 'admin_email' ) );
	$subject = sprintf( 'پیام جدید از %s %s', $first, $last );
	$body = "نام: {$first} {$last}\nتلفن: {$phone}\n\nپیام:\n{$message}";
	$sent = wp_mail( $recipient, $subject, $body, array( 'Content-Type: text/plain; charset=UTF-8' ) );
	rahbar_contact_redirect( $sent ? 'sent' : 'failed' );
}
add_action( 'admin_post_nopriv_rahbar_contact_submit', 'rahbar_contact_handle_submit' );
add_action( 'admin_post_rahbar_contact_submit', 'rahbar_contact_handle_submit' );
