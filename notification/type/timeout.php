<?php
/**
 * Timeout extension for phpBB 3.3.x.
 * @copyright (c) marcozp
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace marcozp\timeout\notification\type;

class timeout extends \phpbb\notification\type\base
{
    /** @var \phpbb\controller\helper */
    protected $helper;
    
    /** @var string */
    protected $phpbb_root_path;
    
    /** @var string */
    protected $php_ext;

    /** @var \phpbb\user_loader */
    protected $user_loader;
    
    /** @var \phpbb\language\language */
    protected $language;

    /**
     * Get helper, root path and php extension from the service container
     */
    public function set_controller_helper(\phpbb\controller\helper $helper, $phpbb_root_path, $php_ext)
    {
        $this->helper = $helper;
        $this->phpbb_root_path = $phpbb_root_path;
        $this->php_ext = $php_ext;
        
        return $this;
    }
    
    /**
     * Set user_loader
     * 
     * @param \phpbb\user_loader $user_loader
     * @return $this
     */
    public function set_user_loader(\phpbb\user_loader $user_loader)
    {
        $this->user_loader = $user_loader;
        
        return $this;
    }
    
    /**
     * Set language
     * 
     * @param \phpbb\language\language $language
     * @return $this
     */
    public function set_language(\phpbb\language\language $language)
    {
        $this->language = $language;
        
        // Carica esplicitamente il file di lingua dell'estensione
        $this->language->add_lang('common', 'marcozp/timeout');
        
        return $this;
    }
    
    /**
     * Get notification type name
     *
     * @return string
     */
    public function get_type()
    {
        return 'marcozp.timeout.notification.type.timeout';
    }
    
    /**
     * Notification option data (for outputting to the user)
     *
     * @var bool|array False if the service should use its default data
     *                Array of data (including keys 'id', 'lang', and 'group')
     */
    public static $notification_option = [
        'lang' => 'NOTIFICATION_TYPE_TIMEOUT',
        'group' => 'NOTIFICATION_GROUP_MODERATION',
    ];
    
    /**
     * Is available
     *
     * @return bool
     */
    public function is_available()
    {
        return true;
    }
    
    /**
     * Get the id of the notification
     *
     * @param array $data The data for the notification
     * @return int The id of the notification
     */
    public static function get_item_id($data)
    {
        return (int) $data['timeout_id'];
    }
    
    /**
     * Get the id of the parent
     *
     * @param array $data The data for the notification
     * @return int The id of the parent
     */
    public static function get_item_parent_id($data)
    {
        return 0;
    }
    
    /**
     * Find the users who want to receive notifications
     *
     * @param array $data The type specific data
     * @param array $options Options for finding users for notification
     *
     * @return array
     */
    public function find_users_for_notification($data, $options = [])
    {
        $users = [];
        $users[$data['user_id']] = $this->notification_manager->get_default_methods();
        
        return $users;
    }
    
    /**
     * Users needed to query before this notification can be displayed
     *
     * @return array Array of user_ids
     */
    public function users_to_query()
    {
        // Non è necessario caricare informazioni del moderatore
        return [];
    }
    
    /**
     * Get the user's avatar
     *
     * @return string
     */
    public function get_avatar()
    {
        // Usa l'avatar di sistema invece di quello del moderatore
        return '';
    }
    
    /**
     * Get the title of the notification
     *
     * @return string
     */
    public function get_title()
    {
        // Titolo standard senza riferimenti al moderatore
        return 'Sei stato messo in timeout';
    }
    
    /**
     * Get the HTML formatted title of this notification
     *
     * @return string
     */
    public function get_reference()
    {
        $end_date = $this->user->format_date($this->get_data('timeout_end'));
        $reason = $this->get_data('timeout_reason');
        return sprintf('Scadenza - %s per il seguente motivo: %s',
                $end_date, $reason);
    }

    /**
     * Get the url to this item
     *
     * @return string URL
     */
    public function get_url()
    {
        return append_sid("{$this->phpbb_root_path}index.{$this->php_ext}");
    }
    
    /**
     * Get email template
     *
     * @return string|bool
     */
    public function get_email_template()
    {
        return '@marcozp_timeout/timeout';
    }
    
    /**
     * Get email template variables
     *
     * @return array
     */
    public function get_email_template_variables()
    {
        // Rimuovi il riferimento al nome del moderatore
        return [
            'TIMEOUT_REASON' => htmlspecialchars_decode($this->get_data('timeout_reason')),
            'TIMEOUT_END' => $this->user->format_date($this->get_data('timeout_end')),
            // Non includiamo ISSUER_NAME per nascondere il moderatore
        ];
    }
    
    /**
     * Function for preparing the data for insertion in an SQL query
     *
     * @param array $data The data for the notification
     * @param array $pre_create_data Data from pre_create_insert_array()
     *
     * @return array Array of data ready to be inserted into the database
     */
    public function create_insert_array($data, $pre_create_data = [])
    {
        $this->set_data('timeout_id', $data['timeout_id']);
        $this->set_data('mod_user_id', $data['mod_user_id']);
        $this->set_data('timeout_reason', $data['timeout_reason']);
        $this->set_data('timeout_end', $data['timeout_end']);
        $this->set_data('user_id', $data['user_id']);
        
        parent::create_insert_array($data, $pre_create_data);
        
        return $this->get_insert_array();
    }
    
    /**
     * Trim the message for the notification
     *
     * @param string $message
     * @param int $max_length
     * @return string Trimmed message
     */
    protected function trim_message($message, $max_length = 70)
    {
        if (utf8_strlen($message) > $max_length) {
            $message = utf8_substr($message, 0, $max_length - 3) . '...';
        }
        
        return $message;
    }
}
