/**
 * Thai Date Formatter JavaScript
 * แปลงวันที่เป็นรูปแบบไทย พ.ศ.
 * 
 * Meeting Room Booking System
 */

const ThaiDate = {
    // Thai month names (full)
    months: [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 
        'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม',
        'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ],
    
    // Thai month names (short)
    monthsShort: [
        'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 
        'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.',
        'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
    ],
    
    // Thai day names (full)
    days: ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'],
    
    // Thai day names (short)
    daysShort: ['อา.', 'จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.'],
    
    // Thai day names (min)
    daysMin: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],

    /**
     * Convert to Buddhist Era year
     * @param {number} year - Christian Era year
     * @returns {number} Buddhist Era year
     */
    toBuddhistYear: function(year) {
        return parseInt(year) + 543;
    },

    /**
     * Convert to Christian Era year
     * @param {number} year - Buddhist Era year
     * @returns {number} Christian Era year
     */
    toChristianYear: function(year) {
        return parseInt(year) - 543;
    },

    /**
     * Format date to Thai format
     * @param {Date|string} date - Date object or date string
     * @param {string} format - Format type: 'short', 'medium', 'long', 'full'
     * @returns {string} Formatted Thai date
     */
    format: function(date, format = 'medium') {
        if (!date) return '-';
        
        const d = (date instanceof Date) ? date : new Date(date);
        if (isNaN(d.getTime())) return '-';
        
        const day = d.getDate();
        const month = d.getMonth();
        const year = this.toBuddhistYear(d.getFullYear());
        const dayOfWeek = d.getDay();
        
        switch (format) {
            case 'short':
                return `${day}/${month + 1}/${year % 100}`;
            case 'medium':
                return `${day} ${this.monthsShort[month]} ${year}`;
            case 'long':
                return `${day} ${this.months[month]} ${year}`;
            case 'full':
                return `วัน${this.days[dayOfWeek]}ที่ ${day} ${this.months[month]} พ.ศ. ${year}`;
            case 'iso':
                return d.toISOString().split('T')[0];
            default:
                return `${day} ${this.monthsShort[month]} ${year}`;
        }
    },

    /**
     * Format datetime to Thai format
     * @param {Date|string} datetime - Date object or datetime string
     * @param {string} format - Format type
     * @returns {string} Formatted Thai datetime
     */
    formatDatetime: function(datetime, format = 'medium') {
        if (!datetime) return '-';
        
        const d = (datetime instanceof Date) ? datetime : new Date(datetime);
        if (isNaN(d.getTime())) return '-';
        
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        
        return `${this.format(d, format)} ${hours}:${minutes} น.`;
    },

    /**
     * Format time only
     * @param {string} time - Time string (HH:mm or HH:mm:ss)
     * @returns {string} Formatted time with น.
     */
    formatTime: function(time) {
        if (!time) return '-';
        const parts = time.split(':');
        return `${parts[0]}:${parts[1]} น.`;
    },

    /**
     * Get current date in Thai format
     * @param {string} format - Format type
     * @returns {string} Current date in Thai format
     */
    today: function(format = 'long') {
        return this.format(new Date(), format);
    },

    /**
     * Get current Buddhist Era year
     * @returns {number}
     */
    currentYear: function() {
        return this.toBuddhistYear(new Date().getFullYear());
    },

    /**
     * Parse Thai date string to Date object
     * @param {string} thaiDate - Thai date string
     * @returns {Date|null}
     */
    parse: function(thaiDate) {
        if (!thaiDate) return null;
        
        // Try to parse "20/1/68" format
        let match = thaiDate.match(/^(\d{1,2})\/(\d{1,2})\/(\d{2,4})$/);
        if (match) {
            let year = parseInt(match[3]);
            if (year < 100) year += 2500; // Assume 2500s
            return new Date(this.toChristianYear(year), parseInt(match[2]) - 1, parseInt(match[1]));
        }
        
        // Try to parse "20 ม.ค. 2568" format
        for (let i = 0; i < this.monthsShort.length; i++) {
            if (thaiDate.includes(this.monthsShort[i])) {
                match = thaiDate.match(/(\d{1,2})\s*\S+\s*(\d{4})/);
                if (match) {
                    return new Date(this.toChristianYear(parseInt(match[2])), i, parseInt(match[1]));
                }
            }
        }
        
        // Try to parse "20 มกราคม 2568" format
        for (let i = 0; i < this.months.length; i++) {
            if (thaiDate.includes(this.months[i])) {
                match = thaiDate.match(/(\d{1,2})\s*\S+\s*(\d{4})/);
                if (match) {
                    return new Date(this.toChristianYear(parseInt(match[2])), i, parseInt(match[1]));
                }
            }
        }
        
        return null;
    },

    /**
     * Get years dropdown options (Buddhist Era)
     * @param {number} startYear - Start year (CE)
     * @param {number} endYear - End year (CE)
     * @returns {Array} Array of {value, label} objects
     */
    getYearOptions: function(startYear = null, endYear = null) {
        const currentYear = new Date().getFullYear();
        startYear = startYear || (currentYear - 5);
        endYear = endYear || (currentYear + 5);
        
        const options = [];
        for (let y = endYear; y >= startYear; y--) {
            options.push({
                value: y,
                label: this.toBuddhistYear(y)
            });
        }
        return options;
    },

    /**
     * Get months dropdown options
     * @param {boolean} short - Use short names
     * @returns {Array} Array of {value, label} objects
     */
    getMonthOptions: function(short = false) {
        const names = short ? this.monthsShort : this.months;
        return names.map((name, index) => ({
            value: index + 1,
            label: name
        }));
    }
};

