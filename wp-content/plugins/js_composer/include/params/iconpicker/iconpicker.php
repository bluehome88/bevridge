<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 *
 * Class Vc_IconPicker
 * @since 4.4
 * See example usage in shortcode 'vc_icon'
 *
 *      `` example
 *        array(
 *            'type' => 'iconpicker',
 *            'heading' => __( 'Icon', 'js_composer' ),
 *            'param_name' => 'icon_fontawesome',
 *            'settings' => array(
 *                'emptyIcon' => false, // default true, display an "EMPTY" icon? - if false it will display first icon
 *     from set as default.
 *                'iconsPerPage' => 200, // default 100, how many icons per/page to display
 *            ),
 *            'dependency' => array(
 *                'element' => 'type',
 *                'value' => 'fontawesome',
 *            ),
 *        ),
 * vc_filter: vc_iconpicker-type-{your_icon_font_name} - filter to add new icon font type. see example for
 *     vc_iconpicker-type-fontawesome in bottom of this file Also // SEE HOOKS FOLDER FOR FONTS REGISTERING/ENQUEUE IN
 *     BASE @path "/include/autoload/hook-vc-iconpicker-param.php"
 */
class Vc_IconPicker {
	/**
	 * @since 4.4
	 * @var array - save current param data array from vc_map
	 */
	protected $settings;
	/**
	 * @since 4.4
	 * @var string - save a current field value
	 */
	protected $value;
	/**
	 * @since 4.4
	 * @var array - optional, can be used as self source from self array., you can pass it also with filter see
	 *     Vc_IconPicker::setDefaults
	 */
	protected $source = array();

	/**
	 * @since 4.4
	 *
	 * @param $settings - param field data array
	 * @param $value - param field value
	 */
	public function __construct( $settings, $value ) {
		$this->settings = $settings;
		$this->setDefaults();

		$this->value = $value; // param field value
	}

	/**
	 * Set default function will extend current settings with defaults
	 * It can be used in Vc_IconPicker::render, but also it is passed to input field and was hooked in composer-atts.js
	 * file See vc.atts.iconpicker in wp-content/plugins/js_composer/assets/js/params/composer-atts.js init method
	 *  - it initializes javascript logic, you can provide ANY default param to it with 'settings' key
	 * @since 4.4
	 */
	protected function setDefaults() {
		if ( ! isset( $this->settings['settings'], $this->settings['settings']['type'] ) ) {
			$this->settings['settings']['type'] = 'fontawesome'; // Default type for icons
		}

		// More about this you can read in http://codeb.it/fonticonpicker/
		if ( ! isset( $this->settings['settings'], $this->settings['settings']['hasSearch'] ) ) {
			// Whether or not to show the search bar.
			$this->settings['settings']['hasSearch'] = true;
		}
		if ( ! isset( $this->settings['settings'], $this->settings['settings']['emptyIcon'] ) ) {
			// Whether or not empty icon should be shown on the icon picker
			$this->settings['settings']['emptyIcon'] = true;
		}
		if ( ! isset( $this->settings['settings'], $this->settings['settings']['allCategoryText'] ) ) {
			// If categorized then use this option
			$this->settings['settings']['allCategoryText'] = __( 'From all categories', 'js_composer' );
		}
		if ( ! isset( $this->settings['settings'], $this->settings['settings']['unCategorizedText'] ) ) {
			// If categorized then use this option
			$this->settings['settings']['unCategorizedText'] = __( 'Uncategorized', 'js_composer' );
		}

		/**
		 * Source for icons, can be passed via "mapping" or with filter vc_iconpicker-type-{your_type} (default fontawesome)
		 * vc_filter: vc_iconpicker-type-{your_type} (default fontawesome)
		 */
		if ( isset( $this->settings['settings'], $this->settings['settings']['source'] ) ) {
			$this->source = $this->settings['settings']['source'];
			unset( $this->settings['settings']['source'] ); // We don't need this on frontend.(js)
		}
	}

	/**
	 * Render edit form field type 'iconpicker' with selected settings and provided value.
	 * It uses javascript file vc-icon-picker (vc_iconpicker_base_register_js, vc_iconpicker_editor_jscss),
	 * see wp-content/plugins/js_composer/include/autoload/hook-vc-iconpicker-param.php folder
	 * @since 4.4
	 * @return string - rendered param field for editor panel
	 */
	public function render() {

		$output = '<div class="vc-iconpicker-wrapper"><select class="vc-iconpicker">';

		// call filter vc_iconpicker-type-{your_type}, e.g. vc_iconpicker-type-fontawesome with passed source from shortcode(default empty array). to get icons
		$arr = apply_filters( 'vc_iconpicker-type-' . esc_attr( $this->settings['settings']['type'] ), $this->source );
		if ( isset( $this->settings['settings'], $this->settings['settings']['emptyIcon'] ) && true === $this->settings['settings']['emptyIcon'] ) {
			array_unshift( $arr, array() );
		}
		if ( ! empty( $arr ) ) {
			foreach ( $arr as $group => $icons ) {
				if ( ! is_array( $icons ) || ! is_array( current( $icons ) ) ) {
					$class_key = key( $icons );
					$output .= '<option value="' . esc_attr( $class_key ) . '" ' . ( strcmp( $class_key, $this->value ) === 0 ? 'selected' : '' ) . '>' . esc_html( current( $icons ) ) . '</option>' . "\n";
				} else {
					$output .= '<optgroup label="' . esc_attr( $group ) . '">' . "\n";
					foreach ( $icons as $key => $label ) {
						$class_key = key( $label );
						$output .= '<option value="' . esc_attr( $class_key ) . '" ' . ( strcmp( $class_key, $this->value ) === 0 ? 'selected' : '' ) . '>' . esc_html( current( $label ) ) . '</option>' . "\n";
					}
					$output .= '</optgroup>' . "\n";
				}
			}
		}
		$output .= '</select></div>';

		$output .= '<input name="' .
		           esc_attr( $this->settings['param_name'] ) .
		           '" class="wpb_vc_param_value  ' .
		           esc_attr( $this->settings['param_name'] ) . ' ' .
		           esc_attr( $this->settings['type'] ) . '_field" type="hidden" value="' . esc_attr( $this->value ) . '" ' .
		           ( ( isset( $this->settings['settings'] ) && ! empty( $this->settings['settings'] ) ) ? ' data-settings="' . esc_attr( json_encode( $this->settings['settings'] ) ) . '" ' : '' ) .
		           ' />';

		return $output;
	}
}

/**
 * Function for rendering param in edit form (add element)
 * Parse settings from vc_map and entered values.
 *
 * @param $settings
 * @param $value
 * @param $tag
 *
 * @since 4.4
 * @return string - rendered template for params in edit form
 *
 */
function vc_iconpicker_form_field( $settings, $value, $tag ) {

	$icon_picker = new Vc_IconPicker( $settings, $value, $tag );

	return apply_filters( 'vc_iconpicker_render_filter', $icon_picker->render() );
}

// SEE HOOKS FOLDER FOR FONTS REGISTERING/ENQUEUE IN BASE @path "/include/autoload/hook-vc-iconpicker-param.php"

add_filter( 'vc_iconpicker-type-fontawesome', 'vc_iconpicker_type_fontawesome' );

/**
 * Fontawesome icons from FontAwesome :)
 *
 * @param $icons - taken from filter - vc_map param field settings['source'] provided icons (default empty array).
 * If array categorized it will auto-enable category dropdown
 *
 * @since 4.4
 * @return array - of icons for iconpicker, can be categorized, or not.
 */
