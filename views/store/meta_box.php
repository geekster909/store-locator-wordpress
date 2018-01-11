<?php $fields = [
    ['label' => 'Address1', 'field' => 'address_1'],
    ['label' => 'Address2', 'field' => 'address_2'],
    ['label' => 'City', 'field' => 'city'],
    ['label' => 'State', 'field' => 'state'],
    ['label' => 'Zip', 'field' => 'zip'],
    ['label' => 'Country', 'field' => 'country'],
    ['label' => 'Website', 'field' => 'website'],
    ['label' => 'Phone', 'field' => 'phone'],
    ['label' => 'Latitude', 'field' => 'latitude'],
    ['label' => 'Longitude', 'field' => 'longitude']
]; ?>
<?php wp_nonce_field( 'store', 'stores_nonce' ); ?>

<?php foreach ($fields as $field): ?>
    <div class="acf-field acf-field-text">
        <div class="acf-label">
            <label for="<?php echo $field['field']; ?>"><?php echo $field['label']; ?></label>
        </div>
        <div class="acf-input">
            <div class="acf-input-wrap">
                <input id="<?php echo $field['field']; ?>" type="text" name="<?php echo $field['field']; ?>" value="<?php echo get_post_meta($post->ID, $field['field'], true); ?>">
            </div>
        </div>
    </div>
<?php endforeach; ?>