/**
 * FullCalendar Thai Locale Configuration
 */
const FullCalendarThaiLocale = {
    code: 'th',
    week: {
        dow: 0, // Sunday is first day
        doy: 6
    },
    buttonText: {
        prev: 'ย้อนกลับ',
        next: 'ถัดไป',
        today: 'วันนี้',
        month: 'เดือน',
        week: 'สัปดาห์',
        day: 'วัน',
        list: 'รายการ'
    },
    weekText: 'สัปดาห์',
    allDayText: 'ตลอดวัน',
    moreLinkText: function(n) {
        return '+อีก ' + n + ' รายการ';
    },
    noEventsText: 'ไม่มีกิจกรรม',
    
    // Custom title format for Buddhist Era
    titleFormat: function(date) {
        const month = ThaiDate.months[date.date.month];
        const year = ThaiDate.toBuddhistYear(date.date.year);
        return `${month} ${year}`;
    }
};

/**
 * Initialize FullCalendar with Thai locale and Buddhist Era
 * @param {string} selector - Element selector
 * @param {Object} options - FullCalendar options
 * @returns {Object} FullCalendar instance
 */
function initThaiCalendar(selector, options = {}) {
    const calendarEl = document.querySelector(selector);
    if (!calendarEl) return null;
    
    const defaultOptions = {
        locale: 'th',
        firstDay: 0,
        buttonText: FullCalendarThaiLocale.buttonText,
        allDayText: FullCalendarThaiLocale.allDayText,
        noEventsText: FullCalendarThaiLocale.noEventsText,
        moreLinkText: FullCalendarThaiLocale.moreLinkText,
        
        // Custom title format for Buddhist Era
        titleFormat: { year: 'numeric', month: 'long' },
        
        // Day header format
        dayHeaderFormat: { weekday: 'short' },
        
        // Override title rendering
        datesSet: function(info) {
            // Update title to Buddhist Era
            const titleEl = calendarEl.querySelector('.fc-toolbar-title');
            if (titleEl) {
                const viewDate = info.view.currentStart;
                const month = ThaiDate.months[viewDate.getMonth()];
                const year = ThaiDate.toBuddhistYear(viewDate.getFullYear());
                titleEl.textContent = `${month} ${year}`;
            }
        },
        
        // Format event times
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }
    };
    
    // Merge options
    const finalOptions = { ...defaultOptions, ...options };
    
    // Create calendar
    const calendar = new FullCalendar.Calendar(calendarEl, finalOptions);
    calendar.render();
    
    return calendar;
}

/**
 * Initialize Bootstrap Datepicker with Thai locale
 * @param {string} selector - Element selector
 * @param {Object} options - Datepicker options
 */
