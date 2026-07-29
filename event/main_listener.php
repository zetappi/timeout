<?php

namespace marcozp\timeout\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class main_listener implements EventSubscriberInterface
{
    /** @var \phpbb\template\template */
    protected $template;

    /** @var \phpbb\request\request */
    protected $request;

    /** @var \phpbb\user */
    protected $user;

    /** @var \phpbb\auth\auth */
    protected $auth;

    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \phpbb\controller\helper */
    protected $helper;

    /** @var string phpbb_root_path */
    protected $phpbb_root_path;

    /** @var string phpEx */
    protected $php_ext;

    /** @var string table_prefix */
    protected $table_prefix;

    /**
     * Constructor
     *
     * @param \phpbb\template\template              $template         Template object
     * @param \phpbb\request\request                $request          Request object
     * @param \phpbb\user                           $user             User object
     * @param \phpbb\auth\auth                      $auth             Auth object
     * @param \phpbb\config\config                  $config           Config object
     * @param \phpbb\db\driver\driver_interface     $db               Database object
     * @param \phpbb\controller\helper              $helper           Controller helper object
     * @param string                                $phpbb_root_path  phpBB root path
     * @param string                                $php_ext          phpEx
     * @param string                                $table_prefix     Table prefix
     */
    public function __construct(
        \phpbb\template\template $template,
        \phpbb\request\request $request,
        \phpbb\user $user,
        \phpbb\auth\auth $auth,
        \phpbb\config\config $config,
        \phpbb\db\driver\driver_interface $db,
        \phpbb\controller\helper $helper,
        $phpbb_root_path,
        $php_ext,
        $table_prefix
    )
    {
        $this->template = $template;
        $this->request = $request;
        $this->user = $user;
        $this->auth = $auth;
        $this->config = $config;
        $this->db = $db;
        $this->helper = $helper;
        $this->phpbb_root_path = $phpbb_root_path;
        $this->php_ext = $php_ext;
        $this->table_prefix = $table_prefix;
        
        // Imposta le variabili template necessarie per lo script JavaScript
        $this->template->assign_vars([
            // Indica se l'utente ha i permessi per il timeout
            'S_AUTH_TIMEOUT' => (bool) $this->auth->acl_get('m_timeout'),
            // ID dell'utente corrente
            'S_USER_ID' => (int) $this->user->data['user_id'],
            // URL base per il pannello di moderazione
            'U_MCP_ROOT' => append_sid("{$this->phpbb_root_path}mcp.{$this->php_ext}")
        ]);
        
        // Se l'utente ha i permessi di timeout, imposta la variabile globale per attivare gli script
        if ($this->auth->acl_get('m_timeout'))
        {
            $this->template->assign_var('S_SHOW_TIMEOUT_BUTTONS', true);
        }
        
        // Carica il linguaggio per il timeout
        $this->user->add_lang_ext('marcozp/timeout', 'common');
    }

    /**
     * Assign functions defined in this class to event listeners in the core
     *
     * @return array
     */
    static public function getSubscribedEvents()
    {
        return [
            'core.viewtopic_modify_post_row'         => 'add_timeout_button',
            'core.memberlist_view_profile'           => 'show_timeout_info_profile',
            'core.viewtopic_post_rowset_data'        => 'load_timeout_language',
            'core.memberlist_view_profile_before'    => 'load_timeout_language_profile',
            
            // Eventi per bloccare gli utenti in timeout
            'core.posting_modify_submit_post_before' => 'check_posting_permission',
            'core.modify_posting_auth'               => 'check_posting_auth',
            
            // Blocco quote e modifica post
            'core.viewtopic_modify_page_title'       => 'check_quote_permission',
            'core.modify_posting_parameters'         => 'check_edit_permission',
            
            // Blocco messaggi privati
            'core.ucp_pm_compose_modify_data'        => 'check_pm_permission',
            'core.ucp_pm_compose_compose_pm_basic_info_query_before' => 'check_pm_permission',
            
            // Blocco risposte rapide
            'core.viewtopic_modify_quickreply_template_vars' => 'disable_quickreply',
            
            // Blocco creazione nuovi topic
            'core.posting_modify_template_vars'      => 'check_new_topic_permission',
            
            // Verifica all'inizio di ogni pagina
            'core.page_header'                       => 'check_timeout_status',
            
            // Carica il file di lingua timeout/common nella pagina del log moderatore
            'core.mcp_logs_view_log_before'          => 'load_timeout_language_mcp_log',
        ];
    }

