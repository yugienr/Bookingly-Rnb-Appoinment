(function ($) {
  const ajaxUrl = bookingly.ajax_url;
  const nonce = bookingly.nonce;

  const getAvailableSlot = (selectedDate, inventoryId, productId) => {
    $.ajax({
      type: "POST",
      url: ajaxUrl,
      data: {
        action: "get_available_slot",
        selectedDate,
        inventoryId,
        productId,
        nonce,
      },
      success: (response) => {
        const { slotsFormatted } = response.data;
        $(".bookingly-slots").html(slotsFormatted);
      },
      error: (error) => {
        console.error(error);
      },
    });
  };

  const disableDates = (date) => {
    if (bookingly_data.weekends.indexOf(date.getDay()) !== -1) {
      return true;
    }

    const formattedMonth = String(date.getMonth() + 1).padStart(2, "0");
    const formattedDay = String(date.getDate()).padStart(2, "0");
    const formattedDate = `${date.getFullYear()}-${formattedMonth}-${formattedDay}`;

    return bookingly_data.blocked_dates.indexOf(formattedDate) !== -1;
  };

  const onDateChange = (selectedDates, dateStr) => {
  const [checkInDate, checkOutDate] = selectedDates;
  const inventoryId = $("#booking_inventory").val();
  const productId = $("#booking_inventory").data("post-id");
  getAvailableSlot(checkInDate, checkOutDate, inventoryId, productId);
};

  flatpickr("#datepicker", {
    locale: bookingly_data.lang,
    dateFormat: "Y-m-d",
    minDate: "today",
    inline: true,
    disable: [disableDates],
    onChange: onDateChange,
  });
  //End handling datepicker

  const updateBookingTime = (start_time, end_time, slot_id) => {
    start_time = start_time.includes(".") ? start_time : start_time + ".00";
    end_time = end_time.includes(".") ? end_time : end_time + ".00";

    $("#pickup-time").val(start_time);
    $("#dropoff-time").val(end_time);
    $("#slot_id").val(slot_id);
  };

  const onSlotClick = () => {
    $("body").on("click", ".bookingly-slots .slot", function () {
      const serializedData = $(".rnb-cart").serialize();
      const slotAttr = $(this).data("slot_attr");
      updateBookingTime(slotAttr.start_time, slotAttr.end_time, slotAttr.id);
      $(".rnb-cart").trigger("change");
      $(this).toggleClass("checked");
      $(this).siblings(".slot").removeClass("checked");
    });
  };
  //End handling slot click

  $(document).ready(() => {
    onSlotClick();
  });
})(jQuery);