function vc_iconpicker_type_fontawesome( $icons ) {
	// Categorized icons ( you can also output simple array ( key=> value ), where key = icon class, value = icon readable name ).

	$fontawesome_icons = array(
		'20 New Icons in 4.5' => array(
			array( 'fa fa-bluetooth' => 'Bluetooth' ),
			array( 'fa fa-bluetooth-b' => 'Bluetooth-b' ),
			array( 'fa fa-codiepie' => 'Codiepie' ),
			array( 'fa fa-credit-card-alt' => 'Credit-card-alt' ),
			array( 'fa fa-edge' => 'Edge' ),
			array( 'fa fa-fort-awesome' => 'Fort-awesome' ),
			array( 'fa fa-hashtag' => 'Hashtag' ),
			array( 'fa fa-mixcloud' => 'Mixcloud' ),
			array( 'fa fa-modx' => 'Modx' ),
			array( 'fa fa-pause-circle' => 'Pause-circle' ),
			array( 'fa fa-pause-circle-o' => 'Pause-circle-o' ),
			array( 'fa fa-percent' => 'Percent' ),
			array( 'fa fa-product-hunt' => 'Product-hunt' ),
			array( 'fa fa-reddit-alien' => 'Reddit-alien' ),
			array( 'fa fa-scribd' => 'Scribd' ),
			array( 'fa fa-shopping-bag' => 'Shopping-bag' ),
			array( 'fa fa-shopping-basket' => 'Shopping-basket' ),
			array( 'fa fa-stop-circle' => 'Stop-circle' ),
			array( 'fa fa-stop-circle-o' => 'Stop-circle-o' ),
			array( 'fa fa-usb' => 'Usb' ),
		),
		'Web Application Icons' => array(
			array( 'fa fa-adjust' => 'Adjust' ),
			array( 'fa fa-anchor' => 'Anchor' ),
			array( 'fa fa-archive' => 'Archive' ),
			array( 'fa fa-area-chart' => 'Area Chart' ),
			array( 'fa fa-arrows' => 'Arrows' ),
			array( 'fa fa-arrows-h' => 'Arrows Horizontal' ),
			array( 'fa fa-arrows-v' => 'Arrows Vertical' ),
			array( 'fa fa-asterisk' => 'Asterisk' ),
			array( 'fa fa-at' => 'At' ),
			array( 'fa fa-balance-scale' => 'Balance Scale' ),
			array( 'fa fa-ban' => 'Ban' ),
			array( 'fa fa-bar-chart' => 'Bar Chart (bar-chart-o)' ),
			array( 'fa fa-barcode' => 'Barcode' ),
			array( 'fa fa-bars' => 'Bars (navicon, reorder)' ),
			array( 'fa fa-battery-empty' => 'Battery Empty (battery-0)' ),
			array( 'fa fa-battery-full' => 'Battery Full (battery-4)' ),
			array( 'fa fa-battery-half' => 'Battery 1/2 Full (battery-2)' ),
			array( 'fa fa-battery-quarter' => 'Battery 1/4 Full (battery-1)' ),
			array( 'fa fa-battery-three-quarters' => 'Battery 3/4 Full (battery-3)' ),
			array( 'fa fa-bed' => 'Bed (hotel)' ),
			array( 'fa fa-beer' => 'Beer' ),
			array( 'fa fa-bell' => 'Bell' ),
			array( 'fa fa-bell-o' => 'Bell Outlined' ),
			array( 'fa fa-bell-slash' => 'Bell Slash' ),
			array( 'fa fa-bell-slash-o' => 'Bell Slash Outlined' ),
			array( 'fa fa-bicycle' => 'Bicycle' ),
			array( 'fa fa-binoculars' => 'Binoculars' ),
			array( 'fa fa-birthday-cake' => 'Birthday Cake' ),
			array( 'fa fa-bluetooth' => 'Bluetooth' ),
			array( 'fa fa-bluetooth-b' => 'Bluetooth' ),
			array( 'fa fa-bolt' => 'Lightning Bolt (flash)' ),
			array( 'fa fa-bomb' => 'Bomb' ),
			array( 'fa fa-book' => 'Book' ),
			array( 'fa fa-bookmark' => 'Bookmark' ),
			array( 'fa fa-bookmark-o' => 'Bookmark Outlined' ),
			array( 'fa fa-briefcase' => 'Briefcase' ),
			array( 'fa fa-bug' => 'Bug' ),
			array( 'fa fa-building' => 'Building' ),
			array( 'fa fa-building-o' => 'Building Outlined' ),
			array( 'fa fa-bullhorn' => 'Bullhorn' ),
			array( 'fa fa-bullseye' => 'Bullseye' ),
			array( 'fa fa-bus' => 'Bus' ),
			array( 'fa fa-calculator' => 'Calculator' ),
			array( 'fa fa-calendar' => 'Calendar' ),
			array( 'fa fa-calendar-check-o' => 'Calendar Check Outlined' ),
			array( 'fa fa-calendar-minus-o' => 'Calendar Minus Outlined' ),
			array( 'fa fa-calendar-o' => 'Calendar-o' ),
			array( 'fa fa-calendar-plus-o' => 'Calendar Plus Outlined' ),
			array( 'fa fa-calendar-times-o' => 'Calendar Times Outlined' ),
			array( 'fa fa-camera' => 'Camera' ),
			array( 'fa fa-camera-retro' => 'Camera-retro' ),
			array( 'fa fa-car' => 'Car (automobile)' ),
			array( 'fa fa-caret-square-o-down' => 'Caret Square Outlined Down (toggle-down)' ),
			array( 'fa fa-caret-square-o-left' => 'Caret Square Outlined Left (toggle-left)' ),
			array( 'fa fa-caret-square-o-right' => 'Caret Square Outlined Right (toggle-right)' ),
			array( 'fa fa-caret-square-o-up' => 'Caret Square Outlined Up (toggle-up)' ),
			array( 'fa fa-cart-arrow-down' => 'Shopping Cart Arrow Down' ),
			array( 'fa fa-cart-plus' => 'Add to Shopping Cart' ),
			array( 'fa fa-cc' => 'Closed Captions' ),
			array( 'fa fa-certificate' => 'Certificate' ),
			array( 'fa fa-check' => 'Check' ),
			array( 'fa fa-check-circle' => 'Check Circle' ),
			array( 'fa fa-check-circle-o' => 'Check Circle Outlined' ),
			array( 'fa fa-check-square' => 'Check Square' ),
			array( 'fa fa-check-square-o' => 'Check Square Outlined' ),
			array( 'fa fa-child' => 'Child' ),
			array( 'fa fa-circle' => 'Circle' ),
			array( 'fa fa-circle-o' => 'Circle Outlined' ),
			array( 'fa fa-circle-o-notch' => 'Circle Outlined Notched' ),
			array( 'fa fa-circle-thin' => 'Circle Outlined Thin' ),
			array( 'fa fa-clock-o' => 'Clock Outlined' ),
			array( 'fa fa-clone' => 'Clone' ),
			array( 'fa fa-cloud' => 'Cloud' ),
			array( 'fa fa-cloud-download' => 'Cloud Download' ),
			array( 'fa fa-cloud-upload' => 'Cloud Upload' ),
			array( 'fa fa-code' => 'Code' ),
			array( 'fa fa-code-fork' => 'Code-fork' ),
			array( 'fa fa-coffee' => 'Coffee' ),
			array( 'fa fa-cog' => 'Cog (gear)' ),
			array( 'fa fa-cogs' => 'Cogs (gears)' ),
			array( 'fa fa-comment' => 'Comment' ),
			array( 'fa fa-comment-o' => 'Comment-o' ),
			array( 'fa fa-commenting' => 'Commenting' ),
			array( 'fa fa-commenting-o' => 'Commenting Outlined' ),
			array( 'fa fa-comments' => 'Comments' ),
			array( 'fa fa-comments-o' => 'Comments-o' ),
			array( 'fa fa-compass' => 'Compass' ),
			array( 'fa fa-copyright' => 'Copyright' ),
			array( 'fa fa-creative-commons' => 'Creative Commons' ),
			array( 'fa fa-credit-card' => 'Credit-card' ),
			array( 'fa fa-credit-card-alt' => 'Credit Card' ),
			array( 'fa fa-crop' => 'Crop' ),
			array( 'fa fa-crosshairs' => 'Crosshairs' ),
			array( 'fa fa-cube' => 'Cube' ),
			array( 'fa fa-cubes' => 'Cubes' ),
			array( 'fa fa-cutlery' => 'Cutlery' ),
			array( 'fa fa-database' => 'Database' ),
			array( 'fa fa-desktop' => 'Desktop' ),
			array( 'fa fa-diamond' => 'Diamond' ),
			array( 'fa fa-dot-circle-o' => 'Dot Circle Outlined' ),
			array( 'fa fa-download' => 'Download' ),
			array( 'fa fa-ellipsis-h' => 'Ellipsis Horizontal' ),
			array( 'fa fa-ellipsis-v' => 'Ellipsis Vertical' ),
			array( 'fa fa-envelope' => 'Envelope' ),
			array( 'fa fa-envelope-o' => 'Envelope Outlined' ),
			array( 'fa fa-envelope-square' => 'Envelope Square' ),
			array( 'fa fa-eraser' => 'Eraser' ),
			array( 'fa fa-exchange' => 'Exchange' ),
			array( 'fa fa-exclamation' => 'Exclamation' ),
			array( 'fa fa-exclamation-circle' => 'Exclamation Circle' ),
			array( 'fa fa-exclamation-triangle' => 'Exclamation Triangle (warning)' ),
			array( 'fa fa-external-link' => 'External Link' ),
			array( 'fa fa-external-link-square' => 'External Link Square' ),
			array( 'fa fa-eye' => 'Eye' ),
			array( 'fa fa-eye-slash' => 'Eye Slash' ),
			array( 'fa fa-eyedropper' => 'Eyedropper' ),
			array( 'fa fa-fax' => 'Fax' ),
			array( 'fa fa-female' => 'Female' ),
			array( 'fa fa-fighter-jet' => 'Fighter-jet' ),
			array( 'fa fa-file-archive-o' => 'Archive File Outlined (file-zip-o)' ),
			array( 'fa fa-file-audio-o' => 'Audio File Outlined (file-sound-o)' ),
			array( 'fa fa-file-code-o' => 'Code File Outlined' ),
			array( 'fa fa-file-excel-o' => 'Excel File Outlined' ),
			array( 'fa fa-file-image-o' => 'Image File Outlined (file-photo-o, file-picture-o)' ),
			array( 'fa fa-file-pdf-o' => 'PDF File Outlined' ),
			array( 'fa fa-file-powerpoint-o' => 'Powerpoint File Outlined' ),
			array( 'fa fa-file-video-o' => 'Video File Outlined (file-movie-o)' ),
			array( 'fa fa-file-word-o' => 'Word File Outlined' ),
			array( 'fa fa-film' => 'Film' ),
			array( 'fa fa-filter' => 'Filter' ),
			array( 'fa fa-fire' => 'Fire' ),
			array( 'fa fa-fire-extinguisher' => 'Fire-extinguisher' ),
			array( 'fa fa-flag' => 'Flag' ),
			array( 'fa fa-flag-checkered' => 'Flag-checkered' ),
			array( 'fa fa-flag-o' => 'Flag Outlined' ),
			array( 'fa fa-flask' => 'Flask' ),
			array( 'fa fa-folder' => 'Folder' ),
			array( 'fa fa-folder-o' => 'Folder Outlined' ),
			array( 'fa fa-folder-open' => 'Folder Open' ),
			array( 'fa fa-folder-open-o' => 'Folder Open Outlined' ),
			array( 'fa fa-frown-o' => 'Frown Outlined' ),
			array( 'fa fa-futbol-o' => 'Futbol Outlined (soccer-ball-o)' ),
			array( 'fa fa-gamepad' => 'Gamepad' ),
			array( 'fa fa-gavel' => 'Gavel (legal)' ),
			array( 'fa fa-gift' => 'Gift' ),
			array( 'fa fa-glass' => 'Glass' ),
			array( 'fa fa-globe' => 'Globe' ),
			array( 'fa fa-graduation-cap' => 'Graduation Cap (mortar-board)' ),
			array( 'fa fa-hand-lizard-o' => 'Lizard (Hand)' ),
			array( 'fa fa-hand-paper-o' => 'Paper (Hand) (hand-stop-o)' ),
			array( 'fa fa-hand-peace-o' => 'Hand Peace' ),
			array( 'fa fa-hand-pointer-o' => 'Hand Pointer' ),
			array( 'fa fa-hand-rock-o' => 'Rock (Hand) (hand-grab-o)' ),
			array( 'fa fa-hand-scissors-o' => 'Scissors (Hand)' ),
			array( 'fa fa-hand-spock-o' => 'Spock (Hand)' ),
			array( 'fa fa-hashtag' => 'Hashtag' ),
			array( 'fa fa-hdd-o' => 'HDD' ),
			array( 'fa fa-headphones' => 'Headphones' ),
			array( 'fa fa-heart' => 'Heart' ),
			array( 'fa fa-heart-o' => 'Heart Outlined' ),
			array( 'fa fa-heartbeat' => 'Heartbeat' ),
			array( 'fa fa-history' => 'History' ),
			array( 'fa fa-home' => 'Home' ),
			array( 'fa fa-hourglass' => 'Hourglass' ),
			array( 'fa fa-hourglass-end' => 'Hourglass End (hourglass-3)' ),
			array( 'fa fa-hourglass-half' => 'Hourglass Half (hourglass-2)' ),
			array( 'fa fa-hourglass-o' => 'Hourglass Outlined' ),
			array( 'fa fa-hourglass-start' => 'Hourglass Start (hourglass-1)' ),
			array( 'fa fa-i-cursor' => 'I Beam Cursor' ),
			array( 'fa fa-inbox' => 'Inbox' ),
			array( 'fa fa-industry' => 'Industry' ),
			array( 'fa fa-info' => 'Info' ),
			array( 'fa fa-info-circle' => 'Info Circle' ),
			array( 'fa fa-key' => 'Key' ),
			array( 'fa fa-keyboard-o' => 'Keyboard Outlined' ),
			array( 'fa fa-language' => 'Language' ),
			array( 'fa fa-laptop' => 'Laptop' ),
			array( 'fa fa-leaf' => 'Leaf' ),
			array( 'fa fa-lemon-o' => 'Lemon Outlined' ),
			array( 'fa fa-level-down' => 'Level Down' ),
			array( 'fa fa-level-up' => 'Level Up' ),
			array( 'fa fa-life-ring' => 'Life Ring (life-bouy, life-buoy, life-saver, support)' ),
			array( 'fa fa-lightbulb-o' => 'Lightbulb Outlined' ),
			array( 'fa fa-line-chart' => 'Line Chart' ),
			array( 'fa fa-location-arrow' => 'Location-arrow' ),
			array( 'fa fa-lock' => 'Lock' ),
			array( 'fa fa-magic' => 'Magic' ),
			array( 'fa fa-magnet' => 'Magnet' ),
			array( 'fa fa-male' => 'Male' ),
			array( 'fa fa-map' => 'Map' ),
			array( 'fa fa-map-marker' => 'Map-marker' ),
			array( 'fa fa-map-o' => 'Map Outline' ),
			array( 'fa fa-map-pin' => 'Map Pin' ),
			array( 'fa fa-map-signs' => 'Map Signs' ),
			array( 'fa fa-meh-o' => 'Meh Outlined' ),
			array( 'fa fa-microphone' => 'Microphone' ),
			array( 'fa fa-microphone-slash' => 'Microphone Slash' ),
			array( 'fa fa-minus' => 'Minus' ),
			array( 'fa fa-minus-circle' => 'Minus Circle' ),
			array( 'fa fa-minus-square' => 'Minus Square' ),
			array( 'fa fa-minus-square-o' => 'Minus Square Outlined' ),
			array( 'fa fa-mobile' => 'Mobile Phone (mobile-phone)' ),
			array( 'fa fa-money' => 'Money' ),
			array( 'fa fa-moon-o' => 'Moon Outlined' ),
			array( 'fa fa-motorcycle' => 'Motorcycle' ),
			array( 'fa fa-mouse-pointer' => 'Mouse Pointer' ),
			array( 'fa fa-music' => 'Music' ),
			array( 'fa fa-newspaper-o' => 'Newspaper Outlined' ),
			array( 'fa fa-object-group' => 'Object Group' ),
			array( 'fa fa-object-ungroup' => 'Object Ungroup' ),
			array( 'fa fa-paint-brush' => 'Paint Brush' ),
			array( 'fa fa-paper-plane' => 'Paper Plane (send)' ),
			array( 'fa fa-paper-plane-o' => 'Paper Plane Outlined (send-o)' ),
			array( 'fa fa-paw' => 'Paw' ),
			array( 'fa fa-pencil' => 'Pencil' ),
			array( 'fa fa-pencil-square' => 'Pencil Square' ),
			array( 'fa fa-pencil-square-o' => 'Pencil Square Outlined (edit)' ),
			array( 'fa fa-percent' => 'Percent' ),
			array( 'fa fa-phone' => 'Phone' ),
			array( 'fa fa-phone-square' => 'Phone Square' ),
			array( 'fa fa-picture-o' => 'Picture Outlined (photo, image)' ),
			array( 'fa fa-pie-chart' => 'Pie Chart' ),
			array( 'fa fa-plane' => 'Plane' ),
			array( 'fa fa-plug' => 'Plug' ),
			array( 'fa fa-plus' => 'Plus' ),
			array( 'fa fa-plus-circle' => 'Plus Circle' ),
			array( 'fa fa-plus-square' => 'Plus Square' ),
			array( 'fa fa-plus-square-o' => 'Plus Square Outlined' ),
			array( 'fa fa-power-off' => 'Power Off' ),
			array( 'fa fa-print' => 'Print' ),
			array( 'fa fa-puzzle-piece' => 'Puzzle Piece' ),
			array( 'fa fa-qrcode' => 'Qrcode' ),
			array( 'fa fa-question' => 'Question' ),
			array( 'fa fa-question-circle' => 'Question Circle' ),
			array( 'fa fa-quote-left' => 'Quote-left' ),
			array( 'fa fa-quote-right' => 'Quote-right' ),
			array( 'fa fa-random' => 'Random' ),
			array( 'fa fa-recycle' => 'Recycle' ),
			array( 'fa fa-refresh' => 'Refresh' ),
			array( 'fa fa-registered' => 'Registered Trademark' ),
			array( 'fa fa-reply' => 'Reply (mail-reply)' ),
			array( 'fa fa-reply-all' => 'Reply-all (mail-reply-all)' ),
			array( 'fa fa-retweet' => 'Retweet' ),
			array( 'fa fa-road' => 'Road' ),
			array( 'fa fa-rocket' => 'Rocket' ),
			array( 'fa fa-rss' => 'Rss (feed)' ),
			array( 'fa fa-rss-square' => 'RSS Square' ),
			array( 'fa fa-search' => 'Search' ),
			array( 'fa fa-search-minus' => 'Search Minus' ),
			array( 'fa fa-search-plus' => 'Search Plus' ),
			array( 'fa fa-server' => 'Server' ),
			array( 'fa fa-share' => 'Share (mail-forward)' ),
			array( 'fa fa-share-alt' => 'Share Alt' ),
			array( 'fa fa-share-alt-square' => 'Share Alt Square' ),
			array( 'fa fa-share-square' => 'Share Square' ),
			array( 'fa fa-share-square-o' => 'Share Square Outlined' ),
			array( 'fa fa-shield' => 'Shield' ),
			array( 'fa fa-ship' => 'Ship' ),
			array( 'fa fa-shopping-bag' => 'Shopping Bag' ),
			array( 'fa fa-shopping-basket' => 'Shopping Basket' ),
			array( 'fa fa-shopping-cart' => 'Shopping-cart' ),
			array( 'fa fa-sign-in' => 'Sign In' ),
			array( 'fa fa-sign-out' => 'Sign Out' ),
			array( 'fa fa-signal' => 'Signal' ),
			array( 'fa fa-sitemap' => 'Sitemap' ),
			array( 'fa fa-sliders' => 'Sliders' ),
			array( 'fa fa-smile-o' => 'Smile Outlined' ),
			array( 'fa fa-sort' => 'Sort (unsorted)' ),
			array( 'fa fa-sort-alpha-asc' => 'Sort Alpha Ascending' ),
			array( 'fa fa-sort-alpha-desc' => 'Sort Alpha Descending' ),
			array( 'fa fa-sort-amount-asc' => 'Sort Amount Ascending' ),
			array( 'fa fa-sort-amount-desc' => 'Sort Amount Descending' ),
			array( 'fa fa-sort-asc' => 'Sort Ascending (sort-up)' ),
			array( 'fa fa-sort-desc' => 'Sort Descending (sort-down)' ),
			array( 'fa fa-sort-numeric-asc' => 'Sort Numeric Ascending' ),
			array( 'fa fa-sort-numeric-desc' => 'Sort Numeric Descending' ),
			array( 'fa fa-space-shuttle' => 'Space Shuttle' ),
			array( 'fa fa-spinner' => 'Spinner' ),
			array( 'fa fa-spoon' => 'Spoon' ),
			array( 'fa fa-square' => 'Square' ),
			array( 'fa fa-square-o' => 'Square Outlined' ),
			array( 'fa fa-star' => 'Star' ),
			array( 'fa fa-star-half' => 'Star-half' ),
			array( 'fa fa-star-half-o' => 'Star Half Outlined (star-half-empty, star-half-full)' ),
			array( 'fa fa-star-o' => 'Star Outlined' ),
			array( 'fa fa-sticky-note' => 'Sticky Note' ),
			array( 'fa fa-sticky-note-o' => 'Sticky Note Outlined' ),
			array( 'fa fa-street-view' => 'Street View' ),
			array( 'fa fa-suitcase' => 'Suitcase' ),
			array( 'fa fa-sun-o' => 'Sun Outlined' ),
			array( 'fa fa-tablet' => 'Tablet' ),
			array( 'fa fa-tachometer' => 'Tachometer (dashboard)' ),
			array( 'fa fa-tag' => 'Tag' ),
			array( 'fa fa-tags' => 'Tags' ),
			array( 'fa fa-tasks' => 'Tasks' ),
			array( 'fa fa-taxi' => 'Taxi (cab)' ),
			array( 'fa fa-television' => 'Television (tv)' ),
			array( 'fa fa-terminal' => 'Terminal' ),
			array( 'fa fa-thumb-tack' => 'Thumb Tack' ),
			array( 'fa fa-thumbs-down' => 'Thumbs-down' ),
			array( 'fa fa-thumbs-o-down' => 'Thumbs Down Outlined' ),
			array( 'fa fa-thumbs-o-up' => 'Thumbs Up Outlined' ),
			array( 'fa fa-thumbs-up' => 'Thumbs-up' ),
			array( 'fa fa-ticket' => 'Ticket' ),
			array( 'fa fa-times' => 'Times (remove, close)' ),
			array( 'fa fa-times-circle' => 'Times Circle' ),
			array( 'fa fa-times-circle-o' => 'Times Circle Outlined' ),
			array( 'fa fa-tint' => 'Tint' ),
			array( 'fa fa-toggle-off' => 'Toggle Off' ),
			array( 'fa fa-toggle-on' => 'Toggle On' ),
			array( 'fa fa-trademark' => 'Trademark' ),
			array( 'fa fa-trash' => 'Trash' ),
			array( 'fa fa-trash-o' => 'Trash Outlined' ),
			array( 'fa fa-tree' => 'Tree' ),
			array( 'fa fa-trophy' => 'Trophy' ),
			array( 'fa fa-truck' => 'Truck' ),
			array( 'fa fa-tty' => 'TTY' ),
			array( 'fa fa-umbrella' => 'Umbrella' ),
			array( 'fa fa-university' => 'University (institution, bank)' ),
			array( 'fa fa-unlock' => 'Unlock' ),
			array( 'fa fa-unlock-alt' => 'Unlock Alt' ),
			array( 'fa fa-upload' => 'Upload' ),
			array( 'fa fa-user' => 'User' ),
			array( 'fa fa-user-plus' => 'Add User' ),
			array( 'fa fa-user-secret' => 'User Secret' ),
			array( 'fa fa-user-times' => 'Remove User' ),
			array( 'fa fa-users' => 'Users (group)' ),
			array( 'fa fa-video-camera' => 'Video Camera' ),
			array( 'fa fa-volume-down' => 'Volume-down' ),
			array( 'fa fa-volume-off' => 'Volume-off' ),
			array( 'fa fa-volume-up' => 'Volume-up' ),
			array( 'fa fa-wheelchair' => 'Wheelchair' ),
			array( 'fa fa-wifi' => 'WiFi' ),
			array( 'fa fa-wrench' => 'Wrench' ),
		),
		'File Type Icons' => array(
			array( 'fa fa-file' => 'File' ),
			array( 'fa fa-file-archive-o' => 'Archive File Outlined (file-zip-o)' ),
			array( 'fa fa-file-audio-o' => 'Audio File Outlined (file-sound-o)' ),
			array( 'fa fa-file-code-o' => 'Code File Outlined' ),
			array( 'fa fa-file-excel-o' => 'Excel File Outlined' ),
			array( 'fa fa-file-image-o' => 'Image File Outlined (file-photo-o, file-picture-o)' ),
			array( 'fa fa-file-o' => 'File Outlined' ),
			array( 'fa fa-file-pdf-o' => 'PDF File Outlined' ),
			array( 'fa fa-file-powerpoint-o' => 'Powerpoint File Outlined' ),
			array( 'fa fa-file-text' => 'File Text' ),
			array( 'fa fa-file-text-o' => 'File Text Outlined' ),
			array( 'fa fa-file-video-o' => 'Video File Outlined (file-movie-o)' ),
			array( 'fa fa-file-word-o' => 'Word File Outlined' ),
		),
		'Spinner Icons' => array(
			array( 'fa fa-circle-o-notch' => 'Circle Outlined Notched' ),
			array( 'fa fa-cog' => 'Cog (gear)' ),
			array( 'fa fa-refresh' => 'Refresh' ),
			array( 'fa fa-spinner' => 'Spinner' ),
		),
		'Form Control Icons' => array(
			array( 'fa fa-check-square' => 'Check Square' ),
			array( 'fa fa-check-square-o' => 'Check Square Outlined' ),
			array( 'fa fa-circle' => 'Circle' ),
			array( 'fa fa-circle-o' => 'Circle Outlined' ),
			array( 'fa fa-dot-circle-o' => 'Dot Circle Outlined' ),
			array( 'fa fa-minus-square' => 'Minus Square' ),
			array( 'fa fa-minus-square-o' => 'Minus Square Outlined' ),
			array( 'fa fa-plus-square' => 'Plus Square' ),
			array( 'fa fa-plus-square-o' => 'Plus Square Outlined' ),
			array( 'fa fa-square' => 'Square' ),
			array( 'fa fa-square-o' => 'Square Outlined' ),
		),
		'Payment Icons' => array(
			array( 'fa fa-cc-amex' => 'American Express Credit Card' ),
			array( 'fa fa-cc-diners-club' => 'Diner\'s Club Credit Card' ),
			array( 'fa fa-cc-discover' => 'Discover Credit Card' ),
			array( 'fa fa-cc-jcb' => 'JCB Credit Card' ),
			array( 'fa fa-cc-mastercard' => 'MasterCard Credit Card' ),
			array( 'fa fa-cc-paypal' => 'Paypal Credit Card' ),
			array( 'fa fa-cc-stripe' => 'Stripe Credit Card' ),
			array( 'fa fa-cc-visa' => 'Visa Credit Card' ),
			array( 'fa fa-credit-card' => 'Credit-card' ),
			array( 'fa fa-credit-card-alt' => 'Credit Card' ),
			array( 'fa fa-google-wallet' => 'Google Wallet' ),
			array( 'fa fa-paypal' => 'Paypal' ),
		),
		'Chart Icons' => array(
			array( 'fa fa-area-chart' => 'Area Chart' ),
			array( 'fa fa-bar-chart' => 'Bar Chart (bar-chart-o)' ),
			array( 'fa fa-line-chart' => 'Line Chart' ),
			array( 'fa fa-pie-chart' => 'Pie Chart' ),
		),
		'Currency Icons' => array(
			array( 'fa fa-btc' => 'Bitcoin (BTC) (bitcoin)' ),
			array( 'fa fa-eur' => 'Euro (EUR) (euro)' ),
			array( 'fa fa-gbp' => 'GBP' ),
			array( 'fa fa-gg' => 'GG Currency' ),
			array( 'fa fa-gg-circle' => 'GG Currency Circle' ),
			array( 'fa fa-ils' => 'Shekel (ILS) (shekel, sheqel)' ),
			array( 'fa fa-inr' => 'Indian Rupee (INR) (rupee)' ),
			array( 'fa fa-jpy' => 'Japanese Yen (JPY) (cny, rmb, yen)' ),
			array( 'fa fa-krw' => 'Korean Won (KRW) (won)' ),
			array( 'fa fa-money' => 'Money' ),
			array( 'fa fa-rub' => 'Russian Ruble (RUB) (ruble, rouble)' ),
			array( 'fa fa-try' => 'Turkish Lira (TRY) (turkish-lira)' ),
			array( 'fa fa-usd' => 'US Dollar (dollar)' ),
		),
		'Text Editor Icons' => array(
			array( 'fa fa-align-center' => 'Align-center' ),
			array( 'fa fa-align-justify' => 'Align-justify' ),
			array( 'fa fa-align-left' => 'Align-left' ),
			array( 'fa fa-align-right' => 'Align-right' ),
			array( 'fa fa-bold' => 'Bold' ),
			array( 'fa fa-chain-broken' => 'Chain Broken (unlink)' ),
			array( 'fa fa-clipboard' => 'Clipboard (paste)' ),
			array( 'fa fa-columns' => 'Columns' ),
			array( 'fa fa-eraser' => 'Eraser' ),
			array( 'fa fa-file' => 'File' ),
			array( 'fa fa-file-o' => 'File Outlined' ),
			array( 'fa fa-file-text' => 'File Text' ),
			array( 'fa fa-file-text-o' => 'File Text Outlined' ),
			array( 'fa fa-files-o' => 'Files Outlined (copy)' ),
			array( 'fa fa-floppy-o' => 'Floppy Outlined (save)' ),
			array( 'fa fa-font' => 'Font' ),
			array( 'fa fa-header' => 'Header' ),
			array( 'fa fa-indent' => 'Indent' ),
			array( 'fa fa-italic' => 'Italic' ),
			array( 'fa fa-link' => 'Link (chain)' ),
			array( 'fa fa-list' => 'List' ),
			array( 'fa fa-list-alt' => 'List-alt' ),
			array( 'fa fa-list-ol' => 'List-ol' ),
			array( 'fa fa-list-ul' => 'List-ul' ),
			array( 'fa fa-outdent' => 'Outdent (dedent)' ),
			array( 'fa fa-paperclip' => 'Paperclip' ),
			array( 'fa fa-paragraph' => 'Paragraph' ),
			array( 'fa fa-repeat' => 'Repeat (rotate-right)' ),
			array( 'fa fa-scissors' => 'Scissors (cut)' ),
			array( 'fa fa-strikethrough' => 'Strikethrough' ),
			array( 'fa fa-subscript' => 'Subscript' ),
			array( 'fa fa-superscript' => 'Superscript' ),
			array( 'fa fa-table' => 'Table' ),
			array( 'fa fa-text-height' => 'Text-height' ),
			array( 'fa fa-text-width' => 'Text-width' ),
			array( 'fa fa-th' => 'Th' ),
			array( 'fa fa-th-large' => 'Th-large' ),
			array( 'fa fa-th-list' => 'Th-list' ),
			array( 'fa fa-underline' => 'Underline' ),
			array( 'fa fa-undo' => 'Undo (rotate-left)' ),
		),
		'Directional Icons' => array(
			array( 'fa fa-angle-double-down' => 'Angle Double Down' ),
			array( 'fa fa-angle-double-left' => 'Angle Double Left' ),
			array( 'fa fa-angle-double-right' => 'Angle Double Right' ),
			array( 'fa fa-angle-double-up' => 'Angle Double Up' ),
			array( 'fa fa-angle-down' => 'Angle-down' ),
			array( 'fa fa-angle-left' => 'Angle-left' ),
			array( 'fa fa-angle-right' => 'Angle-right' ),
			array( 'fa fa-angle-up' => 'Angle-up' ),
			array( 'fa fa-arrow-circle-down' => 'Arrow Circle Down' ),
			array( 'fa fa-arrow-circle-left' => 'Arrow Circle Left' ),
			array( 'fa fa-arrow-circle-o-down' => 'Arrow Circle Outlined Down' ),
			array( 'fa fa-arrow-circle-o-left' => 'Arrow Circle Outlined Left' ),
			array( 'fa fa-arrow-circle-o-right' => 'Arrow Circle Outlined Right' ),
			array( 'fa fa-arrow-circle-o-up' => 'Arrow Circle Outlined Up' ),
			array( 'fa fa-arrow-circle-right' => 'Arrow Circle Right' ),
			array( 'fa fa-arrow-circle-up' => 'Arrow Circle Up' ),
			array( 'fa fa-arrow-down' => 'Arrow-down' ),
			array( 'fa fa-arrow-left' => 'Arrow-left' ),
			array( 'fa fa-arrow-right' => 'Arrow-right' ),
			array( 'fa fa-arrow-up' => 'Arrow-up' ),
			array( 'fa fa-arrows' => 'Arrows' ),
			array( 'fa fa-arrows-alt' => 'Arrows Alt' ),
			array( 'fa fa-arrows-h' => 'Arrows Horizontal' ),
			array( 'fa fa-arrows-v' => 'Arrows Vertical' ),
			array( 'fa fa-caret-down' => 'Caret Down' ),
			array( 'fa fa-caret-left' => 'Caret Left' ),
			array( 'fa fa-caret-right' => 'Caret Right' ),
			array( 'fa fa-caret-square-o-down' => 'Caret Square Outlined Down (toggle-down)' ),
			array( 'fa fa-caret-square-o-left' => 'Caret Square Outlined Left (toggle-left)' ),
			array( 'fa fa-caret-square-o-right' => 'Caret Square Outlined Right (toggle-right)' ),
			array( 'fa fa-caret-square-o-up' => 'Caret Square Outlined Up (toggle-up)' ),
			array( 'fa fa-caret-up' => 'Caret Up' ),
			array( 'fa fa-chevron-circle-down' => 'Chevron Circle Down' ),
			array( 'fa fa-chevron-circle-left' => 'Chevron Circle Left' ),
			array( 'fa fa-chevron-circle-right' => 'Chevron Circle Right' ),
			array( 'fa fa-chevron-circle-up' => 'Chevron Circle Up' ),
			array( 'fa fa-chevron-down' => 'Chevron-down' ),
			array( 'fa fa-chevron-left' => 'Chevron-left' ),
			array( 'fa fa-chevron-right' => 'Chevron-right' ),
			array( 'fa fa-chevron-up' => 'Chevron-up' ),
			array( 'fa fa-exchange' => 'Exchange' ),
			array( 'fa fa-hand-o-down' => 'Hand Outlined Down' ),
			array( 'fa fa-hand-o-left' => 'Hand Outlined Left' ),
			array( 'fa fa-hand-o-right' => 'Hand Outlined Right' ),
			array( 'fa fa-hand-o-up' => 'Hand Outlined Up' ),
			array( 'fa fa-long-arrow-down' => 'Long Arrow Down' ),
			array( 'fa fa-long-arrow-left' => 'Long Arrow Left' ),
			array( 'fa fa-long-arrow-right' => 'Long Arrow Right' ),
			array( 'fa fa-long-arrow-up' => 'Long Arrow Up' ),
		),
		'Video Player Icons' => array(
			array( 'fa fa-arrows-alt' => 'Arrows Alt' ),
			array( 'fa fa-backward' => 'Backward' ),
			array( 'fa fa-compress' => 'Compress' ),
			array( 'fa fa-eject' => 'Eject' ),
			array( 'fa fa-expand' => 'Expand' ),
			array( 'fa fa-fast-backward' => 'Fast-backward' ),
			array( 'fa fa-fast-forward' => 'Fast-forward' ),
			array( 'fa fa-forward' => 'Forward' ),
			array( 'fa fa-pause' => 'Pause' ),
			array( 'fa fa-pause-circle' => 'Pause Circle' ),
			array( 'fa fa-pause-circle-o' => 'Pause Circle Outlined' ),
			array( 'fa fa-play' => 'Play' ),
			array( 'fa fa-play-circle' => 'Play Circle' ),
			array( 'fa fa-play-circle-o' => 'Play Circle Outlined' ),
			array( 'fa fa-random' => 'Random' ),
			array( 'fa fa-step-backward' => 'Step-backward' ),
			array( 'fa fa-step-forward' => 'Step-forward' ),
			array( 'fa fa-stop' => 'Stop' ),
			array( 'fa fa-stop-circle' => 'Stop Circle' ),
			array( 'fa fa-stop-circle-o' => 'Stop Circle Outlined' ),
			array( 'fa fa-youtube-play' => 'YouTube Play' ),
		),
		'Transportation Icons' => array(
			array( 'fa fa-ambulance' => 'Ambulance' ),
			array( 'fa fa-bicycle' => 'Bicycle' ),
			array( 'fa fa-bus' => 'Bus' ),
			array( 'fa fa-car' => 'Car (automobile)' ),
			array( 'fa fa-fighter-jet' => 'Fighter-jet' ),
			array( 'fa fa-motorcycle' => 'Motorcycle' ),
			array( 'fa fa-plane' => 'Plane' ),
			array( 'fa fa-rocket' => 'Rocket' ),
			array( 'fa fa-ship' => 'Ship' ),
			array( 'fa fa-space-shuttle' => 'Space Shuttle' ),
			array( 'fa fa-subway' => 'Subway' ),
			array( 'fa fa-taxi' => 'Taxi (cab)' ),
			array( 'fa fa-train' => 'Train' ),
			array( 'fa fa-truck' => 'Truck' ),
			array( 'fa fa-wheelchair' => 'Wheelchair' ),
		),
		'Hand Icons' => array(
			array( 'fa fa-hand-lizard-o' => 'Lizard (Hand)' ),
			array( 'fa fa-hand-o-down' => 'Hand Outlined Down' ),
			array( 'fa fa-hand-o-left' => 'Hand Outlined Left' ),
			array( 'fa fa-hand-o-right' => 'Hand Outlined Right' ),
			array( 'fa fa-hand-o-up' => 'Hand Outlined Up' ),
			array( 'fa fa-hand-paper-o' => 'Paper (Hand) (hand-stop-o)' ),
			array( 'fa fa-hand-peace-o' => 'Hand Peace' ),
			array( 'fa fa-hand-pointer-o' => 'Hand Pointer' ),
			array( 'fa fa-hand-rock-o' => 'Rock (Hand) (hand-grab-o)' ),
			array( 'fa fa-hand-scissors-o' => 'Scissors (Hand)' ),
			array( 'fa fa-hand-spock-o' => 'Spock (Hand)' ),
			array( 'fa fa-thumbs-down' => 'Thumbs-down' ),
			array( 'fa fa-thumbs-o-down' => 'Thumbs Down Outlined' ),
			array( 'fa fa-thumbs-o-up' => 'Thumbs Up Outlined' ),
			array( 'fa fa-thumbs-up' => 'Thumbs-up' ),
		),
		'Gender Icons' => array(
			array( 'fa fa-genderless' => 'Genderless' ),
			array( 'fa fa-mars' => 'Mars' ),
			array( 'fa fa-mars-double' => 'Mars Double' ),
			array( 'fa fa-mars-stroke' => 'Mars Stroke' ),
			array( 'fa fa-mars-stroke-h' => 'Mars Stroke Horizontal' ),
			array( 'fa fa-mars-stroke-v' => 'Mars Stroke Vertical' ),
			array( 'fa fa-mercury' => 'Mercury' ),
			array( 'fa fa-neuter' => 'Neuter' ),
			array( 'fa fa-transgender' => 'Transgender (intersex)' ),
			array( 'fa fa-transgender-alt' => 'Transgender Alt' ),
			array( 'fa fa-venus' => 'Venus' ),
			array( 'fa fa-venus-double' => 'Venus Double' ),
			array( 'fa fa-venus-mars' => 'Venus Mars' ),
		),
		'Brand Icons' => array(
			array( 'fa fa-500px' => '500px' ),
			array( 'fa fa-adn' => 'App.net' ),
			array( 'fa fa-amazon' => 'Amazon' ),
			array( 'fa fa-android' => 'Android' ),
			array( 'fa fa-angellist' => 'AngelList' ),
			array( 'fa fa-apple' => 'Apple' ),
			array( 'fa fa-behance' => 'Behance' ),
			array( 'fa fa-behance-square' => 'Behance Square' ),
			array( 'fa fa-bitbucket' => 'Bitbucket' ),
			array( 'fa fa-bitbucket-square' => 'Bitbucket Square' ),
			array( 'fa fa-black-tie' => 'Font Awesome Black Tie' ),
			array( 'fa fa-bluetooth' => 'Bluetooth' ),
			array( 'fa fa-bluetooth-b' => 'Bluetooth' ),
			array( 'fa fa-btc' => 'Bitcoin (BTC) (bitcoin)' ),
			array( 'fa fa-buysellads' => 'BuySellAds' ),
			array( 'fa fa-cc-amex' => 'American Express Credit Card' ),
			array( 'fa fa-cc-diners-club' => 'Diner\'s Club Credit Card' ),
			array( 'fa fa-cc-discover' => 'Discover Credit Card' ),
			array( 'fa fa-cc-jcb' => 'JCB Credit Card' ),
			array( 'fa fa-cc-mastercard' => 'MasterCard Credit Card' ),
			array( 'fa fa-cc-paypal' => 'Paypal Credit Card' ),
			array( 'fa fa-cc-stripe' => 'Stripe Credit Card' ),
			array( 'fa fa-cc-visa' => 'Visa Credit Card' ),
			array( 'fa fa-chrome' => 'Chrome' ),
			array( 'fa fa-codepen' => 'Codepen' ),
			array( 'fa fa-codiepie' => 'Codie Pie' ),
			array( 'fa fa-connectdevelop' => 'Connect Develop' ),
			array( 'fa fa-contao' => 'Contao' ),
			array( 'fa fa-css3' => 'CSS 3 Logo' ),
			array( 'fa fa-dashcube' => 'DashCube' ),
			array( 'fa fa-delicious' => 'Delicious Logo' ),
			array( 'fa fa-deviantart' => 'DeviantART' ),
			array( 'fa fa-digg' => 'Digg Logo' ),
			array( 'fa fa-dribbble' => 'Dribbble' ),
			array( 'fa fa-dropbox' => 'Dropbox' ),
			array( 'fa fa-drupal' => 'Drupal Logo' ),
			array( 'fa fa-edge' => 'Edge Browser' ),
			array( 'fa fa-empire' => 'Galactic Empire (ge)' ),
			array( 'fa fa-expeditedssl' => 'ExpeditedSSL' ),
			array( 'fa fa-facebook' => 'Facebook (facebook-f)' ),
			array( 'fa fa-facebook-official' => 'Facebook Official' ),
			array( 'fa fa-facebook-square' => 'Facebook Square' ),
			array( 'fa fa-firefox' => 'Firefox' ),
			array( 'fa fa-flickr' => 'Flickr' ),
			array( 'fa fa-fonticons' => 'Fonticons' ),
			array( 'fa fa-fort-awesome' => 'Fort Awesome' ),
			array( 'fa fa-forumbee' => 'Forumbee' ),
			array( 'fa fa-foursquare' => 'Foursquare' ),
			array( 'fa fa-get-pocket' => 'Get Pocket' ),
			array( 'fa fa-gg' => 'GG Currency' ),
			array( 'fa fa-gg-circle' => 'GG Currency Circle' ),
			array( 'fa fa-git' => 'Git' ),
			array( 'fa fa-git-square' => 'Git Square' ),
			array( 'fa fa-github' => 'GitHub' ),
			array( 'fa fa-github-alt' => 'GitHub Alt' ),
			array( 'fa fa-github-square' => 'GitHub Square' ),
			array( 'fa fa-google' => 'Google Logo' ),
			array( 'fa fa-google-plus' => 'Google Plus' ),
			array( 'fa fa-google-plus-square' => 'Google Plus Square' ),
			array( 'fa fa-google-wallet' => 'Google Wallet' ),
			array( 'fa fa-gittip' => 'Gratipay (Gittip) (gittip)' ),
			array( 'fa fa-hacker-news' => 'Hacker News (y-combinator-square, yc-square)' ),
			array( 'fa fa-houzz' => 'Houzz' ),
			array( 'fa fa-html5' => 'HTML 5 Logo' ),
			array( 'fa fa-instagram' => 'Instagram' ),
			array( 'fa fa-internet-explorer' => 'Internet-explorer' ),
			array( 'fa fa-ioxhost' => 'Ioxhost' ),
			array( 'fa fa-joomla' => 'Joomla Logo' ),
			array( 'fa fa-jsfiddle' => 'JsFiddle' ),
			array( 'fa fa-lastfm' => 'Last.fm' ),
			array( 'fa fa-lastfm-square' => 'Last.fm Square' ),
			array( 'fa fa-leanpub' => 'Leanpub' ),
			array( 'fa fa-linkedin' => 'LinkedIn' ),
			array( 'fa fa-linkedin-square' => 'LinkedIn Square' ),
			array( 'fa fa-linux' => 'Linux' ),
			array( 'fa fa-maxcdn' => 'MaxCDN' ),
			array( 'fa fa-meanpath' => 'Meanpath' ),
			array( 'fa fa-medium' => 'Medium' ),
			array( 'fa fa-mixcloud' => 'Mixcloud' ),
			array( 'fa fa-modx' => 'MODX' ),
			array( 'fa fa-odnoklassniki' => 'Odnoklassniki' ),
			array( 'fa fa-odnoklassniki-square' => 'Odnoklassniki Square' ),
			array( 'fa fa-opencart' => 'OpenCart' ),
			array( 'fa fa-openid' => 'OpenID' ),
			array( 'fa fa-opera' => 'Opera' ),
			array( 'fa fa-optin-monster' => 'Optin Monster' ),
			array( 'fa fa-pagelines' => 'Pagelines' ),
			array( 'fa fa-paypal' => 'Paypal' ),
			array( 'fa fa-pied-piper' => 'Pied Piper Logo' ),
			array( 'fa fa-pied-piper-alt' => 'Pied Piper Alternate Logo' ),
			array( 'fa fa-pinterest' => 'Pinterest' ),
			array( 'fa fa-pinterest-p' => 'Pinterest P' ),
			array( 'fa fa-pinterest-square' => 'Pinterest Square' ),
			array( 'fa fa-product-hunt' => 'Product Hunt' ),
			array( 'fa fa-qq' => 'QQ' ),
			array( 'fa fa-rebel' => 'Rebel Alliance (ra)' ),
			array( 'fa fa-reddit' => 'Reddit Logo' ),
			array( 'fa fa-reddit-alien' => 'Reddit Alien' ),
			array( 'fa fa-reddit-square' => 'Reddit Square' ),
			array( 'fa fa-renren' => 'Renren' ),
			array( 'fa fa-safari' => 'Safari' ),
			array( 'fa fa-scribd' => 'Scribd' ),
			array( 'fa fa-sellsy' => 'Sellsy' ),
			array( 'fa fa-share-alt' => 'Share Alt' ),
			array( 'fa fa-share-alt-square' => 'Share Alt Square' ),
			array( 'fa fa-shirtsinbulk' => 'Shirts in Bulk' ),
			array( 'fa fa-simplybuilt' => 'SimplyBuilt' ),
			array( 'fa fa-skyatlas' => 'Skyatlas' ),
			array( 'fa fa-skype' => 'Skype' ),
			array( 'fa fa-slack' => 'Slack Logo' ),
			array( 'fa fa-slideshare' => 'Slideshare' ),
			array( 'fa fa-soundcloud' => 'SoundCloud' ),
			array( 'fa fa-spotify' => 'Spotify' ),
			array( 'fa fa-stack-exchange' => 'Stack Exchange' ),
			array( 'fa fa-stack-overflow' => 'Stack Overflow' ),
			array( 'fa fa-steam' => 'Steam' ),
			array( 'fa fa-steam-square' => 'Steam Square' ),
			array( 'fa fa-stumbleupon' => 'StumbleUpon Logo' ),
			array( 'fa fa-stumbleupon-circle' => 'StumbleUpon Circle' ),
			array( 'fa fa-tencent-weibo' => 'Tencent Weibo' ),
			array( 'fa fa-trello' => 'Trello' ),
			array( 'fa fa-tripadvisor' => 'TripAdvisor' ),
			array( 'fa fa-tumblr' => 'Tumblr' ),
			array( 'fa fa-tumblr-square' => 'Tumblr Square' ),
			array( 'fa fa-twitch' => 'Twitch' ),
			array( 'fa fa-twitter' => 'Twitter' ),
			array( 'fa fa-twitter-square' => 'Twitter Square' ),
			array( 'fa fa-usb' => 'USB' ),
			array( 'fa fa-viacoin' => 'Viacoin' ),
			array( 'fa fa-vimeo' => 'Vimeo' ),
			array( 'fa fa-vimeo-square' => 'Vimeo Square' ),
			array( 'fa fa-vine' => 'Vine' ),
			array( 'fa fa-vk' => 'VK' ),
			array( 'fa fa-weibo' => 'Weibo' ),
			array( 'fa fa-weixin' => 'Weixin (WeChat) (wechat)' ),
			array( 'fa fa-whatsapp' => 'What\'s App' ),
			array( 'fa fa-wikipedia-w' => 'Wikipedia W' ),
			array( 'fa fa-windows' => 'Windows' ),
			array( 'fa fa-wordpress' => 'WordPress Logo' ),
			array( 'fa fa-xing' => 'Xing' ),
			array( 'fa fa-xing-square' => 'Xing Square' ),
			array( 'fa fa-y-combinator' => 'Y Combinator (yc)' ),
			array( 'fa fa-yahoo' => 'Yahoo Logo' ),
			array( 'fa fa-yelp' => 'Yelp' ),
			array( 'fa fa-youtube' => 'YouTube' ),
			array( 'fa fa-youtube-play' => 'YouTube Play' ),
			array( 'fa fa-youtube-square' => 'YouTube Square' ),
		),
		'Medical Icons' => array(
			array( 'fa fa-ambulance' => 'Ambulance' ),
			array( 'fa fa-h-square' => 'H Square' ),
			array( 'fa fa-heart' => 'Heart' ),
			array( 'fa fa-heart-o' => 'Heart Outlined' ),
			array( 'fa fa-heartbeat' => 'Heartbeat' ),
			array( 'fa fa-hospital-o' => 'Hospital Outlined' ),
			array( 'fa fa-medkit' => 'Medkit' ),
			array( 'fa fa-plus-square' => 'Plus Square' ),
			array( 'fa fa-stethoscope' => 'Stethoscope' ),
			array( 'fa fa-user-md' => 'User-md' ),
			array( 'fa fa-wheelchair' => 'Wheelchair' ),
		),
	);

	return array_merge( $icons, $fontawesome_icons );
}

