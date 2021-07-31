<?php

class yapt_list extends WP_List_Table
{
    public function __construct()
    {
        // Set parent defaults.
        parent::__construct([
            'singular' => __('Pricing Table', 'yapt'),
            'plural' => __('Pricing Tables', 'yapt'),
            'ajax' => false,
        ]);
    }

    /**
     * @param int $per_page
     * @param int $page_number
     * @return array|object|null
     */
    public function get_pricing_tables(int $per_page = 10, int $page_number = 1)
    {
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}yapt_pricing_tables";
        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        }
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;
        return $wpdb->get_results($sql, 'ARRAY_A');
    }

    /**
     * delete pricing table
     * @param int $id
     */
    public static function delete_price_table(int $id)
    {
        global $wpdb;
        $wpdb->delete(
            "{$wpdb->prefix}yapt_pricing_tables",
            ['id' => $id],
            ['%d']
        );
    }

    /**
     * Returns the count of records in the database.
     * @return string|null
     */
    public static function record_count(): ?string
    {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}yapt_pricing_tables";
        return $wpdb->get_var($sql);
    }

    /**
     * Text displayed when no tables is available
     */
    public function no_items()
    {
        __('No pricing table available.', 'yapt');
    }

    /**
     * Render a column when no column specific method exist.
     * @param array $item
     * @param string $column_name
     * @return mixed
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'template_id':
            case 'created_at':
            case 'updated_at':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     * @param array $item
     * @return string
     */
    function column_cb($item): string
    {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    /**
     * Method for name column
     * @param array $item
     * @return string
     */
    function column_name(array $item): string
    {
        $delete_nonce = wp_create_nonce('ya_delete_price_table');
        $title = '<strong>' . $item['name'] . '</strong>';
        $actions = [
            'delete' => sprintf('<a href="?page=%s&action=%s&price_table=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['ID']), $delete_nonce)
        ];
        return $title . $this->row_actions($actions);
    }

    /**
     * @return array
     */
    public function get_columns(): array
    {
        return [
            'cb' => '<input type="checkbox" />', // Render a checkbox instead of text.
            'pt_title' => __('Pricing table title', 'yapt'),
            'template_id' => __('Template id', 'yapt'),
            'created_at' => __('Created date', 'yapt'),
            'updated_at' => __('Updated date', 'yapt'),
        ];
    }

    /**
     * Columns to make sortable
     * @return array
     */
    public function get_sortable_columns(): array
    {
        return [
            'pt_title' => ['pt_title', true],
            'created_at' => ['created_at', true],
            'updated_at' => ['updated_at', true]
        ];
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions(): array
    {
        return [
            'bulk-delete' => 'Delete'
        ];
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items()
    {
        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page = $this->get_items_per_page('tables_per_page', 10);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        $this->set_pagination_args([
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page //WE have to determine how many items to show on a page
        ]);

        $this->items = self::get_pricing_tables($per_page, $current_page);
    }

    public function process_bulk_action()
    {
        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {
            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);

            if (!wp_verify_nonce($nonce, 'ya_delete_price_table')) {
                die('Go get a life script kiddies');
            } else {
                self::delete_price_table(absint($_GET['price_table']));

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url
                wp_redirect(esc_url_raw(add_query_arg()));
                exit;
            }
        }

        // If the delete bulk action is triggered
        if ((isset($_POST['action']) && $_POST['action'] == 'bulk-delete')
            || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete')
        ) {
            $delete_ids = esc_sql($_POST['bulk-delete']);
            // loop over the array of record IDs and delete them
            foreach ($delete_ids as $id) {
                self::delete_price_table($id);
            }
            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            wp_redirect(esc_url_raw(add_query_arg()));
            exit;
        }
    }
}