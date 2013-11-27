<?php

namespace WordPress;

/**
 * Customize Header Image Control Class
 *
 * @package WordPress
 * @subpackage Customize
 * @since 3.4.0
 */
class WP_Customize_Header_Image_Control extends WP_Customize_Image_Control {
	/**
	 * The processed default headers.
	 * @since 3.4.2
	 * @var array
	 */
	protected $default_headers;

	/**
	 * The uploaded headers.
	 * @since 3.4.2
	 * @var array
	 */
	protected $uploaded_headers;

	/**
	 * Constructor.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Image_Control::__construct()
	 * @uses WP_Customize_Image_Control::add_tab()
	 *
	 * @param WP_Customize_Manager $manager
	 */
	public function __construct( $manager ) {
		parent::__construct( $manager, 'header_image', array(
			'label'    => __( 'Header Image' ),
			'settings' => array(
				'default' => 'header_image',
				'data'    => 'header_image_data',
			),
			'section'  => 'header_image',
			'context'  => 'custom-header',
			'removed'  => 'remove-header',
			'get_url'  => 'get_header_image',
			'statuses' => array(
				''                      => __('Default'),
				'remove-header'         => __('No Image'),
				'random-default-image'  => __('Random Default Image'),
				'random-uploaded-image' => __('Random Uploaded Image'),
			)
		) );

		// Remove the upload tab.
		$this->remove_tab( 'upload-new' );
	}

	/**
	 * Prepares the control.
	 *
	 * If no tabs exist, removes the control from the manager.
	 *
	 * @since 3.4.2
	 */
	public function prepare_control() {
		global $custom_image_header;
		if ( empty( $custom_image_header ) )
			return parent::prepare_control();

		// Process default headers and uploaded headers.
		$custom_image_header->process_default_headers();
		$this->default_headers = $custom_image_header->default_headers;
		$this->uploaded_headers = get_uploaded_header_images();

		if ( $this->default_headers )
			$this->add_tab( 'default',  __('Default'),  array( $this, 'tab_default_headers' ) );

		if ( ! $this->uploaded_headers )
			$this->remove_tab( 'uploaded' );

		return parent::prepare_control();
	}

	/**
	 * @since 3.4.0
	 *
	 * @param mixed $choice Which header image to select. (@see Custom_Image_Header::get_header_image() )
	 * @param array $header
	 */
	public function print_header_image( $choice, $header ) {
		$header['url']           = set_url_scheme( $header['url'] );
		$header['thumbnail_url'] = set_url_scheme( $header['thumbnail_url'] );

		$header_image_data = array( 'choice' => $choice );
		foreach ( array( 'attachment_id', 'width', 'height', 'url', 'thumbnail_url' ) as $key ) {
			if ( isset( $header[ $key ] ) )
				$header_image_data[ $key ] = $header[ $key ];
		}


		?>
		<a href="#" class="thumbnail"
			data-customize-image-value="<?php echo esc_url( $header['url'] ); ?>"
			data-customize-header-image-data="<?php echo esc_attr( json_encode( $header_image_data ) ); ?>">
			<img src="<?php echo esc_url( $header['thumbnail_url'] ); ?>" />
		</a>
		<?php
	}

	/**
	 * @since 3.4.0
	 */
	public function tab_uploaded() {
		?><div class="uploaded-target"></div><?php

		foreach ( $this->uploaded_headers as $choice => $header )
			$this->print_header_image( $choice, $header );
	}

	/**
	 * @since 3.4.0
	 */
	public function tab_default_headers() {
		foreach ( $this->default_headers as $choice => $header )
			$this->print_header_image( $choice, $header );
	}
}