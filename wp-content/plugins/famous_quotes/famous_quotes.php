<?php

/*
Plugin Name: Famouse Quotes
Description: Display random quote fetched from remote Symphony API
Author: Pavel Kolas
Version: 1.0
*/

add_action('admin_init', array('FamousQuoteEvent', 'initPlugin'));
add_action('admin_menu', array('FamousQuoteEvent', 'addMenuLinks'));
add_action('wp_footer',	 array('FamousQuoteEvent', 'showRandomQuote'));
add_action('admin_post_new_key', array('FamousQuoteEvent', 'generateNewApiKey'));
add_action('admin_post_add_quote', array('FamousQuoteEvent', 'addQuote'));

require 'famousQuoteSymfonyApiProxy.php';

class FamousQuoteEvent
{
	public function addMenuLinks()
	{
		add_options_page('Famous Quotes', 'Famous Quotes', 'manage_options', 'quote_list', array('FamousQuoteEvent', 'renderQuoteList'));
		add_options_page('Famous Quotes Settings', 'Famous Quotes Settings', 'manage_options', 'fmq-settings-page', array('FamousQuoteEvent', 'renderOptionsPage'));
		add_submenu_page(null,'Add Quote', 'Add Quote', 'manage_options', 'add_quote', array('FamousQuoteEvent', 'renderAddQuoteAction'));
	}

	public function addQuote()
	{
		$options = get_option('fmq_api_settings');
		$api = new famousQuoteSymfonyApiProxy($options['fmq_api_key']);
		$data = $_POST;
		$response = $api->addQuote($data['quote_author'], $data['quote_text']);

		if ($response)
		{
			wp_redirect(admin_url('options-general.php?page=quote_list'));
		}
		else
		{
			print 'Something went wrong! (' . $api->error_message . ')';
		}
	}


	private static function renderQuoteForm($data)
	{
		print '<form action="admin-post.php" method="post">
			<input type="hidden" name="action" value="' . $data['mode'] . '_quote">

			Author:
			<input type=text name="quote_author" value="' . htmlspecialchars($data['quote_author']) . '">
			<br/>
			
			Quote:
			<textarea name="quote_text">' . htmlspecialchars($data['quote_author']) . '</textarea>
			';
                submit_button();
		print '</form>';
		
	}

	public function renderAddQuoteAction()
	{
		print "<h2>Add Quote</h2>";
		self::renderQuoteForm(array('mode' => 'add'));
	}

	public function renderQuoteList()
	{
		print '<h2>Famous Quotes</h2>';

		$options = get_option('fmq_api_settings');
		if (trim($options['fmq_api_key']) == '')
		{
			print "Looks like your plugin isn't configured yet. Please go to <a href='" . admin_url('/options-general.php?page=fmq-settings-page') . "'>settings page</a> and configure it";
			return;
		}

		$api = new famousQuoteSymfonyApiProxy($options['fmq_api_key']);
		$response = $api->getQuotes();

		if (!$response)
		{
			print 'Cannot retrieve quotes (' . $api->error_message . ')';
		}
		else
		{
			if (count($response['data']) == 0)
			{
				print 'There are no quotes yet. Would you like to <a href="options-general.php?page=add_quote">add some</a>?';
			}
			else
			{

				print '<a href="options-general.php?page=add_quote">add quote</a>';

				print '<table cellpadding="5" cellspacing="0" border="1">
					<tr>
						<td>Author</td>
						<td>Quote</td>
					</tr>
				';
				foreach ($response['data'] as $quote)
				{
					print '<tr>
							<td>' . $quote['name'] . '</td>
							<td>' . $quote['text'] . '</td>
						</tr>';
				}
				print '</table>';
			}
		}

	}

	public function initPlugin()
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

	public function showRandomQuote()
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

		print '<form action="admin-post.php" method="post">
			<input type="hidden" name="action" value="new_key">';
		print 'Or click the button below to create a new API account and start your new collection of quotes';
		submit_button('Request new key', 'secondary');
		print '</form>';
	}

	public function renderOptionsSectionDescripton()
	{
		print __('If you have an API key, enter it here', 'wordpress');
	}

	public function renderApiKeyInputField( )
	{
		$options = get_option('fmq_api_settings');
		print '<input type="text" name="fmq_api_settings[fmq_api_key]" value="' . $options['fmq_api_key'] . '">';
	}

	public function generateNewApiKey()
	{
		$api = new famousQuoteSymfonyApiProxy();
		$response = $api->newApiKey();
		if (! $response)
		{
			throw new Exception('Error message occured while talking to API: ' . $api->error_message);
		}
		$key = $response['key'];
		update_option('fmq_api_settings', array('fmq_api_key' => $key));
		wp_redirect(admin_url('/options-general.php?page=fmq-settings-page'));
	}
}
