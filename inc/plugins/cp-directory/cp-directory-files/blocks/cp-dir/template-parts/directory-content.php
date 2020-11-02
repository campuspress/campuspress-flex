<table>
    <thead>
        <tr>
            <?php
            foreach( $fields as $field ) {
                if( !$field['hidden'] ) {
                echo '<th>' . $field['label'] . '</th>';
            }
                else {
                    echo '<th style="display:none;">' . $field['label'] . '</th>';
                }
            }
            ?>
        </tr>
    </thead>
    <tbody class="cp-dir-content-list">
        <?php foreach( $entries as $entry_id ) { ?>
            <tr data-entry-id="<?php esc_attr_e( $entry_id ); ?>">
                <?php 
                foreach( $fields as $field ) {
                    $value = cp_dir_get_field_value( $entry_id, $field );
                    if( !$field['hidden'] ) {
                    echo '<td class="' . esc_attr( $field['field_name'] ) . '" data-value="' . esc_attr( $value['attr'] ) . '">' . $value['content'] . '</td>';
                    }
                    else {
                        echo '<td style="display:none;" class="' . esc_attr( $field['field_name'] ) . '" data-value="' . esc_attr( $value['attr'] ) . '"></td>';
                    }
                }
                ?>
            </tr>
        <?php } ?>
    </tbody>
</table>