    /**
     * Carica il file di lingua timeout/common nella pagina del log moderatore
     */
    public function load_timeout_language_mcp_log($event)
    {
        $this->user->add_lang_ext('marcozp/timeout', 'common');
    }

    /**
     * Carica il linguaggio per il timeout
     *
     * @param \phpbb\event\data $event Oggetto evento
     */
    public function load_timeout_language($event)
    {
        $this->user->add_lang_ext('marcozp/timeout', 'common');
    }

    /**
     * Carica i file di lingua necessari per la pagina del profilo
     *
     * @param \phpbb\event\data $event Oggetto evento
     */
    public function load_timeout_language_profile($event)
    {
        $this->user->add_lang_ext('marcozp/timeout', 'common');
    }

    /**
     * Aggiunge il pulsante timeout sotto ogni post e imposta la variabile S_USER_IN_TIMEOUT
     *
     * @param \phpbb\event\data $event Oggetto evento
     */
    public function add_timeout_button($event)
    {
        // Ottieni i dati del post
        $post_row = $event['post_row'];
        
        // POSTER_ID è essenziale per il badge.
        if (!isset($post_row['POSTER_ID']))
        {
            $available_keys_str = empty($post_row) ? 'empty' : implode(', ', array_keys($post_row));
            return;
        }
        $poster_id = (int)$post_row['POSTER_ID'];

        // POST_ID è utile per i log e potenzialmente per U_TIMEOUT. Non rendiamolo fatale qui per il badge.
        // Se POST_ID non è presente, $post_id sarà 0, il che è gestibile nel log e successivamente nel blocco m_timeout.
        $post_id = isset($post_row['POST_ID']) ? (int)$post_row['POST_ID'] : 0;
        
        // TOPIC_ID sarà recuperato e verificato specificamente all'interno del blocco m_timeout se necessario.
        
        // Determina se l'autore del post è in timeout
        $is_poster_in_timeout = $this->is_user_in_timeout($poster_id);
        
        // Imposta la variabile S_USER_IN_TIMEOUT nel post_row per il banner (visibile a tutti)
        $post_row['S_USER_IN_TIMEOUT'] = $is_poster_in_timeout;
        
        // Calcola e imposta il tempo rimanente del timeout se l'utente è in timeout
        if ($is_poster_in_timeout) {
            $sql = 'SELECT timeout_end FROM ' . $this->table_prefix . 'user_timeouts WHERE user_id = ' . (int) $poster_id . ' AND timeout_end > ' . time() . ' AND timeout_status = 1';
            $result = $this->db->sql_query_limit($sql, 1);
            $timeout_data = $this->db->sql_fetchrow($result);
            $this->db->sql_freeresult($result);
            
            if ($timeout_data && !empty($timeout_data['timeout_end'])) {
                $remaining_time = (int) $timeout_data['timeout_end'] - time();
                // Calcola giorni, ore e minuti separatamente
                $days = floor($remaining_time / 86400);
                $hours = floor(($remaining_time % 86400) / 3600);
                $minutes = floor(($remaining_time % 3600) / 60);
                // Passa i valori separati al template
                $post_row['TIMEOUT_REMAINING_DAYS'] = $days;
                $post_row['TIMEOUT_REMAINING_HOURS'] = $hours;
                $post_row['TIMEOUT_REMAINING_MINUTES'] = $minutes;
                // Mantieni il formato originale come fallback
                $post_row['TIMEOUT_REMAINING'] = $this->human_time($remaining_time);
            } else {
                $post_row['TIMEOUT_REMAINING'] = '';
                $post_row['TIMEOUT_REMAINING_DAYS'] = 0;
                $post_row['TIMEOUT_REMAINING_HOURS'] = 0;
                $post_row['TIMEOUT_REMAINING_MINUTES'] = 0;
            }
        }
        
        // Aggiungi la classe CSS e attributi HTML per il post in timeout (visibile a tutti)
        if ($is_poster_in_timeout) {
            // Aggiungi la classe CSS
            if (isset($post_row['POST_CLASS']) && !empty($post_row['POST_CLASS'])) {
                $post_row['POST_CLASS'] .= ' is-timeout';
            } else {
                $post_row['POST_CLASS'] = 'is-timeout';
            }
            
            // Aggiungi attributo HTML diretto (funziona anche se le classi CSS falliscono)
            if (isset($post_row['POST_ATTRIBUTES']) && !empty($post_row['POST_ATTRIBUTES'])) {
                $post_row['POST_ATTRIBUTES'] .= ' data-timeout="1"';
            } else {
                $post_row['POST_ATTRIBUTES'] = 'data-timeout="1"';
            }
        }
        
        // Logica specifica per i moderatori (pulsante, ecc.)
        if ($this->auth->acl_get('m_timeout'))
        {
            // Gestione avanzata del post e dei pulsanti per utenti in timeout
            if ($is_poster_in_timeout) {
            // Se l'utente è in timeout, assicuriamoci che il pulsante non sia più cliccabile
            
            // 1. Forza disattivazione del pulsante
            $post_row['S_SHOW_TIMEOUT_BUTTON'] = false;
            
            // 2. Imposta un URL vuoto per sicurezza
            $post_row['U_TIMEOUT'] = 'javascript:void(0);';
            
            // 3. Aggiungi una variabile per indicare che il pulsante è disabilitato
            $post_row['S_TIMEOUT_BUTTON_DISABLED'] = true;
            
            // 4. Aggiorna i dati del post
            $event['post_row'] = $post_row;
            return;
        }
        
        // Non mostrare il pulsante per utenti anonimi, amministratori o per se stessi
        if ($poster_id == ANONYMOUS || $poster_id == $this->user->data['user_id']) {
            // Aggiorna i dati del post
            $event['post_row'] = $post_row;
            return;
        }
        
        // Verifica se l'utente è un amministratore
        $sql = 'SELECT group_id FROM ' . USER_GROUP_TABLE . ' WHERE user_id = ' . (int)$poster_id . ' AND group_id = ' . (int)$this->config['config_admin_group'];
        $result = $this->db->sql_query($sql);
        $is_admin = (bool) $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);
        
        // Non mostrare il pulsante per gli amministratori
        if ($is_admin) {
            // Aggiorna i dati del post
            $event['post_row'] = $post_row;
            return;
        }
        
        // Imposta la variabile per indicare se l'utente è un amministratore
        $post_row['S_POSTER_ADMIN'] = $is_admin;
        
        // Imposta la variabile S_SHOW_TIMEOUT_BUTTON per questo specifico post
        // Il pulsante viene mostrato solo se:
        // 1. L'utente ha i permessi di timeout (m_timeout)
        // 2. Il poster non è anonimo
        // 3. Il poster non è l'utente corrente
        // 4. Il poster non è un amministratore
        // 5. Il poster non è già in timeout
        $post_row['S_SHOW_TIMEOUT_BUTTON'] = $this->auth->acl_get('m_timeout') && 
                                           $poster_id != ANONYMOUS && 
                                           $poster_id != $this->user->data['user_id'] && 
                                           !$is_admin && 
                                           !$is_poster_in_timeout;
        
        // Aggiungi variabili globali per il template
        $this->template->assign_vars([
            'S_AUTH_TIMEOUT' => (bool) $this->auth->acl_get('m_timeout'),
            'ANONYMOUS' => ANONYMOUS,
            'S_USER_ID' => $this->user->data['user_id'],
        ]);

        // Aggiungi il link per il timeout SOLO se l'utente non è già in timeout
        if (!$is_poster_in_timeout) {
            $this->user->add_lang_ext('marcozp/timeout', 'info_mcp_timeout');
            $post_row['U_TIMEOUT'] = append_sid("{$this->phpbb_root_path}mcp.$this->php_ext", "i=marcozp_timeout&amp;mode=main&amp;user_id=$poster_id&amp;post_id=$post_id");
        } else {
            // Se l'utente è già in timeout, non creare il link
            $post_row['U_TIMEOUT'] = '#';
        }
    } // Fine blocco if ($this->auth->acl_get('m_timeout'))
        
