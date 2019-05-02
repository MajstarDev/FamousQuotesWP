<?php

/*
Plugin Name: Famouse Quotes
Description: Display random quote fetched from remote Symphony API
Author: Pavel Kolas
Version: 1.0
*/

add_action('admin_init', array('FamousQuoteEvent', 'initPlugin'));
add_action('admin_menu', array('FamousQuoteEvent', 'addSettingsPage'));
add_action('wp_footer',	 array('FamousQuoteEvent', 'showRandomQuote'));

class FamousQuoteEvent
{
	public static function addSettingsPage()
	{
		add_options_page('Famous Quotes', 'Famous Quotes', 'manage_options', 'fq-settings-page', array('FamousQuoteEvent', 'renderOptionsPage'));
	}

	public static function initPlugin()
	{
		register_setting('fmqPlugin', 'fmq_api_settings');

		add_settings_section(
			'fmq_api_fmqPlugin_section',
			__('Server Settings', 'wordpress'),
			array('FamousQuoteEvent', 'renderOptionsSectionDescripton'),
			'fmqPlugin'
		);

		add_settings_field(
			'fmq_api_key',
			__('API key', 'wordpress'),
			array('FamousQuoteEvent', 'renderApiKeyInputField'),
			'fmqPlugin',
			'fmq_api_fmqPlugin_section'
		);		
	}

	public static function showRandomQuote()
	{
		print  '<p style="color: black; padding-left: 15%;">VOILA ' . rand(0, 100) . '</p>';
	}

	public function renderOptionsPage( )
	{
		print '<form action="options.php" method="post">
			<h2>Famous Quotes Settings</h2>';

        	settings_fields('fmqPlugin');
	        do_settings_sections('fmqPlugin');
	        submit_button();
		
		print '</form>';
	}

	public function renderOptionsSectionDescripton()
	{
		print __('Enter API key or get a new one', 'wordpress');
	}

	public function renderApiKeyInputField( )
	{
		$options = get_option('fmq_api_settings');
		print '<input type="text" name="fmq_api_settings[fmq_api_key]" value="' . $options['fmq_api_key'] . '">';
	}

}