add_filter( 'vc_iconpicker-type-openiconic', 'vc_iconpicker_type_openiconic' );

/**
 * Openicons icons from fontello.com
 *
 * @param $icons - taken from filter - vc_map param field settings['source'] provided icons (default empty array).
 * If array categorized it will auto-enable category dropdown
 *
 * @since 4.4
 * @return array - of icons for iconpicker, can be categorized, or not.
 */
function vc_iconpicker_type_openiconic( $icons ) {
	$openiconic_icons = array(
		array( 'vc-oi vc-oi-dial' => 'Dial' ),
		array( 'vc-oi vc-oi-pilcrow' => 'Pilcrow' ),
		array( 'vc-oi vc-oi-at' => 'At' ),
		array( 'vc-oi vc-oi-hash' => 'Hash' ),
		array( 'vc-oi vc-oi-key-inv' => 'Key-inv' ),
		array( 'vc-oi vc-oi-key' => 'Key' ),
		array( 'vc-oi vc-oi-chart-pie-alt' => 'Chart-pie-alt' ),
		array( 'vc-oi vc-oi-chart-pie' => 'Chart-pie' ),
		array( 'vc-oi vc-oi-chart-bar' => 'Chart-bar' ),
		array( 'vc-oi vc-oi-umbrella' => 'Umbrella' ),
		array( 'vc-oi vc-oi-moon-inv' => 'Moon-inv' ),
		array( 'vc-oi vc-oi-mobile' => 'Mobile' ),
		array( 'vc-oi vc-oi-cd' => 'Cd' ),
		array( 'vc-oi vc-oi-split' => 'Split' ),
		array( 'vc-oi vc-oi-exchange' => 'Exchange' ),
		array( 'vc-oi vc-oi-block' => 'Block' ),
		array( 'vc-oi vc-oi-resize-full' => 'Resize-full' ),
		array( 'vc-oi vc-oi-article-alt' => 'Article-alt' ),
		array( 'vc-oi vc-oi-article' => 'Article' ),
		array( 'vc-oi vc-oi-pencil-alt' => 'Pencil-alt' ),
		array( 'vc-oi vc-oi-undo' => 'Undo' ),
		array( 'vc-oi vc-oi-attach' => 'Attach' ),
		array( 'vc-oi vc-oi-link' => 'Link' ),
		array( 'vc-oi vc-oi-search' => 'Search' ),
		array( 'vc-oi vc-oi-mail' => 'Mail' ),
		array( 'vc-oi vc-oi-heart' => 'Heart' ),
		array( 'vc-oi vc-oi-comment' => 'Comment' ),
		array( 'vc-oi vc-oi-resize-full-alt' => 'Resize-full-alt' ),
		array( 'vc-oi vc-oi-lock' => 'Lock' ),
		array( 'vc-oi vc-oi-book-open' => 'Book-open' ),
		array( 'vc-oi vc-oi-arrow-curved' => 'Arrow-curved' ),
		array( 'vc-oi vc-oi-equalizer' => 'Equalizer' ),
		array( 'vc-oi vc-oi-heart-empty' => 'Heart-empty' ),
		array( 'vc-oi vc-oi-lock-empty' => 'Lock-empty' ),
		array( 'vc-oi vc-oi-comment-inv' => 'Comment-inv' ),
		array( 'vc-oi vc-oi-folder' => 'Folder' ),
		array( 'vc-oi vc-oi-resize-small' => 'Resize-small' ),
		array( 'vc-oi vc-oi-play' => 'Play' ),
		array( 'vc-oi vc-oi-cursor' => 'Cursor' ),
		array( 'vc-oi vc-oi-aperture' => 'Aperture' ),
		array( 'vc-oi vc-oi-play-circle2' => 'Play-circle2' ),
		array( 'vc-oi vc-oi-resize-small-alt' => 'Resize-small-alt' ),
		array( 'vc-oi vc-oi-folder-empty' => 'Folder-empty' ),
		array( 'vc-oi vc-oi-comment-alt' => 'Comment-alt' ),
		array( 'vc-oi vc-oi-lock-open' => 'Lock-open' ),
		array( 'vc-oi vc-oi-star' => 'Star' ),
		array( 'vc-oi vc-oi-user' => 'User' ),
		array( 'vc-oi vc-oi-lock-open-empty' => 'Lock-open-empty' ),
		array( 'vc-oi vc-oi-box' => 'Box' ),
		array( 'vc-oi vc-oi-resize-vertical' => 'Resize-vertical' ),
		array( 'vc-oi vc-oi-stop' => 'Stop' ),
		array( 'vc-oi vc-oi-aperture-alt' => 'Aperture-alt' ),
		array( 'vc-oi vc-oi-book' => 'Book' ),
		array( 'vc-oi vc-oi-steering-wheel' => 'Steering-wheel' ),
		array( 'vc-oi vc-oi-pause' => 'Pause' ),
		array( 'vc-oi vc-oi-to-start' => 'To-start' ),
		array( 'vc-oi vc-oi-move' => 'Move' ),
		array( 'vc-oi vc-oi-resize-horizontal' => 'Resize-horizontal' ),
		array( 'vc-oi vc-oi-rss-alt' => 'Rss-alt' ),
		array( 'vc-oi vc-oi-comment-alt2' => 'Comment-alt2' ),
		array( 'vc-oi vc-oi-rss' => 'Rss' ),
		array( 'vc-oi vc-oi-comment-inv-alt' => 'Comment-inv-alt' ),
		array( 'vc-oi vc-oi-comment-inv-alt2' => 'Comment-inv-alt2' ),
		array( 'vc-oi vc-oi-eye' => 'Eye' ),
		array( 'vc-oi vc-oi-pin' => 'Pin' ),
		array( 'vc-oi vc-oi-video' => 'Video' ),
		array( 'vc-oi vc-oi-picture' => 'Picture' ),
		array( 'vc-oi vc-oi-camera' => 'Camera' ),
		array( 'vc-oi vc-oi-tag' => 'Tag' ),
		array( 'vc-oi vc-oi-chat' => 'Chat' ),
		array( 'vc-oi vc-oi-cog' => 'Cog' ),
		array( 'vc-oi vc-oi-popup' => 'Popup' ),
		array( 'vc-oi vc-oi-to-end' => 'To-end' ),
		array( 'vc-oi vc-oi-book-alt' => 'Book-alt' ),
		array( 'vc-oi vc-oi-brush' => 'Brush' ),
		array( 'vc-oi vc-oi-eject' => 'Eject' ),
		array( 'vc-oi vc-oi-down' => 'Down' ),
		array( 'vc-oi vc-oi-wrench' => 'Wrench' ),
		array( 'vc-oi vc-oi-chat-inv' => 'Chat-inv' ),
		array( 'vc-oi vc-oi-tag-empty' => 'Tag-empty' ),
		array( 'vc-oi vc-oi-ok' => 'Ok' ),
		array( 'vc-oi vc-oi-ok-circle' => 'Ok-circle' ),
		array( 'vc-oi vc-oi-download' => 'Download' ),
		array( 'vc-oi vc-oi-location' => 'Location' ),
		array( 'vc-oi vc-oi-share' => 'Share' ),
		array( 'vc-oi vc-oi-left' => 'Left' ),
		array( 'vc-oi vc-oi-target' => 'Target' ),
		array( 'vc-oi vc-oi-brush-alt' => 'Brush-alt' ),
		array( 'vc-oi vc-oi-cancel' => 'Cancel' ),
		array( 'vc-oi vc-oi-upload' => 'Upload' ),
		array( 'vc-oi vc-oi-location-inv' => 'Location-inv' ),
		array( 'vc-oi vc-oi-calendar' => 'Calendar' ),
		array( 'vc-oi vc-oi-right' => 'Right' ),
		array( 'vc-oi vc-oi-signal' => 'Signal' ),
		array( 'vc-oi vc-oi-eyedropper' => 'Eyedropper' ),
		array( 'vc-oi vc-oi-layers' => 'Layers' ),
		array( 'vc-oi vc-oi-award' => 'Award' ),
		array( 'vc-oi vc-oi-up' => 'Up' ),
		array( 'vc-oi vc-oi-calendar-inv' => 'Calendar-inv' ),
		array( 'vc-oi vc-oi-location-alt' => 'Location-alt' ),
		array( 'vc-oi vc-oi-download-cloud' => 'Download-cloud' ),
		array( 'vc-oi vc-oi-cancel-circle' => 'Cancel-circle' ),
		array( 'vc-oi vc-oi-plus' => 'Plus' ),
		array( 'vc-oi vc-oi-upload-cloud' => 'Upload-cloud' ),
		array( 'vc-oi vc-oi-compass' => 'Compass' ),
		array( 'vc-oi vc-oi-calendar-alt' => 'Calendar-alt' ),
		array( 'vc-oi vc-oi-down-circle' => 'Down-circle' ),
		array( 'vc-oi vc-oi-award-empty' => 'Award-empty' ),
		array( 'vc-oi vc-oi-layers-alt' => 'Layers-alt' ),
		array( 'vc-oi vc-oi-sun' => 'Sun' ),
		array( 'vc-oi vc-oi-list' => 'List' ),
		array( 'vc-oi vc-oi-left-circle' => 'Left-circle' ),
		array( 'vc-oi vc-oi-mic' => 'Mic' ),
		array( 'vc-oi vc-oi-trash' => 'Trash' ),
		array( 'vc-oi vc-oi-quote-left' => 'Quote-left' ),
		array( 'vc-oi vc-oi-plus-circle' => 'Plus-circle' ),
		array( 'vc-oi vc-oi-minus' => 'Minus' ),
		array( 'vc-oi vc-oi-quote-right' => 'Quote-right' ),
		array( 'vc-oi vc-oi-trash-empty' => 'Trash-empty' ),
		array( 'vc-oi vc-oi-volume-off' => 'Volume-off' ),
		array( 'vc-oi vc-oi-right-circle' => 'Right-circle' ),
		array( 'vc-oi vc-oi-list-nested' => 'List-nested' ),
		array( 'vc-oi vc-oi-sun-inv' => 'Sun-inv' ),
		array( 'vc-oi vc-oi-bat-empty' => 'Bat-empty' ),
		array( 'vc-oi vc-oi-up-circle' => 'Up-circle' ),
		array( 'vc-oi vc-oi-volume-up' => 'Volume-up' ),
		array( 'vc-oi vc-oi-doc' => 'Doc' ),
		array( 'vc-oi vc-oi-quote-left-alt' => 'Quote-left-alt' ),
		array( 'vc-oi vc-oi-minus-circle' => 'Minus-circle' ),
		array( 'vc-oi vc-oi-cloud' => 'Cloud' ),
		array( 'vc-oi vc-oi-rain' => 'Rain' ),
		array( 'vc-oi vc-oi-bat-half' => 'Bat-half' ),
		array( 'vc-oi vc-oi-cw' => 'Cw' ),
		array( 'vc-oi vc-oi-headphones' => 'Headphones' ),
		array( 'vc-oi vc-oi-doc-inv' => 'Doc-inv' ),
		array( 'vc-oi vc-oi-quote-right-alt' => 'Quote-right-alt' ),
		array( 'vc-oi vc-oi-help' => 'Help' ),
		array( 'vc-oi vc-oi-info' => 'Info' ),
		array( 'vc-oi vc-oi-pencil' => 'Pencil' ),
		array( 'vc-oi vc-oi-doc-alt' => 'Doc-alt' ),
		array( 'vc-oi vc-oi-clock' => 'Clock' ),
		array( 'vc-oi vc-oi-loop' => 'Loop' ),
		array( 'vc-oi vc-oi-bat-full' => 'Bat-full' ),
		array( 'vc-oi vc-oi-flash' => 'Flash' ),
		array( 'vc-oi vc-oi-moon' => 'Moon' ),
		array( 'vc-oi vc-oi-bat-charge' => 'Bat-charge' ),
		array( 'vc-oi vc-oi-loop-alt' => 'Loop-alt' ),
		array( 'vc-oi vc-oi-lamp' => 'Lamp' ),
		array( 'vc-oi vc-oi-doc-inv-alt' => 'Doc-inv-alt' ),
		array( 'vc-oi vc-oi-pencil-neg' => 'Pencil-neg' ),
		array( 'vc-oi vc-oi-home' => 'Home' ),
	);

	return array_merge( $icons, $openiconic_icons );
}

