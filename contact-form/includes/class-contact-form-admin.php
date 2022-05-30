<?php

if(is_admin())
{
    new Admin_Page_Adder();
}

/**
 * Admin_Page_Adder class will create the page to load the table
 */
class Admin_Page_Adder
{
    /**
     * Constructor will create the menu item
     */
    public function __construct()
    {
        add_action( 'admin_menu', array($this, 'add_menu_example_list_table_page' ));
    }

    /**
     * Menu item will allow us to load the page to display the table
     */
    public function add_menu_example_list_table_page()
    {
        add_menu_page( 'Contact Form', 'Contact Form', 'manage_options', 'contact-form-data', array($this, 'list_table_page'), 'dashicons-editor-table' );
    }

    /**
     * Display the list table page
     *
     * @return Void
     */
    public function list_table_page()
    {
        $contactFormListTable = new Contact_Form_List_Table();
        $contactFormListTable->prepare_items();
        ?>
            <div class="wrap">
                <form method="get">
                    <h2>Contact Form Data</h2>

                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />

                    <input type="text" name="email_search" 
                        value="<?php echo !empty($_REQUEST['email_search']) ? $_REQUEST['email_search'] : ''; ?>" 
                        placeholder="Search by Email" style="float: right; margin-bottom: 3px">
                    <input type="text" name="name_search" 
                        value="<?php echo !empty($_REQUEST['name_search']) ? $_REQUEST['name_search'] : ''; ?>" 
                        placeholder="Search by Name" style="float: right; margin-bottom: 3px">

                    <input type="submit" style="display: none">

                    <?php $contactFormListTable->display(); ?>
                </form>
            </div>
        <?php
    }
}

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class Contact_Form_List_Table extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->table_data();

        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'id'    => 'ID',
            'name'  => 'Name',
            'email' => 'Email',
            'date' => 'Date',
            'multiple_choice' => 'Multiple Choice',
            'file_name' => 'File',
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array(
            'name' => array('name', false),
            'email' => array('email', false),
            'id' => array('id', false)
        );
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        global $wpdb;
        $wpdb_table = $wpdb->prefix . 'contact_form_data';		
        $orderby = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : 'id';
        $order = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'ASC';

        $data_query = "SELECT id, name, email, date, multiple_choice, file_name
                        FROM $wpdb_table"; 
        
        // Search
        if( !empty( $_GET['name_search'] ) || !empty( $_GET['email_search'] ) ) {
            $data_query .= " WHERE";
            if( !empty( $_GET['name_search'] ) )
                $data_query .= " name like '%" . $_GET['name_search'] . "%'";
            if( !empty( $_GET['name_search'] ) && !empty( $_GET['email_search'] ) )
                $data_query .= " AND";
            if( !empty( $_GET['email_search'] ) )
                $data_query .= " email like '%" . $_GET['email_search'] . "%'";
        }

        //Order
        $data_query .= " ORDER BY $orderby $order";

        $query_results = $wpdb->get_results( $data_query, ARRAY_A  );
        
        return $query_results;	
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'id':
            case 'name':
            case 'email':
            case 'date':
            case 'multiple_choice':
                return $item[ $column_name ];
            case 'file_name':
                $file_name = $item[ $column_name ];
                return empty($file_name) ? "" : "<a href='/uploads/$file_name' download>download<a>";
            default:
                return print_r( $item, true ) ;
        }
    }
}
?>