<?php
/**
 * Core file
 *
 * @author Vince Wooll <sales@jomres.net>
 * @version Jomres 9
 * @package Jomres
 * @copyright	2005-2016 Vince Wooll
 * Jomres (tm) PHP, CSS & Javascript files are released under both MIT and GPL2 licenses. This means that you can choose the license that best suits your project, and use it accordingly.
 **/


defined( '_JOMRES_INITCHECK' ) or die( '' );

$jrConfig = array (
  'defaultCrate' => '1',
  'property_list_limit' => '9',
  'useGlobalCurrency' => '1',
  'globalCurrency' => '€',
  'globalCurrencyCode' => 'EUR',
  'menusAsImages' => '1',
  'google_maps_api_key' => '',
  'minimalconfiguration' => '0',
  'useSSLinBookingform' => '0',
  'editingModeAffectsAllProperties' => '0',
  'allowHTMLeditor' => '1',
  'selfRegistrationAllowed' => '1',
  'cal_output' => 'jS M Y',
  'cal_input' => '%d/%m/%Y',
  'isInIframe' => '0',
  'errorChecking' => '0',
  'dumpTemplate' => '0',
  'jscalendarLangfile' => 'calendar-en.js',
  'jscalendarCSSfile' => 'calendar-blue.css',
  'maxwidth' => '900',
  'fileSize' => '5000',
  'slideshow' => 'tooltips',
  'propertyListDescriptionLimit' => '120',
  'randomsearchlimit' => '5000',
  'useGlobalPFeatures' => '1',
  'useGlobalRoomTypes' => '1',
  'dynamicMinIntervalRecalculation' => '0',
  'disableAudit' => '1',
  'allowedTags' => '|||||||||;pre&#38;#38;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;#62;||||;p&#38;#38;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;amp;#62;',
  'utfHTMLdecode' => '0',
  'emailErrors' => '0',
  'useJomresEmailCheck' => '0',
  'siteLang' => 'en-GB.php',
  'autoDetectJSCalendarLang' => '1',
  'sef_jomres_url_prefix' => 'accommodation',
  'sef_task_alias_viewproperty' => 'details',
  'sef_task_alias_dobooking' => 'book',
  'sef_task_alias_search' => 'search',
  'sef_property_url_country' => '1',
  'sef_property_url_region' => '1',
  'sef_property_url_town' => '1',
  'sef_property_url_ptype' => '1',
  'sef_property_url_propertyname' => '1',
  'sef_property_url_property_id' => '1',
  'sef_search_url_country' => '1',
  'sef_default_country' => 'United Kingdom',
  'sef_search_url_region' => '1',
  'sef_default_region' => 'Pembrokeshire',
  'sef_search_url_town' => '1',
  'sef_default_town' => 'Tenby',
  'sef_search_url_ptype' => '1',
  'sef_default_ptype' => 'hotels',
  'editinplace' => '1',
  'composite_property_details' => '1',
  'integratedSearch_enable' => '1',
  'integratedSearch_useCols' => '0',
  'integratedSearch_featurecols' => '3',
  'integratedSearch_selectcombo' => '1',
  'integratedSearch_propertyname' => '0',
  'integratedSearch_propertyname_dropdown' => '1',
  'integratedSearch_geosearchtype' => '',
  'integratedSearch_geosearchtype_dropdown' => '1',
  'integratedSearch_ptype' => '1',
  'integratedSearch_ptype_dropdown' => '1',
  'integratedSearch_room_type' => '0',
  'integratedSearch_room_type_dropdown' => '1',
  'integratedSearch_features' => '0',
  'integratedSearch_features_dropdown' => '1',
  'integratedSearch_description' => '0',
  'integratedSearch_availability' => '1',
  'integratedSearch_priceranges' => '0',
  'integratedSearch_pricerange_increments' => '20',
  'integratedSearch_guestnumber' => '1',
  'integratedSearch_stars' => '0',
  'useCaching' => '0',
  'showLangDropdown' => '0',
  'thumbnail_width' => '50',
  'useJomresMessaging' => '1',
  'useSubscriptions' => '0',
  'useNewusers' => '0',
  'jomresItemid' => 0,
  'outputHeadersInline' => '0',
  'lifetime' => '18000',
  'business_name' => 'BLANK',
  'business_vat_number' => 'BLANK',
  'business_address' => 'BLANK',
  'business_street' => 'BLANK',
  'business_town' => 'BLANK',
  'business_region' => '1061',
  'business_country' => 'GB',
  'business_postcode' => 'BLANK',
  'business_telephone' => 'BLANK',
  'business_email' => 'BLANK',
  'contact_owner_emails_to_alternative' => '0',
  'contact_owner_emails_to_alternative_email' => '',
  'auto_translate' => '0',
  'is_single_property_installation' => '0',
  'use_html_purifier' => '0',
  'limit_property_country' => '0',
  'limit_property_country_country' => 'GB',
  'jquery_ui_theme' => 'jomres',
  'use_reviews' => '1',
  'autopublish_reviews' => '1',
  'reviews_test_mode' => '0',
  'show_booking_form_in_property_details' => '0',
  'search_order_default' => '1',
  'show_search_order' => '1',
  'calendarstartofweekday' => '1',
  'only_guests_can_review' => '1',
  'jquery_ui_theme_detected' => 'jomres^jquery-ui.css',
  'use_timezone_switcher' => '1',
  'load_jquery' => '1',
  'thumbnail_property_list_max_width' => '250',
  'thumbnail_property_list_max_height' => '140',
  'thumbnail_property_header_max_width' => '400',
  'thumbnail_property_header_max_height' => '225',
  'use_commission' => '0',
  'manager_bookings_trigger_commission' => '1',
  'commission_autosuspend_on_overdue' => '1',
  'commission_autosuspend_on_overdue_threashold' => '60',
  'language_context' => '',
  'advanced_site_config' => '1',
  'booking_form_lnks_as_buttons' => '1',
  'load_jquery_ui' => '1',
  'guestnumbersearch' => 'greaterthan',
  'load_jquery_ui_css' => '1',
  'use_conversion_feature' => '1',
  'javascript_caching_enabled' => '0',
  'geolocation_api_key' => '',
  'booking_form_modal_popup' => '0',
  'booking_form_totalspanel_position' => '455',
  'booking_form_width' => '450',
  'booking_form_totals_panel_as_slider' => '0',
  'useNewusers_sendemail' => '1',
  'show_tax_in_totals_summary' => '1',
  'alternate_smtp_use_settings' => '0',
  'alternate_smtp_authentication' => '1',
  'alternate_smtp_host' => '',
  'alternate_smtp_port' => '',
  'alternate_smtp_protocol' => '',
  'alternate_smtp_username' => '',
  'alternate_smtp_password' => '',
  'alternate_mainmenu' => '1',
  'full_access_control' => '0',
  'license_server_username' => '',
  'license_server_password' => '',
  'useshoppingcart' => '1',
  'default_lat' => '51.50068',
  'default_long' => '-0.14317',
  'default_from_address' => '',
  'css_caching_enabled' => '0',
  'use_cleardate_checkbox' => '1',
  'use_cookie_policy' => '0',
  'safe_mode' => '0',
  'use_jomres_own_editor' => '0',
  'room_lock_timeout' => '3600',
  'input_filtering' => 'strong',
  'html_purifier_allowed_tags' => 'p[class],b,strong,a[href],i,em,img[src],ul,li,h1[class],table[width],table[border],tr,td,th,br',
  'inputs_allowing_html' => 'property_description property_checkin_times property_area_activities property_driving_directions property_airports property_othertransport property_policies_disclaimers email_text description',
  'property_details_in_tabs' => '0',
  'property_list_layout_default' => 'tile',
  'automatically_approve_new_properties' => '1',
  'region_names_are_translatable' => '0',
  'use_bootstrap_in_frontend' => '1',
  'recaptcha_public_key' => '',
  'recaptcha_private_key' => '',
  'use_bootstrap_in_admin' => '0',
  'jquery18_2_switch' => '0',
  'gmap_pois' => '0',
  'gmap_layer_weather' => '1',
  'gmap_layer_panoramio' => '0',
  'gmap_layer_transit' => '0',
  'gmap_layer_traffic' => '0',
  'gmap_layer_bicycling' => '0',
  'gmap_layer_temperature_grad' => 'CELCIUS',
  'development_production' => 'production',
  'useArrayCaching' => '0',
  'navbar_location' => 'component_area',
  'navbar_inverse' => '0',
  'bootstrap_version' => '',
  'show_powered_by' => '1',
  'use_budget_feature' => '1',
  'update_time' => '',
  'show_cumulative_price_overlay' => '1',
  'live_scrolling_enabled' => '1',
  'load_font_awesome' => '0',
  'licensekey' => '',
  'openexchangerates_api_key' => '',
  'subscriptionPackagePriceIncludesTax' => '1',
  'subscriptionSendReminderEmail' => '1',
  'subscriptionSendReminderEmailDays' => '10',
  'subscriptionSendExpirationEmail' => '1',
  'featured_listings_emphasis' => '',
  'override_property_contact_details' => '0',
  'override_property_contact_email' => '',
  'override_property_contact_tel' => '',
  'override_property_contact_fax' => '',
  'currency_symbol_swap' => '0',
  'send_tracking_data' => '0',
  'log_path' => '',
  'map_style' => 'ultralight',
  'syslog_host' => '',
  'syslog_port' => '',
  'sendErrorEmails' => '0',
  'minimum_deposit_percentage' => ""
);
