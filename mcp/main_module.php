<?php

namespace marcozp\timeout\mcp;

// Inclusione delle funzioni di paginazione e altre funzioni comuni di phpBB
if (!defined('IN_PHPBB')) {
    exit;
}

class main_module
{
    public $u_action;
    public $tpl_name;
    public $page_title;

    public function main($id, $mode)
    {
        global $phpbb_container, $phpbb_root_path, $phpEx, $user, $template, $request, $db, $table_prefix, $auth;
        
        // Inclusione esplicita delle funzioni di paginazione
        if (!function_exists('\generate_pagination'))
        {
            include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
        }

        $this->page_title = $user->lang('MCP_TIMEOUT_TITLE');
        
        // Carica il file di lingua
        $user->add_lang_ext('marcozp/timeout', 'timeout_mcp');
        $user->add_lang_ext('marcozp/timeout', 'common');
        
        // Ottieni il controller helper per la costruzione degli URL
        $helper = $phpbb_container->get('controller.helper');
        
        $action = $request->variable('action', '');
        $username = $request->variable('username', '', true);
        $user_id = $request->variable('user_id', 0);
        $post_id = $request->variable('post_id', 0);
        $topic_id = $request->variable('topic_id', 0);
        $timeout_id = $request->variable('timeout_id', 0);
        
        switch ($mode) {
            case 'main':
                $this->tpl_name = 'mcp_timeout_main';
                $this->page_title = $user->lang('MCP_TIMEOUT_MAIN');

                // Precompila il motivo se si arriva da un post
                $reason_default = '';
                if ($post_id > 0 && !$request->is_set_post('submit_timeout')) {
                    $reason_default = 'Questa notifica ti avverte che hai ricevuto un timeout a causa del post (ID: ' . $post_id . ').';
                }
                $template->assign_var('REASON', $reason_default);
                
                // Se è stato inviato il form di timeout
                if ($request->is_set_post('submit_timeout')) {
                    // Verifica il form token per la sicurezza
                    if (!check_form_key('mcp_timeout')) {
                        trigger_error('FORM_INVALID');
                    }
                    
                    // Durata del timeout in minuti, uso il valore di suggested_duration
                    $duration_minutes = $request->variable('suggested_duration', 0);
                    $reason = $request->variable('timeout_reason', '', true); // La motivazione è facoltativa, può essere vuota
                    $notify_user = $request->variable('notify_user', false);
                    
                    // Ottieni la durata massima dalle impostazioni ACP
                    $max_duration = isset($phpbb_container->get('config')['timeout_max_duration']) ? 
                                    (int)$phpbb_container->get('config')['timeout_max_duration'] : 1440; // 24 ore default
                    
                    if ($duration_minutes <= 0) {
                        trigger_error($user->lang('TIMEOUT_DURATION_INVALID'));
                    }
                    
                    // Verifica se la durata supera il massimo consentito
                    if ($duration_minutes > $max_duration) {
                        trigger_error($user->lang('TIMEOUT_MAX_DURATION_EXCEEDED', $max_duration));
                    }
                    
                    if (empty($username) && $user_id <= 0) {
                        trigger_error($user->lang('TIMEOUT_USER_NOT_SPECIFIED'));
                    }
                    
                    // Se è stato specificato solo il nome utente, ottieni l'ID
                    if (empty($user_id) && !empty($username)) {
                        $sql = 'SELECT user_id FROM ' . USERS_TABLE . "
                                WHERE username_clean = '" . $db->sql_escape(utf8_clean_string($username)) . "'";
                        $result = $db->sql_query($sql);
                        $user_id = (int) $db->sql_fetchfield('user_id');
                        $db->sql_freeresult($result);
                        
                        if ($user_id <= 0) {
                            trigger_error($user->lang('TIMEOUT_USER_NOT_FOUND'));
                        }
                    }
                    
                    // Controlla se l'utente è già in timeout
                    $sql = 'SELECT * FROM ' . $table_prefix . 'user_timeouts
                            WHERE user_id = ' . (int) $user_id . '
                            AND timeout_end > ' . time();
                    $result = $db->sql_query($sql);
                    $existing_timeout = $db->sql_fetchrow($result);
                    $db->sql_freeresult($result);
                    
                    if ($existing_timeout) {
                        trigger_error($user->lang('TIMEOUT_USER_ALREADY_IN_TIMEOUT'));
                    }
                    
                    // Calcola il tempo di fine timeout
                    $start_time = time();
                    $end_time = $start_time + ($duration_minutes * 60);
                    
                    // Inserisci il nuovo timeout
                    $sql_ary = [
                        'user_id' => (int) $user_id,
                        'mod_user_id' => (int) $user->data['user_id'],
                        'timeout_start' => $start_time,
                        'timeout_end' => $end_time,
                        'timeout_reason' => $reason,
                        'timeout_status' => 1, // 1 = attivo
                        'post_id' => $post_id, // Aggiungi questa linea
                        'topic_id' => $topic_id,
                    ];
                    
                    $sql = 'INSERT INTO ' . $table_prefix . 'user_timeouts ' . $db->sql_build_array('INSERT', $sql_ary);
                    $db->sql_query($sql);
                    $timeout_id = $db->sql_nextid();
                    
                    // Aggiorna i campi dell'utente per applicare il timeout
                    $sql = 'UPDATE ' . USERS_TABLE . '
                            SET user_timeout_start = ' . $start_time . ',
                                user_timeout_end = ' . $end_time . '
                            WHERE user_id = ' . (int) $user_id;
                    $db->sql_query($sql);
                    
                    // Aggiorna la sessione dell'utente se è online
                    $sql = 'UPDATE ' . SESSIONS_TABLE . '
                            SET session_timeout_start = ' . $start_time . ',
                                session_timeout_end = ' . $end_time . '
                            WHERE session_user_id = ' . (int) $user_id;
                    $db->sql_query($sql);
                    
                    // Invia una notifica all'utente se richiesto
                    if ($notify_user) {
                        // Ottieni il notification manager
                        $notification_manager = $phpbb_container->get('notification_manager');
                        
                        // Prepara i dati della notifica
                        $notification_data = [
                            'timeout_id'     => $timeout_id,
                            'user_id'       => $user_id,
                            'mod_user_id'   => $user->data['user_id'],
                            'timeout_reason'=> $reason,
                            'timeout_end'   => $end_time,
                        ];
                        
                        // Invia la notifica
                        $notification_manager->add_notifications('marcozp.timeout.notification.type.timeout', $notification_data);
                    }
                    
                    // Aggiungi un log per l'azione di timeout
                    $log = $phpbb_container->get('log');
                    $log->add('mod', $user->data['user_id'], $user->ip, 'Timeout aggiunto a ' . $username . ' fino a ' . $user->format_date($end_time) . ($reason ? ' [Motivo: ' . $reason . ']' : ''), false, []);
                    
                    $message = $user->lang('TIMEOUT_ADDED_SUCCESS', $username);
                    $message .= '<br><br>' . $user->lang('RETURN_PAGE', '<a href="' . $this->u_action . '">', '</a>');
                    trigger_error($message);
                }
                
                add_form_key('mcp_timeout');
                
                // Se abbiamo l'ID utente ma non il nome utente, otteniamo il nome utente
                if ($user_id > 0 && empty($username)) {
                    $sql = 'SELECT username FROM ' . USERS_TABLE . ' WHERE user_id = ' . (int) $user_id;
                    $result = $db->sql_query($sql);
                    $username = $db->sql_fetchfield('username');
                    $db->sql_freeresult($result);
                }
                
                // Ottieni la durata massima impostata nell'ACP
                $max_duration = isset($phpbb_container->get('config')['timeout_max_duration']) ? 
                                (int)$phpbb_container->get('config')['timeout_max_duration'] : 1440; // 24 ore default
                
                // Imposta la durata predefinita a 60 minuti come richiesto
                $default_duration = 60;
                
                // Ottieni la cronologia ban/warning
                $listener = $phpbb_container->get('marcozp.timeout.event.listener');
                $history = $listener->get_user_history_from_log($user_id);
                
                // Calcola indice di rischio
                $risk_index = $listener->calculate_disciplinary_index(
                    $history['ban_count'],
                    $history['warning_count'],
                    $history['timeout_count']
                );

                // Calcola il colore in base al rischio
                $risk_color = '#4CAF50';
                if ($risk_index <= 4) {
                    $risk_color = '#CCCCCC';
                } elseif ($risk_index <= 20) {
                    $risk_color = '#4CAF50';
                } elseif ($risk_index <= 40) {
                    $risk_color = '#FFC107';
                } elseif ($risk_index <= 60) {
                    $risk_color = '#FF9800';
                } elseif ($risk_index <= 85) {
                    $risk_color = '#F44336';
                } else {
                    $risk_color = '#9C27B0';
                }

                // DEBUG: Verifica template
                error_log("Active template: " . $this->tpl_name);
                
                // Output diretto come fallback
                if (empty($this->tpl_name)) {
                    echo '<div class="panel"><div class="inner">';
                    echo '<h3>DEBUG: User History</h3>';
                    echo '<p>Bans: ' . $history['ban_count'] . '</p>';
                    echo '<p>Warnings: ' . $history['warning_count'] . '</p>';
                    echo '</div></div>';
                }
                
                // Assegnazione standard al template
                $template->assign_vars([
                    'BAN_COUNT' => $history['ban_count'],
                    'WARNING_COUNT' => $history['warning_count'],
                    'TIMEOUT_COUNT' => $history['timeout_count'] ?? 0, // Aggiunto questa linea
                    'NEW_RISK_INDEX' => $risk_index,
                    'RISK_COLOR' => $risk_color,
                    'L_RISK_INDEX' => $user->lang('RISK_INDEX'),
                    'SHOW_HISTORY' => true,
                    'L_BAN_HISTORY' => $user->lang('BAN_HISTORY'),
                    'L_WARNING_HISTORY' => $user->lang('WARNING_HISTORY')
                ]);
                
                $template->assign_vars([
                    'U_ACTION' => $this->u_action,
                    'U_VIEW_POST' => append_sid("{$phpbb_root_path}viewtopic.{$phpEx}"),
                    'USERNAME' => $username,
                    'USER_ID' => $user_id,
                    'S_IS_POST' => ($post_id > 0),
                    'POST_ID' => $post_id,
                    'TOPIC_ID' => $topic_id,
                    'DURATION' => $default_duration, // Imposta la durata predefinita a 60 minuti
                    'MAX_DURATION' => $max_duration, // Passa il valore massimo al template
                    'S_TIMEOUT_USERNAME_READONLY' => ($user_id > 0), // Rende il campo username in sola lettura se abbiamo l'ID
                ]);
                
                break;
                
            case 'active':
                $this->tpl_name = 'mcp_timeout_active';
                $this->page_title = $user->lang('MCP_TIMEOUT_ACTIVE');
                
                // Rimozione di un timeout attivo
                if ($action === 'remove' && $timeout_id > 0) {
                    // Verifica il token di sicurezza
                    if (!check_link_hash($request->variable('hash', ''), 'timeout_remove_' . $timeout_id)) {
                        trigger_error($user->lang('FORM_INVALID'));
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
                        // 1. Rimuovi il flag di timeout dal campo user_options
                        $sql = 'UPDATE ' . USERS_TABLE . '
                                SET user_timeout_end = 0,
                                    user_timeout_start = 0
                                WHERE user_id = ' . $timeout_user_id;
                        $db->sql_query($sql);
                        
                        // 2. Aggiorna la sessione dell'utente se è online
                        $sql = 'UPDATE ' . SESSIONS_TABLE . '
                                SET session_timeout_end = 0,
                                    session_timeout_start = 0
                                WHERE session_user_id = ' . $timeout_user_id;
                        $db->sql_query($sql);
                        
                        $message = $user->lang('TIMEOUT_REMOVED_SUCCESS');
                        $message .= '<br><br>' . $user->lang('RETURN_PAGE', '<a href="' . $this->u_action . '">', '</a>');
                        trigger_error($message);
                    } else {
                        trigger_error($user->lang('TIMEOUT_NOT_FOUND'));
                    }
                }
                
                $sql = 'SELECT t.*, u.username, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, 
                                m.username AS mod_username, m.user_colour AS mod_user_colour, p.post_subject, p.topic_id
                        FROM ' . $table_prefix . 'user_timeouts t
                        LEFT JOIN ' . USERS_TABLE . ' u ON (u.user_id = t.user_id)
                        LEFT JOIN ' . USERS_TABLE . ' m ON (m.user_id = t.mod_user_id)
                        LEFT JOIN ' . POSTS_TABLE . ' p ON (p.post_id = t.post_id)
                        WHERE t.timeout_end > ' . time() . '
                        AND t.timeout_status = 1
                        ORDER BY t.timeout_end DESC';
                $result = $db->sql_query($sql);
                
                while ($row = $db->sql_fetchrow($result)) {
                    $template->assign_block_vars('timeouts', [
                        'USER_ID' => $row['user_id'],
                        'USERNAME' => \get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
                        'MOD_USERNAME' => \get_username_string('full', $row['mod_user_id'], $row['mod_username'], $row['mod_user_colour']),
                        'REASON' => $row['timeout_reason'],
                        'START_TIME' => $user->format_date($row['timeout_start']),
                        'END_TIME' => $user->format_date($row['timeout_end']),
                        'DURATION' => (int)round(($row['timeout_end'] - $row['timeout_start']) / 60), // in minuti (arrotondato)
                        'REMAINING' => (int)round(($row['timeout_end'] - time()) / 60), // in minuti (arrotondato)
                        'POST_SUBJECT' => $row['post_subject'],
                        'TOPIC_ID' => $row['topic_id'],
                        'POST_ID' => $row['post_id'],
                        'U_REMOVE' => $this->u_action . '&amp;action=remove&amp;timeout_id=' . $row['timeout_id'] . '&amp;hash=' . \generate_link_hash('timeout_remove_' . $row['timeout_id']),
                    ]);
                }
                $db->sql_freeresult($result);
                
                // Conteggio timeout per determinare se ci sono timeout da mostrare
                $sql = 'SELECT COUNT(*) as count FROM ' . $table_prefix . 'user_timeouts 
                        WHERE timeout_end > ' . time() . ' AND timeout_status = 1';
                $result = $db->sql_query($sql);
                $timeout_count = (int) $db->sql_fetchfield('count');
                $db->sql_freeresult($result);
                
                $template->assign_vars([
                    'U_ACTION' => $this->u_action,
                    'S_TIMEOUTS' => ($timeout_count > 0),
                ]);
                
                break;
                
            case 'history':
                $this->tpl_name = 'mcp_timeout_history';
                $this->page_title = $user->lang('MCP_TIMEOUT_HISTORY');
                
                $start = $request->variable('start', 0);
                $limit = 25; // Numero di record per pagina
                
                // Conteggio totale per la paginazione
                $sql = 'SELECT COUNT(*) as total_count FROM ' . $table_prefix . 'user_timeouts';
                $result = $db->sql_query($sql);
                $total_count = (int) $db->sql_fetchfield('total_count');
                $db->sql_freeresult($result);
                
                // Query principale
                $sql = 'SELECT t.*, u.username, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, 
                                m.username AS mod_username, m.user_colour AS mod_user_colour, p.post_subject, p.topic_id
                        FROM ' . $table_prefix . 'user_timeouts t
                        LEFT JOIN ' . USERS_TABLE . ' u ON (u.user_id = t.user_id)
                        LEFT JOIN ' . USERS_TABLE . ' m ON (m.user_id = t.mod_user_id)
                        LEFT JOIN ' . POSTS_TABLE . ' p ON (p.post_id = t.post_id)
                        ORDER BY t.timeout_start DESC';
                $result = $db->sql_query_limit($sql, $limit, $start);
                
                while ($row = $db->sql_fetchrow($result)) {
                    $template->assign_block_vars('timeouts', [
                        'USER_ID' => $row['user_id'],
                        'USERNAME' => \get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
                        'MOD_USERNAME' => \get_username_string('full', $row['mod_user_id'], $row['mod_username'], $row['mod_user_colour']),
                        'REASON' => $row['timeout_reason'],
                        'START_TIME' => $user->format_date($row['timeout_start']),
                        'END_TIME' => $user->format_date($row['timeout_end']),
                        'DURATION' => (int)round(($row['timeout_end'] - $row['timeout_start']) / 60), // in minuti (arrotondato)
                        'STATUS' => ($row['timeout_end'] > time() && $row['timeout_status'] == 1) ? $user->lang('TIMEOUT_ACTIVE') : $user->lang('TIMEOUT_EXPIRED'),
                        'IS_ACTIVE' => ($row['timeout_end'] > time() && $row['timeout_status'] == 1),
                        'POST_SUBJECT' => $row['post_subject'],
                        'TOPIC_ID' => $row['topic_id'],
                        'POST_ID' => $row['post_id'],
                        'U_REMOVE' => ($row['timeout_end'] > time() && $row['timeout_status'] == 1) ? $this->u_action . '&amp;mode=active&amp;action=remove&amp;timeout_id=' . $row['timeout_id'] . '&amp;hash=' . \generate_link_hash('timeout_remove_' . $row['timeout_id']) : '',
                    ]);
                }
                $db->sql_freeresult($result);
                
                // Creazione manuale della paginazione
                $pagination = '';
                $num_pages = ceil($total_count / $limit);
                
                if ($num_pages > 1) {
                    for ($i = 0; $i < $num_pages; $i++) {
                        $page_start = $i * $limit;
                        $page_num = $i + 1;
                        
                        if ($page_start == $start) {
                            $pagination .= '<strong>' . $page_num . '</strong> ';
                        } else {
                            $pagination .= '<a href="' . $this->u_action . '&amp;start=' . $page_start . '">' . $page_num . '</a> ';
                        }
                    }
                }
                
                // Calcolo della pagina corrente
                $current_page = floor($start / $limit) + 1;
                $page_info = $current_page . ' di ' . $num_pages;
                
                $template->assign_vars([
                    'U_ACTION' => $this->u_action,
                    'PAGINATION' => $pagination,
                    'PAGE_NUMBER' => $page_info,
                    'TOTAL_TIMEOUTS' => $total_count,
                    'S_TIMEOUTS' => ($total_count > 0),
                ]);
                
                break;
        }
    }
}
