<div class="wrap">
    <h1><?php _e( 'Add Customer', 'ac' ); ?></h1>

    <form action="" method="post">

        <table class="form-table">
            <tbody>
                <tr class="row-name">
                    <th scope="row">
                        <label for="name"><?php _e( 'Name', 'ac' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="name" id="name" class="regular-text" value="" required="required" />
                    </td>
                </tr>
                <tr class="row-address">
                    <th scope="row">
                        <label for="address"><?php _e( 'Address', 'ac' ); ?></label>
                    </th>
                    <td>
                        <textarea name="address" id="address" rows="5" cols="40"></textarea>
                    </td>
                </tr>
                <tr class="row-city">
                    <th scope="row">
                        <label for="city"><?php _e( 'City', 'ac' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="city" id="city" class="regular-text" value="" />
                    </td>
                </tr>
            </tbody>
        </table>

        <input type="hidden" name="field_id" value="0">

        <?php wp_nonce_field( 'ac_new_customer' ); ?>
        <?php submit_button( __( 'Add Customer', 'ac' ), 'primary', 'submit_customer' ); ?>

    </form>
</div>
