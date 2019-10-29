import {
    Component,
    ElementRef,
    HostListener,
    forwardRef,
    Injectable,
    ViewChild,
} from '@angular/core';
import { NG_VALUE_ACCESSOR, ControlValueAccessor } from '@angular/forms';

export const CUSTOM_INPUT_CONTROL_VALUE_ACCESSOR: any = {
    provide: NG_VALUE_ACCESSOR,
    multi: true,
    /* tslint:disable */
    useExisting: forwardRef(() => WoDatepickerComponent)
    /* tslint:enable */
};

const noop = () => {};

@Component({
    selector: 'wo-datepicker',
    templateUrl: 'wo-datepicker.component.html',
    styleUrls: ['wo-datepicker.component.css'],
    providers: [CUSTOM_INPUT_CONTROL_VALUE_ACCESSOR]
})

@Injectable()
export class WoDatepickerComponent implements ControlValueAccessor {
    @ViewChild('wrapper', {static: false}) wrapperEl: ElementRef;
    public leftOffset = 0;
    public toLeft = false;
    public toRight = false;
    public mode = 'days'; // days, months

    // The internal data model
    public innerValue: any = '';

    public isShow = false;

    // Placeholders for the callbacks which are later providesd
    // by the Control Value Accessor
    private onTouchedCallback: () => void = noop;
    private onChangeCallback: (_: any) => void = noop;

