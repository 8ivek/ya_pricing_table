<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/8ivek/yapt
 * @since      1.0.0
 *
 * @package    Yapt
 * @subpackage Yapt/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Yapt
 * @subpackage Yapt/includes
 */
class Yapt_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        global $wpdb;
        if (function_exists('is_multisite') && is_multisite()) {
            // check if it is a network activation - if so, run the activation function for each blog id
            $old_blog = $wpdb->blogid;
            // Get all blog ids
            $blogids = $wpdb->get_col("SELECT `blog_id` FROM $wpdb->blogs");
            foreach ($blogids as $blog_id) {
                switch_to_blog($blog_id);
                // create tables
                (new Yapt_Activator)->create_tables();
            }
            switch_to_blog($old_blog);
        } else {
            // create tables
            (new Yapt_Activator)->create_tables();
        }
    }

    /**
     * Create tables when activating plugin
     * @throws Exception
     */
    private function create_tables()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        global $wpdb;

        // yapt_templates
        $table_name = $wpdb->prefix . 'yapt_templates';
        $charset_collate = $wpdb->get_charset_collate();
        if ($wpdb->get_var("show tables like '{$table_name}'") != $table_name) {
            $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
             `id` INT(11) NOT NULL AUTO_INCREMENT,
             `template_name` VARCHAR(255) NOT NULL,
             `style` VARCHAR(255) NOT NULL,
             `html` VARCHAR(255) NOT NULL,
             `image` VARCHAR(255) NOT NULL,
			 `created_at` DATETIME NOT NULL,
			 `updated_at` DATETIME NOT NULL,
			  PRIMARY KEY id (id)
            )$charset_collate;";
                dbDelta($sql);

            // insert_query
            $table_name = $wpdb->prefix . 'yapt_templates';
            $datetime = new DateTime('now', new DateTimeZone('UTC'));
            $now = $datetime->format('Y-m-d H:i:s');
            $wpdb->insert($table_name, ['template_name' => 'default', 'style' => 'default.css', 'html' => 'default.html', 'image' => 'default.jpeg', 'created_at' => $now, 'updated_at' => $now]);
            $wpdb->insert($table_name, ['template_name' => 'yapt2021', 'style' => 'yapt2021.css', 'html' => 'yapt2021.html', 'image' => 'yapt2021.png', 'created_at' => $now, 'updated_at' => $now]);
            $wpdb->insert($table_name, ['template_name' => 'aanya', 'style' => 'default.css', 'html' => 'default.html', 'image' => 'default.png', 'created_at' => $now, 'updated_at' => $now]);
        }

        // yapt_pricing_tables
        $table_name = $wpdb->prefix . 'yapt_pricing_tables';
        $charset_collate = $wpdb->get_charset_collate();
        if ($wpdb->get_var("show tables like '{$table_name}'") != $table_name) {
            $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
             `id` INT(11) NOT NULL AUTO_INCREMENT,
             `pt_title` VARCHAR(255) NOT NULL,
             `custom_styles` TEXT NULL,
             `template_id` INT(11) NOT NULL,
			 `created_at` DATETIME NOT NULL,
			 `updated_at` DATETIME NOT NULL,
			  PRIMARY KEY id (id),
			  FOREIGN KEY(template_id) 
                REFERENCES " . $wpdb->prefix . "yapt_templates (id)
            )$charset_collate;";
                dbDelta($sql);
        }

        // yapt_columns
        $table_name = $wpdb->prefix . 'yapt_columns';
        $charset_collate = $wpdb->get_charset_collate();
        if ($wpdb->get_var("show tables like '{$table_name}'") != $table_name) {
            $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
             `id` INT(11) NOT NULL AUTO_INCREMENT,
             `column_title` VARCHAR(255) NOT NULL,
             `highlighted` ENUM('0', '1') NOT NULL DEFAULT '0',
             `description` TEXT NULL,
             `table_id` INT(11) NOT NULL,
             `price_currency` VARCHAR(255) NOT NULL,
             `price` VARCHAR(255) NOT NULL,
             `price_suffix` VARCHAR(255) NOT NULL,
             `ctoa_btn_text` VARCHAR(255) NOT NULL,/** ctoa => call to action */
             `ctoa_btn_link` VARCHAR(255) NOT NULL,
			 `created_at` DATETIME NOT NULL,
			 `updated_at` DATETIME NOT NULL,
			  PRIMARY KEY id (id),
			  FOREIGN KEY(table_id) 
                REFERENCES " . $wpdb->prefix . "yapt_pricing_tables (id)
                ON DELETE CASCADE
		    )$charset_collate;";
            dbDelta($sql);
        }

        // yapt_features
        $table_name = $wpdb->prefix . 'yapt_features';
        $charset_collate = $wpdb->get_charset_collate();
        if ($wpdb->get_var("show tables like '{$table_name}'") != $table_name) {
            $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
             `id` INT(11) NOT NULL AUTO_INCREMENT,
             `column_id` INT(11) NOT NULL,
             `feature_text` VARCHAR(255) NOT NULL,
             `is_set` ENUM('0', '1') NOT NULL DEFAULT '1',
             `sort_value` VARCHAR(255) NOT NULL,
			 `created_at` DATETIME NOT NULL,
			 `updated_at` DATETIME NOT NULL,
			  PRIMARY KEY id (id),
			  FOREIGN KEY(column_id) 
                REFERENCES " . $wpdb->prefix . "yapt_columns (id)
                ON DELETE CASCADE
            )$charset_collate;";
                dbDelta($sql);
        }

        // yapt_features
        $table_name = $wpdb->prefix . 'yapt_currency';
        $charset_collate = $wpdb->get_charset_collate();
        if ($wpdb->get_var("show tables like '{$table_name}'") != $table_name) {
            $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
                    `country`  VARCHAR(100) NOT NULL,
                    `currency` VARCHAR(100),
                    `code`     VARCHAR(100),
                    `symbol`   VARCHAR(100),
                    PRIMARY KEY (`country`),
                    UNIQUE INDEX `country_UNIQUE` (`country` ASC)
                )$charset_collate;";
            dbDelta($sql);

            $sql_insert = <<<CUR
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Albania', 'Leke', 'ALL', 'Lek');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Afghanistan', 'Afghanis', 'AFN', '؋');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Argentina', 'Pesos', 'ARS', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Aruba', 'Guilders', 'AWG', 'ƒ');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Australia', 'Dollars', 'AUD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Azerbaijan', 'New Manats', 'AZN', 'ман');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Bahamas', 'Dollars', 'BSD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Barbados', 'Dollars', 'BBD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Belarus', 'Rubles', 'BYR', 'p.');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Belgium', 'Euro', 'EUR', '€');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Beliz', 'Dollars', 'BZD', 'BZ$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Bermuda', 'Dollars', 'BMD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Bolivia', 'Bolivianos', 'BOB', '\$b');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Bosnia and Herzegovina', 'Convertible Marka', 'BAM', 'KM');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Botswana', 'Pula', 'BWP', 'P');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Bulgaria', 'Leva', 'BGN', 'лв');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Brazil', 'Reais', 'BRL', 'R$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Britain (United Kingdom)', 'Pounds', 'GBP', '£');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Brunei Darussalam', 'Dollars', 'BND', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Cambodia', 'Riels', 'KHR', '៛');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Canada', 'Dollars', 'CAD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Cayman Islands', 'Dollars', 'KYD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Chile', 'Pesos', 'CLP', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('China', 'Yuan Renminbi', 'CNY', '¥');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Colombia', 'Pesos', 'COP', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Costa Rica', 'Colón', 'CRC', '₡');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Croatia', 'Kuna', 'HRK', 'kn');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Cuba', 'Pesos', 'CUP', '₱');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Cyprus', 'Euro', 'EUR', '€');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Czech Republic', 'Koruny', 'CZK', 'Kč');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Denmark', 'Kroner', 'DKK', 'kr');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Dominican Republic', 'Pesos', 'DOP ', 'RD$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('East Caribbean', 'Dollars', 'XCD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Egypt', 'Pounds', 'EGP', '£');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('El Salvador', 'Colones', 'SVC', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('England (United Kingdom)', 'Pounds', 'GBP', '£');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Euro', 'Euro', 'EUR', '€');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Falkland Islands', 'Pounds', 'FKP', '£');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Fiji', 'Dollars', 'FJD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('France', 'Euro', 'EUR', '€');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Ghana', 'Cedis', 'GHC', '¢');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Gibraltar', 'Pounds', 'GIP', '£');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Greece', 'Euro', 'EUR', '€');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Guatemala', 'Quetzales', 'GTQ', 'Q');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Guernsey', 'Pounds', 'GGP', '£');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Guyana', 'Dollars', 'GYD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Holland (Netherlands)', 'Euro', 'EUR', '€');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Honduras', 'Lempiras', 'HNL', 'L');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Hong Kong', 'Dollars', 'HKD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Hungary', 'Forint', 'HUF', 'Ft');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Iceland', 'Kronur', 'ISK', 'kr');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('India', 'Rupees', 'INR', '₹');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Indonesia', 'Rupiahs', 'IDR', 'Rp');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Iran', 'Rials', 'IRR', '﷼');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Ireland', 'Euro', 'EUR', '€');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Isle of Man', 'Pounds', 'IMP', '£');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Israel', 'New Shekels', 'ILS', '₪');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Italy', 'Euro', 'EUR', '€');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Jamaica', 'Dollars', 'JMD', 'J$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Japan', 'Yen', 'JPY', '¥');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Jersey', 'Pounds', 'JEP', '£');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Kazakhstan', 'Tenge', 'KZT', 'лв');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Korea (North)', 'Won', 'KPW', '₩');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Korea (South)', 'Won', 'KRW', '₩');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Kyrgyzstan', 'Soms', 'KGS', 'лв');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Laos', 'Kips', 'LAK', '₭');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Latvia', 'Lati', 'LVL', 'Ls');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Lebanon', 'Pounds', 'LBP', '£');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Liberia', 'Dollars', 'LRD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Liechtenstein', 'Switzerland Francs', 'CHF', 'CHF');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Lithuania', 'Litai', 'LTL', 'Lt');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Luxembourg', 'Euro', 'EUR', '€');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Macedonia', 'Denars', 'MKD', 'ден');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Malaysia', 'Ringgits', 'MYR', 'RM');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Malta', 'Euro', 'EUR', '€');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Mauritius', 'Rupees', 'MUR', '₨');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Mexico', 'Pesos', 'MXN', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Mongolia', 'Tugriks', 'MNT', '₮');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Mozambique', 'Meticais', 'MZN', 'MT');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Namibia', 'Dollars', 'NAD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Nepal', 'Rupees', 'NPR', '₨');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Netherlands Antilles', 'Guilders', 'ANG', 'ƒ');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Netherlands', 'Euro', 'EUR', '€');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('New Zealand', 'Dollars', 'NZD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Nicaragua', 'Cordobas', 'NIO', 'C$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Nigeria', 'Nairas', 'NGN', '₦');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('North Korea', 'Won', 'KPW', '₩');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Norway', 'Krone', 'NOK', 'kr');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Oman', 'Rials', 'OMR', '﷼');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Pakistan', 'Rupees', 'PKR', '₨');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Panama', 'Balboa', 'PAB', 'B/.');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Paraguay', 'Guarani', 'PYG', 'Gs');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Peru', 'Nuevos Soles', 'PEN', 'S/.');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Philippines', 'Pesos', 'PHP', 'Php');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Poland', 'Zlotych', 'PLN', 'zł');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Qatar', 'Rials', 'QAR', '﷼');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Romania', 'New Lei', 'RON', 'lei');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Russia', 'Rubles', 'RUB', 'руб');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Saint Helena', 'Pounds', 'SHP', '£');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Saudi Arabia', 'Riyals', 'SAR', '﷼');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Serbia', 'Dinars', 'RSD', 'Дин.');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Seychelles', 'Rupees', 'SCR', '₨');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Singapore', 'Dollars', 'SGD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Slovenia', 'Euro', 'EUR', '€');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Solomon Islands', 'Dollars', 'SBD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Somalia', 'Shillings', 'SOS', 'S');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('South Africa', 'Rand', 'ZAR', 'R');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('South Korea', 'Won', 'KRW', '₩');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Spain', 'Euro', 'EUR', '€');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Sri Lanka', 'Rupees', 'LKR', '₨');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Sweden', 'Kronor', 'SEK', 'kr');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Switzerland', 'Francs', 'CHF', 'CHF');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Suriname', 'Dollars', 'SRD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Syria', 'Pounds', 'SYP', '£');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Taiwan', 'New Dollars', 'TWD', 'NT$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Thailand', 'Baht', 'THB', '฿');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Trinidad and Tobago', 'Dollars', 'TTD', 'TT$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Turkey', 'Liras', 'TRL', '£');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Tuvalu', 'Dollars', 'TVD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Ukraine', 'Hryvnia', 'UAH', '₴');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('United Kingdom', 'Pounds', 'GBP', '£');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('United States of America', 'Dollars', 'USD', '$');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Uruguay', 'Pesos', 'UYU', '\$U');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Uzbekistan', 'Sums', 'UZS', 'лв');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Vatican City', 'Euro', 'EUR', '€');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Venezuela', 'Bolivares Fuertes', 'VEF', 'Bs');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Vietnam', 'Dong', 'VND', '₫');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Yemen', 'Rials', 'YER', '﷼');
INSERT INTO {$table_name} (country, currency, code, symbol) VALUES ('Zimbabwe', 'Zimbabwe Dollars', 'ZWD', 'Z$')
CUR;
            dbDelta($sql_insert);
        }
    }
}