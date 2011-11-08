<?php

/**
 * Random HTML plugin for Habari 0.7+
 * 
 * @package random_html
 */

class RandomHTML extends Plugin
{
	/**
	* get the absolute path to the folder for given package name.
	*
	**/
	private function get_path()
	{
		return dirname( $this->get_file() );
	}

	/**
	 * Gets an array of all filenames that are available.
	 *
	 * @return array An array of filenames.
	 **/
	public function get_all_filenames()
	{
		$files = array();
		foreach ( glob( $this->get_path() . "/files/*.xml" ) as $file ) {
			$filename = basename ( $file, ".xml" );
			$files[$filename]= simplexml_load_file( $file );
 		}
		return $files;
	}

	/**
	 * On plugin init, add the template included with this plugin to the available templates in the theme
	 **/
	public function action_init()
	{
		$this->load_text_domain( 'random_html' );
		$this->add_template( 'block.random_html', dirname(__FILE__) . '/block.random_html.php' );
	}


	/**
	 * Add random quote block to the list of selectable blocks
	 **/
	public function filter_block_list( $block_list )
	{
		$block_list[ 'random_html' ] = _t( 'Random HTML', 'random_html' );
		return $block_list;
	}


	/**
	 * Output the content of the block
	 **/
	public function action_block_content_random_html( $block, $theme )
	{
		$filename = ( isset( $block->filename ) ? $block->filename : 'habari' );
		$file = simplexml_load_file ( $this->get_path() . "/files/$filename.xml" );
		$whichone = rand(0,count($file->quote)-1);
		$block->text = (string) $file->quote[$whichone];
		return $block;
	}

	/**
	 * Block options
	 **/
	public function action_block_form_randomquote( $form, $block )
	{
		$control = $form->append( 'select', 'filename', $block, _t( 'Source file', 'random_html' ) );
		foreach( $this->get_all_filenames() as $filename => $file ) {
			$control->options[$filename] = $file->info->name . ": " . $file->info->description;
		}
		$control->add_validator( 'validate_required' );
	}

}
?>
