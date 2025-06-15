$(function () {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    $('td.calendar-td input[type="hidden"][name="getData[]"]').each(
        function () {
            const dateStr = $(this).val();
            const cellDate = new Date(dateStr);
            cellDate.setHours(0, 0, 0, 0);

            if (cellDate < today) {
                $(this).parent().addClass("past-day");
            }
        }
    );
});
