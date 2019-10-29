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
    useExisting: forwardRef(() => WoTimepickerComponent),
    multi: true
};

const noop = () => {};

@Component({
    selector: 'wo-timepicker',
    templateUrl: 'wo-timepicker.component.html',
    styleUrls: ['wo-timepicker.component.css'],
    providers: [CUSTOM_INPUT_CONTROL_VALUE_ACCESSOR]
})

@Injectable()
export class WoTimepickerComponent implements ControlValueAccessor {
    @ViewChild('wrapper', {static: false}) wrapperEl:ElementRef;

    // The internal data model
    public innerValue: any = '';

    public isOpen = false;

    public hours = 12;
    public minutes = 0;
    private hour12 = true;
    public isAM = true;
    public showValue = '';

    // Placeholders for the callbacks which are later providesd
    // by the Control Value Accessor
    private onTouchedCallback: () => void = noop;
    private onChangeCallback: (_: any) => void = noop;

    @HostListener('window:mousedown', ['$event']) clickOnWindow(event: KeyboardEvent) {
        if (this.wrapperEl.nativeElement.contains(event.target) === false) {
            this.isOpen = false;
        }
    }

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
            this.parseValue(value);
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

    onChange (event) {
        this.onChangeCallback(this.innerValue);
    }

    constructor (
        private el: ElementRef
    ) {
    }

    open() {
        this.isOpen = true;
    }

    doUpHour() {
        this.hours++;
        if (this.hour12 === true) {
            if (this.hours > 12) {
                this.hours = 1;
            }
        } else if (this.hours > 23) {
            this.hours = 0;
        }

        this.updateShowValue();
        this.updateValue();
    }

    doDownHour() {
        this.hours--;
        if (this.hours < 1) {
            this.hours = this.hour12 === true ? 12 : 23;
        }

        this.updateShowValue();
        this.updateValue();
    }

    doChangePeriod() {
        this.isAM = !this.isAM;

        this.updateShowValue();
        this.updateValue();
    }

    doUpMinute() {
        this.minutes += -this.minutes % 5 + 5;
        if (this.minutes > 59) {
            this.minutes = 0;
        }

        this.updateShowValue();
        this.updateValue();
    }

    doDownMinute() {
        if (this.minutes % 5 !== 0) {
            this.minutes -= (5 - (5 - this.minutes % 5));
        } else {
            this.minutes -= 5;
        }

        if (this.minutes < 0) {
            this.minutes = 55;
        }

        this.updateShowValue();
        this.updateValue();
    }

    parseValue(value) {
        if (value) {
            const partsOfStr = value.split(':');

            if (partsOfStr.length > 1) {
                this.hours = parseInt(partsOfStr[0], 10);
                this.minutes = parseInt(partsOfStr[1], 10);

                if (this.hours > 12) {
                    this.isAM = false;
                } else {
                    this.isAM = true;
                }

                if (this.hour12 === true) {
                    this.hours = this.hours % 12;
                    if (this.hours === 0) {
                        this.hours = 12;
                    }
                }
            } else {
                this.hours = 12;
                this.minutes = 0;
            }
        }

        this.updateShowValue();
        this.updateValue();
    }

    updateValue() {
        const hours = (this.isAM === true ? 0 : 12) + this.hours;
        this.value = (hours >= 0 && hours <= 9 ? '0' : '') + hours +
                    ':'
                    + (this.minutes >= 0 && this.minutes <= 9 ? '0' : '') + this.minutes;
    }

    getFormatValue() {
        return (this.hours >= 0 && this.hours <= 9 ? '0' : '') + this.hours + ':'
            + (this.minutes >= 0 && this.minutes <= 9 ? '0' : '') + this.minutes + ' '
            + (this.isAM ? 'AM' : 'PM');
    }

    updateShowValue() {
        this.showValue = this.getFormatValue();
    }

    doBlur(event) {
        const partsOfStr = event.target.value.split(':');

        if (partsOfStr.length > 1) {
            this.hours = parseInt(partsOfStr[0], 10);
            this.minutes = parseInt(partsOfStr[1], 10);
        }

        this.isAM = event.target.value.toUpperCase().indexOf('AM') !== -1;

        this.updateShowValue();
        this.updateValue();
    }
}