add_filter( 'vc_iconpicker-type-typicons', 'vc_iconpicker_type_typicons' );

/**
 * Typicons icons from github.com/stephenhutchings/typicons.font
 *
 * @param $icons - taken from filter - vc_map param field settings['source'] provided icons (default empty array).
 * If array categorized it will auto-enable category dropdown
 *
 * @since 4.4
 * @return array - of icons for iconpicker, can be categorized, or not.
 */
function vc_iconpicker_type_typicons( $icons ) {
	$typicons_icons = array(
		array( 'typcn typcn-adjust-brightness' => 'Adjust Brightness' ),
		array( 'typcn typcn-adjust-contrast' => 'Adjust Contrast' ),
		array( 'typcn typcn-anchor-outline' => 'Anchor Outline' ),
		array( 'typcn typcn-anchor' => 'Anchor' ),
		array( 'typcn typcn-archive' => 'Archive' ),
		array( 'typcn typcn-arrow-back-outline' => 'Arrow Back Outline' ),
		array( 'typcn typcn-arrow-back' => 'Arrow Back' ),
		array( 'typcn typcn-arrow-down-outline' => 'Arrow Down Outline' ),
		array( 'typcn typcn-arrow-down-thick' => 'Arrow Down Thick' ),
		array( 'typcn typcn-arrow-down' => 'Arrow Down' ),
		array( 'typcn typcn-arrow-forward-outline' => 'Arrow Forward Outline' ),
		array( 'typcn typcn-arrow-forward' => 'Arrow Forward' ),
		array( 'typcn typcn-arrow-left-outline' => 'Arrow Left Outline' ),
		array( 'typcn typcn-arrow-left-thick' => 'Arrow Left Thick' ),
		array( 'typcn typcn-arrow-left' => 'Arrow Left' ),
		array( 'typcn typcn-arrow-loop-outline' => 'Arrow Loop Outline' ),
		array( 'typcn typcn-arrow-loop' => 'Arrow Loop' ),
		array( 'typcn typcn-arrow-maximise-outline' => 'Arrow Maximise Outline' ),
		array( 'typcn typcn-arrow-maximise' => 'Arrow Maximise' ),
		array( 'typcn typcn-arrow-minimise-outline' => 'Arrow Minimise Outline' ),
		array( 'typcn typcn-arrow-minimise' => 'Arrow Minimise' ),
		array( 'typcn typcn-arrow-move-outline' => 'Arrow Move Outline' ),
		array( 'typcn typcn-arrow-move' => 'Arrow Move' ),
		array( 'typcn typcn-arrow-repeat-outline' => 'Arrow Repeat Outline' ),
		array( 'typcn typcn-arrow-repeat' => 'Arrow Repeat' ),
		array( 'typcn typcn-arrow-right-outline' => 'Arrow Right Outline' ),
		array( 'typcn typcn-arrow-right-thick' => 'Arrow Right Thick' ),
		array( 'typcn typcn-arrow-right' => 'Arrow Right' ),
		array( 'typcn typcn-arrow-shuffle' => 'Arrow Shuffle' ),
		array( 'typcn typcn-arrow-sorted-down' => 'Arrow Sorted Down' ),
		array( 'typcn typcn-arrow-sorted-up' => 'Arrow Sorted Up' ),
		array( 'typcn typcn-arrow-sync-outline' => 'Arrow Sync Outline' ),
		array( 'typcn typcn-arrow-sync' => 'Arrow Sync' ),
		array( 'typcn typcn-arrow-unsorted' => 'Arrow Unsorted' ),
		array( 'typcn typcn-arrow-up-outline' => 'Arrow Up Outline' ),
		array( 'typcn typcn-arrow-up-thick' => 'Arrow Up Thick' ),
		array( 'typcn typcn-arrow-up' => 'Arrow Up' ),
		array( 'typcn typcn-at' => 'At' ),
		array( 'typcn typcn-attachment-outline' => 'Attachment Outline' ),
		array( 'typcn typcn-attachment' => 'Attachment' ),
		array( 'typcn typcn-backspace-outline' => 'Backspace Outline' ),
		array( 'typcn typcn-backspace' => 'Backspace' ),
		array( 'typcn typcn-battery-charge' => 'Battery Charge' ),
		array( 'typcn typcn-battery-full' => 'Battery Full' ),
		array( 'typcn typcn-battery-high' => 'Battery High' ),
		array( 'typcn typcn-battery-low' => 'Battery Low' ),
		array( 'typcn typcn-battery-mid' => 'Battery Mid' ),
		array( 'typcn typcn-beaker' => 'Beaker' ),
		array( 'typcn typcn-beer' => 'Beer' ),
		array( 'typcn typcn-bell' => 'Bell' ),
		array( 'typcn typcn-book' => 'Book' ),
		array( 'typcn typcn-bookmark' => 'Bookmark' ),
		array( 'typcn typcn-briefcase' => 'Briefcase' ),
		array( 'typcn typcn-brush' => 'Brush' ),
		array( 'typcn typcn-business-card' => 'Business Card' ),
		array( 'typcn typcn-calculator' => 'Calculator' ),
		array( 'typcn typcn-calendar-outline' => 'Calendar Outline' ),
		array( 'typcn typcn-calendar' => 'Calendar' ),
		array( 'typcn typcn-camera-outline' => 'Camera Outline' ),
		array( 'typcn typcn-camera' => 'Camera' ),
		array( 'typcn typcn-cancel-outline' => 'Cancel Outline' ),
		array( 'typcn typcn-cancel' => 'Cancel' ),
		array( 'typcn typcn-chart-area-outline' => 'Chart Area Outline' ),
		array( 'typcn typcn-chart-area' => 'Chart Area' ),
		array( 'typcn typcn-chart-bar-outline' => 'Chart Bar Outline' ),
		array( 'typcn typcn-chart-bar' => 'Chart Bar' ),
		array( 'typcn typcn-chart-line-outline' => 'Chart Line Outline' ),
		array( 'typcn typcn-chart-line' => 'Chart Line' ),
		array( 'typcn typcn-chart-pie-outline' => 'Chart Pie Outline' ),
		array( 'typcn typcn-chart-pie' => 'Chart Pie' ),
		array( 'typcn typcn-chevron-left-outline' => 'Chevron Left Outline' ),
		array( 'typcn typcn-chevron-left' => 'Chevron Left' ),
		array( 'typcn typcn-chevron-right-outline' => 'Chevron Right Outline' ),
		array( 'typcn typcn-chevron-right' => 'Chevron Right' ),
		array( 'typcn typcn-clipboard' => 'Clipboard' ),
		array( 'typcn typcn-cloud-storage' => 'Cloud Storage' ),
		array( 'typcn typcn-cloud-storage-outline' => 'Cloud Storage Outline' ),
		array( 'typcn typcn-code-outline' => 'Code Outline' ),
		array( 'typcn typcn-code' => 'Code' ),
		array( 'typcn typcn-coffee' => 'Coffee' ),
		array( 'typcn typcn-cog-outline' => 'Cog Outline' ),
		array( 'typcn typcn-cog' => 'Cog' ),
		array( 'typcn typcn-compass' => 'Compass' ),
		array( 'typcn typcn-contacts' => 'Contacts' ),
		array( 'typcn typcn-credit-card' => 'Credit Card' ),
		array( 'typcn typcn-css3' => 'Css3' ),
		array( 'typcn typcn-database' => 'Database' ),
		array( 'typcn typcn-delete-outline' => 'Delete Outline' ),
		array( 'typcn typcn-delete' => 'Delete' ),
		array( 'typcn typcn-device-desktop' => 'Device Desktop' ),
		array( 'typcn typcn-device-laptop' => 'Device Laptop' ),
		array( 'typcn typcn-device-phone' => 'Device Phone' ),
		array( 'typcn typcn-device-tablet' => 'Device Tablet' ),
		array( 'typcn typcn-directions' => 'Directions' ),
		array( 'typcn typcn-divide-outline' => 'Divide Outline' ),
		array( 'typcn typcn-divide' => 'Divide' ),
		array( 'typcn typcn-document-add' => 'Document Add' ),
		array( 'typcn typcn-document-delete' => 'Document Delete' ),
		array( 'typcn typcn-document-text' => 'Document Text' ),
		array( 'typcn typcn-document' => 'Document' ),
		array( 'typcn typcn-download-outline' => 'Download Outline' ),
		array( 'typcn typcn-download' => 'Download' ),
		array( 'typcn typcn-dropbox' => 'Dropbox' ),
		array( 'typcn typcn-edit' => 'Edit' ),
		array( 'typcn typcn-eject-outline' => 'Eject Outline' ),
		array( 'typcn typcn-eject' => 'Eject' ),
		array( 'typcn typcn-equals-outline' => 'Equals Outline' ),
		array( 'typcn typcn-equals' => 'Equals' ),
		array( 'typcn typcn-export-outline' => 'Export Outline' ),
		array( 'typcn typcn-export' => 'Export' ),
		array( 'typcn typcn-eye-outline' => 'Eye Outline' ),
		array( 'typcn typcn-eye' => 'Eye' ),
		array( 'typcn typcn-feather' => 'Feather' ),
		array( 'typcn typcn-film' => 'Film' ),
		array( 'typcn typcn-filter' => 'Filter' ),
		array( 'typcn typcn-flag-outline' => 'Flag Outline' ),
		array( 'typcn typcn-flag' => 'Flag' ),
		array( 'typcn typcn-flash-outline' => 'Flash Outline' ),
		array( 'typcn typcn-flash' => 'Flash' ),
		array( 'typcn typcn-flow-children' => 'Flow Children' ),
		array( 'typcn typcn-flow-merge' => 'Flow Merge' ),
		array( 'typcn typcn-flow-parallel' => 'Flow Parallel' ),
		array( 'typcn typcn-flow-switch' => 'Flow Switch' ),
		array( 'typcn typcn-folder-add' => 'Folder Add' ),
		array( 'typcn typcn-folder-delete' => 'Folder Delete' ),
		array( 'typcn typcn-folder-open' => 'Folder Open' ),
		array( 'typcn typcn-folder' => 'Folder' ),
		array( 'typcn typcn-gift' => 'Gift' ),
		array( 'typcn typcn-globe-outline' => 'Globe Outline' ),
		array( 'typcn typcn-globe' => 'Globe' ),
		array( 'typcn typcn-group-outline' => 'Group Outline' ),
		array( 'typcn typcn-group' => 'Group' ),
		array( 'typcn typcn-headphones' => 'Headphones' ),
		array( 'typcn typcn-heart-full-outline' => 'Heart Full Outline' ),
		array( 'typcn typcn-heart-half-outline' => 'Heart Half Outline' ),
		array( 'typcn typcn-heart-outline' => 'Heart Outline' ),
		array( 'typcn typcn-heart' => 'Heart' ),
		array( 'typcn typcn-home-outline' => 'Home Outline' ),
		array( 'typcn typcn-home' => 'Home' ),
		array( 'typcn typcn-html5' => 'Html5' ),
		array( 'typcn typcn-image-outline' => 'Image Outline' ),
		array( 'typcn typcn-image' => 'Image' ),
		array( 'typcn typcn-infinity-outline' => 'Infinity Outline' ),
		array( 'typcn typcn-infinity' => 'Infinity' ),
		array( 'typcn typcn-info-large-outline' => 'Info Large Outline' ),
		array( 'typcn typcn-info-large' => 'Info Large' ),
		array( 'typcn typcn-info-outline' => 'Info Outline' ),
		array( 'typcn typcn-info' => 'Info' ),
		array( 'typcn typcn-input-checked-outline' => 'Input Checked Outline' ),
		array( 'typcn typcn-input-checked' => 'Input Checked' ),
		array( 'typcn typcn-key-outline' => 'Key Outline' ),
		array( 'typcn typcn-key' => 'Key' ),
		array( 'typcn typcn-keyboard' => 'Keyboard' ),
		array( 'typcn typcn-leaf' => 'Leaf' ),
		array( 'typcn typcn-lightbulb' => 'Lightbulb' ),
		array( 'typcn typcn-link-outline' => 'Link Outline' ),
		array( 'typcn typcn-link' => 'Link' ),
		array( 'typcn typcn-location-arrow-outline' => 'Location Arrow Outline' ),
		array( 'typcn typcn-location-arrow' => 'Location Arrow' ),
		array( 'typcn typcn-location-outline' => 'Location Outline' ),
		array( 'typcn typcn-location' => 'Location' ),
		array( 'typcn typcn-lock-closed-outline' => 'Lock Closed Outline' ),
		array( 'typcn typcn-lock-closed' => 'Lock Closed' ),
		array( 'typcn typcn-lock-open-outline' => 'Lock Open Outline' ),
		array( 'typcn typcn-lock-open' => 'Lock Open' ),
		array( 'typcn typcn-mail' => 'Mail' ),
		array( 'typcn typcn-map' => 'Map' ),
		array( 'typcn typcn-media-eject-outline' => 'Media Eject Outline' ),
		array( 'typcn typcn-media-eject' => 'Media Eject' ),
		array( 'typcn typcn-media-fast-forward-outline' => 'Media Fast Forward Outline' ),
		array( 'typcn typcn-media-fast-forward' => 'Media Fast Forward' ),
		array( 'typcn typcn-media-pause-outline' => 'Media Pause Outline' ),
		array( 'typcn typcn-media-pause' => 'Media Pause' ),
		array( 'typcn typcn-media-play-outline' => 'Media Play Outline' ),
		array( 'typcn typcn-media-play-reverse-outline' => 'Media Play Reverse Outline' ),
		array( 'typcn typcn-media-play-reverse' => 'Media Play Reverse' ),
		array( 'typcn typcn-media-play' => 'Media Play' ),
		array( 'typcn typcn-media-record-outline' => 'Media Record Outline' ),
		array( 'typcn typcn-media-record' => 'Media Record' ),
		array( 'typcn typcn-media-rewind-outline' => 'Media Rewind Outline' ),
		array( 'typcn typcn-media-rewind' => 'Media Rewind' ),
		array( 'typcn typcn-media-stop-outline' => 'Media Stop Outline' ),
		array( 'typcn typcn-media-stop' => 'Media Stop' ),
		array( 'typcn typcn-message-typing' => 'Message Typing' ),
		array( 'typcn typcn-message' => 'Message' ),
		array( 'typcn typcn-messages' => 'Messages' ),
		array( 'typcn typcn-microphone-outline' => 'Microphone Outline' ),
		array( 'typcn typcn-microphone' => 'Microphone' ),
		array( 'typcn typcn-minus-outline' => 'Minus Outline' ),
		array( 'typcn typcn-minus' => 'Minus' ),
		array( 'typcn typcn-mortar-board' => 'Mortar Board' ),
		array( 'typcn typcn-news' => 'News' ),
		array( 'typcn typcn-notes-outline' => 'Notes Outline' ),
		array( 'typcn typcn-notes' => 'Notes' ),
		array( 'typcn typcn-pen' => 'Pen' ),
		array( 'typcn typcn-pencil' => 'Pencil' ),
		array( 'typcn typcn-phone-outline' => 'Phone Outline' ),
		array( 'typcn typcn-phone' => 'Phone' ),
		array( 'typcn typcn-pi-outline' => 'Pi Outline' ),
		array( 'typcn typcn-pi' => 'Pi' ),
		array( 'typcn typcn-pin-outline' => 'Pin Outline' ),
		array( 'typcn typcn-pin' => 'Pin' ),
		array( 'typcn typcn-pipette' => 'Pipette' ),
		array( 'typcn typcn-plane-outline' => 'Plane Outline' ),
		array( 'typcn typcn-plane' => 'Plane' ),
		array( 'typcn typcn-plug' => 'Plug' ),
		array( 'typcn typcn-plus-outline' => 'Plus Outline' ),
		array( 'typcn typcn-plus' => 'Plus' ),
		array( 'typcn typcn-point-of-interest-outline' => 'Point Of Interest Outline' ),
		array( 'typcn typcn-point-of-interest' => 'Point Of Interest' ),
		array( 'typcn typcn-power-outline' => 'Power Outline' ),
		array( 'typcn typcn-power' => 'Power' ),
		array( 'typcn typcn-printer' => 'Printer' ),
		array( 'typcn typcn-puzzle-outline' => 'Puzzle Outline' ),
		array( 'typcn typcn-puzzle' => 'Puzzle' ),
		array( 'typcn typcn-radar-outline' => 'Radar Outline' ),
		array( 'typcn typcn-radar' => 'Radar' ),
		array( 'typcn typcn-refresh-outline' => 'Refresh Outline' ),
		array( 'typcn typcn-refresh' => 'Refresh' ),
		array( 'typcn typcn-rss-outline' => 'Rss Outline' ),
		array( 'typcn typcn-rss' => 'Rss' ),
		array( 'typcn typcn-scissors-outline' => 'Scissors Outline' ),
		array( 'typcn typcn-scissors' => 'Scissors' ),
		array( 'typcn typcn-shopping-bag' => 'Shopping Bag' ),
		array( 'typcn typcn-shopping-cart' => 'Shopping Cart' ),
		array( 'typcn typcn-social-at-circular' => 'Social At Circular' ),
		array( 'typcn typcn-social-dribbble-circular' => 'Social Dribbble Circular' ),
		array( 'typcn typcn-social-dribbble' => 'Social Dribbble' ),
		array( 'typcn typcn-social-facebook-circular' => 'Social Facebook Circular' ),
		array( 'typcn typcn-social-facebook' => 'Social Facebook' ),
		array( 'typcn typcn-social-flickr-circular' => 'Social Flickr Circular' ),
		array( 'typcn typcn-social-flickr' => 'Social Flickr' ),
		array( 'typcn typcn-social-github-circular' => 'Social Github Circular' ),
		array( 'typcn typcn-social-github' => 'Social Github' ),
		array( 'typcn typcn-social-google-plus-circular' => 'Social Google Plus Circular' ),
		array( 'typcn typcn-social-google-plus' => 'Social Google Plus' ),
		array( 'typcn typcn-social-instagram-circular' => 'Social Instagram Circular' ),
		array( 'typcn typcn-social-instagram' => 'Social Instagram' ),
		array( 'typcn typcn-social-last-fm-circular' => 'Social Last Fm Circular' ),
		array( 'typcn typcn-social-last-fm' => 'Social Last Fm' ),
		array( 'typcn typcn-social-linkedin-circular' => 'Social Linkedin Circular' ),
		array( 'typcn typcn-social-linkedin' => 'Social Linkedin' ),
		array( 'typcn typcn-social-pinterest-circular' => 'Social Pinterest Circular' ),
		array( 'typcn typcn-social-pinterest' => 'Social Pinterest' ),
		array( 'typcn typcn-social-skype-outline' => 'Social Skype Outline' ),
		array( 'typcn typcn-social-skype' => 'Social Skype' ),
		array( 'typcn typcn-social-tumbler-circular' => 'Social Tumbler Circular' ),
		array( 'typcn typcn-social-tumbler' => 'Social Tumbler' ),
		array( 'typcn typcn-social-twitter-circular' => 'Social Twitter Circular' ),
		array( 'typcn typcn-social-twitter' => 'Social Twitter' ),
		array( 'typcn typcn-social-vimeo-circular' => 'Social Vimeo Circular' ),
		array( 'typcn typcn-social-vimeo' => 'Social Vimeo' ),
		array( 'typcn typcn-social-youtube-circular' => 'Social Youtube Circular' ),
		array( 'typcn typcn-social-youtube' => 'Social Youtube' ),
		array( 'typcn typcn-sort-alphabetically-outline' => 'Sort Alphabetically Outline' ),
		array( 'typcn typcn-sort-alphabetically' => 'Sort Alphabetically' ),
		array( 'typcn typcn-sort-numerically-outline' => 'Sort Numerically Outline' ),
		array( 'typcn typcn-sort-numerically' => 'Sort Numerically' ),
		array( 'typcn typcn-spanner-outline' => 'Spanner Outline' ),
		array( 'typcn typcn-spanner' => 'Spanner' ),
		array( 'typcn typcn-spiral' => 'Spiral' ),
		array( 'typcn typcn-star-full-outline' => 'Star Full Outline' ),
		array( 'typcn typcn-star-half-outline' => 'Star Half Outline' ),
		array( 'typcn typcn-star-half' => 'Star Half' ),
		array( 'typcn typcn-star-outline' => 'Star Outline' ),
		array( 'typcn typcn-star' => 'Star' ),
		array( 'typcn typcn-starburst-outline' => 'Starburst Outline' ),
		array( 'typcn typcn-starburst' => 'Starburst' ),
		array( 'typcn typcn-stopwatch' => 'Stopwatch' ),
		array( 'typcn typcn-support' => 'Support' ),
		array( 'typcn typcn-tabs-outline' => 'Tabs Outline' ),
		array( 'typcn typcn-tag' => 'Tag' ),
		array( 'typcn typcn-tags' => 'Tags' ),
		array( 'typcn typcn-th-large-outline' => 'Th Large Outline' ),
		array( 'typcn typcn-th-large' => 'Th Large' ),
		array( 'typcn typcn-th-list-outline' => 'Th List Outline' ),
		array( 'typcn typcn-th-list' => 'Th List' ),
		array( 'typcn typcn-th-menu-outline' => 'Th Menu Outline' ),
		array( 'typcn typcn-th-menu' => 'Th Menu' ),
		array( 'typcn typcn-th-small-outline' => 'Th Small Outline' ),
		array( 'typcn typcn-th-small' => 'Th Small' ),
		array( 'typcn typcn-thermometer' => 'Thermometer' ),
		array( 'typcn typcn-thumbs-down' => 'Thumbs Down' ),
		array( 'typcn typcn-thumbs-ok' => 'Thumbs Ok' ),
		array( 'typcn typcn-thumbs-up' => 'Thumbs Up' ),
		array( 'typcn typcn-tick-outline' => 'Tick Outline' ),
		array( 'typcn typcn-tick' => 'Tick' ),
		array( 'typcn typcn-ticket' => 'Ticket' ),
		array( 'typcn typcn-time' => 'Time' ),
		array( 'typcn typcn-times-outline' => 'Times Outline' ),
		array( 'typcn typcn-times' => 'Times' ),
		array( 'typcn typcn-trash' => 'Trash' ),
		array( 'typcn typcn-tree' => 'Tree' ),
		array( 'typcn typcn-upload-outline' => 'Upload Outline' ),
		array( 'typcn typcn-upload' => 'Upload' ),
		array( 'typcn typcn-user-add-outline' => 'User Add Outline' ),
		array( 'typcn typcn-user-add' => 'User Add' ),
		array( 'typcn typcn-user-delete-outline' => 'User Delete Outline' ),
		array( 'typcn typcn-user-delete' => 'User Delete' ),
		array( 'typcn typcn-user-outline' => 'User Outline' ),
		array( 'typcn typcn-user' => 'User' ),
		array( 'typcn typcn-vendor-android' => 'Vendor Android' ),
		array( 'typcn typcn-vendor-apple' => 'Vendor Apple' ),
		array( 'typcn typcn-vendor-microsoft' => 'Vendor Microsoft' ),
		array( 'typcn typcn-video-outline' => 'Video Outline' ),
		array( 'typcn typcn-video' => 'Video' ),
		array( 'typcn typcn-volume-down' => 'Volume Down' ),
		array( 'typcn typcn-volume-mute' => 'Volume Mute' ),
		array( 'typcn typcn-volume-up' => 'Volume Up' ),
		array( 'typcn typcn-volume' => 'Volume' ),
		array( 'typcn typcn-warning-outline' => 'Warning Outline' ),
		array( 'typcn typcn-warning' => 'Warning' ),
		array( 'typcn typcn-watch' => 'Watch' ),
		array( 'typcn typcn-waves-outline' => 'Waves Outline' ),
		array( 'typcn typcn-waves' => 'Waves' ),
		array( 'typcn typcn-weather-cloudy' => 'Weather Cloudy' ),
		array( 'typcn typcn-weather-downpour' => 'Weather Downpour' ),
		array( 'typcn typcn-weather-night' => 'Weather Night' ),
		array( 'typcn typcn-weather-partly-sunny' => 'Weather Partly Sunny' ),
		array( 'typcn typcn-weather-shower' => 'Weather Shower' ),
		array( 'typcn typcn-weather-snow' => 'Weather Snow' ),
		array( 'typcn typcn-weather-stormy' => 'Weather Stormy' ),
		array( 'typcn typcn-weather-sunny' => 'Weather Sunny' ),
		array( 'typcn typcn-weather-windy-cloudy' => 'Weather Windy Cloudy' ),
		array( 'typcn typcn-weather-windy' => 'Weather Windy' ),
		array( 'typcn typcn-wi-fi-outline' => 'Wi Fi Outline' ),
		array( 'typcn typcn-wi-fi' => 'Wi Fi' ),
		array( 'typcn typcn-wine' => 'Wine' ),
		array( 'typcn typcn-world-outline' => 'World Outline' ),
		array( 'typcn typcn-world' => 'World' ),
		array( 'typcn typcn-zoom-in-outline' => 'Zoom In Outline' ),
		array( 'typcn typcn-zoom-in' => 'Zoom In' ),
		array( 'typcn typcn-zoom-out-outline' => 'Zoom Out Outline' ),
		array( 'typcn typcn-zoom-out' => 'Zoom Out' ),
		array( 'typcn typcn-zoom-outline' => 'Zoom Outline' ),
		array( 'typcn typcn-zoom' => 'Zoom' ),
	);

	return array_merge( $icons, $typicons_icons );
}

