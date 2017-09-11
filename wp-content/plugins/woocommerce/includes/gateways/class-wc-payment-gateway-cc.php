<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Credit Card Payment Gateway
 *
 * @since       2.6.0
 * @package		WooCommerce/Classes
 * @author 		WooThemes
 */
class WC_Payment_Gateway_CC extends WC_Payment_Gateway {

	/**
	 * Builds our payment fields area - including tokenization fields for logged
	 * in users, and the actual payment fields.
	 * @since 2.6.0
	 */
	public function payment_fields() {
		if ( $this->supports( 'tokenization' ) && is_checkout() ) {
			$this->tokenization_script();
			$this->saved_payment_methods();
			$this->form();
			$this->save_payment_method_checkbox();
		} else {
			$this->form();
		}
	}

	/**
	 * Output field name HTML
	 *
	 * Gateways which support tokenization do not require names - we don't want the data to post to the server.
	 *
	 * @since  2.6.0
	 * @param  string $name
	 * @return string
	 */
	public function field_name( $name ) {
		return $this->supports( 'tokenization' ) ? '' : ' name="' . esc_attr( $this->id . '-' . $name ) . '" ';
	}

	/**
	 * Outputs fields for entering credit card information.
	 * @since 2.6.0
	 */
	public function form() {
		wp_enqueue_script( 'wc-credit-card-form' );

		$fields = array();

		$cvc_field = '<p class="form-row form-row-last">
			<label for="' . esc_attr( $this->id ) . '-card-cvc">' . esc_html__( 'Card code', 'woocommerce' ) . ' <span class="required">*</span></label>
			<input id="' . esc_attr( $this->id ) . '-card-cvc" class="input-text wc-credit-card-form-card-cvc" inputmode="numeric" autocomplete="off" autocorrect="no" autocapitalize="no" spellcheck="no" type="tel" maxlength="4" placeholder="' . esc_attr__( 'CVC', 'woocommerce' ) . '" ' . $this->field_name( 'card-cvc' ) . ' style="width:100px" />
		</p>';

		$default_fields = array(
			'card-number-field' => '<p class="form-row form-row-wide">
				<label for="' . esc_attr( $this->id ) . '-card-number">' . esc_html__( 'Card number', 'woocommerce' ) . ' <span class="required">*</span></label>
				<input id="' . esc_attr( $this->id ) . '-card-number" class="input-text wc-credit-card-form-card-number" inputmode="numeric" autocomplete="cc-number" autocorrect="no" autocapitalize="no" spellcheck="no" type="tel" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" ' . $this->field_name( 'card-number' ) . ' />
			</p>',
			'expdateyear' => '<p class="form-row form-row-first">
				<label for="' . esc_attr( $this->id ) . '-card-expiry-year">Expiry year <span class="required">*</span></label>
				<select name="expdateyear" id="' . esc_attr( $this->id ) . '-card-expiry-year" class="select-expdate select-expdate-year" tabindex="-1">
					<option value="2017">2017</option>
					<option value="2018">2018</option>
					<option value="2019">2019</option>
					<option value="2020">2020</option>
					<option value="2021">2021</option>
					<option value="2022">2022</option>
					<option value="2023">2023</option>
					<option value="2024">2024</option>
					<option value="2025">2025</option>
					<option value="2026">2026</option>
					<option value="2027">2027</option>
					<option value="2028">2028</option>
					<option value="2029">2029</option>
					<option value="2030">2030</option>
				</select>',
			'expdatemonth' => '<p class="form-row form-row-first">
				<label for="' . esc_attr( $this->id ) . '-card-expiry-month">Expiry Month <span class="required">*</span></label>
				<select name="expdatemonth" id="' . esc_attr( $this->id ) . '-card-expiry-month" class="select-expdate select-expdate-month" tabindex="-1">
					<option value="01">01-January</option>
					<option value="02">02-February</option>
					<option value="03">03-March</option>
					<option value="04">04-April</option>
					<option value="05">05-May</option>
					<option value="06">06-June</option>
					<option value="07">07-July</option>
					<option value="08">08-August</option>
					<option value="09">09-September</option>
					<option value="10">10-October</option>
					<option value="11">11-November</option>
					<option value="12">12-December</option>
				</select>',
		);

		if ( ! $this->supports( 'credit_card_form_cvc_on_saved_method' ) ) {
			$default_fields['card-cvc-field'] = $cvc_field;
		}

		$fields = wp_parse_args( $fields, apply_filters( 'woocommerce_credit_card_form_fields', $default_fields, $this->id ) );
		?>

		<fieldset id="wc-<?php echo esc_attr( $this->id ); ?>-cc-form" class='wc-credit-card-form wc-payment-form'>
			<?php do_action( 'woocommerce_credit_card_form_start', $this->id ); ?>
			<?php
				foreach ( $fields as $field ) {
				echo $field;
				}
			?>
			<?php do_action( 'woocommerce_credit_card_form_end', $this->id ); ?>
			<div class="clear"></div>
		</fieldset>
		<?php

		if ( $this->supports( 'credit_card_form_cvc_on_saved_method' ) ) {
			echo '<fieldset>' . $cvc_field . '</fieldset>';
		}
	}
}
