<?php

/*
Plugin Name: Famouse Quotes
Description: Display random quote fetched from remote Symphony API
Author: Pavel Kolas
Version: 1.0
*/

add_action('admin_init', array('FamousQuoteEvent', 'initPlugin'));
add_action('admin_menu', array('FamousQuoteEvent', 'addMenuLinks'));
add_action('admin_post_new_key', 	array('FamousQuoteEvent', 'generateNewApiKey'));
add_action('admin_post_add_quote', 	array('FamousQuoteEvent', 'addQuote'));
add_action('admin_post_edit_quote', 	array('FamousQuoteEvent', 'editQuote'));
add_action('admin_post_delete_quote', 	array('FamousQuoteEvent', 'deleteQuote'));
add_action('admin_enqueue_scripts', array('FamousQuoteEvent', 'register_admin_styles'));
add_action('admin_enqueue_scripts', array('FamousQuoteEvent', 'register_admin_scripts'));
add_action('wp_footer',	 	array('FamousQuoteEvent', 'showRandomQuote'));
add_action('wp_enqueue_scripts', array('FamousQuoteEvent', 'register_plugin_styles'));

require 'famousQuoteSymfonyApiProxy.php';

class FamousQuoteEvent
{
	public function addMenuLinks()
	{
		add_options_page('Famous Quotes', 'Famous Quotes', 'manage_options', 'quote_list', array('FamousQuoteEvent', 'renderQuoteList'));
		add_options_page('Famous Quotes Settings', 'Famous Quotes Settings', 'manage_options', 'fmq-settings-page', array('FamousQuoteEvent', 'renderOptionsPage'));
		add_submenu_page(null,'Add Quote', 'Add Quote', 'manage_options', 'add_quote', array('FamousQuoteEvent', 'renderAddQuoteAction'));
		add_submenu_page(null,'View Quote', 'View Quote', 'manage_options', 'view_quote', array('FamousQuoteEvent', 'renderViewQuoteAction'));
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

	public function editQuote()
	{
		$options = get_option('fmq_api_settings');
		$api = new famousQuoteSymfonyApiProxy($options['fmq_api_key']);
		$data = $_POST;

		$response = $api->editQuote(intval($data['id']), $data['quote_author'], $data['quote_text']);

		if ($response)
		{
			wp_redirect(admin_url('options-general.php?page=quote_list'));
		}
		else
		{
			print 'Something went wrong! (' . $api->error_message . ')';
		}
	}

	public function deleteQuote()
	{
		$options = get_option('fmq_api_settings');
		$api = new famousQuoteSymfonyApiProxy($options['fmq_api_key']);

		$response = $api->deleteQuote(intval($_POST['id']));

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
		print '<form action="admin-post.php" method="post" id="quote_form">
		<input type="hidden" name="action" value="' . $data['mode'] . '_quote">
			';
		if ($data['id'])
		{
			print '<input type="hidden" name="id" value="' . $data['id'] .'">';
		}

		print '
			<table class="form-table">
				<tr>
					<td colspan="2" class="fmq-form-error"></td>
				</tr>
			<tr>
					<th><label for="quote_author">Author:</label></th>
					<td><input type=text name="quote_author" class="fmq" value="' . htmlspecialchars($data['quote_author']) . '"></td>
				</tr>
				<tr>
					<th>Quote:</th>
					<td><textarea name="quote_text" class="fmq" rows="5" cols="30">' . htmlspecialchars($data['quote_text']) . '</textarea></td>
				</tr>
			</table>
			';
        submit_button();
		print '</form>';
		
	}

	public function renderAddQuoteAction()
	{
		print "<h2>Add Quote</h2>";
		self::renderQuoteForm(array('mode' => 'add'));
	}

	public function renderViewQuoteAction()
	{
		print "<h2>Edit Quote</h2>";

		$id = intval($_GET['id']);

		$options = get_option('fmq_api_settings');
		$api = new famousQuoteSymfonyApiProxy($options['fmq_api_key']);

		$response = $api->getQuote($id);
		if ($response)
		{
			$data = array(
				'mode' => 'edit',
				'id' => $id,
				'quote_author' => $response['data'][0]['name'],
				'quote_text' => $response['data'][0]['text']
			);
			self::renderQuoteForm($data);
		}
		else
		{
			print "Something went wrong (quote has just been deleted?)";
		}
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

				print '<form action="options-general.php" method="get">
					<input type="hidden" name="page" value="add_quote">
					';
				submit_button('Add Quote');
				print '</form>';

				print '
				<div class="wrap">
					<table class="wp-list-table widefat striped">
					<thead>
					<tr>
						<td>Author</td>
						<td>Quote</td>
						<td>&nbsp;</td>
					</tr>
					</thead>
					<tbody>
				';
				foreach ($response['data'] as $quote)
				{
					print '
						<tr>
							<td>' . $quote['name'] . '</td>
							<td class="column-primary">' . $quote['text'] . '</td>
							<td style="display:flex; justify-content:flex-end;">

								<form action="options-general.php" method="get">
								<input type="hidden" name="page" value="view_quote">
								<input type="hidden" name="id" value="' . $quote['id'] . '">
								<input type="submit" value="Edit" class="button">
								</form>
								&nbsp;

								<form action="admin-post.php" method="post">
								<input type="hidden" name="action" value="delete_quote">
								<input type="hidden" name="id" value="' . $quote['id'] . '">
								<input type="submit" value="Delete" class="button">
								</form>
							</td>
						</tr>';
				}
				print '
				</tbody>
				<tfoot>
					<tr>
							<td>Author</td>
							<td>Quote</td>
							<td>&nbsp;</td>
						</tr>
					</tfoot>
				</table></div>';
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
		$options = get_option('fmq_api_settings');
		$api = new famousQuoteSymfonyApiProxy($options['fmq_api_key']);

		$response = $api->getRandomQuote();
		if ($response)
		{
			print '<div class="fmq-container">
				<blockquote class="fmq" cite="' . $response['data'][0]['name'] . '">
					' .  $response['data'][0]['text'] . '
				<blockquote>
			</div>';
		}
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

	public function register_admin_styles()
	{
		wp_register_style('fmqPlugin', plugins_url('famous_quotes/assets/quotes_admin.css'));
		wp_enqueue_style('fmqPlugin');
	}

	public function register_admin_scripts($hook)
	{
		if ($hook != 'settings_page_add_quote' && $hook != 'settings_page_view_quote')
			return;
		wp_enqueue_script('newscript', plugins_url('famous_quotes/assets/quotes_admin.js'), array( 'jquery'));
	}
	public function register_plugin_styles()
	{
		wp_register_style('fmqPlugin', plugins_url('famous_quotes/assets/quotes_public.css'));
		wp_enqueue_style('fmqPlugin');
	}
}

?>