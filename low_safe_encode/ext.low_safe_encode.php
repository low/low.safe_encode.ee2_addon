<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Low Safe Encode Extension class
*
* @package			low-safe_encode-ee2_addon
* @version			2.1
* @author			Lodewijk Schutte ~ Low <low@loweblog.com>
* @link				http://loweblog.com/software/low-safe-encode/
* @license			http://creativecommons.org/licenses/by-sa/3.0/
*/
class Low_safe_encode_ext
{
	/**
	* Extension settings
	*
	* @var	array
	*/
	var $settings = array();

	/**
	* Extension name
	*
	* @var	string
	*/
	var $name = 'Low Safe Encode';

	/**
	* Extension version
	*
	* @var	string
	*/
	var $version = '2.1';

	/**
	* Extension description
	*
	* @var	string
	*/
	var $description = 'Safe encoding of email addresses, without JavaScript';

	/**
	* Do settings exist?
	*
	* @var	bool
	*/
	var $settings_exist = FALSE;
	
	/**
	* Documentation link
	*
	* @var	string
	*/
	var $docs_url = 'http://loweblog.com/software/low-safe-encode/';
	
	/**
	* NSM Addon Updater link
	*
	* @var	string
	*/
	var $versions_xml = 'http://loweblog.com/software/low-safe-encode/feed/';

	// --------------------------------------------------------------------

	/**
	* PHP4 Constructor
	*
	* @see	__construct()
	*/
	function Low_safe_encode_ext($settings = FALSE)
	{
		$this->__construct($settings);
	}

	// --------------------------------------------------------------------

	/**
	* PHP5 Constructor
	*
	* $param	mixed	$settings
	*/
	function __construct($settings = FALSE)
	{
		/** -------------------------------------
		/**  Get global instance
		/** -------------------------------------*/

		$this->EE =& get_instance();

		$this->settings = $settings;

	}

	// --------------------------------------------------------------------

	/**
	 * Settings
	 *
	 * @return	array
	 */
	function settings()
	{
		// no settings...
		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	* Check input and replace {encode="some@emails.com"}
	*
	* @param	string	$str
	* @param	object	$obj
	* @param	object	$prefs
	* @return	string
	*/
	function typography_parse_type_start($str = '', &$obj, $prefs)
	{
		// Check for previous extension calling
		$str = ($this->EE->extensions->last_call !== FALSE) ? $this->EE->extensions->last_call : $str;
		
		// Search for encode pattern and replace accordingly
		if (preg_match_all('/'.LD.'encode=(.+?)'.RD.'/i', $str, $matches))
		{	
			for ($i = 0; $i < count($matches[0]); $i++)
			{	
				$str = str_replace($matches[0][$i], $this->_encode_email($matches[1][$i]), $str);
			}
		}
		
		// return modified string
		return $str;
	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Actual replacing of encode string
	 *
	 * @param	string	$str
	 * @return	string
	 */
	
	function _encode_email($str)
	{
		// Check input
		$email = (is_array($str)) ? trim($str[1]) : trim($str);
		
		// init vars
		$title = '';
		$email = str_replace(array('"', "'"), '', $email);
		
		// check for title
		if ($p = strpos($email, "title="))
		{
			$title = substr($email, $p + 6);
			$email = trim(substr($email, 0, $p));
		}
		// no title? use email
		else
		{
			$title = $email;
		}
		
		// return encoded link
		return str_replace(array("@","."),array("&#64;","&#46;"),"<a href=\"mailto:{$email}\">{$title}</a>");
	}
	
	// --------------------------------------------------------------------

	/**
	* Activate extension
	*
	* @return	null
	*/
	function activate_extension()
	{
		// data to insert
		$data = array(
			'class'		=> __CLASS__,
			'method'	=> 'typography_parse_type_start',
			'hook'		=> 'typography_parse_type_start',
			'priority'	=> 1,
			'version'	=> $this->version,
			'enabled'	=> 'y',
			'settings'	=> ''
		);
		
		// insert in database
		$this->EE->db->insert('exp_extensions', $data);
	}
	 
	// --------------------------------------------------------------------
	
	/**
	* Update extension
	*
	* @param	string	$current
	* @return	null
	*/
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
		
		// init data array
		$data = array();
		
		// Add version to data array
		$data['version'] = $this->version;

		// Update records using data array
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->update('exp_extensions', $data);
	}

	// --------------------------------------------------------------------
	
	/**
	* Disable extension
	*
	* @return	null
	*/
	function disable_extension()
	{
		// Delete records
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('exp_extensions');
	}

	// --------------------------------------------------------------------
	 
}
// END CLASS

/* End of file ext.low_safe_encode.php */