add_filter( 'vc_iconpicker-type-entypo', 'vc_iconpicker_type_entypo' );
/**
 * Entypo icons from github.com/danielbruce/entypo
 *
 * @param $icons - taken from filter - vc_map param field settings['source'] provided icons (default empty array).
 * If array categorized it will auto-enable category dropdown
 *
 * @since 4.4
 * @return array - of icons for iconpicker, can be categorized, or not.
 */
function vc_iconpicker_type_entypo( $icons ) {
	$entypo_icons = array(
		array( 'entypo-icon entypo-icon-note' => 'Note' ),
		array( 'entypo-icon entypo-icon-note-beamed' => 'Note Beamed' ),
		array( 'entypo-icon entypo-icon-music' => 'Music' ),
		array( 'entypo-icon entypo-icon-search' => 'Search' ),
		array( 'entypo-icon entypo-icon-flashlight' => 'Flashlight' ),
		array( 'entypo-icon entypo-icon-mail' => 'Mail' ),
		array( 'entypo-icon entypo-icon-heart' => 'Heart' ),
		array( 'entypo-icon entypo-icon-heart-empty' => 'Heart Empty' ),
		array( 'entypo-icon entypo-icon-star' => 'Star' ),
		array( 'entypo-icon entypo-icon-star-empty' => 'Star Empty' ),
		array( 'entypo-icon entypo-icon-user' => 'User' ),
		array( 'entypo-icon entypo-icon-users' => 'Users' ),
		array( 'entypo-icon entypo-icon-user-add' => 'User Add' ),
		array( 'entypo-icon entypo-icon-video' => 'Video' ),
		array( 'entypo-icon entypo-icon-picture' => 'Picture' ),
		array( 'entypo-icon entypo-icon-camera' => 'Camera' ),
		array( 'entypo-icon entypo-icon-layout' => 'Layout' ),
		array( 'entypo-icon entypo-icon-menu' => 'Menu' ),
		array( 'entypo-icon entypo-icon-check' => 'Check' ),
		array( 'entypo-icon entypo-icon-cancel' => 'Cancel' ),
		array( 'entypo-icon entypo-icon-cancel-circled' => 'Cancel Circled' ),
		array( 'entypo-icon entypo-icon-cancel-squared' => 'Cancel Squared' ),
		array( 'entypo-icon entypo-icon-plus' => 'Plus' ),
		array( 'entypo-icon entypo-icon-plus-circled' => 'Plus Circled' ),
		array( 'entypo-icon entypo-icon-plus-squared' => 'Plus Squared' ),
		array( 'entypo-icon entypo-icon-minus' => 'Minus' ),
		array( 'entypo-icon entypo-icon-minus-circled' => 'Minus Circled' ),
		array( 'entypo-icon entypo-icon-minus-squared' => 'Minus Squared' ),
		array( 'entypo-icon entypo-icon-help' => 'Help' ),
		array( 'entypo-icon entypo-icon-help-circled' => 'Help Circled' ),
		array( 'entypo-icon entypo-icon-info' => 'Info' ),
		array( 'entypo-icon entypo-icon-info-circled' => 'Info Circled' ),
		array( 'entypo-icon entypo-icon-back' => 'Back' ),
		array( 'entypo-icon entypo-icon-home' => 'Home' ),
		array( 'entypo-icon entypo-icon-link' => 'Link' ),
		array( 'entypo-icon entypo-icon-attach' => 'Attach' ),
		array( 'entypo-icon entypo-icon-lock' => 'Lock' ),
		array( 'entypo-icon entypo-icon-lock-open' => 'Lock Open' ),
		array( 'entypo-icon entypo-icon-eye' => 'Eye' ),
		array( 'entypo-icon entypo-icon-tag' => 'Tag' ),
		array( 'entypo-icon entypo-icon-bookmark' => 'Bookmark' ),
		array( 'entypo-icon entypo-icon-bookmarks' => 'Bookmarks' ),
		array( 'entypo-icon entypo-icon-flag' => 'Flag' ),
		array( 'entypo-icon entypo-icon-thumbs-up' => 'Thumbs Up' ),
		array( 'entypo-icon entypo-icon-thumbs-down' => 'Thumbs Down' ),
		array( 'entypo-icon entypo-icon-download' => 'Download' ),
		array( 'entypo-icon entypo-icon-upload' => 'Upload' ),
		array( 'entypo-icon entypo-icon-upload-cloud' => 'Upload Cloud' ),
		array( 'entypo-icon entypo-icon-reply' => 'Reply' ),
		array( 'entypo-icon entypo-icon-reply-all' => 'Reply All' ),
		array( 'entypo-icon entypo-icon-forward' => 'Forward' ),
		array( 'entypo-icon entypo-icon-quote' => 'Quote' ),
		array( 'entypo-icon entypo-icon-code' => 'Code' ),
		array( 'entypo-icon entypo-icon-export' => 'Export' ),
		array( 'entypo-icon entypo-icon-pencil' => 'Pencil' ),
		array( 'entypo-icon entypo-icon-feather' => 'Feather' ),
		array( 'entypo-icon entypo-icon-print' => 'Print' ),
		array( 'entypo-icon entypo-icon-retweet' => 'Retweet' ),
		array( 'entypo-icon entypo-icon-keyboard' => 'Keyboard' ),
		array( 'entypo-icon entypo-icon-comment' => 'Comment' ),
		array( 'entypo-icon entypo-icon-chat' => 'Chat' ),
		array( 'entypo-icon entypo-icon-bell' => 'Bell' ),
		array( 'entypo-icon entypo-icon-attention' => 'Attention' ),
		array( 'entypo-icon entypo-icon-alert' => 'Alert' ),
		array( 'entypo-icon entypo-icon-vcard' => 'Vcard' ),
		array( 'entypo-icon entypo-icon-address' => 'Address' ),
		array( 'entypo-icon entypo-icon-location' => 'Location' ),
		array( 'entypo-icon entypo-icon-map' => 'Map' ),
		array( 'entypo-icon entypo-icon-direction' => 'Direction' ),
		array( 'entypo-icon entypo-icon-compass' => 'Compass' ),
		array( 'entypo-icon entypo-icon-cup' => 'Cup' ),
		array( 'entypo-icon entypo-icon-trash' => 'Trash' ),
		array( 'entypo-icon entypo-icon-doc' => 'Doc' ),
		array( 'entypo-icon entypo-icon-docs' => 'Docs' ),
		array( 'entypo-icon entypo-icon-doc-landscape' => 'Doc Landscape' ),
		array( 'entypo-icon entypo-icon-doc-text' => 'Doc Text' ),
		array( 'entypo-icon entypo-icon-doc-text-inv' => 'Doc Text Inv' ),
		array( 'entypo-icon entypo-icon-newspaper' => 'Newspaper' ),
		array( 'entypo-icon entypo-icon-book-open' => 'Book Open' ),
		array( 'entypo-icon entypo-icon-book' => 'Book' ),
		array( 'entypo-icon entypo-icon-folder' => 'Folder' ),
		array( 'entypo-icon entypo-icon-archive' => 'Archive' ),
		array( 'entypo-icon entypo-icon-box' => 'Box' ),
		array( 'entypo-icon entypo-icon-rss' => 'Rss' ),
		array( 'entypo-icon entypo-icon-phone' => 'Phone' ),
		array( 'entypo-icon entypo-icon-cog' => 'Cog' ),
		array( 'entypo-icon entypo-icon-tools' => 'Tools' ),
		array( 'entypo-icon entypo-icon-share' => 'Share' ),
		array( 'entypo-icon entypo-icon-shareable' => 'Shareable' ),
		array( 'entypo-icon entypo-icon-basket' => 'Basket' ),
		array( 'entypo-icon entypo-icon-bag' => 'Bag' ),
		array( 'entypo-icon entypo-icon-calendar' => 'Calendar' ),
		array( 'entypo-icon entypo-icon-login' => 'Login' ),
		array( 'entypo-icon entypo-icon-logout' => 'Logout' ),
		array( 'entypo-icon entypo-icon-mic' => 'Mic' ),
		array( 'entypo-icon entypo-icon-mute' => 'Mute' ),
		array( 'entypo-icon entypo-icon-sound' => 'Sound' ),
		array( 'entypo-icon entypo-icon-volume' => 'Volume' ),
		array( 'entypo-icon entypo-icon-clock' => 'Clock' ),
		array( 'entypo-icon entypo-icon-hourglass' => 'Hourglass' ),
		array( 'entypo-icon entypo-icon-lamp' => 'Lamp' ),
		array( 'entypo-icon entypo-icon-light-down' => 'Light Down' ),
		array( 'entypo-icon entypo-icon-light-up' => 'Light Up' ),
		array( 'entypo-icon entypo-icon-adjust' => 'Adjust' ),
		array( 'entypo-icon entypo-icon-block' => 'Block' ),
		array( 'entypo-icon entypo-icon-resize-full' => 'Resize Full' ),
		array( 'entypo-icon entypo-icon-resize-small' => 'Resize Small' ),
		array( 'entypo-icon entypo-icon-popup' => 'Popup' ),
		array( 'entypo-icon entypo-icon-publish' => 'Publish' ),
		array( 'entypo-icon entypo-icon-window' => 'Window' ),
		array( 'entypo-icon entypo-icon-arrow-combo' => 'Arrow Combo' ),
		array( 'entypo-icon entypo-icon-down-circled' => 'Down Circled' ),
		array( 'entypo-icon entypo-icon-left-circled' => 'Left Circled' ),
		array( 'entypo-icon entypo-icon-right-circled' => 'Right Circled' ),
		array( 'entypo-icon entypo-icon-up-circled' => 'Up Circled' ),
		array( 'entypo-icon entypo-icon-down-open' => 'Down Open' ),
		array( 'entypo-icon entypo-icon-left-open' => 'Left Open' ),
		array( 'entypo-icon entypo-icon-right-open' => 'Right Open' ),
		array( 'entypo-icon entypo-icon-up-open' => 'Up Open' ),
		array( 'entypo-icon entypo-icon-down-open-mini' => 'Down Open Mini' ),
		array( 'entypo-icon entypo-icon-left-open-mini' => 'Left Open Mini' ),
		array( 'entypo-icon entypo-icon-right-open-mini' => 'Right Open Mini' ),
		array( 'entypo-icon entypo-icon-up-open-mini' => 'Up Open Mini' ),
		array( 'entypo-icon entypo-icon-down-open-big' => 'Down Open Big' ),
		array( 'entypo-icon entypo-icon-left-open-big' => 'Left Open Big' ),
		array( 'entypo-icon entypo-icon-right-open-big' => 'Right Open Big' ),
		array( 'entypo-icon entypo-icon-up-open-big' => 'Up Open Big' ),
		array( 'entypo-icon entypo-icon-down' => 'Down' ),
		array( 'entypo-icon entypo-icon-left' => 'Left' ),
		array( 'entypo-icon entypo-icon-right' => 'Right' ),
		array( 'entypo-icon entypo-icon-up' => 'Up' ),
		array( 'entypo-icon entypo-icon-down-dir' => 'Down Dir' ),
		array( 'entypo-icon entypo-icon-left-dir' => 'Left Dir' ),
		array( 'entypo-icon entypo-icon-right-dir' => 'Right Dir' ),
		array( 'entypo-icon entypo-icon-up-dir' => 'Up Dir' ),
		array( 'entypo-icon entypo-icon-down-bold' => 'Down Bold' ),
		array( 'entypo-icon entypo-icon-left-bold' => 'Left Bold' ),
		array( 'entypo-icon entypo-icon-right-bold' => 'Right Bold' ),
		array( 'entypo-icon entypo-icon-up-bold' => 'Up Bold' ),
		array( 'entypo-icon entypo-icon-down-thin' => 'Down Thin' ),
		array( 'entypo-icon entypo-icon-left-thin' => 'Left Thin' ),
		array( 'entypo-icon entypo-icon-right-thin' => 'Right Thin' ),
		array( 'entypo-icon entypo-icon-up-thin' => 'Up Thin' ),
		array( 'entypo-icon entypo-icon-ccw' => 'Ccw' ),
		array( 'entypo-icon entypo-icon-cw' => 'Cw' ),
		array( 'entypo-icon entypo-icon-arrows-ccw' => 'Arrows Ccw' ),
		array( 'entypo-icon entypo-icon-level-down' => 'Level Down' ),
		array( 'entypo-icon entypo-icon-level-up' => 'Level Up' ),
		array( 'entypo-icon entypo-icon-shuffle' => 'Shuffle' ),
		array( 'entypo-icon entypo-icon-loop' => 'Loop' ),
		array( 'entypo-icon entypo-icon-switch' => 'Switch' ),
		array( 'entypo-icon entypo-icon-play' => 'Play' ),
		array( 'entypo-icon entypo-icon-stop' => 'Stop' ),
		array( 'entypo-icon entypo-icon-pause' => 'Pause' ),
		array( 'entypo-icon entypo-icon-record' => 'Record' ),
		array( 'entypo-icon entypo-icon-to-end' => 'To End' ),
		array( 'entypo-icon entypo-icon-to-start' => 'To Start' ),
		array( 'entypo-icon entypo-icon-fast-forward' => 'Fast Forward' ),
		array( 'entypo-icon entypo-icon-fast-backward' => 'Fast Backward' ),
		array( 'entypo-icon entypo-icon-progress-0' => 'Progress 0' ),
		array( 'entypo-icon entypo-icon-progress-1' => 'Progress 1' ),
		array( 'entypo-icon entypo-icon-progress-2' => 'Progress 2' ),
		array( 'entypo-icon entypo-icon-progress-3' => 'Progress 3' ),
		array( 'entypo-icon entypo-icon-target' => 'Target' ),
		array( 'entypo-icon entypo-icon-palette' => 'Palette' ),
		array( 'entypo-icon entypo-icon-list' => 'List' ),
		array( 'entypo-icon entypo-icon-list-add' => 'List Add' ),
		array( 'entypo-icon entypo-icon-signal' => 'Signal' ),
		array( 'entypo-icon entypo-icon-trophy' => 'Trophy' ),
		array( 'entypo-icon entypo-icon-battery' => 'Battery' ),
		array( 'entypo-icon entypo-icon-back-in-time' => 'Back In Time' ),
		array( 'entypo-icon entypo-icon-monitor' => 'Monitor' ),
		array( 'entypo-icon entypo-icon-mobile' => 'Mobile' ),
		array( 'entypo-icon entypo-icon-network' => 'Network' ),
		array( 'entypo-icon entypo-icon-cd' => 'Cd' ),
		array( 'entypo-icon entypo-icon-inbox' => 'Inbox' ),
		array( 'entypo-icon entypo-icon-install' => 'Install' ),
		array( 'entypo-icon entypo-icon-globe' => 'Globe' ),
		array( 'entypo-icon entypo-icon-cloud' => 'Cloud' ),
		array( 'entypo-icon entypo-icon-cloud-thunder' => 'Cloud Thunder' ),
		array( 'entypo-icon entypo-icon-flash' => 'Flash' ),
		array( 'entypo-icon entypo-icon-moon' => 'Moon' ),
		array( 'entypo-icon entypo-icon-flight' => 'Flight' ),
		array( 'entypo-icon entypo-icon-paper-plane' => 'Paper Plane' ),
		array( 'entypo-icon entypo-icon-leaf' => 'Leaf' ),
		array( 'entypo-icon entypo-icon-lifebuoy' => 'Lifebuoy' ),
		array( 'entypo-icon entypo-icon-mouse' => 'Mouse' ),
		array( 'entypo-icon entypo-icon-briefcase' => 'Briefcase' ),
		array( 'entypo-icon entypo-icon-suitcase' => 'Suitcase' ),
		array( 'entypo-icon entypo-icon-dot' => 'Dot' ),
		array( 'entypo-icon entypo-icon-dot-2' => 'Dot 2' ),
		array( 'entypo-icon entypo-icon-dot-3' => 'Dot 3' ),
		array( 'entypo-icon entypo-icon-brush' => 'Brush' ),
		array( 'entypo-icon entypo-icon-magnet' => 'Magnet' ),
		array( 'entypo-icon entypo-icon-infinity' => 'Infinity' ),
		array( 'entypo-icon entypo-icon-erase' => 'Erase' ),
		array( 'entypo-icon entypo-icon-chart-pie' => 'Chart Pie' ),
		array( 'entypo-icon entypo-icon-chart-line' => 'Chart Line' ),
		array( 'entypo-icon entypo-icon-chart-bar' => 'Chart Bar' ),
		array( 'entypo-icon entypo-icon-chart-area' => 'Chart Area' ),
		array( 'entypo-icon entypo-icon-tape' => 'Tape' ),
		array( 'entypo-icon entypo-icon-graduation-cap' => 'Graduation Cap' ),
		array( 'entypo-icon entypo-icon-language' => 'Language' ),
		array( 'entypo-icon entypo-icon-ticket' => 'Ticket' ),
		array( 'entypo-icon entypo-icon-water' => 'Water' ),
		array( 'entypo-icon entypo-icon-droplet' => 'Droplet' ),
		array( 'entypo-icon entypo-icon-air' => 'Air' ),
		array( 'entypo-icon entypo-icon-credit-card' => 'Credit Card' ),
		array( 'entypo-icon entypo-icon-floppy' => 'Floppy' ),
		array( 'entypo-icon entypo-icon-clipboard' => 'Clipboard' ),
		array( 'entypo-icon entypo-icon-megaphone' => 'Megaphone' ),
		array( 'entypo-icon entypo-icon-database' => 'Database' ),
		array( 'entypo-icon entypo-icon-drive' => 'Drive' ),
		array( 'entypo-icon entypo-icon-bucket' => 'Bucket' ),
		array( 'entypo-icon entypo-icon-thermometer' => 'Thermometer' ),
		array( 'entypo-icon entypo-icon-key' => 'Key' ),
		array( 'entypo-icon entypo-icon-flow-cascade' => 'Flow Cascade' ),
		array( 'entypo-icon entypo-icon-flow-branch' => 'Flow Branch' ),
		array( 'entypo-icon entypo-icon-flow-tree' => 'Flow Tree' ),
		array( 'entypo-icon entypo-icon-flow-line' => 'Flow Line' ),
		array( 'entypo-icon entypo-icon-flow-parallel' => 'Flow Parallel' ),
		array( 'entypo-icon entypo-icon-rocket' => 'Rocket' ),
		array( 'entypo-icon entypo-icon-gauge' => 'Gauge' ),
		array( 'entypo-icon entypo-icon-traffic-cone' => 'Traffic Cone' ),
		array( 'entypo-icon entypo-icon-cc' => 'Cc' ),
		array( 'entypo-icon entypo-icon-cc-by' => 'Cc By' ),
		array( 'entypo-icon entypo-icon-cc-nc' => 'Cc Nc' ),
		array( 'entypo-icon entypo-icon-cc-nc-eu' => 'Cc Nc Eu' ),
		array( 'entypo-icon entypo-icon-cc-nc-jp' => 'Cc Nc Jp' ),
		array( 'entypo-icon entypo-icon-cc-sa' => 'Cc Sa' ),
		array( 'entypo-icon entypo-icon-cc-nd' => 'Cc Nd' ),
		array( 'entypo-icon entypo-icon-cc-pd' => 'Cc Pd' ),
		array( 'entypo-icon entypo-icon-cc-zero' => 'Cc Zero' ),
		array( 'entypo-icon entypo-icon-cc-share' => 'Cc Share' ),
		array( 'entypo-icon entypo-icon-cc-remix' => 'Cc Remix' ),
		array( 'entypo-icon entypo-icon-github' => 'Github' ),
		array( 'entypo-icon entypo-icon-github-circled' => 'Github Circled' ),
		array( 'entypo-icon entypo-icon-flickr' => 'Flickr' ),
		array( 'entypo-icon entypo-icon-flickr-circled' => 'Flickr Circled' ),
		array( 'entypo-icon entypo-icon-vimeo' => 'Vimeo' ),
		array( 'entypo-icon entypo-icon-vimeo-circled' => 'Vimeo Circled' ),
		array( 'entypo-icon entypo-icon-twitter' => 'Twitter' ),
		array( 'entypo-icon entypo-icon-twitter-circled' => 'Twitter Circled' ),
		array( 'entypo-icon entypo-icon-facebook' => 'Facebook' ),
		array( 'entypo-icon entypo-icon-facebook-circled' => 'Facebook Circled' ),
		array( 'entypo-icon entypo-icon-facebook-squared' => 'Facebook Squared' ),
		array( 'entypo-icon entypo-icon-gplus' => 'Gplus' ),
		array( 'entypo-icon entypo-icon-gplus-circled' => 'Gplus Circled' ),
		array( 'entypo-icon entypo-icon-pinterest' => 'Pinterest' ),
		array( 'entypo-icon entypo-icon-pinterest-circled' => 'Pinterest Circled' ),
		array( 'entypo-icon entypo-icon-tumblr' => 'Tumblr' ),
		array( 'entypo-icon entypo-icon-tumblr-circled' => 'Tumblr Circled' ),
		array( 'entypo-icon entypo-icon-linkedin' => 'Linkedin' ),
		array( 'entypo-icon entypo-icon-linkedin-circled' => 'Linkedin Circled' ),
		array( 'entypo-icon entypo-icon-dribbble' => 'Dribbble' ),
		array( 'entypo-icon entypo-icon-dribbble-circled' => 'Dribbble Circled' ),
		array( 'entypo-icon entypo-icon-stumbleupon' => 'Stumbleupon' ),
		array( 'entypo-icon entypo-icon-stumbleupon-circled' => 'Stumbleupon Circled' ),
		array( 'entypo-icon entypo-icon-lastfm' => 'Lastfm' ),
		array( 'entypo-icon entypo-icon-lastfm-circled' => 'Lastfm Circled' ),
		array( 'entypo-icon entypo-icon-rdio' => 'Rdio' ),
		array( 'entypo-icon entypo-icon-rdio-circled' => 'Rdio Circled' ),
		array( 'entypo-icon entypo-icon-spotify' => 'Spotify' ),
		array( 'entypo-icon entypo-icon-spotify-circled' => 'Spotify Circled' ),
		array( 'entypo-icon entypo-icon-qq' => 'Qq' ),
		array( 'entypo-icon entypo-icon-instagrem' => 'Instagrem' ),
		array( 'entypo-icon entypo-icon-dropbox' => 'Dropbox' ),
		array( 'entypo-icon entypo-icon-evernote' => 'Evernote' ),
		array( 'entypo-icon entypo-icon-flattr' => 'Flattr' ),
		array( 'entypo-icon entypo-icon-skype' => 'Skype' ),
		array( 'entypo-icon entypo-icon-skype-circled' => 'Skype Circled' ),
		array( 'entypo-icon entypo-icon-renren' => 'Renren' ),
		array( 'entypo-icon entypo-icon-sina-weibo' => 'Sina Weibo' ),
		array( 'entypo-icon entypo-icon-paypal' => 'Paypal' ),
		array( 'entypo-icon entypo-icon-picasa' => 'Picasa' ),
		array( 'entypo-icon entypo-icon-soundcloud' => 'Soundcloud' ),
		array( 'entypo-icon entypo-icon-mixi' => 'Mixi' ),
		array( 'entypo-icon entypo-icon-behance' => 'Behance' ),
		array( 'entypo-icon entypo-icon-google-circles' => 'Google Circles' ),
		array( 'entypo-icon entypo-icon-vkontakte' => 'Vkontakte' ),
		array( 'entypo-icon entypo-icon-smashing' => 'Smashing' ),
		array( 'entypo-icon entypo-icon-sweden' => 'Sweden' ),
		array( 'entypo-icon entypo-icon-db-shape' => 'Db Shape' ),
		array( 'entypo-icon entypo-icon-logo-db' => 'Logo Db' ),
	);

	return array_merge( $icons, $entypo_icons );
}