        // Aggiorna i dati del post
        $event['post_row'] = $post_row;
    }

    /**
     * Mostra le informazioni sul timeout nel profilo utente
     *
     * @param \phpbb\event\data $event Oggetto evento
     */
    public function show_timeout_info_profile($event)
    {
        // Se l'utente non ha i permessi, non mostriamo le informazioni sul timeout
        if (!$this->auth->acl_get('m_timeout'))
        {
            return;
        }

        $user_id = $event['member']['user_id'];

        // Ottieni la cronologia ban/warning
        $history = $this->get_user_history_from_log($user_id);

        // Controlla se l'utente è attualmente in timeout
        $sql = 'SELECT *
                FROM ' . $this->table_prefix . 'user_timeouts
                WHERE user_id = ' . (int) $user_id . '
                AND timeout_end > ' . time() . '
                AND timeout_status = 1
                ORDER BY timeout_end DESC';
        $result = $this->db->sql_query_limit($sql, 1);
        $active_timeout = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        // Ottieni il conteggio totale dei timeout ricevuti dall'utente
        $sql = 'SELECT COUNT(*) as total_timeouts
                FROM ' . $this->table_prefix . 'user_timeouts
                WHERE user_id = ' . (int) $user_id;
        $result = $this->db->sql_query($sql);
        $total_timeouts = (int) $this->db->sql_fetchfield('total_timeouts');
        $this->db->sql_freeresult($result);

        // Preparazione dei dati per il template
        $this->template->assign_vars([
            'S_SHOW_TIMEOUT_INFO' => true,
            'TIMEOUT_COUNT' => $total_timeouts,
            'S_USER_IS_IN_TIMEOUT' => ($active_timeout) ? true : false,
            'BAN_COUNT' => $history['ban_count'],
            'WARNING_COUNT' => $history['warning_count'],
            'TIMEOUT_COUNT' => $history['timeout_count'],
        ]);

        // Se l'utente è in timeout, mostra i dettagli
        if ($active_timeout)
        {
            $this->template->assign_vars([
                'TIMEOUT_END_DATE' => $this->user->format_date($active_timeout['timeout_end']),
                'TIMEOUT_REASON' => $active_timeout['timeout_reason'],
            ]);
        }
    }
    
    /**
     * Verifica se un utente è attualmente in timeout
     *
     * @param int $user_id ID dell'utente
     * @return bool true se l'utente è in timeout, false altrimenti
     */
    protected function is_user_in_timeout($user_id)
    {
        // Metodo 1: Controlla nella tabella user_timeouts
        $sql = 'SELECT COUNT(*) as timeout_count
                FROM ' . $this->table_prefix . 'user_timeouts
                WHERE user_id = ' . (int) $user_id . '
                AND timeout_end > ' . time() . '
                AND timeout_status = 1';
        $result = $this->db->sql_query($sql);
        $timeout_count = (int) $this->db->sql_fetchfield('timeout_count');
        $this->db->sql_freeresult($result);
        
        if ($timeout_count > 0) {
            return true;
        }
        
        // Metodo 2: Controlla i campi dell'utente
        $sql = 'SELECT user_timeout_end
                FROM ' . USERS_TABLE . '
                WHERE user_id = ' . (int) $user_id . '
                AND user_timeout_end > ' . time();
        $result = $this->db->sql_query($sql);
        $user_timeout = (int) $this->db->sql_fetchfield('user_timeout_end');
        $this->db->sql_freeresult($result);
        
        // Se l'utente ha un timeout attivo nei campi utente, verifichiamo che sia anche presente nella tabella user_timeouts
        if ($user_timeout > 0) {
            // Controlla se esiste un record corrispondente nella tabella user_timeouts
            $sql = 'SELECT COUNT(*) as timeout_exists
                    FROM ' . $this->table_prefix . 'user_timeouts
                    WHERE user_id = ' . (int) $user_id;
            $result = $this->db->sql_query($sql);
            $timeout_exists = (int) $this->db->sql_fetchfield('timeout_exists');
            $this->db->sql_freeresult($result);
            
            // Se non esiste un record nella tabella user_timeouts, azzera i campi dell'utente
            if ($timeout_exists == 0) {
                $sql = 'UPDATE ' . USERS_TABLE . '
                        SET user_timeout_end = 0,
                            user_timeout_start = 0
                        WHERE user_id = ' . (int) $user_id;
                $this->db->sql_query($sql);
                
                return false;
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Verifica se l'utente può postare (non è in timeout)
     * Questo metodo viene chiamato prima che il post sia inviato
     *
     * @param \phpbb\event\data $event Oggetto evento
     */
    public function check_posting_permission($event)
    {
        // Skip per admin e moderatori
        if ($this->auth->acl_gets('a_', 'm_'))
        {
            return;
        }
        
        // Verifica se l'utente è in timeout
        $user_id = $this->user->data['user_id'];
        if ($this->is_user_in_timeout($user_id))
        {
            // Carica le stringhe di linguaggio
            $this->user->add_lang_ext('marcozp/timeout', 'common');
            
            // Errore - utente in timeout non può postare
            trigger_error($this->user->lang('TIMEOUT_USER_CANNOT_POST'));
        }
    }
    
    /**
     * Verifica i permessi di posting (interviene prima di entrare nella pagina di posting)
     *
     * @param \phpbb\event\data $event Oggetto evento
     */
    public function check_posting_auth($event)
    {
        // Skip per admin e moderatori
        if ($this->auth->acl_gets('a_', 'm_'))
        {
            return;
        }
        
        // Verifica se l'utente è in timeout
        $user_id = $this->user->data['user_id'];
        if ($this->is_user_in_timeout($user_id))
        {
            // Carica le stringhe di linguaggio
            $this->user->add_lang_ext('marcozp/timeout', 'common');
            
            // Modifica l'array di errori per bloccare il posting
            $event['is_authed'] = false;
            $event['auth_msg'] = $this->user->lang('TIMEOUT_USER_CANNOT_POST');
        }
    }
    
    /**
     * Verifica lo stato di timeout dell'utente all'inizio di ogni pagina
     * Questo metodo viene chiamato per ogni pagina caricata
     *
     * @param \phpbb\event\data $event Oggetto evento
     */
    public function check_timeout_status($event)
    {
        // Skip per admin, moderatori e utenti anonimi
        if ($this->auth->acl_gets('a_', 'm_') || $this->user->data['user_id'] == ANONYMOUS)
        {
            return;
        }
        
        // Verifica se l'utente è in timeout
        $user_id = $this->user->data['user_id'];
        
        // Ottieni i dettagli del timeout attivo dall'utente
        $sql = 'SELECT *
                FROM ' . $this->table_prefix . 'user_timeouts
                WHERE user_id = ' . (int) $user_id . '
                AND timeout_end > ' . time() . '
                AND timeout_status = 1
                ORDER BY timeout_end DESC';
        $result = $this->db->sql_query_limit($sql, 1);
        $timeout_data = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);
        
        if ($timeout_data)
        {
            // Carica le stringhe di linguaggio
            $this->user->add_lang_ext('marcozp/timeout', 'common');
            
            // Formatta la data di fine timeout usando le funzioni di phpBB per la localizzazione
            $timeout_end_timestamp = (int)$timeout_data['timeout_end'];
            $timeout_end_date = $this->user->format_date($timeout_end_timestamp);
            
            // Usa le stringhe di lingua per il messaggio di timeout
            $timeout_message = $this->user->lang('TIMEOUT_USER_RESTRICTED');
            $timeout_info = $this->user->lang('TIMEOUT_CURRENT_INFO', $timeout_end_date);
            
            // Crea un messaggio HTML usando le stringhe di lingua
            $warning_html = '<div class="rules"><div class="inner">' . $timeout_message . '<br><strong>' . $timeout_info . '</strong></div></div>';
            
            // Aggiungi una notifica globale che l'utente è in timeout
            $this->template->assign_vars([
                'S_USER_IN_TIMEOUT' => true,
                'TIMEOUT_WARNING_HTML' => $warning_html,
                'TIMEOUT_END_DATE' => $timeout_end_date,
            ]);
        }
    }
    
    /**
     * Disabilita la funzione di quote per gli utenti in timeout
     *
     * @param \phpbb\event\data $event Oggetto evento
     */
    public function check_quote_permission($event)
    {
        // Skip per admin, moderatori e utenti anonimi
        if ($this->auth->acl_gets('a_', 'm_') || $this->user->data['user_id'] == ANONYMOUS)
        {
            return;
        }
        
        // Verifica se l'utente è in timeout
        $user_id = $this->user->data['user_id'];
        if ($this->is_user_in_timeout($user_id))
        {
            // Carica le stringhe di linguaggio
            $this->user->add_lang_ext('marcozp/timeout', 'common');
            
            // Blocca l'accesso alla pagina di quote
            if ($this->request->is_set_post('qr_submit') || $this->request->is_set_post('quote'))
            {
                trigger_error($this->user->lang('TIMEOUT_USER_CANNOT_QUOTE'));
            }
        }
    }
    
    /**
     * Verifica i permessi di modifica post
     *
     * @param \phpbb\event\data $event Oggetto evento
     */
    public function check_edit_permission($event)
    {
        // Skip per admin, moderatori e utenti anonimi
        if ($this->auth->acl_gets('a_', 'm_') || $this->user->data['user_id'] == ANONYMOUS)
        {
            return;
        }
        
        // Verifica se l'utente è in timeout
        $user_id = $this->user->data['user_id'];
        if ($this->is_user_in_timeout($user_id) && $event['mode'] == 'edit')
        {
            // Carica le stringhe di linguaggio
            $this->user->add_lang_ext('marcozp/timeout', 'common');
            
            // Blocca l'accesso alla pagina di modifica
            trigger_error($this->user->lang('TIMEOUT_USER_CANNOT_EDIT'));
        }
    }
    
    /**
     * Verifica i permessi per i messaggi privati
     *
     * @param \phpbb\event\data $event Oggetto evento
     */
    public function check_pm_permission($event)
    {
        // Skip per admin, moderatori e utenti anonimi
        if ($this->auth->acl_gets('a_', 'm_') || $this->user->data['user_id'] == ANONYMOUS)
        {
            return;
        }
        
        // Verifica se l'utente è in timeout
        $user_id = $this->user->data['user_id'];
        if ($this->is_user_in_timeout($user_id))
        {
            // Carica le stringhe di linguaggio
            $this->user->add_lang_ext('marcozp/timeout', 'common');
            
            // Blocca l'accesso ai messaggi privati
            trigger_error($this->user->lang('TIMEOUT_USER_CANNOT_PM'));
        }
    }
    
    /**
     * Disabilita la risposta rapida per gli utenti in timeout
     *
     * @param \phpbb\event\data $event Oggetto evento
     */
    public function disable_quickreply($event)
    {
        // Skip per admin, moderatori e utenti anonimi
        if ($this->auth->acl_gets('a_', 'm_') || $this->user->data['user_id'] == ANONYMOUS)
        {
            return;
        }
        
        // Verifica se l'utente è in timeout
        $user_id = $this->user->data['user_id'];
        if ($this->is_user_in_timeout($user_id))
        {
            // Disabilita la risposta rapida
            $event['s_quick_reply'] = false;
            
            // Aggiungi un messaggio di avviso
            $this->template->assign_var('TIMEOUT_QUICKREPLY_DISABLED', $this->user->lang('TIMEOUT_USER_CANNOT_POST'));
        }
    }
    
    /**
     * Verifica i permessi per la creazione di nuovi topic
     *
     * @param \phpbb\event\data $event Oggetto evento
     */
    public function check_new_topic_permission($event)
    {
        // Skip per admin, moderatori e utenti anonimi
        if ($this->auth->acl_gets('a_', 'm_') || $this->user->data['user_id'] == ANONYMOUS)
        {
            return;
        }
        
        // Verifica se l'utente è in timeout
        $user_id = $this->user->data['user_id'];
        if ($this->is_user_in_timeout($user_id) && $event['mode'] == 'post')
        {
            // Carica le stringhe di linguaggio
            $this->user->add_lang_ext('marcozp/timeout', 'common');
            
            // Blocca la creazione di nuovi topic
            trigger_error($this->user->lang('TIMEOUT_USER_CANNOT_POST'));
        }
    }
    
    /**
     * Estrae la cronologia di ban e warning per un utente
     * 
     * @param int $user_id ID dell'utente
     * @return array Array con i dati dei ban/warning
     */
    public function get_user_history_from_log($user_id)
    {
        global $db;
        
        $user_id = (int) $user_id;
        $log_table = $this->table_prefix . 'log';
        $two_years_ago = time() - (86400 * 730);
        
        // Prima ottieni lo username dall'ID
        $sql = "SELECT username FROM " . $this->table_prefix . "users WHERE user_id = {$user_id}";
        $result = $this->db->sql_query($sql);
        $username = $this->db->sql_fetchfield('username');
        $this->db->sql_freeresult($result);
        
        if (empty($username)) {
            return ['ban_count' => 0, 'warning_count' => 0, 'timeout_count' => 0];
        }
        
        // Conta i ban (ultimi 2 anni)
        $sql_ban = "SELECT COUNT(*) as count 
                   FROM {$log_table} 
                   WHERE log_operation = 'LOG_BAN_USER' 
                   AND log_time > {$two_years_ago}
                   AND log_data LIKE '%" . $this->db->sql_escape($username) . "%'";
        $result = $this->db->sql_query($sql_ban);
        $ban_count = (int) ($this->db->sql_fetchfield('count') / 2); // Divisione per 2
        $this->db->sql_freeresult($result);
        
        // Conta i warning (ultimi 2 anni)
        $sql_warning = "SELECT COUNT(*) as count 
                       FROM {$log_table} 
                       WHERE log_operation = 'LOG_USER_WARNING' 
                       AND log_time > {$two_years_ago}
                       AND log_data LIKE '%" . $this->db->sql_escape($username) . "%'";
        $result = $this->db->sql_query($sql_warning);
        $warning_count = (int) ($this->db->sql_fetchfield('count') / 2); // Divisione per 2
        $this->db->sql_freeresult($result);
        
        // Conta i timeout (ultimi 2 anni)
        $sql_timeout = "SELECT COUNT(*) as count 
                       FROM {$log_table} 
                       WHERE (log_operation = 'LOG_MOD_TIMEOUT' OR log_operation LIKE '%TIMEOUT%')
                       AND log_time > {$two_years_ago}
                       AND log_data LIKE '%" . $this->db->sql_escape($username) . "%'";
        $result = $this->db->sql_query($sql_timeout);
        $timeout_count = (int) $this->db->sql_fetchfield('count');
        $this->db->sql_freeresult($result);
        
        error_log("[TIMEOUT_ESTIMATED_COUNTS] User: {$username} (ID: {$user_id}) - Estimated Bans: {$ban_count}, Estimated Warnings: {$warning_count}, Estimated Timeouts: {$timeout_count} (Last 2 years)");
        
        return [
            'ban_count' => $ban_count,
            'warning_count' => $warning_count,
            'timeout_count' => $timeout_count
        ];
    }
    
    /**
     * Aggiunge un timeout ad un utente
     * 
     * @param \phpbb\event\data $event Oggetto evento
     */
    public function add_timeout($event)
    {
        global $db, $user;
        
        $user_id = $event['user_id'];
        $duration = $event['duration'];
        $reason = $event['reason'];
        
        // Verifica se esiste già un timeout attivo
        $sql = 'SELECT * FROM ' . $this->table_prefix . 'timeouts 
               WHERE user_id = ' . (int)$user_id . ' 
               AND timeout_end > ' . time();
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);
        
        if ($row)
        {
            return;
        }
        
        // Inserisce il nuovo timeout
        $data = array(
            'user_id' => (int)$user_id,
            'timeout_start' => time(),
            'timeout_end' => time() + (int)$duration,
            'reason' => $reason,
            'moderator_id' => $user->data['user_id']
        );
        
        $sql = 'INSERT INTO ' . $this->table_prefix . 'timeouts ' . $db->sql_build_array('INSERT', $data);
        $db->sql_query($sql);
        
        // Aggiungi voce di log
        $log_data = array($user_id, $duration, $reason);
        add_log('mod', 0, 0, 'LOG_MOD_TIMEOUT', time(), $log_data);
    }
    
    /**
     * Calcola un indice di rischio disciplinare
     */
    public function calculate_disciplinary_index($ban, $warning, $timeout, $weights = [], $max_score = 100)
    {
        $defaults = [
            'ban'     => 5,
            'warning' => 2,
            'timeout' => 3,
        ];

        $w = array_merge($defaults, $weights);
        $score = ($ban * $w['ban']) + ($warning * $w['warning']) + ($timeout * $w['timeout']);
        $risk_index = min(100, max(0, ($score / $max_score) * 100));
        
        // Debug del calcolo
        error_log("[RISK_CALC] Ban: {$ban}, Warn: {$warning}, Timeout: {$timeout} => Score: {$score}/{$max_score} => Index: {$risk_index}");
        
        return round($risk_index, 2);
    }
    
    /**
     * Converte il tempo rimanente in un formato leggibile GG-hh:mm
     */
    private function human_time($seconds)
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        if ($days > 0) {
            return sprintf('%02dG-%02d:%02d', $days, $hours, $minutes);
        } else {
            return sprintf('%02d:%02d', $hours, $minutes);
        }
    }
}
