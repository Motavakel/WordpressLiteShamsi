jQuery(document).ready(function ($) {
    const jalaliMonthNames = ['', 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
    const timestampdiv = $('#timestampdiv');

    function timestampDivModifier(year, month, day, hour, min) {
        let content = '<div class="timestamp-wrap jalaliDivBox">';
        content += '<select id="Jmm" name="Jmm">' + jalaliMonthNames.map((name, index) =>
            `<option value="${index}" ${index === month ? 'selected' : ''}>${name}</option>`
        ).join('') + '</select>';
        
        content += `<input type="text" name="Jjj" value="${day}" id="Jjj" size="2" maxlength="2" autocomplete="off" />`;
        content += `<input type="text" name="Jaa" value="${year}" id="Jaa" size="4" maxlength="4" autocomplete="off" /> @`;
        content += `<input type="text" name="Jmn" value="${min}" id="Jmn" size="2" maxlength="2" autocomplete="off" /> : `;
        content += `<input type="text" name="Jhh" value="${hour}" id="Jhh" size="2" maxlength="2" autocomplete="off" />`;
        content += '</div>';
        return content;
    }

    function updateTimestampViewer() {
        const y = $('input[name=aa]').val();
        const m = $('select[name=mm]').val();
        const d = $('input[name=jj]').val();
        const h = $('input[name=hh]').val();
        const i = $('input[name=mn]').val();
        
        const jd = JalaliDate.gregorianToJalali(y, m, d);
        const text = `${jalaliMonthNames[jd[1]]} ${jd[2]}, ${jd[0]} @${h}:${i}`;
        const formattedText = text.replace(/\d/g, (match) => String.fromCharCode(match.charCodeAt(0) + 1728));
        
        $('#timestamp b').text(formattedText);
    }

    $('#the-list').on('click', '.editinline', function () {
        const tr = $(this).closest('td');
        const year = tr.find('.aa').html();
        if (year > 1700) {
            const month = tr.find('.mm').html();
            const day = tr.find('.jj').html();
            const hour = tr.find('.hh').html();
            const min = tr.find('.mn').html();
            const date = JalaliDate.gregorianToJalali(year, month, day);
            $('.inline-edit-date div').hide();
            $('.inline-edit-date').prepend(timestampDivModifier(date[0], date[1], date[2], hour, min));
        }
    });

    $('.inline-edit-date').on('keyup change', '#Jhh, #Jmn, #Jaa, #Jjj, #Jmm', function () {
        const year = $('#Jaa').val();
        const month = $('#Jmm').val();
        const day = $('#Jjj').val();
        
        if (this.id === 'Jhh') {
            $('input[name=hh]').val($(this).val());
        } else if (this.id === 'Jmn') {
            $('input[name=mn]').val($(this).val());
        } else {
            const date = JalaliDate.jalaliToGregorian(year, month, day);
            $('input[name=aa]').val(date[0]);
            $('select[name=mm]').val(date[1].toString().padStart(2, '0'));
            $('input[name=jj]').val(date[2]);
        }
    });

    $('a.edit-timestamp').on('click', function () {
        const date = JalaliDate.gregorianToJalali($('#aa').val(), $('#mm').val(), $('#jj').val());
        const divCnt = timestampDivModifier(date[0], date[1], date[2], $('#hh').val(), $('#mn').val());
        $('#timestampdiv .timestamp-wrap').hide();
        $('#timestampdiv').prepend(divCnt);
    });

    $('#timestampdiv').on('click', '.cancel-timestamp', function () {
        $('.jalaliDivBox').remove();
    });

    $('.save-timestamp,#publish').on('click', function () {
        if ($('#Jhh').val() !== undefined) {
            $('input[name=hh]').val($('#Jhh').val());
            $('input[name=mn]').val($('#Jmn').val());
            const year = $('#Jaa').val();
            const month = $('#Jmm').val();
            const day = $('#Jjj').val();
            const date = JalaliDate.jalaliToGregorian(year, month, day);
            $('input[name=aa]').val(date[0]);
            $('select[name=mm]').val(date[1].toString().padStart(2, '0'));
            $('input[name=jj]').val(date[2]);
        }

        setTimeout(function () {
            if ($('#timestampdiv .timestamp-wrap:eq(1)').hasClass('form-invalid')) {
                $('.jalaliDivBox').addClass('.form-invalid');
            } else {
                $('.jalaliDivBox').remove();
                $('#timestampdiv').slideUp('fast');
                $('a.edit-timestamp').slideDown('fast');
                setTimeout(updateTimestampViewer, 100);
            }
        }, 100);
    });
});


