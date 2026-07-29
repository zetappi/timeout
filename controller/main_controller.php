<?php
namespace marcozp\timeout\controller;

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\request\request_interface;
use phpbb\template\template;
use phpbb\user;
use phpbb\auth\auth;
use phpbb\controller\helper;

class main_controller
{
    protected $config;
    protected $db;
    protected $request;
    protected $template;
    protected $user;
    protected $auth;
    protected $helper;

    public function __construct(
        config $config,
        driver_interface $db,
        request_interface $request,
        template $template,
        user $user,
        auth $auth,
        helper $helper
    ) {
        $this->config = $config;
        $this->db = $db;
        $this->request = $request;
        $this->template = $template;
        $this->user = $user;
        $this->auth = $auth;
        $this->helper = $helper;
    }

    /**
     * Mostra le informazioni di timeout per un utente specifico
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function main()
    {
        // Controlla se l'estensione è abilitata
        if (!$this->config['timeout_enable'])
        {
            return $this->helper->error($this->user->lang('TIMEOUT_DISABLED'), 403);
        }
    }
    
    /**
     * Reindirizza al modulo MCP per il timeout di un utente
     *
     * @param int $user_id ID dell'utente da mettere in timeout
     * @param int $post_id ID del post
     * @param int $topic_id ID del topic
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function timeout_user($user_id, $post_id, $topic_id)
    {
        // Verifica che l'utente abbia i permessi necessari
        if (!$this->auth->acl_get('m_timeout'))
        {
            return $this->helper->error($this->user->lang('NO_AUTH_OPERATION'), 403);
        }
        
        // Verifica che l'utente non stia cercando di mettere in timeout se stesso
        if ($user_id == $this->user->data['user_id'])
        {
            return $this->helper->error($this->user->lang('CANNOT_TIMEOUT_SELF'), 403);
        }
        
        global $phpbb_root_path, $phpEx;
        
        // Costruisci l'URL per il modulo MCP
        $mcp_url = append_sid("{$phpbb_root_path}mcp.{$phpEx}", "i=main&mode=main&user_id={$user_id}&post_id={$post_id}&topic_id={$topic_id}");
        
        // Reindirizza al modulo MCP
        redirect($mcp_url);
        
        // Ottieni l'ID dell'utente
        $user_id = $this->request->variable('user_id', 0);
        if (!$user_id)
        {
            return $this->helper->error($this->user->lang('TIMEOUT_USER_NOT_FOUND'), 404);
        }
        
        // Se l'utente non è un moderatore, verifica che stia cercando le proprie informazioni di timeout
        if (!$this->auth->acl_get('m_timeout') && $user_id != $this->user->data['user_id'])
        {
            return $this->helper->error($this->user->lang('NOT_AUTHORISED'), 403);
        }
        
        // Ottieni i dati dell'utente
        $sql = 'SELECT username, user_colour FROM ' . USERS_TABLE . ' WHERE user_id = ' . (int) $user_id;
        $result = $this->db->sql_query($sql);
        $user_row = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);
        
        if (!$user_row)
        {
            return $this->helper->error($this->user->lang('TIMEOUT_USER_NOT_FOUND'), 404);
        }
        
        // Ottieni i dati di timeout attivo
        $sql = 'SELECT * FROM ' . $this->db->get_table_name('user_timeouts') . '
                WHERE user_id = ' . (int) $user_id . '
                AND timeout_end > ' . time() . '
                AND timeout_status = 1
                ORDER BY timeout_end DESC';
        $result = $this->db->sql_query_limit($sql, 1);
        $active_timeout = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);
        
        // Imposta le variabili per il template
        $this->template->assign_vars([
            'S_IS_SELF' => ($user_id == $this->user->data['user_id']),
            'S_CAN_TIMEOUT' => $this->auth->acl_get('m_timeout'),
            'S_IS_IN_TIMEOUT' => !empty($active_timeout),
            'USERNAME' => get_username_string('full', $user_id, $user_row['username'], $user_row['user_colour']),
            'USER_ID' => $user_id,
            'TIMEOUT_START' => !empty($active_timeout) ? $this->user->format_date($active_timeout['timeout_start']) : '',
            'TIMEOUT_END' => !empty($active_timeout) ? $this->user->format_date($active_timeout['timeout_end']) : '',
            'TIMEOUT_REASON' => !empty($active_timeout) ? $active_timeout['timeout_reason'] : '',
            'TIMEOUT_REMAINING' => !empty($active_timeout) ? human_time((int) $active_timeout['timeout_end'] - time()) : '',
        ]);
        
        // Ottieni la cronologia dei timeout per questo utente
        $sql = 'SELECT t.*, u.username, u.user_colour
                FROM ' . $this->db->get_table_name('user_timeouts') . ' t
                LEFT JOIN ' . USERS_TABLE . ' u ON t.mod_user_id = u.user_id
                WHERE t.user_id = ' . (int) $user_id . '
                ORDER BY t.timeout_start DESC';
        $result = $this->db->sql_query_limit($sql, 10);
        
        $i = 0;
        while ($row = $this->db->sql_fetchrow($result))
        {
            $this->template->assign_block_vars('timeout_history', [
                'MOD_USERNAME' => get_username_string('full', $row['mod_user_id'], $row['username'], $row['user_colour']),
                'START_DATE' => $this->user->format_date($row['timeout_start']),
                'END_DATE' => $this->user->format_date($row['timeout_end']),
                'REASON' => $row['timeout_reason'],
                'POST_URL' => ($row['post_id'] > 0) ? append_sid('viewtopic.php', 'p=' . $row['post_id'] . '#p' . $row['post_id']) : '',
                'IS_ACTIVE' => ($row['timeout_end'] > time() && $row['timeout_status'] == 1),
                'STATUS' => ($row['timeout_end'] > time() && $row['timeout_status'] == 1) ? $this->user->lang('TIMEOUT_STATUS_ACTIVE') : $this->user->lang('TIMEOUT_STATUS_INACTIVE'),
                'ROW_CLASS' => ($i++ % 2) ? 'bg1' : 'bg2',
            ]);
        }
        $this->db->sql_freeresult($result);
        
        // Carica il file di lingua necessario
        $this->user->add_lang_ext('marcozp/timeout', 'common');
        
        // Imposta il titolo della pagina
        $page_title = $this->user->lang('TIMEOUT_USER_INFO', $user_row['username']);
        
        return $this->helper->render('timeout_user_info.html', $page_title);
    }
    
    /**
     * Controller per verificare se un utente è in timeout
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function check()
    {
        // Verifica se la richiesta è ajax
        if (!$this->request->is_ajax())
        {
            return $this->helper->error($this->user->lang('NOT_AUTHORISED'), 403);
        }
        
        // Verifica se la funzionalità è abilitata
        if (!$this->config['timeout_enable'])
        {
            return new \phpbb\controller\json_response(['status' => false, 'message' => $this->user->lang('TIMEOUT_DISABLED')]);
        }
        
        // Ottieni l'utente corrente
        $user_id = $this->user->data['user_id'];
        
        if ($user_id == ANONYMOUS)
        {
            return new \phpbb\controller\json_response(['status' => false, 'in_timeout' => false]);
        }
        
        // Verifica se l'utente è in timeout
        $sql = 'SELECT * FROM ' . $this->db->get_table_name('user_timeouts') . '
                WHERE user_id = ' . (int) $user_id . '
                AND timeout_end > ' . time() . '
                AND timeout_status = 1';
        $result = $this->db->sql_query_limit($sql, 1);
        $timeout_data = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);
        
        $is_in_timeout = !empty($timeout_data);
        
        // Costruisci la risposta JSON
        $response_data = [
            'status' => true,
            'in_timeout' => $is_in_timeout,
        ];
        
        if ($is_in_timeout)
        {
            $response_data['timeout'] = [
                'end_time' => (int) $timeout_data['timeout_end'],
                'end_time_formatted' => $this->user->format_date($timeout_data['timeout_end']),
                'remaining_time' => (int) $timeout_data['timeout_end'] - time(),
                'remaining_time_formatted' => human_time((int) $timeout_data['timeout_end'] - time()),
                'reason' => $timeout_data['timeout_reason'],
            ];
        }
        
        return new \phpbb\controller\json_response($response_data);
    }
}