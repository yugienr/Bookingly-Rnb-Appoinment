<?php 

$calendar_type = bookingly_get_layout_type();

 if($calendar_type == 'full_calender'){
?>
<div class="bookingly-calendar">
    <label for="datepicker"><?php echo esc_html__('Choose a date:', 'bookingly'); ?> </label>
    <input type="text" id="datepicker" style="display: none;">
</div>

<div class="bookingly-slots"></div>
<?php }else{ 
  
  $current_date = get_current_month_year();
  ?>
 <div class="date-display">
    <!-- SVG Icon -->
    <span class="date-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM5 8V6h14v2H5z"/>
        </svg>
    </span>
    <!-- Date Text -->
    <span class="date-text">
        <?php printf(
            '%s %s',
            esc_html($current_date['month']),
            esc_html($current_date['year'])
        ); ?>
    </span>
</div>
<div id="calendar">
  <div id="week-days" class="days">
  <div class="loader">
    <svg width="38" height="38" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" stroke="#000">
      <g fill="none" fill-rule="evenodd">
        <g transform="translate(1 1)" stroke-width="2">
          <circle stroke-opacity=".5" cx="18" cy="18" r="18"/>
          <path d="M36 18c0-9.94-8.06-18-18-18">
            <animateTransform
              attributeName="transform"
              type="rotate"
              from="0 18 18"
              to="360 18 18"
              dur="1s"
              repeatCount="indefinite"/>
          </path>
        </g>
      </g>
    </svg>
  </div>
  </div>
  <div id="slots" class="slots"></div>
</div>
<?php }