<?php

namespace marcozp\timeout\acp;

class main_module
{
    public $u_action;
    public $tpl_name;
    public $page_title;

    /**
     * Funzione per generare l'hash per i link
     */
    private function generate_timeout_hash($value)
    {
        return generate_link_hash($value);
    }
    
    /**
     * Funzione semplificata per creare la paginazione
     */
    private function generate_simple_pagination($base_url, $num_items, $per_page, $start_item)
    {
        $total_pages = ceil($num_items / $per_page);
        $on_page = floor($start_item / $per_page) + 1;
        $page_string = '';
        
        if ($total_pages <= 1)
        {
            return '';
        }
        
        for ($i = 1; $i <= $total_pages; $i++)
        {
            $page_start = ($i - 1) * $per_page;
            if ($i == $on_page)
            {
                $page_string .= '<strong>' . $i . '</strong>';
            }
            else
            {
                $page_string .= '<a href="' . $base_url . '&amp;start=' . $page_start . '">' . $i . '</a>';
            }
            
            if ($i < $total_pages)
            {
                $page_string .= '&nbsp;|&nbsp;';
            }
        }
        
        return $page_string;
    }
    
    public function main($id, $mode)
    {
        global $phpbb_container, $table_prefix;

        // Ottieni i servizi necessari dal container
        $config = $phpbb_container->get('config');
        $request = $phpbb_container->get('request');
        $template = $phpbb_container->get('template');
        $user = $phpbb_container->get('user');
        $db = $phpbb_container->get('dbal.conn');
        $auth = $phpbb_container->get('auth');

        // Carica i file di lingua
        $user->add_lang_ext('marcozp/timeout', 'common');
        $user->add_lang_ext('marcozp/timeout', 'info_acp_timeout');
        
        switch ($mode) {
            case 'settings':
                $this->tpl_name = 'acp_timeout_settings';
                $this->page_title = $user->lang('ACP_TIMEOUT_SETTINGS');
                
                add_form_key('marcozp/timeout');
                
                if ($request->is_set_post('submit')) {
                    if (!check_form_key('marcozp/timeout')) {
                        trigger_error('FORM_INVALID' . adm_back_link($this->u_action), E_USER_WARNING);
                    }
                    
                    // Aggiorna le configurazioni
                    $config->set('timeout_enable', $request->variable('timeout_enable', 0));
                    $config->set('timeout_notify_user', $request->variable('timeout_notify_user', 0));
                    $config->set('timeout_max_duration', $request->variable('timeout_max_duration', 1440)); // Default 24 ore
                    $config->set('timeout_log', $request->variable('timeout_log', 1));
                    
                    trigger_error($user->lang('ACP_TIMEOUT_SETTINGS_SAVED') . adm_back_link($this->u_action));
                }
                
                // Prepara il template
                $template->assign_vars([
                    'U_ACTION' => $this->u_action,
                    'TIMEOUT_ENABLE' => $config['timeout_enable'] ?? 0,
                    'TIMEOUT_NOTIFY_USER' => $config['timeout_notify_user'] ?? 0,
                    'TIMEOUT_MAX_DURATION' => $config['timeout_max_duration'] ?? 1440,
                    'TIMEOUT_LOG' => $config['timeout_log'] ?? 1,
                ]);
                
                break;
                
            case 'manage':
                $this->tpl_name = 'acp_timeout_manage';
                $this->page_title = $user->lang('ACP_TIMEOUT_MANAGE');
                
                $action = $request->variable('action', '');
                $timeout_id = $request->variable('timeout_id', 0);
                
                if ($action === 'delete' && $timeout_id) {
                    // Verifica il token di sicurezza
                    if (!check_link_hash($request->variable('hash', ''), 'timeout_delete_' . $timeout_id)) {
                        trigger_error($user->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
                    }
                    
                    // Ottieni l'ID utente dal timeout
                    $sql = 'SELECT user_id FROM ' . $table_prefix . 'user_timeouts WHERE timeout_id = ' . (int) $timeout_id;
                    $result = $db->sql_query($sql);
                    $timeout_user_id = (int) $db->sql_fetchfield('user_id');
                    $db->sql_freeresult($result);
                    
                    if ($timeout_user_id > 0) {                        
                        // Imposta il timeout come scaduto
                        $current_time = time();
                        $sql = "UPDATE {$table_prefix}user_timeouts 
                                SET timeout_status = 0, 
                                    timeout_end = {$current_time} 
                                WHERE timeout_id = {$timeout_id}";
                        $db->sql_query($sql);
                        $db->sql_query($sql);
                        
                        // Ripristina lo stato normale dell'utente
                        $sql = 'UPDATE ' . USERS_TABLE . '
                                SET user_timeout_end = 0,
                                    user_timeout_start = 0
                                WHERE user_id = ' . $timeout_user_id;
                        $db->sql_query($sql);
                        
                        // Aggiorna la sessione dell'utente se è online
                        $sql = 'UPDATE ' . SESSIONS_TABLE . '
                                SET session_timeout_end = 0,
                                    session_timeout_start = 0
                                WHERE session_user_id = ' . $timeout_user_id;
                        $db->sql_query($sql);
                        
                        // Forza il commit delle modifiche al database
                        $db->sql_transaction('commit');
                        
                        // Messaggio di conferma
                        trigger_error($user->lang('ACP_TIMEOUT_DELETED') . adm_back_link($this->u_action));
                    } else {
                        $debug_message .= '<p>Errore: Timeout non trovato o user_id non valido</p></div>';
                        trigger_error($debug_message . $user->lang('ACP_TIMEOUT_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
                    }
                    
                    // Ottieni l'ID utente dal timeout
                    $sql = 'SELECT user_id FROM ' . $table_prefix . 'user_timeouts WHERE timeout_id = ' . (int) $timeout_id;
                    $result = $db->sql_query($sql);
                    $timeout_user_id = (int) $db->sql_fetchfield('user_id');
                    $db->sql_freeresult($result);
                    
                    if ($timeout_user_id > 0) {
                        // Ripristina lo stato normale dell'utente
                        $sql = 'UPDATE ' . USERS_TABLE . '
                                SET user_timeout_end = 0,
                                    user_timeout_start = 0
                                WHERE user_id = ' . $timeout_user_id;
                        $db->sql_query($sql);
                        
                        // Aggiorna la sessione dell'utente se è online
                        $sql = 'UPDATE ' . SESSIONS_TABLE . '
                                SET session_timeout_end = 0,
                                    session_timeout_start = 0
                                WHERE session_user_id = ' . $timeout_user_id;
                        $db->sql_query($sql);
                        
                        // Imposta il timeout come scaduto
                        $current_time = time();
                        
                        // Usa una query diretta con sintassi SQL standard
                        $sql = "UPDATE {$table_prefix}user_timeouts 
                                SET timeout_status = 0, 
                                    timeout_end = {$current_time} 
                                WHERE timeout_id = {$timeout_id}";
                        $db->sql_query($sql);
                        
                        // Forza il commit delle modifiche al database
                        $db->sql_transaction('commit');
                        
                        // Messaggio di conferma
                        trigger_error($user->lang('ACP_TIMEOUT_DELETED') . adm_back_link($this->u_action));
                    } else {
                        trigger_error($user->lang('ACP_TIMEOUT_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
                    }
                }
                
                if ($action === 'purge' && $timeout_id) {
                    // Verifica il token di sicurezza
                    if (!check_link_hash($request->variable('hash', ''), 'timeout_purge_' . $timeout_id)) {
                        trigger_error($user->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
                    }
                    
                    // Ottieni l'ID utente e il nome utente dal timeout prima di eliminarlo
                    $sql = 'SELECT t.user_id, u.username 
                            FROM ' . $table_prefix . 'user_timeouts t
                            LEFT JOIN ' . USERS_TABLE . ' u ON (u.user_id = t.user_id)
                            WHERE t.timeout_id = ' . (int) $timeout_id;
                    $result = $db->sql_query($sql);
                    $timeout_data = $db->sql_fetchrow($result);
                    $db->sql_freeresult($result);
                    
                    if ($timeout_data) {
                        $timeout_user_id = (int) $timeout_data['user_id'];
                        $username = $timeout_data['username'];
                        
                        // Elimina fisicamente il record dal database
                        $sql = "DELETE FROM {$table_prefix}user_timeouts WHERE timeout_id = {$timeout_id}";
                        $db->sql_query($sql);
                        
                        // Aggiungi un log per l'azione di cancellazione timeout
                        $log = $phpbb_container->get('log');
                        $log->add('admin', $user->data['user_id'], $user->ip, 'TIMEOUT_LOG_PURGED', false, [
                            'reportee_id' => $timeout_user_id,
                            $username
                        ]);
                        
                        // Forza il commit delle modifiche al database
                        $db->sql_transaction('commit');
                        
                        // Messaggio di conferma
                        trigger_error($user->lang('ACP_TIMEOUT_PURGED') . adm_back_link($this->u_action));
                    } else {
                        trigger_error($user->lang('ACP_TIMEOUT_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
                    }
                }
                
                if ($action === 'end_now' && $timeout_id) {
                    // Verifica il token di sicurezza
                    if (!check_link_hash($request->variable('hash', ''), 'timeout_end_' . $timeout_id)) {
                        trigger_error($user->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
                    }
                    
                    // Ottieni l'ID utente dal timeout
                    $sql = 'SELECT user_id FROM ' . $table_prefix . 'user_timeouts WHERE timeout_id = ' . (int) $timeout_id;
                    $result = $db->sql_query($sql);
                    $timeout_user_id = (int) $db->sql_fetchfield('user_id');
                    $db->sql_freeresult($result);
                    
                    if ($timeout_user_id > 0) {
                        // Ripristina lo stato normale dell'utente
                        $sql = 'UPDATE ' . USERS_TABLE . '
                                SET user_timeout_end = 0,
                                    user_timeout_start = 0
                                WHERE user_id = ' . $timeout_user_id;
                        $db->sql_query($sql);
                        
                        // Aggiorna la sessione dell'utente se è online
                        $sql = 'UPDATE ' . SESSIONS_TABLE . '
                                SET session_timeout_end = 0,
                                    session_timeout_start = 0
                                WHERE session_user_id = ' . $timeout_user_id;
                        $db->sql_query($sql);
                        
                        // Imposta il timeout come scaduto
                        $sql = 'UPDATE ' . $table_prefix . 'user_timeouts
                                SET timeout_end = ' . time() . ',
                                    timeout_status = 0
                                WHERE timeout_id = ' . (int) $timeout_id;
                        $db->sql_query($sql);
                        
                        // Ottieni il nome utente per il log
                        $sql = 'SELECT username FROM ' . USERS_TABLE . ' WHERE user_id = ' . $timeout_user_id;
                        $result = $db->sql_query($sql);
                        $username = $db->sql_fetchfield('username');
                        $db->sql_freeresult($result);
                        
                        // Aggiungi un log per l'azione di terminazione timeout
                        $log = $phpbb_container->get('log');
                        $log->add('admin', $user->data['user_id'], $user->ip, 'TIMEOUT_LOG_ENDED', false, [
                            'reportee_id' => $timeout_user_id,
                            $username
                        ]);
                        
                        // Forza il commit delle modifiche al database
                        $db->sql_transaction('commit');
                        
                        // Messaggio di conferma
                        trigger_error($user->lang('ACP_TIMEOUT_ENDED') . adm_back_link($this->u_action));
                    } else {
                        trigger_error($user->lang('ACP_TIMEOUT_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
                    }
                }
                
                if ($action === 'remove' && $timeout_id) {
                    // Verifica il token di sicurezza
                    if (!check_link_hash($request->variable('hash', ''), 'timeout_remove_' . $timeout_id)) {
                        trigger_error($user->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
                    }
                    
                    // Ottieni l'ID utente dal timeout prima di aggiornarlo
                    $sql = 'SELECT user_id FROM ' . $table_prefix . 'user_timeouts WHERE timeout_id = ' . (int) $timeout_id;
                    $result = $db->sql_query($sql);
                    $timeout_user_id = (int) $db->sql_fetchfield('user_id');
                    $db->sql_freeresult($result);
                    
                    if ($timeout_user_id > 0) {
                        // Imposta il timeout come terminato
                        $sql = 'UPDATE ' . $table_prefix . 'user_timeouts
                                SET timeout_end = ' . time() . ',
                                    timeout_status = 0
                                WHERE timeout_id = ' . (int) $timeout_id;
                        $db->sql_query($sql);
                        
                        // Ripristina lo stato normale dell'utente
                        $sql = 'UPDATE ' . USERS_TABLE . '
                                SET user_timeout_end = 0,
                                    user_timeout_start = 0
                                WHERE user_id = ' . $timeout_user_id;
                        $db->sql_query($sql);
                        
                        // Aggiorna la sessione dell'utente se è online
                        $sql = 'UPDATE ' . SESSIONS_TABLE . '
                                SET session_timeout_end = 0,
                                    session_timeout_start = 0
                                WHERE session_user_id = ' . $timeout_user_id;
                        $db->sql_query($sql);
                        
                        trigger_error($user->lang('ACP_TIMEOUT_REMOVED') . adm_back_link($this->u_action));
                    } else {
                        trigger_error($user->lang('ACP_TIMEOUT_NOT_FOUND') . adm_back_link($this->u_action), E_USER_WARNING);
                    }
                }
                
                // Vista per tutti i timeout
                $start = $request->variable('start', 0);
                $limit = 25; // Timeouts per pagina
                $filter_status = $request->variable('status', '');
                $filter_username = $request->variable('username', '', true);
                
                // Costruisci la clausola WHERE per il filtro
                $where_sql = '';
                $current_time = time();
                
                // Filtro per stato (attivo/scaduto)
                if ($filter_status !== '') {
                    if ($filter_status == 1) { // Attivo
                        $where_sql .= ($where_sql ? ' AND ' : ' WHERE ') . '(t.timeout_end > ' . $current_time . ' AND t.timeout_status = 1)';
                    } else { // Scaduto
                        $where_sql .= ($where_sql ? ' AND ' : ' WHERE ') . '(t.timeout_end <= ' . $current_time . ' OR t.timeout_status = 0)';
                    }
                }
                
                // Filtro per username
                if ($filter_username) {
                    $where_sql .= ($where_sql ? ' AND ' : ' WHERE ') . 'u.username_clean ' . $db->sql_like_expression($db->sql_escape($filter_username) . $db->get_any_char());
                }
                
                // Conteggio totale per paginazione
                $sql = 'SELECT COUNT(*) as total_count 
                        FROM ' . $table_prefix . 'user_timeouts t
                        LEFT JOIN ' . USERS_TABLE . ' u ON (u.user_id = t.user_id)' . 
                        $where_sql;
                $result = $db->sql_query($sql);
                $total_count = (int) $db->sql_fetchfield('total_count');
                $db->sql_freeresult($result);
                
                // Query principale
                $sql = 'SELECT t.*, u.username, u.user_colour, m.username AS mod_username, m.user_colour AS mod_user_colour
                        FROM ' . $table_prefix . 'user_timeouts t
                        LEFT JOIN ' . USERS_TABLE . ' u ON (u.user_id = t.user_id)
                        LEFT JOIN ' . USERS_TABLE . ' m ON (m.user_id = t.mod_user_id)' .
                        $where_sql . '
                        ORDER BY t.timeout_start DESC';
                $result = $db->sql_query_limit($sql, $limit, $start);
                
                while ($row = $db->sql_fetchrow($result)) {
                    // Calcola se il timeout è attivo
                    $current_time = time();
                    $is_active = ($row['timeout_end'] > $current_time && $row['timeout_status'] == 1);
                    
                    // Usa solo la ragione originale senza debug
                    $reason = $row['timeout_reason'];
                    
                    $template->assign_block_vars('timeouts', [
                        'TIMEOUT_ID' => $row['timeout_id'],
                        'USER_ID' => $row['user_id'],
                        'USERNAME' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
                        'MOD_USERNAME' => get_username_string('full', $row['mod_user_id'], $row['mod_username'], $row['mod_user_colour']),
                        'TIMEOUT_REASON' => $reason,
                        'TIMEOUT_START' => $user->format_date($row['timeout_start']),
                        'TIMEOUT_END' => $user->format_date($row['timeout_end']),
                        'DURATION' => ($row['timeout_end'] - $row['timeout_start']) / 60, // in minuti
                        'STATUS' => ($is_active) ? $user->lang('TIMEOUT_ACTIVE') : $user->lang('TIMEOUT_EXPIRED'),
                        'IS_ACTIVE' => $is_active,
                        'TIMEOUT_STATUS' => $row['timeout_status'],
                        'U_DELETE' => $this->u_action . '&amp;action=delete&amp;timeout_id=' . $row['timeout_id'] . '&amp;hash=' . $this->generate_timeout_hash('timeout_delete_' . $row['timeout_id']),
                        'U_PURGE' => $this->u_action . '&amp;action=purge&amp;timeout_id=' . $row['timeout_id'] . '&amp;hash=' . $this->generate_timeout_hash('timeout_purge_' . $row['timeout_id']),
                    ]);
                    
                    // Aggiungi il blocco switch_timeout_active se il timeout è attivo
                    if ($is_active) {
                        $template->assign_block_vars('timeouts.switch_timeout_active', []);
                    }
                }
                $db->sql_freeresult($result);
                
                // Calcolo della pagina corrente per la visualizzazione
                $page_number = floor($start / $limit) + 1;
                
                $template->assign_vars([
                    'U_ACTION' => $this->u_action,
                    'PAGINATION' => $this->generate_simple_pagination($this->u_action . '&amp;status=' . $filter_status . '&amp;username=' . urlencode($filter_username), $total_count, $limit, $start),
                    'PAGE_NUMBER' => $page_number . ' / ' . ceil($total_count / $limit),
                    'TOTAL_TIMEOUTS' => $total_count,
                    'FILTER_STATUS' => $filter_status,
                    'FILTER_USERNAME' => $filter_username,
                ]);
                
                break;
        }
    }
}