add_filter( 'vc_iconpicker-type-linecons', 'vc_iconpicker_type_linecons' );

/**
 * Linecons icons from fontello.com
 *
 * @param $icons - taken from filter - vc_map param field settings['source'] provided icons (default empty array).
 * If array categorized it will auto-enable category dropdown
 *
 * @since 4.4
 * @return array - of icons for iconpicker, can be categorized, or not.
 */
function vc_iconpicker_type_linecons( $icons ) {
	$linecons_icons = array(
		array( 'vc_li vc_li-heart' => 'Heart' ),
		array( 'vc_li vc_li-cloud' => 'Cloud' ),
		array( 'vc_li vc_li-star' => 'Star' ),
		array( 'vc_li vc_li-tv' => 'Tv' ),
		array( 'vc_li vc_li-sound' => 'Sound' ),
		array( 'vc_li vc_li-video' => 'Video' ),
		array( 'vc_li vc_li-trash' => 'Trash' ),
		array( 'vc_li vc_li-user' => 'User' ),
		array( 'vc_li vc_li-key' => 'Key' ),
		array( 'vc_li vc_li-search' => 'Search' ),
		array( 'vc_li vc_li-settings' => 'Settings' ),
		array( 'vc_li vc_li-camera' => 'Camera' ),
		array( 'vc_li vc_li-tag' => 'Tag' ),
		array( 'vc_li vc_li-lock' => 'Lock' ),
		array( 'vc_li vc_li-bulb' => 'Bulb' ),
		array( 'vc_li vc_li-pen' => 'Pen' ),
		array( 'vc_li vc_li-diamond' => 'Diamond' ),
		array( 'vc_li vc_li-display' => 'Display' ),
		array( 'vc_li vc_li-location' => 'Location' ),
		array( 'vc_li vc_li-eye' => 'Eye' ),
		array( 'vc_li vc_li-bubble' => 'Bubble' ),
		array( 'vc_li vc_li-stack' => 'Stack' ),
		array( 'vc_li vc_li-cup' => 'Cup' ),
		array( 'vc_li vc_li-phone' => 'Phone' ),
		array( 'vc_li vc_li-news' => 'News' ),
		array( 'vc_li vc_li-mail' => 'Mail' ),
		array( 'vc_li vc_li-like' => 'Like' ),
		array( 'vc_li vc_li-photo' => 'Photo' ),
		array( 'vc_li vc_li-note' => 'Note' ),
		array( 'vc_li vc_li-clock' => 'Clock' ),
		array( 'vc_li vc_li-paperplane' => 'Paperplane' ),
		array( 'vc_li vc_li-params' => 'Params' ),
		array( 'vc_li vc_li-banknote' => 'Banknote' ),
		array( 'vc_li vc_li-data' => 'Data' ),
		array( 'vc_li vc_li-music' => 'Music' ),
		array( 'vc_li vc_li-megaphone' => 'Megaphone' ),
		array( 'vc_li vc_li-study' => 'Study' ),
		array( 'vc_li vc_li-lab' => 'Lab' ),
		array( 'vc_li vc_li-food' => 'Food' ),
		array( 'vc_li vc_li-t-shirt' => 'T Shirt' ),
		array( 'vc_li vc_li-fire' => 'Fire' ),
		array( 'vc_li vc_li-clip' => 'Clip' ),
		array( 'vc_li vc_li-shop' => 'Shop' ),
		array( 'vc_li vc_li-calendar' => 'Calendar' ),
		array( 'vc_li vc_li-vallet' => 'Vallet' ),
		array( 'vc_li vc_li-vynil' => 'Vynil' ),
		array( 'vc_li vc_li-truck' => 'Truck' ),
		array( 'vc_li vc_li-world' => 'World' ),
	);

	return array_merge( $icons, $linecons_icons );
}

