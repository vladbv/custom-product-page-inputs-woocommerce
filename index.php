// Add this in functions.php in your WordPress theme, in order
// to add custom product page inputs and send the variable and its data in the checkout page and the e-mails

// First we have to add an action on the 'woocommerce_before_add_to_cart_button' hook
// Adding a custom field for a certain product
// @return [type] [description]
<?php
add_action('woocommerce_before_add_to_cart_button', 'pr_add_custom_fields');

function pr_add_custom_fields(){
    if(is_product() && get_the_id() === 1000){
        global $product;
        ob_start();
        ?>
            <div class="pr-custom-fields">
                <label for="pr_name">What kind of label would you like?</label>
                    <input type="text" name="pr_name" />
            </div>
            <div class="clear"></div>

            <?php 

        $content = ob_get_contents();
        ob_end_flush();
        return $content;
    }
}

add_filter('woocommerce_add_cart_item_data', 'pr_add_item_data', 10, 3);

// Add custom data to Cart
// @param [type] $cart_item_data [description]
// @param [type] $product_id [description]
// @param [type] $variation_id [description]
// @return [type]

function pr_add_item_data($cart_item_data, $product_id, $variation_id){
    if(isset($_REQUEST['pr_name'])) {
        $cart_item_data['pr_name'] = sanitize_text_field($_REQUEST['pr_name']);
    }
    return $cart_item_data;
}

add_filter('woocommerce_get_item_data', 'pr_add_item_meta', 10, 2);

// Displaying information as Meta on the Cart page
// @param [type] $item_data [description]
// @param [type] $cart_item [description]
// @return [type]           [description]

function pr_add_item_meta($item_data, $cart_item) {
        if(array_key_exists('pr_name', $cart_item)){
            $custom_details = $cart_item['pr_name'];
            $item_data[] = array(
                'key' => 'Label',
                'value' => $custom_details
            );
        }
    return $item_data;
}

add_action('woocommerce_checkout_create_order_line_item', 'pr_add_custom_order_line_meta', 10, 4);

function pr_add_custom_order_line_meta($item, $cart_item_key, $values, $order) {
    if(array_key_exists('pr_name', $values)) {
        $item->add_meta_data('_pr_name', $values['pr_name']);
    }
}

add_filter('woocommerce_order_item_meta_end', 'custom_woocommerce_email_order_meta_fields', 10, 3);

function custom_woocommerce_email_order_meta_fields($item_id, $item, $order){
    $pr = wc_get_order_item_data($item_id, '_pr_name', true);
    echo 'Label: ' . $pr;
}
?>