    public months = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ];
    public short_months = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec'
    ];
    private daysofweek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    private wD = [0, 1, 2, 3, 4, 5, 6];
    // private wD = [6, 0, 1, 2, 3, 4, 5];

    private today: Date;
    public calendarDate: Date;
    date: Date;
    options = {format: 'mm/dd/yyyy'};
    // options = {format: 'dd-mm-yyyy'};

    public monthDays;

    // get accessor
    get value(): any {
        return this.innerValue;
    }

    // set accessor including call the onchange callback
    set value(v: any) {
        if (v !== this.innerValue) {
            this.innerValue = v;
            this.onChangeCallback(v);
        }
    }

    // From ControlValueAccessor interface
    writeValue(value: any) {
        if (value !== this.innerValue) {
            this.innerValue = value;
            this.onChangeCallback(this.innerValue);
        }
    }

    // From ControlValueAccessor interface
    registerOnChange(fn: any) {
        this.onChangeCallback = fn;
    }

    // From ControlValueAccessor interface
    registerOnTouched(fn: any) {
        this.onTouchedCallback = fn;
    }

    onChange () {
        this.onChangeCallback(this.innerValue);
    }

    constructor (
    ) {
        this.today = new Date();
        this.date = null;
    }

    @HostListener('window:keydown', ['$event']) keyboardInput(event: KeyboardEvent) {
        if (this.isShow === true) {
            if (event.key === 'Escape') {
                this.isShow = false;
            } else if (event.key === 'Tab') {
                this.isShow = false;
            } else if (event.key === 'ArrowRight') {
                this.nextMonth();
            } else if (event.key === 'ArrowLeft') {
                this.prevMonth();
            }
        }
    }

    @HostListener('window:mousedown', ['$event']) clickOnWindow(event: KeyboardEvent) {
        this.isShow = false;
    }

    @HostListener('mousedown', ['$event']) clickOnHost(event: KeyboardEvent) {
        if (this.isShow !== false) {
            this.toLeft = false;
            this.toRight = false;
            event.stopPropagation();
        }
    }

    open () {
        this.isShow = true;
        this.toLeft = false;
        this.toRight = false;

        this.leftOffset = Math.min(0, window.innerWidth - (this.wrapperEl.nativeElement.getBoundingClientRect().left + 230));

        const innserDate = this.parseFormat(this.innerValue, this.options.format);

        if (!isNaN(innserDate)) {
            this.date = innserDate;
            this.calendarDate = new Date(this.date);
        } else {
            this.date = null;
            this.calendarDate = new Date();
        }
        this.monthDays = this.getMonthDays();
        this.setMode('days');
    }

    nextMonth () {
        this.toLeft = true;
        this.calendarDate.setMonth(this.calendarDate.getMonth() + 1);
        this.monthDays = this.getMonthDays();
    }

    prevMonth () {
        this.toRight = true;
        this.calendarDate.setMonth(this.calendarDate.getMonth() - 1);
        this.monthDays = this.getMonthDays();
    }


    setMonth (i: number) {
        this.calendarDate.setMonth(i);
        this.setMode('days');
    }

    nextYear () {
        this.calendarDate.setFullYear(this.calendarDate.getFullYear() + 1);
    }

    prevYear () {
        this.calendarDate.setFullYear(this.calendarDate.getFullYear() - 1);
    }

    getMonthDays() {
        const monthDays = [];
        const monthWeek = [];

        const startMonthDate = new Date(this.calendarDate.getFullYear(), this.calendarDate.getMonth(), 1);

        const firstDay = new Date(startMonthDate.getFullYear(), startMonthDate.getMonth(), 1);
        const lastDay = new Date(startMonthDate.getFullYear(), startMonthDate.getMonth() + 1, 0);
        const lastMonthDay = new Date(startMonthDate.getFullYear(), startMonthDate.getMonth(), 0);

        const nextDate = new Date(startMonthDate);
        nextDate.setMonth(nextDate.getMonth() + 1);

        for (let i = 1 - this.wD[firstDay.getDay()]; i < 43 - this.wD[firstDay.getDay()]; i++) {
            if (i < 1) {
                monthDays.push({
                    num: lastMonthDay.getDate() + i,
                    isNextMonth: false,
                    isPrevMonth: true,
                    isCurrent: false,
                    year: lastMonthDay.getFullYear(),
                    month: lastMonthDay.getMonth() + 1
                });
            } else if (i > 0 && i <= lastDay.getDate()) {
                let isSelected = false;
                if (this.date) {
                    if (startMonthDate.getFullYear() === this.date.getFullYear()) {
                        if (startMonthDate.getMonth() === this.date.getMonth()) {
                            if (i === this.date.getDate()) {
                                isSelected = true;
                            }
                        }
                    }
                }

                let isCurrent = false;
                if (i === this.today.getDate()) {
                    if (this.today.getMonth() === startMonthDate.getMonth()) {
                        if (this.today.getFullYear() === startMonthDate.getFullYear()) {
                            isCurrent = true;
                        }
                    }
                }

                monthDays.push({
                    num: i,
                    isNextMonth: false,
                    isPrevMonth: false,
                    isCurrent: isCurrent,
                    isSelected: isSelected,
                    year: startMonthDate.getFullYear(),
                    month: startMonthDate.getMonth() + 1
                });
            } else {
                monthDays.push({
                    num: i - lastDay.getDate(),
                    isNextMonth: true,
                    isPrevMonth: true,
                    isCurrent: false,
                    year: nextDate.getFullYear(),
                    month: nextDate.getMonth() + 1
                });
            }
        }

        let k, j;
        const chunk = 7;
        for (k = 0, j = monthDays.length; k < j; k += chunk) {
            monthWeek.push(monthDays.slice(k, k + chunk));
        }

        return monthWeek;
    }

    setDate (event: Event, selectedItem) {
        const selectedDate = new Date();

        selectedDate.setMonth(0);

        selectedDate.setDate(selectedItem.num);
        selectedDate.setFullYear(selectedItem.year);
        selectedDate.setMonth(selectedItem.month - 1);

        this.isShow = false;
        this.writeValue(this.formatDate(selectedDate));
    }

    formatDate (d) {
        const day = d.getDate();
        const m = d.getMonth();
        const y = d.getFullYear();
        return this.options.format.replace(/(yyyy|yy|mmmm|mmm|mm|m|dd|d)/gi, function (e) {
            switch (e.toLowerCase()) {
                case 'd': return day;
                case 'dd': return (day < 10 ? '0' + day : day);
                case 'm': return m + 1;
                case 'mm': return (m + 1 < 10 ? '0' + (m + 1) : (m + 1));
                case 'mmm': return this.short_months[m];
                case 'mmmm': return this.months[m];
                case 'yy': return y.toString().substr(2, 2);
                case 'yyyy': return y;
            }
        });
    }

    setMode (mode) {
        this.mode = mode;
        if (mode === 'days') {
            this.monthDays = this.getMonthDays();
        }
    }

    parseFormat(value, format): any {
        if (value === '' || typeof value === 'undefined') {
            return NaN;
        }

        value = value.replace(/[\.\-\/\s]/g, '$').split('$');
        format = format.replace(/[\.\-\/\s]/g, '$').split('$');

        const date = new Date();

        for (let i = format.length - 1; i >= 0; i--) {
            if (format[i] === 'yyyy') {
                date.setFullYear(value[i]);
            } else if (format[i] === 'mm') {
                date.setMonth(parseInt(value[i], 10) - 1);
            } else if (format[i] === 'dd') {
                date.setDate(value[i]);
            }
        }

        return date;
    }
}

CUSTOM_INPUT_CONTROL_VALUE_ACCESSOR.useExisting = forwardRef(() => WoDatepickerComponent);
