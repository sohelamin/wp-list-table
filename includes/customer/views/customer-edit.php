<div class="wrap">
    <h1><?php _e( 'Edit Customer', 'ac' ); ?></h1>

    <?php $item = $this->customer_obj->get_customer( $id ); ?>

    <form action="" method="post">

        <table class="form-table">
            <tbody>
                <tr class="row-name">
                    <th scope="row">
                        <label for="name"><?php _e( 'Name', 'ac' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="name" id="name" class="regular-text" value="<?php echo esc_attr( $item->name ); ?>" required="required" />
                    </td>
                </tr>
                <tr class="row-address">
                    <th scope="row">
                        <label for="address"><?php _e( 'Address', 'ac' ); ?></label>
                    </th>
                    <td>
                        <textarea name="address" id="address" rows="5" cols="30"><?php echo esc_textarea( $item->address ); ?></textarea>
                    </td>
                </tr>
                <tr class="row-city">
                    <th scope="row">
                        <label for="city"><?php _e( 'City', 'ac' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="city" id="city" class="regular-text" value="<?php echo esc_attr( $item->city ); ?>" />
                    </td>
                </tr>
             </tbody>
        </table>

        <input type="hidden" name="field_id" value="<?php echo $item->id; ?>">

        <?php wp_nonce_field( 'ac_new_customer' ); ?>
        <?php submit_button( __( 'Update Customer', 'ac' ), 'primary', 'submit_customer' ); ?>

    </form>
</div>