add_filter( 'vc_iconpicker-type-monosocial', 'vc_iconpicker_type_monosocial' );

/**
 * monosocial icons from drinchev.github.io/monosocialiconsfont
 *
 * @param $icons - taken from filter - vc_map param field settings['source'] provided icons (default empty array).
 * If array categorized it will auto-enable category dropdown
 *
 * @since 4.4
 * @return array - of icons for iconpicker, can be categorized, or not.
 */
function vc_iconpicker_type_monosocial( $icons ){
	$monosocial = array(
		array( 'vc-mono vc-mono-fivehundredpx' => 'Five Hundred px' ),
		array( 'vc-mono vc-mono-aboutme' => 'About me' ),
		array( 'vc-mono vc-mono-addme' => 'Add me' ),
		array( 'vc-mono vc-mono-amazon' => 'Amazon' ),
		array( 'vc-mono vc-mono-aol' => 'Aol' ),
		array( 'vc-mono vc-mono-appstorealt' => 'App-store-alt' ),
		array( 'vc-mono vc-mono-appstore' => 'Appstore' ),
		array( 'vc-mono vc-mono-apple' => 'Apple' ),
		array( 'vc-mono vc-mono-bebo' => 'Bebo' ),
		array( 'vc-mono vc-mono-behance' => 'Behance' ),
		array( 'vc-mono vc-mono-bing' => 'Bing' ),
		array( 'vc-mono vc-mono-blip' => 'Blip' ),
		array( 'vc-mono vc-mono-blogger' => 'Blogger' ),
		array( 'vc-mono vc-mono-coroflot' => 'Coroflot' ),
		array( 'vc-mono vc-mono-daytum' => 'Daytum' ),
		array( 'vc-mono vc-mono-delicious' => 'Delicious' ),
		array( 'vc-mono vc-mono-designbump' => 'Design bump' ),
		array( 'vc-mono vc-mono-designfloat' => 'Design float' ),
		array( 'vc-mono vc-mono-deviantart' => 'Deviant-art' ),
		array( 'vc-mono vc-mono-diggalt' => 'Digg-alt' ),
		array( 'vc-mono vc-mono-digg' => 'Digg' ),
		array( 'vc-mono vc-mono-dribble' => 'Dribble' ),
		array( 'vc-mono vc-mono-drupal' => 'Drupal' ),
		array( 'vc-mono vc-mono-ebay' => 'Ebay' ),
		array( 'vc-mono vc-mono-email' => 'Email' ),
		array( 'vc-mono vc-mono-emberapp' => 'Ember app' ),
		array( 'vc-mono vc-mono-etsy' => 'Etsy' ),
		array( 'vc-mono vc-mono-facebook' => 'Facebook' ),
		array( 'vc-mono vc-mono-feedburner' => 'Feed burner' ),
		array( 'vc-mono vc-mono-flickr' => 'Flickr' ),
		array( 'vc-mono vc-mono-foodspotting' => 'Food spotting' ),
		array( 'vc-mono vc-mono-forrst' => 'Forrst' ),
		array( 'vc-mono vc-mono-foursquare' => 'Fours quare' ),
		array( 'vc-mono vc-mono-friendsfeed' => 'Friends feed' ),
		array( 'vc-mono vc-mono-friendstar' => 'Friend star' ),
		array( 'vc-mono vc-mono-gdgt' => 'Gdgt' ),
		array( 'vc-mono vc-mono-github' => 'Github' ),
		array( 'vc-mono vc-mono-githubalt' => 'Github-alt' ),
		array( 'vc-mono vc-mono-googlebuzz' => 'Google buzz' ),
		array( 'vc-mono vc-mono-googleplus' => 'Google plus' ),
		array( 'vc-mono vc-mono-googletalk' => 'Google talk' ),
		array( 'vc-mono vc-mono-gowallapin' => 'Gowallapin' ),
		array( 'vc-mono vc-mono-gowalla' => 'Gowalla' ),
		array( 'vc-mono vc-mono-grooveshark' => 'Groove shark' ),
		array( 'vc-mono vc-mono-heart' => 'Heart' ),
		array( 'vc-mono vc-mono-hyves' => 'Hyves' ),
		array( 'vc-mono vc-mono-icondock' => 'Icondock' ),
		array( 'vc-mono vc-mono-icq' => 'Icq' ),
		array( 'vc-mono vc-mono-identica' => 'Identica' ),
		array( 'vc-mono vc-mono-imessage' => 'I message' ),
		array( 'vc-mono vc-mono-itunes' => 'I-tunes' ),
		array( 'vc-mono vc-mono-lastfm' => 'Lastfm' ),
		array( 'vc-mono vc-mono-linkedin' => 'Linkedin' ),
		array( 'vc-mono vc-mono-meetup' => 'Meetup' ),
		array( 'vc-mono vc-mono-metacafe' => 'Metacafe' ),
		array( 'vc-mono vc-mono-mixx' => 'Mixx' ),
		array( 'vc-mono vc-mono-mobileme' => 'Mobile me' ),
		array( 'vc-mono vc-mono-mrwong' => 'Mrwong' ),
		array( 'vc-mono vc-mono-msn' => 'Msn' ),
		array( 'vc-mono vc-mono-myspace' => 'Myspace' ),
		array( 'vc-mono vc-mono-newsvine' => 'Newsvine' ),
		array( 'vc-mono vc-mono-paypal' => 'Paypal' ),
		array( 'vc-mono vc-mono-photobucket' => 'Photo bucket' ),
		array( 'vc-mono vc-mono-picasa' => 'Picasa' ),
		array( 'vc-mono vc-mono-pinterest' => 'Pinterest' ),
		array( 'vc-mono vc-mono-podcast' => 'Podcast' ),
		array( 'vc-mono vc-mono-posterous' => 'Posterous' ),
		array( 'vc-mono vc-mono-qik' => 'Qik' ),
		array( 'vc-mono vc-mono-quora' => 'Quora' ),
		array( 'vc-mono vc-mono-reddit' => 'Reddit' ),
		array( 'vc-mono vc-mono-retweet' => 'Retweet' ),
		array( 'vc-mono vc-mono-rss' => 'Rss' ),
		array( 'vc-mono vc-mono-scribd' => 'Scribd' ),
		array( 'vc-mono vc-mono-sharethis' => 'Sharethis' ),
		array( 'vc-mono vc-mono-skype' => 'Skype' ),
		array( 'vc-mono vc-mono-slashdot' => 'Slashdot' ),
		array( 'vc-mono vc-mono-slideshare' => 'Slideshare' ),
		array( 'vc-mono vc-mono-smugmug' => 'Smugmug' ),
		array( 'vc-mono vc-mono-soundcloud' => 'Soundcloud' ),
		array( 'vc-mono vc-mono-spotify' => 'Spotify' ),
		array( 'vc-mono vc-mono-squidoo' => 'Squidoo' ),
		array( 'vc-mono vc-mono-stackoverflow' => 'Stackoverflow' ),
		array( 'vc-mono vc-mono-star' => 'Star' ),
		array( 'vc-mono vc-mono-stumbleupon' => 'Stumble upon' ),
		array( 'vc-mono vc-mono-technorati' => 'Technorati' ),
		array( 'vc-mono vc-mono-tumblr' => 'Tumblr' ),
		array( 'vc-mono vc-mono-twitterbird' => 'Twitterbird' ),
		array( 'vc-mono vc-mono-twitter' => 'Twitter' ),
		array( 'vc-mono vc-mono-viddler' => 'Viddler' ),
		array( 'vc-mono vc-mono-vimeo' => 'Vimeo' ),
		array( 'vc-mono vc-mono-virb' => 'Virb' ),
		array( 'vc-mono vc-mono-www' => 'Www' ),
		array( 'vc-mono vc-mono-wikipedia' => 'Wikipedia' ),
		array( 'vc-mono vc-mono-windows' => 'Windows' ),
		array( 'vc-mono vc-mono-wordpress' => 'WordPress' ),
		array( 'vc-mono vc-mono-xing' => 'Xing' ),
		array( 'vc-mono vc-mono-yahoobuzz' => 'Yahoo buzz' ),
		array( 'vc-mono vc-mono-yahoo' => 'Yahoo' ),
		array( 'vc-mono vc-mono-yelp' => 'Yelp' ),
		array( 'vc-mono vc-mono-youtube' => 'Youtube' ),
		array( 'vc-mono vc-mono-instagram' => 'Instagram' ),
	);

	return array_merge( $icons, $monosocial );
}