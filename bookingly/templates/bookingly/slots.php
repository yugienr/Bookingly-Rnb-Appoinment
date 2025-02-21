<?php if (!count($slots)) : ?>
    <p class="no-slot-notice"> <?php echo esc_html__('Sorry! No slots available!'); ?> </p>
<?php endif; ?>

<?php if (count($slots)) : ?>
    <ul class="slots">
        <?php foreach ($slots as $key => $slot) : ?>
            <?php $slot_attr = htmlspecialchars(json_encode($slot), ENT_QUOTES, 'UTF-8'); ?>
            <li class="slot" data-slot_attr="<?php echo $slot_attr; ?>">
                <input type="radio" name="slot_id" value="<?php echo esc_attr($slot['id']); ?>" id="slot<?php echo esc_attr($slot['id']); ?>">
                <label for="slot<?php echo esc_attr($slot['id']); ?>"><?php echo esc_attr($slot['full_name']); ?></label>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<input type="hidden" name="pickup_date" id="pickup-date" value="<?php echo esc_attr($date) ?>">
<input type="hidden" name="pickup_time" id="pickup-time" value="">
<input type="hidden" name="dropoff_time" id="dropoff-time" value="">