// Wait until the DOM is fully loaded
jQuery(function ($) {
  const ajaxUrl = bookingly.ajax_url;
  const nonce = bookingly.nonce;

  const calendar = {
    daysContainer: $("#week-days"),
    slotsContainer: $("#slots"),
    timeSlots: Array.from(
      { length: 24 },
      (_, i) => `${i.toString().padStart(2, "0")}:00`
    ),

    init() {
      this.renderNextSevenDays();
    },

    renderNextSevenDays() {
      const now = new Date();
      const nextSevenDays = Array.from({ length: 7 }, (_, i) => {
        const day = new Date(now);
        day.setDate(now.getDate() + i);
        return day;
      });

      this.daysContainer.html(
        nextSevenDays
          .map((day, index) => {
            const formattedDate = day.toISOString().split("T")[0];
            const isDisabled = this.isDateDisabled(day);
            return `<div class="day-wrapper"><div class="day ${
              isDisabled ? "disabled" : ""
            }" data-date="${formattedDate}" data-index="${index}">
              <span>${day.toLocaleString("default", {
                weekday: "short",
              })}</span><span>${day.getDate()}</span>
            </div></div/>`;
          })
          .join("")
      );

      this.addDayClickListeners();
    },

    isDateDisabled(date) {
      if (bookingly_data.weekends.includes(date.getDay())) {
        return true;
      }

      const formattedMonth = String(date.getMonth() + 1).padStart(2, "0");
      const formattedDay = String(date.getDate()).padStart(2, "0");
      const formattedDate = `${date.getFullYear()}-${formattedMonth}-${formattedDay}`;

      return bookingly_data.blocked_dates.includes(formattedDate);
    },

    renderSlots(selectedDate, inventoryId, productId) {
      $.ajax({
        type: "POST",
        url: ajaxUrl,
        data: {
          action: "get_available_slot",
          selectedDate,
          inventoryId,
          productId,
          nonce,
        },
        success: (response) => {
          const { slotsFormatted } = response.data;
          this.slotsContainer.html(
            slotsFormatted || "<p>Sorry! No slots available!</p>"
          );
          this.addSlotClickListeners();
        },
        error: (error) => {
          console.error(error);
        },
      });
    },

    addDayClickListeners() {
      const self = this;
      $(".day").on("click", function () {
        if ($(this).hasClass("disabled")) return;

        $(".day").removeClass("selected");
        $(this).addClass("selected");

        const selectedDate = $(this).data("date");
        const inventoryId = $("#booking_inventory").val();
        const productId = $("#booking_inventory").data("post-id");

        // Only render slots if this was a real click (not programmatic)
        if (event.isTrusted) {
          self.renderSlots(selectedDate, inventoryId, productId);
        }
      });
    },

    addSlotClickListeners() {
      const self = this;
      $(".slot").on("click", function () {
        $(this).siblings("li").removeClass("checked");
        const slotAttr = $(this).data("slot_attr");
        self.updateBookingTime(
          slotAttr.start_time,
          slotAttr.end_time,
          slotAttr.id
        );
      });
    },

    updateBookingTime(start_time, end_time, slot_id) {
      start_time = start_time.includes(".") ? start_time : start_time + ".00";
      end_time = end_time.includes(".") ? end_time : end_time + ".00";

      $("#pickup-time").val(start_time);
      $("#dropoff-time").val(end_time);
      $("#slot_id").val(slot_id);
    },
  };

  calendar.init();

  $("#week-days")
    .slick({
      slidesToShow: 5,
      slidesToScroll: 5,
      arrows: true,
      infinite: false,
      prevArrow: `<span class="prev">
        <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M6 11L1 6L6 1" stroke="#212121" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </span>`,
      nextArrow: `<span class="next">
        <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1 1L6 6L1 11" stroke="#212121" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </span>`,
    });

  $(window).on("load", function () {
    // Find the first non-disabled day
    const $firstAvailableDay = $("#week-days .day").not(".disabled").first();
    if ($firstAvailableDay.length) {
      $firstAvailableDay.addClass("selected");
      // Only render slots for this specific day
      calendar.renderSlots(
        $firstAvailableDay.data("date"),
        $("#booking_inventory").val(),
        $("#booking_inventory").data("post-id")
      );
    }
  });
});

(function ($) {
  const ajaxUrl = bookingly.ajax_url;
  const nonce = bookingly.nonce;

  const getAvailableRooms = (checkInDate, checkOutDate) => {
    $.ajax({
      type: "POST",
      url: ajaxUrl,
      data: {
        action: "get_available_rooms",
        checkInDate,
        checkOutDate,
        nonce,
      },
      success: (response) => {
        const { roomsFormatted } = response.data;
        $(".bookingly-rooms").html(roomsFormatted);
      },
      error: (error) => {
        console.error(error);
      },
    });
  };

  const disableDates = (date) => {
    if (bookingly_data.weekends.indexOf(date.getDay()) !== -1) {
      return true;
    }

    const formattedMonth = String(date.getMonth() + 1).padStart(2, "0");
    const formattedDay = String(date.getDate()).padStart(2, "0");
    const formattedDate = `${date.getFullYear()}-${formattedMonth}-${formattedDay}`;

    return bookingly_data.blocked_dates.indexOf(formattedDate) !== -1;
  };

  flatpickr("#check-in-datepicker", {
    locale: bookingly_data.lang,
    dateFormat: "Y-m-d",
    minDate: "today",
    inline: true,
    disable: [disableDates],
    onChange: (selectedDates, dateStr) => {
      const checkOutDate = $("#check-out-datepicker").val();
      if (checkOutDate) {
        getAvailableRooms(dateStr, checkOutDate);
      }
    },
  });

  flatpickr("#check-out-datepicker", {
    locale: bookingly_data.lang,
    dateFormat: "Y-m-d",
    minDate: "today",
    inline: true,
    disable: [disableDates],
    onChange: (selectedDates, dateStr) => {
      const checkInDate = $("#check-in-datepicker").val();
      if (checkInDate) {
        getAvailableRooms(checkInDate, dateStr);
      }
    },
  });

  $(document).ready(() => {
    // Initial load to display rooms if dates are already set
    const checkInDate = $("#check-in-datepicker").val();
    const checkOutDate = $("#check-out-datepicker").val();
    if (checkInDate && checkOutDate) {
      getAvailableRooms(checkInDate, checkOutDate);
    }
  });
})(jQuery);
