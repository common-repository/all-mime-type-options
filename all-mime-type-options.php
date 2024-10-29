<?php

/**
 * @link              http://stehle-internet.de/
 * @since             1.0.0
 * @package           All_Mime_Type_Options
 *
 * @wordpress-plugin
 * Plugin Name:       All Mime Type Options
 * Plugin URI:        http://wordpress.org/plugins/all-mime-type-options/
 * Description:       Get access to all uploaded mime types to the media type filter on the media list
 * Version:           1.2.4
 * Requires at least: 3.5.0
 * Requires PHP:      5.2
 * Author:            Martin Stehle
 * Author URI:        http://stehle-internet.de/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       all-mime-type-options
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class All_Mime_Type_Options {
	
	/**
	 * Constructor of this plugin
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct () {
		add_filter( 'post_mime_types', array( $this, 'add_mime_type_options'  ) );
		add_action( 'plugins_loaded',  array( $this, 'load_plugin_textdomain' ) );
		add_action( 'admin_print_styles-upload.php',  array( $this, 'print_css' ) );
	}
	
	
	/**
	 * Retrieves currently uploaded mime types and returns the list
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $mime_types    Array of mime types
	 */
	public function add_mime_type_options ( $mime_types ) {
		
		// quit without changes if library is in grid mode (until a solution is found to alter the media filter in JS)
		if ( 'grid' == get_user_option( 'media_library_mode' ) ) {
			return $mime_types;
		}
		
		// get uploaded file types
		$uploaded_allowed_mime_types = array_values( get_allowed_mime_types() );

		// WP standard texts, tranlated once
		$text = 'Audio';
		$translated[ 'audio' ]			= __( $text );
		$text = 'Video';
		$translated[ 'video' ]			= __( $text );
		$text = 'Image';
		$translated[ 'image' ]			= __( $text );
		$text = 'Text';
		$translated[ 'text' ]			= __( $text );
		//$text = 'File';
		//$translated[ 'file' ]			= _x( $text, 'column name' );
		// translate labels once
		$translated[ 'managex' ]		= __( 'Manage %s', 'all-mime-type-options' );
		$translated[ 'xfile' ]			= __( '%s file', 'all-mime-type-options' );
		$translated[ 'xfiles' ]			= __( '%s files', 'all-mime-type-options' );
		//. translators: 1: name 2: medium type
		$translated[ 'xxfile' ]			= __( '%1$s %2$s file', 'all-mime-type-options' );
		$translated[ 'xxfiles' ]		= __( '%1$s %2$s files', 'all-mime-type-options' );
		
		// initialize
		$new_mime_types = array();
		
		// create labels based on mime type
		foreach ( $uploaded_allowed_mime_types as $type ) {
			
			// catch types for images, audios and videos
			if ( preg_match( '/^(image|audio|video)\//', $type ) ) {
				// assign
				$key = 'xxfile';
				// split string at slash
				list( $medium, $name ) = explode( '/', $type );
				// catch special mime types
				if ( false !== strpos( $name, 'ogg' ) or false !== strpos( $name, 'mpeg' ) ) {
					// uppercase all letters
					$name = strtoupper( $name );
				} elseif ( false !== strpos( $name, 'matroska' ) ) {
					$name = __( 'Matroska', 'all-mime-type-options' );
				} elseif ( false !== strpos( $name, 'quicktime' ) ) {
					$name = __( 'Quicktime', 'all-mime-type-options' );
				} elseif ( false !== strpos( $name, 'realaudio' ) ) {
					$name = __( 'RealAudio', 'all-mime-type-options' );
				// check others for vendor prefix
				} elseif ( false !== strpos ( $name, 'x-' ) ) {
					// remove prefix
					$name = str_replace( 'x-', '', $name );
					// reformat MS vendor prefix
					if ( false !== strpos ( $name, 'ms-' ) ) {
						// replace it
						$name = str_replace( 'ms-', 'MS ', $name );
					}
					// uppercase all letters
					$name = strtoupper( $name );
				// all other img/audio/video types
				} else {
					// uppercase all letters
					$name = strtoupper( $name );
				}
			// all other mime types
			} else {		
				// assign
				$key	= 'xfile';
				// set label
				switch ( $type ) {
					case 'text/plain':
						$name	= __( 'Plain text', 'all-mime-type-options' );
						break;
					case 'text/csv':
						$name	= __( 'CSV', 'all-mime-type-options' );
						break;
					case 'text/tab-separated-values':
						$name	= __( 'TSV', 'all-mime-type-options' );
						break;
					case 'text/calendar':
						$name	= __( 'Calendar', 'all-mime-type-options' );
						break;
					case 'text/richtext':
						$name	= __( 'RichText', 'all-mime-type-options' );
						break;
					case 'text/css':
						$name	= __( 'CSS', 'all-mime-type-options' );
						break;
					case 'text/html':
						$name	= __( 'HTML', 'all-mime-type-options' );
						break;
					case 'text/vtt':
						$name	= __( 'WebTTV', 'all-mime-type-options' );
						break;
					case 'application/ttaf+xml':
						$name	= __( 'TTAF', 'all-mime-type-options' );
						break;
					case 'application/rtf':
						$name	= __( 'RTF', 'all-mime-type-options' );
						break;
					case 'application/javascript':
						$name	= __( 'Javascript', 'all-mime-type-options' );
						break;
					case 'application/pdf':
						$name	= __( 'PDF', 'all-mime-type-options' );
						break;
					case 'application/java':
						$name	= __( 'Java', 'all-mime-type-options' );
						break;
					case 'application/x-msdownload':
						$name	= __( 'EXE', 'all-mime-type-options' );
						break;
					case 'application/x-shockwave-flash':
						$name	= __( 'Shockwave Flash', 'all-mime-type-options' );
						break;
					case 'application/x-tar':
						$name	= __( 'TAR', 'all-mime-type-options' );
						break;
					case 'application/zip':
						$name	= __( 'ZIP', 'all-mime-type-options' );
						break;
					case 'application/x-gzip':
						$name	= __( 'GZIP', 'all-mime-type-options' );
						break;
					case 'application/rar':
						$name	= __( 'RAR', 'all-mime-type-options' );
						break;
					case 'application/x-7z-compressed':
						$name	= __( '7z', 'all-mime-type-options' );
						break;
					case 'application/octet-stream':
						$name	= __( 'Octet-stream', 'all-mime-type-options' );
						break;
					case 'application/msword':
						$name	= __( 'MS Word', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-powerpoint':
						$name	= __( 'MS PowerPoint', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-write':
						$name	= __( 'MS Write', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-excel':
						$name	= __( 'MS Excel', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-access':
						$name	= __( 'MS Access', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-project':
						$name	= __( 'MS Project', 'all-mime-type-options' );
						break;
					case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
						$name	= __( 'MS Word 2007 document', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-word.document.macroEnabled.12':
						$name	= __( 'MS Word 2007 macro-enabled document', 'all-mime-type-options' );
						break;
					case 'application/vnd.openxmlformats-officedocument.wordprocessingml.template':
						$name	= __( 'MS Word 2007 template', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-word.template.macroEnabled.12':
						$name	= __( 'MS Word 2007 macro-enabled template', 'all-mime-type-options' );
						break;
					case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
						$name	= __( 'MS Excel 2007 workbook', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-excel.sheet.macroEnabled.12':
						$name	= __( 'MS Excel 2007 macro-enabled workbook', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-excel.sheet.binary.macroEnabled.12':
						$name	= __( 'MS Excel 2007 binary workbook', 'all-mime-type-options' );
						break;
					case 'application/vnd.openxmlformats-officedocument.spreadsheetml.template':
						$name	= __( 'MS Excel 2007 template', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-excel.template.macroEnabled.12':
						$name	= __( 'MS Excel 2007 macro-enabled template', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-excel.addin.macroEnabled.12':
						$name	= __( 'MS Excel 2007 add-in', 'all-mime-type-options' );
						break;
					case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
						$name	= __( 'MS PowerPoint 2007 presentation', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-powerpoint.presentation.macroEnabled.12':
						$name	= __( 'MS PowerPoint 2007 macro-enabled presentation', 'all-mime-type-options' );
						break;
					case 'application/vnd.openxmlformats-officedocument.presentationml.slideshow':
						$name	= __( 'MS PowerPoint 2007 slide show', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12':
						$name	= __( 'MS PowerPoint 2007 macro-enabled slide show', 'all-mime-type-options' );
						break;
					case 'application/vnd.openxmlformats-officedocument.presentationml.template':
						$name	= __( 'MS PowerPoint 2007 template', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-powerpoint.template.macroEnabled.12':
						$name	= __( 'MS PowerPoint 2007 macro-enabled template', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-powerpoint.addin.macroEnabled.12':
						$name	= __( 'MS PowerPoint 2007 add-in', 'all-mime-type-options' );
						break;
					case 'application/vnd.openxmlformats-officedocument.presentationml.slide':
						$name	= __( 'MS PowerPoint 2007 slide', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-powerpoint.slide.macroEnabled.12':
						$name	= __( 'MS PowerPoint 2007 macro-enabled slide', 'all-mime-type-options' );
						break;
					case 'application/onenote':
						$name	= __( 'OneNote', 'all-mime-type-options' );
						break;
					case 'application/oxps':
						$name	= __( 'Open XPS', 'all-mime-type-options' );
						break;
					case 'application/vnd.ms-xpsdocument':
						$name	= __( 'MS XPS', 'all-mime-type-options' );
						break;
					case 'application/vnd.oasis.opendocument.text':
						$name	= __( 'OpenDocument text', 'all-mime-type-options' );
						break;
					case 'application/vnd.oasis.opendocument.presentation':
						$name	= __( 'OpenDocument presentation', 'all-mime-type-options' );
						break;
					case 'application/vnd.oasis.opendocument.spreadsheet':
						$name	= __( 'OpenDocument spreadsheet', 'all-mime-type-options' );
						break;
					case 'application/vnd.oasis.opendocument.graphics':
						$name	= __( 'OpenDocument drawing', 'all-mime-type-options' );
						break;
					case 'application/vnd.oasis.opendocument.chart':
						$name	= __( 'OpenDocument chart', 'all-mime-type-options' );
						break;
					case 'application/vnd.oasis.opendocument.database':
						$name	= __( 'OpenDocument database', 'all-mime-type-options' );
						break;
					case 'application/vnd.oasis.opendocument.formula':
						$name	= __( 'OpenDocument formula', 'all-mime-type-options' );
						break;
					case 'application/wordperfect':
						$name	= __( 'WordPerfect', 'all-mime-type-options' );
						break;
					case 'application/vnd.apple.keynote':
						$name	= __( 'Apple Keynote', 'all-mime-type-options' );
						break;
					case 'application/vnd.apple.numbers':
						$name	= __( 'Apple Numbers', 'all-mime-type-options' );
						break;
					case 'application/vnd.apple.pages':
						$name	= __( 'Apple Pages', 'all-mime-type-options' );
						break;
					default:
						// split string at slash
						$parts = explode( '/', $type );
						// get name part
						if ( isset( $parts[ 1 ] ) ) {
							$name = $parts[ 1 ];
						} else {
							$name = $parts[ 0 ];
						}
						// delete vendor prefix
						$name = str_replace( 'vnd.', '', $name );
						// change dots to spaces
						$name = str_replace( '.', ' ', $name );
						// change first char of every word to uppercase
						$name = ucwords( $name );
				} // switch( type )
			}

			// translate
			if ( 'xxfile' == $key ) {
				$label_singular	= sprintf( $translated[ $key ],		$name, $translated[ $medium ] );
				$label_plural	= sprintf( $translated[ $key.'s' ],	$name, $translated[ $medium ] );
			} else {
				$label_singular	= sprintf( $translated[ $key ],		$name );
				$label_plural	= sprintf( $translated[ $key.'s' ],	$name );
			}
			
			// add option
			$new_mime_types[ $type ] = array(
				$label_plural,
				sprintf( $translated[ 'managex' ], $label_plural ),
				_n_noop( esc_html( $label_singular ) . ' <span class="count">(%s)</span>', esc_html( $label_plural ) . ' <span class="count">(%s)</span>' )
			);
		}

		// quit without changes if no new options
		if ( empty( $new_mime_types ) ) {
			return $mime_types;
		}
		
		// sort if more than 1 option
		if ( 1 < count( $new_mime_types ) ) {
			// create type=>label array for easy sorting
			$type_labels = array();
			foreach( $new_mime_types as $type => $data ) {
				$type_labels[ $type ] = $data[0];
			}
			// sort labels case-insensitive in natural order
			natcasesort( $type_labels );
			// rebuild options in sorted order
			$sorted_mime_types = array();
			foreach( array_keys( $type_labels ) as $mtype ) {
				foreach( $new_mime_types as $type => $data ) {
					if ( $type == $mtype ) {
						$sorted_mime_types[ $type ] = $data;
						break;
					}
				}
			}
			// return sorted options
			return array_merge( $mime_types, $sorted_mime_types );
		} else {
			// return options
			return array_merge( $mime_types, $new_mime_types );
		}
	}

	/**
	 * Loads the translation file
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function load_plugin_textdomain() {
		
		load_plugin_textdomain(
			'all-mime-type-options',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages/'
		);
		
	}

	/**
	 * Shortens filter menu visually if long option labels on media library page
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function print_css() {

		echo '<style type="text/css">#attachment-filter, #media-attachment-filters { max-width: 16em; }</style>';
		print "\n";

	}

}

// create class instance
$amto_all_mime_type_options = new All_Mime_Type_Options();