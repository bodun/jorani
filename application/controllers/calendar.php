<?php
/**
 * This controller displays the calendars of the leave requests
 * @copyright  Copyright (c) 2014-2015 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }
 
/**
 * This class displays the calendars of the leave requests.
 * In opposition to the other pages of the application, some calendars can be public (no need to be logged in).
 */
class Calendar extends CI_Controller {
    
    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        parent::__construct();
        //This controller differs from the others, because some calendars can be public
    }

    /**
     * Display a yearly individual calendar
     * @param int $id identifier of the employee
     * @param int $year Year number
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function year($employee = 0, $year = 0) {
            setUserContext($this);
            $this->lang->load('calendar', $this->language);
            $this->auth->checkIfOperationIsAllowed('organization_calendar');
            $data = getUserContext($this);
            $this->load->model('users_model');
            $user = $this->users_model->getUsers($employee);
            if ($year==0) $year = date("Y");
            //Either self access, Manager or HR
            if ($employee == 0) {
                $employee = $this->user_id;
                $user = $this->users_model->getUsers($employee);
            } else {
                if (!$this->is_hr) {
                    if ($this->user_id != $user['manager']) {
                        $employee = $this->user_id;
                        $user = $this->users_model->getUsers($employee);
                    }
                }
            }
            
            $data['employee_name'] =  $user['firstname'] . ' ' . $user['lastname'];
            //Load the leaves for all the months of the selected year
            $this->load->model('leaves_model');
            $months = array(
                lang('January') => $this->leaves_model->linear($employee, 1, $year, TRUE, TRUE, TRUE, TRUE),
                lang('February') => $this->leaves_model->linear($employee, 2, $year, TRUE, TRUE, TRUE, TRUE),
                lang('March') => $this->leaves_model->linear($employee, 3, $year, TRUE, TRUE, TRUE, TRUE),
                lang('April') => $this->leaves_model->linear($employee, 4, $year, TRUE, TRUE, TRUE, TRUE),
                lang('May') => $this->leaves_model->linear($employee, 5, $year, TRUE, TRUE, TRUE, TRUE),
                lang('June') => $this->leaves_model->linear($employee, 6, $year, TRUE, TRUE, TRUE, TRUE),
                lang('July') => $this->leaves_model->linear($employee, 7, $year, TRUE, TRUE, TRUE, TRUE),
                lang('August') => $this->leaves_model->linear($employee, 8, $year, TRUE, TRUE, TRUE, TRUE),
                lang('September') => $this->leaves_model->linear($employee, 9, $year, TRUE, TRUE, TRUE, TRUE),
                lang('October') => $this->leaves_model->linear($employee, 10, $year, TRUE, TRUE, TRUE, TRUE),
                lang('November') => $this->leaves_model->linear($employee, 11, $year, TRUE, TRUE, TRUE, TRUE),
                lang('December') => $this->leaves_model->linear($employee, 12, $year, TRUE, TRUE, TRUE, TRUE),
            );
            $data['months'] = $months;
            $data['year'] = $year;
            $data['title'] = lang('calendar_year_title');
            $data['help'] = '';
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('calendar/year', $data);
            $this->load->view('templates/footer');
    }    
    
    /**
     * Display the page of the individual calendar (of the connected user)
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function individual() {
        setUserContext($this);
        $this->lang->load('calendar', $this->language);
        $this->auth->checkIfOperationIsAllowed('individual_calendar');
        $data = getUserContext($this);
        $data['title'] = lang('calendar_individual_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_individual');
        $data['googleApi'] = FALSE;
        $data['clientId'] = 'key';
        $data['apiKey'] = 'key';
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('calendar/individual', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display the page of the team calendar (users having the same manager
     * than the connected user)
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function workmates() {
        setUserContext($this);
        $this->lang->load('calendar', $this->language);
        $this->auth->checkIfOperationIsAllowed('workmates_calendar');
        $data = getUserContext($this);
        $data['title'] = lang('calendar_workmates_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_workmates');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('calendar/workmates', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Display the calendar of the employees managed by the connected user
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function collaborators() {
        setUserContext($this);
        $this->lang->load('calendar', $this->language);
        $this->auth->checkIfOperationIsAllowed('collaborators_calendar');
        $data = getUserContext($this);
        $data['title'] = lang('calendar_collaborators_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_collaborators');
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('calendar/collaborators', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Display the calendar of the employees working in the same department
     * than the connected user.
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function department() {
        setUserContext($this);
        $this->lang->load('calendar', $this->language);
        $this->auth->checkIfOperationIsAllowed('department_calendar');
        $data = getUserContext($this);
        $data['title'] = lang('calendar_department_title');
        $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_department');
        $this->load->model('organization_model');
        $department = $this->organization_model->getDepartment($this->user_id);
        if (empty($department)) {
            $this->session->set_flashdata('msg', lang('calendar_department_msg_error'));
            redirect('leaves');
        } else {
            $data['department'] = $department[0]['name'];
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('calendar/department', $data);
            $this->load->view('templates/footer');
        }
    }
    
    /**
     * Display a global calendar filtered by organization/entity
     * Data (calendar events) is retrieved by AJAX from leaves' controller
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function organization() {
        if (($this->config->item('public_calendar') == TRUE) && (!$this->session->userdata('logged_in'))) {
            $this->load->library('polyglot');;
            $data['language'] = $this->config->item('language');
            $data['language_code'] =  $this->polyglot->language2code($data['language']);
            $data['title'] = lang('calendar_organization_title');
            $data['help'] = '';
            $data['logged_in'] = FALSE;
            $this->lang->load('calendar', $data['language']);
            $this->load->view('templates/header', $data);
            $this->load->view('calendar/organization', $data);
            $this->load->view('templates/footer_simple');
        } else {
            setUserContext($this);
            $this->lang->load('calendar', $this->language);
            $this->auth->checkIfOperationIsAllowed('organization_calendar');
            $data = getUserContext($this);
            $data['logged_in'] = TRUE;
            $data['title'] = lang('calendar_organization_title');
            $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_organization');
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('calendar/organization', $data);
            $this->load->view('templates/footer');
        }
    }
    
    /**
     * Ajax endpoint : Send a list of fullcalendar events
     * This code is duplicated from controller/leaves for public access
     * @param int $entity_id Entity identifier
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function publicOrganization($entity_id) {
        header("Content-Type: application/json");
        if ($this->config->item('public_calendar') == TRUE) {
            $this->load->model('leaves_model');
            $start = $this->input->get('start', TRUE);
            $end = $this->input->get('end', TRUE);
            $children = filter_var($this->input->get('children', TRUE), FILTER_VALIDATE_BOOLEAN);
            echo $this->leaves_model->department($entity_id, $start, $end, $children);
        } else {
            echo 'Forbidden';
        }
    }
    
    /**
     * Ajax endpoint : Send a list of fullcalendar events: List of all possible day offs
     * This code is duplicated from controller/contract for public access
     * @param int $entity_id Entity identifier
     */
    public function publicDayoffs() {
        header("Content-Type: application/json");
        if ($this->config->item('public_calendar') == TRUE) {
            $start = $this->input->get('start', TRUE);
            $end = $this->input->get('end', TRUE);
            $entity = $this->input->get('entity', TRUE);
            $children = filter_var($this->input->get('children', TRUE), FILTER_VALIDATE_BOOLEAN);
            $this->load->model('dayoffs_model');
            echo $this->dayoffs_model->allDayoffs($start, $end, $entity, $children);
        } else {
            echo 'Forbidden';
        }
    }
    
    /**
     * Display a global tabular calendar
     * @param int $id identifier of the entity
     * @param int $month Month number
     * @param int $year Year number
     * @param bool $children If TRUE, includes children entity, FALSE otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function tabular($id=-1, $month=0, $year=0, $children=TRUE) {
        if (($this->config->item('public_calendar') == TRUE) && (!$this->session->userdata('logged_in'))) {
            $this->load->library('polyglot');;
            $data['language'] = $this->config->item('language');
            $data['language_code'] =  $this->polyglot->language2code($data['language']);
            $this->load->model('leaves_model');
            $this->load->model('organization_model');
            $data['tabular'] = $this->leaves_model->tabular($id, $month, $year, $children);
            $data['entity'] = $id;
            $data['month'] = $month;
            $data['year'] = $year;
            $data['children'] = $children;
            $data['department'] = $this->organization_model->getName($id);
            $data['title'] = lang('calendar_tabular_title');
            $data['help'] = '';
            $this->load->view('templates/header', $data);
            $this->load->view('calendar/tabular', $data);
            $this->load->view('templates/footer_simple');
        } else {
            setUserContext($this);
            $this->lang->load('calendar', $this->language);
            $this->auth->checkIfOperationIsAllowed('organization_calendar');
            $data = getUserContext($this);
            $this->load->model('leaves_model');
            $this->load->model('organization_model');
            $data['tabular'] = $this->leaves_model->tabular($id, $month, $year, $children);
            $data['entity'] = $id;
            $data['month'] = $month;
            $data['year'] = $year;
            $data['children'] = $children;
            $data['department'] = $this->organization_model->getName($id);
            $data['title'] = lang('calendar_tabular_title');
            $data['help'] = $this->help->create_help_link('global_link_doc_page_calendar_tabular');
            $this->load->view('templates/header', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('calendar/tabular', $data);
            $this->load->view('templates/footer');
        }
    }
    
    /**
     * Export the tabular calendar into Excel. The presentation differs a bit according to the limitation of Excel
     * We'll get one line for the morning and one line for the afternoon
     * @param int $id identifier of the entity
     * @param int $month Month number
     * @param int $year Year number
     * @param bool $children If TRUE, includes children entity, FALSE otherwise
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function exportTabular($id=-1, $month=0, $year=0, $children=TRUE) {        
        //Load the language file (the loaded language depends if it was called from the public view)
        if (($this->config->item('public_calendar') == TRUE) && (!$this->session->userdata('logged_in'))) {
            $this->load->library('polyglot');;
            $language = $this->config->item('language');
        } else {
            setUserContext($this);
            $language = $this->language;
        }
        $this->lang->load('calendar', $language);
        $this->lang->load('global', $language);
        $this->load->model('organization_model');
        $this->load->model('leaves_model');
        $this->load->library('excel');
        $data['id'] = $id;
        $data['month'] = $month;
        $data['year'] = $year;
        $data['children'] = $children;
        $this->load->view('calendar/export_tabular', $data);
    }
    
    /**
     * Export the yearly calendar into Excel. The presentation differs a bit according to the limitation of Excel
     * We'll get one line for the morning and one line for the afternoon
     * @param int $id identifier of the employee
     * @param int $year Year number
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function exportYear($employee = 0, $year = 0) {
        setUserContext($this);
        $this->lang->load('calendar', $this->language);
        $this->auth->checkIfOperationIsAllowed('organization_calendar');
        //Either self access, Manager or HR
        if ($employee == 0) {
            $employee = $this->user_id;
            $user = $this->users_model->getUsers($employee);
        } else {
            if (!$this->is_hr) {
                if ($this->user_id != $user['manager']) {
                    $employee = $this->user_id;
                    $user = $this->users_model->getUsers($employee);
                }
            }
        }
        if ($year == 0) {
            $year = date("Y");
        }
        $this->load->model('leaves_model');
        $this->load->model('users_model');
        $this->load->library('excel');
        $data['employee'] = $employee;
        $data['year'] = $year;
        $this->load->view('calendar/export_year', $data);
    }
}