function initThaiDatepicker(selector, options = {}) {
    // Check if Bootstrap Datepicker is available
    if (typeof $.fn.datepicker === 'undefined') {
        console.warn('Bootstrap Datepicker not loaded');
        return;
    }
    
    // Thai locale for Bootstrap Datepicker
    $.fn.datepicker.dates['th'] = {
        days: ThaiDate.days.map(d => 'วัน' + d),
        daysShort: ThaiDate.daysShort,
        daysMin: ThaiDate.daysMin,
        months: ThaiDate.months,
        monthsShort: ThaiDate.monthsShort,
        today: 'วันนี้',
        clear: 'ล้าง',
        format: 'dd/mm/yyyy',
        titleFormat: 'MM yyyy',
        weekStart: 0
    };
    
    const defaultOptions = {
        language: 'th',
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true,
        todayBtn: 'linked',
        clearBtn: true,
        
        // Override to show Buddhist Era year
        beforeShowYear: function(date) {
            return ThaiDate.toBuddhistYear(date.getFullYear());
        }
    };
    
    $(selector).datepicker({ ...defaultOptions, ...options });
}

/**
 * Initialize Flatpickr with Thai locale
 * @param {string} selector - Element selector
 * @param {Object} options - Flatpickr options
 */
function initThaiFlatpickr(selector, options = {}) {
    // Check if Flatpickr is available
    if (typeof flatpickr === 'undefined') {
        console.warn('Flatpickr not loaded');
        return;
    }
    
    // Thai locale for Flatpickr
    const thaiLocale = {
        firstDayOfWeek: 0,
        weekdays: {
            shorthand: ThaiDate.daysShort,
            longhand: ThaiDate.days.map(d => 'วัน' + d)
        },
        months: {
            shorthand: ThaiDate.monthsShort,
            longhand: ThaiDate.months
        },
        rangeSeparator: ' ถึง ',
        weekAbbreviation: 'สัปดาห์',
        scrollTitle: 'เลื่อนเพื่อเพิ่มหรือลด',
        toggleTitle: 'คลิกเพื่อเปลี่ยน',
        time_24hr: true
    };
    
    const defaultOptions = {
        locale: thaiLocale,
        dateFormat: 'd/m/Y',
        allowInput: true,
        
        // Custom formatting for Buddhist Era
        formatDate: function(date, format) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = ThaiDate.toBuddhistYear(date.getFullYear());
            return `${day}/${month}/${year}`;
        },
        
        // Parse input with Buddhist Era
        parseDate: function(dateStr, format) {
            const parts = dateStr.split('/');
            if (parts.length === 3) {
                let year = parseInt(parts[2]);
                if (year > 2500) year = ThaiDate.toChristianYear(year);
                return new Date(year, parseInt(parts[1]) - 1, parseInt(parts[0]));
            }
            return null;
        }
    };
    
    return flatpickr(selector, { ...defaultOptions, ...options });
}

/**
 * Format all date elements on page with Thai Buddhist Era
 * @param {string} selector - Elements selector (default: [data-thai-date])
 */
function formatThaiDates(selector = '[data-thai-date]') {
    document.querySelectorAll(selector).forEach(el => {
        const date = el.dataset.thaiDate || el.textContent;
        const format = el.dataset.format || 'medium';
        if (date) {
            el.textContent = ThaiDate.format(date, format);
        }
    });
}

/**
 * Format all datetime elements on page with Thai Buddhist Era
 * @param {string} selector - Elements selector (default: [data-thai-datetime])
 */
function formatThaiDatetimes(selector = '[data-thai-datetime]') {
    document.querySelectorAll(selector).forEach(el => {
        const datetime = el.dataset.thaiDatetime || el.textContent;
        const format = el.dataset.format || 'medium';
        if (datetime) {
            el.textContent = ThaiDate.formatDatetime(datetime, format);
        }
    });
}

// Auto-initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    formatThaiDates();
    formatThaiDatetimes();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ThaiDate, FullCalendarThaiLocale, initThaiCalendar, initThaiDatepicker, initThaiFlatpickr };
}
